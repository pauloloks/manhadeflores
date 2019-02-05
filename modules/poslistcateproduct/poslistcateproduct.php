<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since   1.5.0
 */

if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_MODULE_DIR_.'poslistcateproduct/PosCatePro.php');

class PosListCateProduct extends Module
{
	protected $_html = '';
	protected $identifier = false;
	public $className;

	protected $default_row = 1;
	protected $default_items = 5;
	protected $default_speed = 1000;
	protected $default_delay = 3000;
	protected $default_auto = 0;
	protected $default_arrow = 1;
	protected $default_nav = 0;
	protected $default_limit = 8;
	protected $default_sub = 1;

	public function __construct()
	{
		$this->name = 'poslistcateproduct';
		$this->tab = 'front_office_features';
		$this->version = '1.6.0';
		$this->author = 'Posthemes';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);
		$this->bootstrap = true;
		$this->_htmlm        = '';

		parent::__construct();

		$this->displayName = $this->l('Pos list category products');
		$this->description = $this->l('Show products from categories which display in homepage');
		$this->ps_versions_compliancy = array('min' => '1.6.0.9', 'max' => _PS_VERSION_);
	}

	/**
	 * @see Module::install()
	 */
	public function install()
	{
		/* Adds Module */
		if (parent::install() &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('blockPosition2') &&
			$this->registerHook('actionShopDataDuplication')
		)
		{
		$shops = Shop::getContextListShopID();
		$shop_groups_list = array();

		/* Setup each shop */
		foreach ($shops as $shop_id)
		{
			$shop_group_id = (int)Shop::getGroupFromShop($shop_id, true);

			if (!in_array($shop_group_id, $shop_groups_list))
				$shop_groups_list[] = $shop_group_id;

			/* Sets up configuration */
			$res = Configuration::updateValue($this->name.'_row', $this->default_row, false, $shop_group_id, $shop_id);
			$res &= Configuration::updateValue($this->name.'_items', $this->default_items, false, $shop_group_id, $shop_id);
			$res &= Configuration::updateValue($this->name.'_speed', $this->default_speed, false, $shop_group_id, $shop_id);
			$res &= Configuration::updateValue($this->name.'_delay', $this->default_delay, false, $shop_group_id, $shop_id);
			$res &= Configuration::updateValue($this->name.'_auto', $this->default_auto, false, $shop_group_id, $shop_id);
			$res &= Configuration::updateValue($this->name.'_arrow', $this->default_arrow, false, $shop_group_id, $shop_id);
			$res &= Configuration::updateValue($this->name.'_nav', $this->default_nav, false, $shop_group_id, $shop_id);
			$res &= Configuration::updateValue($this->name.'_limit', $this->default_limit, false, $shop_group_id, $shop_id);
			$res &= Configuration::updateValue($this->name.'_sub', $this->default_sub, false, $shop_group_id, $shop_id);
		}

		/* Sets up Shop Group configuration */
		if (count($shop_groups_list))
		{
			foreach ($shop_groups_list as $shop_group_id)
			{
				$res = Configuration::updateValue($this->name.'_row', $this->default_row, false, $shop_group_id);
				$res &= Configuration::updateValue($this->name.'_items', $this->default_items, false, $shop_group_id);
				$res &= Configuration::updateValue($this->name.'_speed', $this->default_speed, false, $shop_group_id);
				$res &= Configuration::updateValue($this->name.'_delay', $this->default_delay, false, $shop_group_id);
				$res &= Configuration::updateValue($this->name.'_auto', $this->default_auto, false, $shop_group_id);
				$res &= Configuration::updateValue($this->name.'_arrow', $this->default_arrow, false, $shop_group_id);
				$res &= Configuration::updateValue($this->name.'_nav', $this->default_nav, false, $shop_group_id);
				$res &= Configuration::updateValue($this->name.'_limit', $this->default_limit, false, $shop_group_id);
				$res &= Configuration::updateValue($this->name.'_sub', $this->default_sub, false, $shop_group_id);
			}
		}

		/* Sets up Global configuration */
		$res = Configuration::updateValue($this->name.'_row', $this->default_row);
		$res &= Configuration::updateValue($this->name.'_items', $this->default_items);
		$res &= Configuration::updateValue($this->name.'_speed', $this->default_speed);
		$res &= Configuration::updateValue($this->name.'_delay', $this->default_delay);
		$res &= Configuration::updateValue($this->name.'_auto', $this->default_auto);
		$res &= Configuration::updateValue($this->name.'_arrow', $this->default_arrow);
		$res &= Configuration::updateValue($this->name.'_nav', $this->default_nav);
		$res &= Configuration::updateValue($this->name.'_limit', $this->default_limit);
		$res &= Configuration::updateValue($this->name.'_sub', $this->default_sub);
			/* Creates tables */
			$res &= $this->createTables();

			/* Adds samples */
			if ($res)
				$this->installDemoData();

			// Disable on mobiles and tablets
			$this->disableDevice(Context::DEVICE_MOBILE);

			return (bool)$res;
		}

		return false;
	}

	/**
	 * Adds samples
	 */
	protected function installDemoData()
	{
	  $languages = Language::getLanguages(false);
	  for ($i = 1; $i <= 3; ++$i)
	  {
	   $item = new PosCatePro();
	   $item->position = $i;
	   $item->active = 1;
	   $item->id_category = $i +2 ;
	   $item->list_subcategories= '9,10,11';
	   foreach ($languages as $language)
	   {
		$item->url[$language['id_lang']] = 'http://posthemes.com';
		$item->image[$language['id_lang']] = 'thumb-'.$i.'.jpg';
	   }
	   $item->add();
	  }
	}

	/**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
		/* Deletes Module */
		if (parent::uninstall())
		{
			/* Deletes tables */
			$res = $this->deleteTables();

			/* Unsets configuration */
			$res &= Configuration::deleteByName($this->name.'_row');
			$res &= Configuration::deleteByName($this->name.'_items');
			$res &= Configuration::deleteByName($this->name.'_speed');
			$res &= Configuration::deleteByName($this->name.'_delay');
			$res &= Configuration::deleteByName($this->name.'_auto');
			$res &= Configuration::deleteByName($this->name.'_arrow');
			$res &= Configuration::deleteByName($this->name.'_nav');
			$res &= Configuration::deleteByName($this->name.'_limit');
			$res &= Configuration::deleteByName($this->name.'_sub');

			return (bool)$res;
		}

		return false;
	}

	/**
	 * Creates tables
	 */
	protected function createTables()
	{
		/* Slides */
		$res = (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'poslistcateproduct` (
				`id_poslistcateproduct_items` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_poslistcateproduct_items`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* Slides configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'poslistcateproduct_items` (
			  `id_poslistcateproduct_items` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `position` int(10) unsigned NOT NULL DEFAULT \'0\',
			  `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  `id_category` int(10) unsigned NOT NULL DEFAULT \'0\',
			  `list_subcategories` varchar(255),
			  PRIMARY KEY (`id_poslistcateproduct_items`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		/* Slides lang configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'poslistcateproduct_items_lang` (
			  `id_poslistcateproduct_items` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `description` text,
			  `url` varchar(255),
			  `image` varchar(255),
			  PRIMARY KEY (`id_poslistcateproduct_items`,`id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		return $res;
	}

	/**
	 * deletes tables
	 */
	protected function deleteTables()
	{
		$items = $this->getItems();
		foreach ($items as $item)
		{
			$to_del = new PosCatePro($item['id_item']);
			$to_del->delete();
		}

		return Db::getInstance()->execute('
			DROP TABLE IF EXISTS `'._DB_PREFIX_.'poslistcateproduct`, `'._DB_PREFIX_.'poslistcateproduct_items`, `'._DB_PREFIX_.'poslistcateproduct_items_lang`;
		');
	}

	public function getContent()
	{
		$this->_html .= $this->headerHTML();

		/* Validate & process */
		if (Tools::isSubmit('submitPosProductCatesItem') || Tools::isSubmit('delete_id_item') ||
			Tools::isSubmit('submitPosProductCates') ||
			Tools::isSubmit('changeStatus')
		)
		{
			if ($this->_postValidation())
			{
				$this->_postProcess();
				$this->_html .= $this->renderForm();
				$this->_html .= $this->renderList();
			}
			else
				$this->_html .= $this->renderAddForm();

			$this->clearCache();
		}
		elseif (Tools::isSubmit('addItem') || (Tools::isSubmit('id_item') && $this->slideExists((int)Tools::getValue('id_item'))))
		{
			if (Tools::isSubmit('addItem'))
				$mode = 'add';
			else
				$mode = 'edit';

			if ($mode == 'add')
			{
				if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL)
					$this->_html .= $this->renderAddForm();
				else
					$this->_html .= $this->getShopContextError(null, $mode);
			}
			else
			{
				$associated_shop_ids = PosCatePro::getAssociatedIdsShop((int)Tools::getValue('id_item'));
				$context_shop_id = (int)Shop::getContextShopID();

				if ($associated_shop_ids === false)
					$this->_html .= $this->getShopAssociationError((int)Tools::getValue('id_item'));
				else if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL && in_array($context_shop_id, $associated_shop_ids))
				{
					if (count($associated_shop_ids) > 1)
						$this->_html = $this->getSharedSlideWarning();
					$this->_html .= $this->renderAddForm();
				}
				else
				{
					$shops_name_list = array();
					foreach ($associated_shop_ids as $shop_id)
					{
						$associated_shop = new Shop((int)$shop_id);
						$shops_name_list[] = $associated_shop->name;
					}
					$this->_html .= $this->getShopContextError($shops_name_list, $mode);
				}
			}
		}
		else // Default viewport
		{
			$this->_html .= $this->getWarningMultishopHtml().$this->getCurrentShopInfoMsg().$this->renderForm();

			if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL)
				$this->_html .= $this->renderList();
		}

		return $this->_html;
	}
			
	protected function _postValidation()
	{
		$errors = array();

		/* Validation for Slider configuration */
		if (Tools::isSubmit('submitPosProductCates'))
		{

			if (!Validate::isInt(Tools::getValue($this->name.'_row')) || !Validate::isInt(Tools::getValue($this->name.'_items')) ||
				!Validate::isInt(Tools::getValue($this->name.'_speed')) || !Validate::isInt(Tools::getValue($this->name.'_delay')) ||
				!Validate::isInt(Tools::getValue($this->name.'_limit'))
			)
				$errors[] = $this->l('Invalid values');
		} /* Validation for status */
		elseif (Tools::isSubmit('changeStatus'))
		{
			if (!Validate::isInt(Tools::getValue('id_item')))
				$errors[] = $this->l('Invalid slide');
		}
		/* Validation for Slide */
		elseif (Tools::isSubmit('submitPosProductCatesItem'))
		{
			/* Checks state (active) */
			if (!Validate::isInt(Tools::getValue('active_slide')) || (Tools::getValue('active_slide') != 0 && Tools::getValue('active_slide') != 1))
				$errors[] = $this->l('Invalid slide state.');
			/* Checks position */
			if (!Validate::isInt(Tools::getValue('position')) || (Tools::getValue('position') < 0))
				$errors[] = $this->l('Invalid slide position.');
			/* If edit : checks id_item */
			if (Tools::isSubmit('id_item'))
			{
				
				//d(var_dump(Tools::getValue('id_item')));
				if (!Validate::isInt(Tools::getValue('id_item')) && !$this->slideExists(Tools::getValue('id_item')))
					$errors[] = $this->l('Invalid slide ID');
			}
			/* Checks url/description/image */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (Tools::strlen(Tools::getValue('url_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The URL is too long.');
				if (Tools::strlen(Tools::getValue('description_'.$language['id_lang'])) > 4000)
					$errors[] = $this->l('The description is too long.');
				if (Tools::strlen(Tools::getValue('url_'.$language['id_lang'])) > 0 && !Validate::isUrl(Tools::getValue('url_'.$language['id_lang'])))
					$errors[] = $this->l('The URL format is not correct.');
				if (Tools::getValue('image_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_'.$language['id_lang'])))
					$errors[] = $this->l('Invalid filename.');
				if (Tools::getValue('image_old_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_old_'.$language['id_lang'])))
					$errors[] = $this->l('Invalid filename.');
			}

			/* Checks url/description for default lang */
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::getValue('id_category') == 0)
				$errors[] = $this->l('You have to select a category.');
			if (!Tools::isSubmit('has_picture') && (!isset($_FILES['image_'.$id_lang_default]) || empty($_FILES['image_'.$id_lang_default]['tmp_name'])))
				$errors[] = $this->l('The image is not set.');
			if (Tools::getValue('image_old_'.$id_lang_default) && !Validate::isFileName(Tools::getValue('image_old_'.$id_lang_default)))
				$errors[] = $this->l('The image is not set.');
		} /* Validation for deletion */
		elseif (Tools::isSubmit('delete_id_item') && (!Validate::isInt(Tools::getValue('delete_id_item')) || !$this->slideExists((int)Tools::getValue('delete_id_item'))))
			$errors[] = $this->l('Invalid slide ID');

		/* Display errors if needed */
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));

			return false;
		}

		/* Returns if validation is ok */

		return true;
	}

	protected function _postProcess()
	{
		$errors = array();
		$shop_context = Shop::getContext();

		/* Processes Slider */
		if (Tools::isSubmit('submitPosProductCates'))
		{
			$shop_groups_list = array();
			$shops = Shop::getContextListShopID();

			foreach ($shops as $shop_id)
			{
				$shop_group_id = (int)Shop::getGroupFromShop($shop_id, true);

				if (!in_array($shop_group_id, $shop_groups_list))
					$shop_groups_list[] = $shop_group_id;

				$res = Configuration::updateValue($this->name.'_row', (int)Tools::getValue($this->name.'_row'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue($this->name.'_items', (int)Tools::getValue($this->name.'_items'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue($this->name.'_speed', (int)Tools::getValue($this->name.'_speed'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue($this->name.'_delay', (int)Tools::getValue($this->name.'_delay'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue($this->name.'_auto', (int)Tools::getValue($this->name.'_auto'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue($this->name.'_arrow', (int)Tools::getValue($this->name.'_arrow'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue($this->name.'_nav', (int)Tools::getValue($this->name.'_nav'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue($this->name.'_limit', (int)Tools::getValue($this->name.'_limit'), false, $shop_group_id, $shop_id);
				$res &= Configuration::updateValue($this->name.'_sub', (int)Tools::getValue($this->name.'_sub'), false, $shop_group_id, $shop_id);
			}

			/* Update global shop context if needed*/
			switch ($shop_context)
			{
				case Shop::CONTEXT_ALL:
					$res = Configuration::updateValue($this->name.'_row', (int)Tools::getValue($this->name.'_row'));
					$res &= Configuration::updateValue($this->name.'_items', (int)Tools::getValue($this->name.'_items'));
					$res &= Configuration::updateValue($this->name.'_speed', (int)Tools::getValue($this->name.'_speed'));
					$res &= Configuration::updateValue($this->name.'_delay', (int)Tools::getValue($this->name.'_delay'));
					$res &= Configuration::updateValue($this->name.'_auto', (int)Tools::getValue($this->name.'_auto'));
					$res &= Configuration::updateValue($this->name.'_arrow', (int)Tools::getValue($this->name.'_arrow'));
					$res &= Configuration::updateValue($this->name.'_nav', (int)Tools::getValue($this->name.'_nav'));
					$res &= Configuration::updateValue($this->name.'_limit', (int)Tools::getValue($this->name.'_limit'));
					$res &= Configuration::updateValue($this->name.'_sub', (int)Tools::getValue($this->name.'_sub'));
					if (count($shop_groups_list))
					{
						foreach ($shop_groups_list as $shop_group_id)
						{
							$res = Configuration::updateValue($this->name.'_row', (int)Tools::getValue($this->name.'_row'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_items', (int)Tools::getValue($this->name.'_items'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_speed', (int)Tools::getValue($this->name.'_speed'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_delay', (int)Tools::getValue($this->name.'_delay'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_auto', (int)Tools::getValue($this->name.'_auto'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_arrow', (int)Tools::getValue($this->name.'_arrow'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_nav', (int)Tools::getValue($this->name.'_nav'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_limit', (int)Tools::getValue($this->name.'_limit'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_sub', (int)Tools::getValue($this->name.'_sub'), false, $shop_group_id);
						}
					}
					break;
				case Shop::CONTEXT_GROUP:
					if (count($shop_groups_list))
					{
						foreach ($shop_groups_list as $shop_group_id)
						{
							$res = Configuration::updateValue($this->name.'_row', (int)Tools::getValue($this->name.'_row'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_items', (int)Tools::getValue($this->name.'_items'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_speed', (int)Tools::getValue($this->name.'_speed'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_delay', (int)Tools::getValue($this->name.'_delay'), false, $shop_group_id);
							$res = Configuration::updateValue($this->name.'_auto', (int)Tools::getValue($this->name.'_auto'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_arrow', (int)Tools::getValue($this->name.'_arrow'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_nav', (int)Tools::getValue($this->name.'_nav'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_limit', (int)Tools::getValue($this->name.'_limit'), false, $shop_group_id);
							$res &= Configuration::updateValue($this->name.'_sub', (int)Tools::getValue($this->name.'_sub'), false, $shop_group_id);
						}
					}
					break;
			}

			$this->clearCache();

			if (!$res)
				$errors[] = $this->displayError($this->l('The configuration could not be updated.'));
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=6&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		} /* Process Slide status */
		elseif (Tools::isSubmit('changeStatus') && Tools::isSubmit('id_item'))
		{
			$item = new PosCatePro((int)Tools::getValue('id_item'));
			if ($item->active == 0)
				$item->active = 1;
			else
				$item->active = 0;
			$res = $item->update();
			$this->clearCache();
			$this->_html .= ($res ? $this->displayConfirmation($this->l('Configuration updated')) : $this->displayError($this->l('The configuration could not be updated.')));
		}
		/* Processes item */
		elseif (Tools::isSubmit('submitPosProductCatesItem'))
		{
			/* Sets ID if needed */
			if (Tools::getValue('id_item'))
			{
				$item = new PosCatePro((int)Tools::getValue('id_item'));
				if (!Validate::isLoadedObject($item))
				{
					$this->_html .= $this->displayError($this->l('Invalid item ID'));
					return false;
				}
			}
			else
				$item = new PosCatePro();
			
			/* Sets active */
			$item->active = (int)Tools::getValue('active_slide');
			
			$item->id_category = (int)Tools::getValue('id_category');

			$item->list_subcategories = implode(',',Tools::getValue('list_cate'));

			/* Sets each langue fields */
			$languages = Language::getLanguages(false);

			foreach ($languages as $language)
			{
				$item->url[$language['id_lang']] = Tools::getValue('url_'.$language['id_lang']);
				$item->description[$language['id_lang']] = Tools::getValue('description_'.$language['id_lang']);

				/* Uploads image and sets item */
				$type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
				$imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name']);
				if (isset($_FILES['image_'.$language['id_lang']]) &&
					isset($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
					!empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
					!empty($imagesize) &&
					in_array(
						Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), array(
							'jpg',
							'gif',
							'jpeg',
							'png'
						)
					) &&
					in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
				)
				{
					$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
					//print_r($temp_name);die;
					$salt = sha1(microtime());
					if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']]))
						$errors[] = $error;
					elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name))
						return false;
					elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/images/'.$salt.'_'.$_FILES['image_'.$language['id_lang']]['name'], null, null, $type))
						$errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
					if (isset($temp_name))
						@unlink($temp_name);
					$item->image[$language['id_lang']] = $salt.'_'.$_FILES['image_'.$language['id_lang']]['name'];
				}
				elseif (Tools::getValue('image_old_'.$language['id_lang']) != '')
					$item->image[$language['id_lang']] = Tools::getValue('image_old_'.$language['id_lang']);
			}

			/* Processes if no errors  */
			if (!$errors)
			{
				/* Adds */
				if (!Tools::getValue('id_item'))
				{
					/* Sets position */
					$position = $this->getNextPosition();
					$item->position = $position;
					if (!$item->add())
						$errors[] = $this->displayError($this->l('The item could not be added.'));
				}
				/* Update */
				elseif (!$item->update())
					$errors[] = $this->displayError($this->l('The item could not be updated.'));
				$this->clearCache();
			}
		} /* Deletes */
		elseif (Tools::isSubmit('delete_id_item'))
		{
			$item = new PosCatePro((int)Tools::getValue('delete_id_item'));
			$res = $item->delete();
			$this->clearCache();
			if (!$res)
				$this->_html .= $this->displayError('Could not delete.');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		}

		/* Display errors if needed */
		if (count($errors))
			$this->_html .= $this->displayError(implode('<br />', $errors));
		elseif (Tools::isSubmit('submitPosProductCatesItem') && Tools::getValue('id_item'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		elseif (Tools::isSubmit('submitPosProductCatesItem'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
	}

	public function hookdisplayHeader($params)
	{
	    Tools::addCSS(($this->_path).'poslistcateproduct.css');
	}

	public function hookblockPosition2($params)
	{
		if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index')
			return;

		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
		
		
		 $config = $this->getConfigFieldsValues();
		 $configure = array(
			 'row' => $config[$this->name.'_row'],
			 'items' => $config[$this->name.'_items'],
			 'speed' => $config[$this->name.'_speed'],
			 'delay' => $config[$this->name.'_delay'],
			 'auto' => (bool)$config[$this->name.'_auto'],
			 'arrow' => (bool)$config[$this->name.'_arrow'],
			 'nav' => (bool)$config[$this->name.'_nav'],
			 'limit' => $config[$this->name.'_limit'],	
			 'show_sub' => $config[$this->name.'_sub'],	
		 );

		 $this->smarty->assign('configure', $configure);
		
		    $nb = $configure['limit']; 
		
			$items = $this->getItems(true);
			
			
			if (is_array($items))
				foreach ($items as &$item)
				{	
					$subcategories= array();
					$item['sizes'] = @getimagesize((dirname(__FILE__).DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$item['image']));
					if (isset($item['sizes'][3]) && $item['sizes'][3])
						$item['size'] = $item['sizes'][3];
					 $category =  new Category((int) $item['id_category'], (int) $id_lang, (int) $id_shop);
					 $item['category_name'] = $category->name;	
					 $categoryProducts = $category->getProducts($this->context->language->id, 0, ($nb ? $nb :20),'date_add','DESC');
					 $item['product'] = $categoryProducts;
					 $list_subcategories= explode(',',$item['list_subcategories']);
					 foreach($list_subcategories as $subcategory){
					 	$category =  new Category((int) $subcategory, (int) $id_lang, (int) $id_shop);

					 	$subcategories[]= array(
					 		'name' => $category->name,
					 		'id_category' => $subcategory
					 	);
					 }
					 $item['list_subcategories'] = $subcategories;
	
				}
			//echo '<pre>'; print_r($items);die;
			if (!$items)
				return false;
			$this->smarty->assign(array(
				'productCates' => $items,
			));
		
		return $this->display(__FILE__, 'poslistcateproduct.tpl', $this->getCacheId());
	}

	public function clearCache()
	{
		$this->_clearCache('poslistcateproduct.tpl');
	}

	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
			INSERT IGNORE INTO '._DB_PREFIX_.'poslistcateproduct (id_poslistcateproduct_items, id_shop)
			SELECT id_poslistcateproduct_items, '.(int)$params['new_id_shop'].'
			FROM '._DB_PREFIX_.'poslistcateproduct
			WHERE id_shop = '.(int)$params['old_id_shop']
		);
		$this->clearCache();
	}

	public function headerHTML()
	{
		if (Tools::getValue('controller') != 'AdminModules' && Tools::getValue('configure') != $this->name)
			return;

		$this->context->controller->addJqueryUI('ui.sortable');
		/* Style & js for fieldset 'slides configuration' */
		$html = '<script type="text/javascript">
			$(function() {
				var $mySlides = $("#items");
				$mySlides.sortable({
					opacity: 0.6,
					cursor: "move",
					update: function() {
						var order = $(this).sortable("serialize") + "&action=updateItemsPosition";
						$.post("'.$this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/ajax_'.$this->name.'.php?secure_key='.$this->secure_key.'", order);
						}
						

					});
				$mySlides.hover(function() {
					$(this).css("cursor","move");
					},
					function() {
					$(this).css("cursor","auto");
				});
			});
		</script>';

		return $html;
	}

	public function getNextPosition()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT MAX(pci.`position`) AS `next_position`
			FROM `'._DB_PREFIX_.'poslistcateproduct_items` pci, `'._DB_PREFIX_.'poslistcateproduct` pc
			WHERE pci.`id_poslistcateproduct_items` = pc.`id_poslistcateproduct_items` AND pc.`id_shop` = '.(int)$this->context->shop->id
		);

		return (++$row['next_position']);
	}

	public function getItems($active = null)
	{
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT pc.`id_poslistcateproduct_items` as id_item, pci.`list_subcategories`, pci.`position`, pci.`active`, pci.`id_category`,
			pcil.`url`, pcil.`description`, pcil.`image`
			FROM '._DB_PREFIX_.'poslistcateproduct pc 
			LEFT JOIN '._DB_PREFIX_.'poslistcateproduct_items pci ON (pc.id_poslistcateproduct_items= pci.id_poslistcateproduct_items)
			LEFT JOIN '._DB_PREFIX_.'poslistcateproduct_items_lang pcil ON (pci.id_poslistcateproduct_items = pcil.id_poslistcateproduct_items)
			WHERE id_shop = '.(int)$id_shop.'
			AND pcil.id_lang = '.(int)$id_lang.
			($active ? ' AND pci.`active` = 1' : ' ').'
			ORDER BY pci.position'
			
		);
	}

	public function getAllImagesBySlidesId($id_items, $active = null, $id_shop = null)
	{
		$this->context = Context::getContext();
		$images = array();

		if (!isset($id_shop))
			$id_shop = $this->context->shop->id;

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hssl.`image`, hssl.`id_lang`
			FROM '._DB_PREFIX_.'poslistcateproduct hs
			LEFT JOIN '._DB_PREFIX_.'poslistcateproduct_items hss ON (hs.id_poslistcateproduct_items = hss.id_poslistcateproduct_items)
			LEFT JOIN '._DB_PREFIX_.'poslistcateproduct_items_lang hssl ON (hss.id_poslistcateproduct_items = hssl.id_poslistcateproduct_items)
			WHERE hs.`id_poslistcateproduct_items` = '.(int)$id_items.' AND hs.`id_shop` = '.(int)$id_shop.
			($active ? ' AND hss.`active` = 1' : ' ')
		);

		foreach ($results as $result)
			$images[$result['id_lang']] = $result['image'];

		return $images;
	}

	public function displayStatus($id_item, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&id_item='.(int)$id_item.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

		return $html;
	}

	public function slideExists($id_item)
	{
		$req = 'SELECT hs.`id_poslistcateproduct_items` as id_item
				FROM `'._DB_PREFIX_.'poslistcateproduct` hs
				WHERE hs.`id_poslistcateproduct_items` = '.(int)$id_item;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

		return ($row);
	}

	public function renderList()
	{	
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
		$items = $this->getItems();
		foreach ($items as $key => $item)
		{
			$items[$key]['status'] = $this->displayStatus($item['id_item'], $item['active']);
			$associated_shop_ids = PosCatePro::getAssociatedIdsShop((int)$item['id_item']);
			if ($associated_shop_ids && count($associated_shop_ids) > 1)
				$items[$key]['is_shared'] = true;
			else
				$items[$key]['is_shared'] = false;
			$category =  new Category((int) $items[$key]['id_category'], (int) $id_lang, (int) $id_shop);
			$items[$key]['category_name'] = $category->name;
			
		}

		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'items' => $items,
				'image_baseurl' => $this->_path.'images/'
			)
		);

		return $this->display(__FILE__, 'list.tpl');
	}

	public function renderAddForm()
	{	$id_item = Tools::getValue('id_item');
		//echo '<pre>'; print_r(Tools::getValue('items'));die;
        $selected_categories = array((int)$this->getCurrentCategory($id_item));

		$selected_subcategories = $this->getCurrentSubcategories($id_item);
		$selected_subcategories = explode(',', $selected_subcategories);
		$id_lang = (int) Context::getContext()->language->id;
        $options =  $this->getCategoryOption(1, (int)$id_lang, (int)Shop::getContextShopID());
		
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Slide information'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'file_lang',
						'label' => $this->l('Image/Thumbnail'),
						'name' => 'image',
						'required' => true,
						'lang' => true,
						'desc' => sprintf($this->l('Maximum image size: %s.'), ini_get('upload_max_filesize'))
					),
					array(
						'type' => 'text',
						'label' => $this->l('Target URL'),
						'name' => 'url',
						'lang' => true,
					),
					 array(
                    'type'  => 'categories',
                    'label' => $this->l('Select category'),
                    'name'  => 'id_category',
					'required' => true,
					'hint' => $this->l('This module will show products which belong to this category'),
                    'tree'  => array(
                        'id'                  => 'categories-tree',
                        'selected_categories' => $selected_categories
                    	)
                	),
					 array(
						'type' => 'selectlist',
						'label' => 'Choose the subcategories:',
						'name' => 'list_cate',
						'multiple'=>true,
						'size' => 500,
						'hint' => $this->l('The subcategories, which are chosen, will be shown as links.'),
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Description'),
						'name' => 'description',
						'autoload_rte' => true,
						'lang' => true,
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Enabled'),
						'name' => 'active_slide',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		if (Tools::isSubmit('id_item') && $this->slideExists((int)Tools::getValue('id_item')))
		{
			$slide = new PosCatePro((int)Tools::getValue('id_item'));
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_item');
			$fields_form['form']['images'] = $slide->image;

			$has_picture = true;

			foreach (Language::getLanguages(false) as $lang)
				if (!isset($slide->image[$lang['id_lang']]))
					$has_picture &= false;

			if ($has_picture)
				$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'has_picture');
		}

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->className = $this->className;
		$helper->submit_action = 'submitPosProductCatesItem';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'images/',
			'options' => $options,
			'selected_subcategories' => $selected_subcategories,
		);
		

		$helper->override_folder = '/';

		$languages = Language::getLanguages(false);

		if (count($languages) > 1)
			return $this->getMultiLanguageInfoMsg().$helper->generateForm(array($fields_form));
		else
			return $helper->generateForm(array($fields_form));
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Rows'),
						'name' => 'poslistcateproduct_row',
						'class' => 'fixed-width-sm',
						'desc' => $this->l('Number rows of module')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Number items'),
						'name' => 'poslistcateproduct_items',
						'class' => 'fixed-width-sm',
						'desc' => $this->l('Number items which is shown on resolution.')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Slide speed'),
						'name' => 'poslistcateproduct_speed',
						'suffix' => 'milliseconds',
						'class' => 'fixed-width-sm',
						'desc' => $this->l('Default is 1000ms.')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Auto time'),
						'name' => 'poslistcateproduct_delay',
						'suffix' => 'milliseconds',
						'class' => 'fixed-width-sm',
						'desc' => $this->l('Delay time when slider run automatically. Default is 3000ms.')
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Auto play'),
						'name' => 'poslistcateproduct_auto',
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Show Next/Back control:'),
						'name' => 'poslistcateproduct_arrow',
						'class' => 'fixed-width-xs',
						'desc' => $this->l(''),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
								),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						)
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Show navigation control:'),
						'name' => 'poslistcateproduct_nav',
						'class' => 'fixed-width-xs',
						'desc' => $this->l(''),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
								),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						)
					),
					array(
						'type' => 'text',
						'label' => $this->l('Product limit'),
						'name' => 'poslistcateproduct_limit',
						'class' => 'fixed-width-sm'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Show subcategories'),
						'name' => 'poslistcateproduct_sub',
						'is_bool' => true,
						'desc' => $this->l('Subcategories will be shown as links.'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
					
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitPosProductCates';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		$id_shop_group = Shop::getContextShopGroupID();
		$id_shop = Shop::getContextShopID();

		return array(
			$this->name.'_row' => Tools::getValue($this->name.'_row', Configuration::get($this->name.'_row', null, $id_shop_group, $id_shop)),
			$this->name.'_items' => Tools::getValue($this->name.'_items', Configuration::get($this->name.'_items', null, $id_shop_group, $id_shop)),
			$this->name.'_speed' => Tools::getValue($this->name.'_speed', Configuration::get($this->name.'_speed', null, $id_shop_group, $id_shop)),
			$this->name.'_delay' => Tools::getValue($this->name.'_delay', Configuration::get($this->name.'_delay', null, $id_shop_group, $id_shop)),
			$this->name.'_auto' => Tools::getValue($this->name.'_auto', Configuration::get($this->name.'_auto', null, $id_shop_group, $id_shop)),
			$this->name.'_arrow' => Tools::getValue($this->name.'_arrow', Configuration::get($this->name.'_arrow', null, $id_shop_group, $id_shop)),
			$this->name.'_nav' => Tools::getValue($this->name.'_nav', Configuration::get($this->name.'_nav', null, $id_shop_group, $id_shop)),
			$this->name.'_limit' => Tools::getValue($this->name.'_limit', Configuration::get($this->name.'_limit', null, $id_shop_group, $id_shop)),
			$this->name.'_sub' => Tools::getValue($this->name.'_limit', Configuration::get($this->name.'_sub', null, $id_shop_group, $id_shop)),
		);
	}

	public function getAddFieldsValues() 
	{
		$fields = array();

		if (Tools::isSubmit('id_item') && $this->slideExists((int)Tools::getValue('id_item')))
		{
			$slide = new PosCatePro((int)Tools::getValue('id_item'));
			$fields['id_item'] = (int)Tools::getValue('id_item', $slide->id);
		}
		else
			$slide = new PosCatePro();
		
		$fields['active_slide'] = Tools::getValue('active_slide', $slide->active);
		$fields['id_category'] = Tools::getValue('id_category', $slide->id_category);
		
		$fields['has_picture'] = true;

		$languages = Language::getLanguages(false);

		foreach ($languages as $lang)
		{
			$fields['image'][$lang['id_lang']] = Tools::getValue('image_'.(int)$lang['id_lang']);
			$fields['url'][$lang['id_lang']] = Tools::getValue('url_'.(int)$lang['id_lang'], $slide->url[$lang['id_lang']]);
			$fields['description'][$lang['id_lang']] = Tools::getValue('description_'.(int)$lang['id_lang'], $slide->description[$lang['id_lang']]);
		}

		return $fields;
	}

	protected function getMultiLanguageInfoMsg()
	{
		return '<p class="alert alert-warning">'.
					$this->l('Since multiple languages are activated on your shop, please mind to upload your image for each one of them').
				'</p>';
	}

	protected function getWarningMultishopHtml()
	{
		if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL)
			return '<p class="alert alert-warning">'.
						$this->l('You cannot manage slides items from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit').
					'</p>';
		else
			return '';
	}

	protected function getShopContextError($shop_contextualized_name, $mode)
	{
		if (is_array($shop_contextualized_name))
			$shop_contextualized_name = implode('<br/>', $shop_contextualized_name);

		if ($mode == 'edit')
			return '<p class="alert alert-danger">'.
							sprintf($this->l('You can only edit this item from the shop(s) context: %s'), $shop_contextualized_name).
					'</p>';
		else
			return '<p class="alert alert-danger">'.
							sprintf($this->l('You cannot add items from a "All Shops" or a "Group Shop" context')).
					'</p>';
	}

	protected function getShopAssociationError($id_item)
	{
		return '<p class="alert alert-danger">'.
						sprintf($this->l('Unable to get item shop association information (id_item: %d)'), (int)$id_item).
				'</p>';
	}


	protected function getCurrentShopInfoMsg()
	{
		$shop_info = null;

		if (Shop::isFeatureActive())
		{
			if (Shop::getContext() == Shop::CONTEXT_SHOP)
				$shop_info = sprintf($this->l('The modifications will be applied to shop: %s'), $this->context->shop->name);
			else if (Shop::getContext() == Shop::CONTEXT_GROUP)
				$shop_info = sprintf($this->l('The modifications will be applied to this group: %s'), Shop::getContextShopGroup()->name);
			else
				$shop_info = $this->l('The modifications will be applied to all shops and shop groups');

			return '<div class="alert alert-info">'.
						$shop_info.
					'</div>';
		}
		else
			return '';
	}

	protected function getSharedSlideWarning()
	{
		return '<p class="alert alert-warning">'.
					$this->l('This slide is shared with other shops! All shops associated to this slide will apply modifications made here').
				'</p>';
	}
	
	public function getCurrentCategory($id_item)
    {	
		$req = 'SELECT hs.`id_category`
				FROM `'._DB_PREFIX_.'poslistcateproduct_items` hs
				WHERE hs.`id_poslistcateproduct_items` = '.(int)$id_item;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
		return ((int)$row['id_category']);
    }

    public function getCurrentSubcategories($id_item)
    {	
		$req = 'SELECT hs.`list_subcategories`
				FROM `'._DB_PREFIX_.'poslistcateproduct_items` hs
				WHERE hs.`id_poslistcateproduct_items` = '.(int)$id_item;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
		return ($row['list_subcategories']);
    }

    public function getCategoryOption($id_category = 1, $id_lang = false, $id_shop = false, $recursive = true) {
    	$id_item = Tools::getValue('id_item');
		$selected_subcategories = $this->getCurrentSubcategories($id_item);
		$selected_subcategories = explode(',', $selected_subcategories);
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$category = new Category((int)$id_category, (int)$id_lang, (int)$id_shop);
		if (is_null($category->id))
			return;
		if ($recursive)
		{
			$children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)$id_shop); // array	
		}
		
		if (isset($children) && count($children)){
			 if($category->id != 1 && $category->id != 2){
				 $this->_htmlm .='<li class="tree-folder">';
				 $this->_htmlm .='<span class="tree-folder-name"><input type="checkbox" name="list_cate[]" value="'.$category->id.'"/><i class="icon-folder-close" style="padding-right: 3px;"></i><label>'.$category->name.'</label></span>';
				 $this->_htmlm .='<ul class="tree">';
			 }
			 foreach ($children as $child){
				$this->getCategoryOption((int)$child['id_category'], (int)$id_lang, (int)$child['id_shop']);
			 }
			 if($category->id != 1 && $category->id != 2){
				 $this->_htmlm .='</ul>';
				 $this->_htmlm .='</li>';
			 }
			
		 }else{
			 $this->_htmlm .='<li class="tree-item">';
			 $this->_htmlm .='<span class="tree-item-name"><input type="checkbox" name="list_cate[]" value="'.$category->id.'"/><i class="tree-dot"></i><label>'.$category->name.'</label></span>';
			 $this->_htmlm .='</li>';
		 }
		
		$shop = (object) Shop::getShop((int)$category->getShopID());
         return $this->_htmlm ;
    }
}
