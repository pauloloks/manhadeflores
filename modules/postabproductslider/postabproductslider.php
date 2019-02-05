<?php

class postabproductslider extends Module {
	var $_postErrors  = array();
	var $_html ='';
	public function __construct() {
		$this->name 		= 'postabproductslider';
		$this->tab 			= 'front_office_features';
		$this->version 		= '1.5';
		$this->author 		= 'posthemes';
		$this->bootstrap = true;
		$this->displayName 	= $this->l('Product Tabs Slider');
		$this->description 	= $this->l('Product Tabs Slider');
        
		parent :: __construct();
       
	}
	
	public function install() {
	    Configuration::updateValue($this->name . '_show_new', 1);
        Configuration::updateValue($this->name . '_show_sale', 1);
        Configuration::updateValue($this->name . '_show_feature', 1);
        Configuration::updateValue($this->name . '_show_best', 0);
        Configuration::updateValue($this->name . '_row', 1);
        Configuration::updateValue($this->name . '_number_item', 4);
		Configuration::updateValue($this->name . '_speed_slide', 1000);
        Configuration::updateValue($this->name . '_auto_play', 0);
		Configuration::updateValue($this->name . '_pause_time', 3000);
        Configuration::updateValue($this->name . '_show_arrow', 1);
        Configuration::updateValue($this->name . '_show_ctr', 0);
        Configuration::updateValue($this->name . '_limit', 12);

		return parent :: install()
			&& $this->registerHook('home')
			&& $this->registerHook('top')
			&& $this->registerHook('header')
			&& $this->registerHook('actionOrderStatusPostUpdate')
			&& $this->registerHook('addproduct')
			&& $this->registerHook('tabsProducts')
			&& $this->registerHook('updateproduct')
			&& $this->registerHook('deleteproduct')
			&& $this->registerHook('blockPosition1')
			&& $this->registerHook('blockPosition2')
			&& $this->installFixtures();
	}
	protected function installFixtures()
	{
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang){
			$this->installFixture((int)$lang['id_lang'], 'tabbanner.jpg');
		}

		return true;
	}

	protected function installFixture($id_lang, $image = null)
	{	
		$values['postabproductslider_img'][(int)$id_lang] = $image;
		$values['postabproductslider_link'][(int)$id_lang] = '#';
		$values['postabproductslider_title'][(int)$id_lang] = 'Top Trending';
		Configuration::updateValue($this->name . '_title', $values['postabproductslider_title']);
		Configuration::updateValue($this->name . '_img', $values['postabproductslider_img']);
		Configuration::updateValue($this->name . '_link', $values['postabproductslider_link']);
	}
	
      public function uninstall() {
        $this->_clearCache('productab.tpl');
        return parent::uninstall();
    }

  
	public function psversion() {
		$version=_PS_VERSION_;
		$exp=$explode=explode(".",$version);
		return $exp[1];
	}
    
    
    // public function hookHeader($params){
         // $this->context->controller->addCSS(($this->_path).'producttab.css', 'all');
    // }
	public function hookblockPosition1($params) {
	        $nb = Configuration::get($this->name . '_limit');
			$newProducts = Product::getNewProducts((int) Context::getContext()->language->id, 0, ($nb ? $nb : 5));
			$specialProducts = Product::getPricesDrop((int) Context::getContext()->language->id, 0, ($nb ? $nb : 5));
			ProductSale::fillProductSales();
			$bestseller =  $this->getBestSales ((int) Context::getContext()->language->id, 0, ($nb ? $nb : 5), null,  null);
			$category = new Category(Context::getContext()->shop->getCategory(), (int) Context::getContext()->language->id);
         	$featureProduct = $category->getProducts((int) Context::getContext()->language->id, 0, ($nb ? $nb : 5),'date_add','DESC');

      
			if(!$newProducts) $newProducts = null;
			if(!$bestseller) $bestseller = null;
			if(!$specialProducts) $specialProducts = null;
			
			$productTabslider = array();
			if(Configuration::get($this->name . '_show_new')) {
				$productTabslider[] = array('id'=>'new_product', 'name' => $this->l('New products'), 'productInfo' => $newProducts);
			}
			if(Configuration::get($this->name . '_show_feature')) {
				$productTabslider[] = array('id'=>'feature_product','name' => $this->l('Featured Products'), 'productInfo' =>  $featureProduct);
			}
			if(Configuration::get($this->name . '_show_sale')) {
				$productTabslider[] = array('id'=> 'special_product','name' => $this->l('OnSale'), 'productInfo' =>  $specialProducts);
			}
			if(Configuration::get($this->name . '_show_best')) {
				$productTabslider[] = array('id'=>'besseller_product','name' => $this->l('Bestseller'), 'productInfo' =>  $bestseller);
			}
			
				$options = array(
					'rows' => (int)Configuration::get($this->name . '_row'),
					'number_item' => (int)Configuration::get($this->name . '_number_item'),
					'speed_slide' => (int)Configuration::get($this->name . '_speed_slide'),
					'auto_play' => (int)Configuration::get($this->name . '_auto_play'),
					'auto_time' => (int)Configuration::get($this->name . '_pause_time'),
					'show_arrow' => (int)Configuration::get($this->name . '_show_arrow'),
					'show_pagination' => (int)Configuration::get($this->name . '_show_ctr'),
					'limit' => (int)Configuration::get($this->name . '_limit'),
					
				);
			$imgname = Configuration::get($this->name . '_img', $this->context->language->id);

			if ($imgname && file_exists(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$imgname))
				$this->smarty->assign('banner_img', $this->context->link->protocol_content.Tools::getMediaServer($imgname).$this->_path.'img/'.$imgname);

            $this->smarty->assign(array(
                'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
                'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
				'tab_effect' => Configuration::get($this->name . '_tab_effect'),
				'title' => Configuration::get($this->name . '_title', $this->context->language->id),
				'image_link' => Configuration::get($this->name . '_link', $this->context->language->id),
	
            ));
			
			$this->context->smarty->assign('productTabslider', $productTabslider);
			$this->context->smarty->assign('slideOptions', $options);
		return $this->display(__FILE__, 'producttabslider.tpl');
	}
	public function hookblockPosition2($params) {
	        $nb = Configuration::get($this->name . '_limit');
			$newProducts = Product::getNewProducts((int) Context::getContext()->language->id, 0, ($nb ? $nb : 5));
			$specialProducts = Product::getPricesDrop((int) Context::getContext()->language->id, 0, ($nb ? $nb : 5));
			ProductSale::fillProductSales();
			$bestseller =  $this->getBestSales ((int) Context::getContext()->language->id, 0, ($nb ? $nb : 5), null,  null);
			$category = new Category(Context::getContext()->shop->getCategory(), (int) Context::getContext()->language->id);
         	$featureProduct = $category->getProducts((int) Context::getContext()->language->id, 0, ($nb ? $nb : 5),'date_add','DESC');

      
			if(!$newProducts) $newProducts = null;
			if(!$bestseller) $bestseller = null;
			if(!$specialProducts) $specialProducts = null;
			
			$productTabslider = array();
			if(Configuration::get($this->name . '_show_new')) {
				$productTabslider[] = array('id'=>'new_product', 'name' => $this->l('New products'), 'productInfo' => $newProducts);
			}
			if(Configuration::get($this->name . '_show_feature')) {
				$productTabslider[] = array('id'=>'feature_product','name' => $this->l('Featured Products'), 'productInfo' =>  $featureProduct);
			}
			if(Configuration::get($this->name . '_show_sale')) {
				$productTabslider[] = array('id'=> 'special_product','name' => $this->l('OnSale'), 'productInfo' =>  $specialProducts);
			}
			if(Configuration::get($this->name . '_show_best')) {
				$productTabslider[] = array('id'=>'besseller_product','name' => $this->l('Bestseller'), 'productInfo' =>  $bestseller);
			}
			
				$options = array(
					'rows' => (int)Configuration::get($this->name . '_row'),
					'number_item' => (int)Configuration::get($this->name . '_number_item'),
					'speed_slide' => (int)Configuration::get($this->name . '_speed_slide'),
					'auto_play' => (int)Configuration::get($this->name . '_auto_play'),
					'auto_time' => (int)Configuration::get($this->name . '_pause_time'),
					'show_arrow' => (int)Configuration::get($this->name . '_show_arrow'),
					'show_pagination' => (int)Configuration::get($this->name . '_show_ctr'),
					'limit' => (int)Configuration::get($this->name . '_limit'),
					
				);
			$imgname = Configuration::get($this->name . '_img', $this->context->language->id);

			if ($imgname && file_exists(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$imgname))
				$this->smarty->assign('banner_img', $this->context->link->protocol_content.Tools::getMediaServer($imgname).$this->_path.'img/'.$imgname);

            $this->smarty->assign(array(
                'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
                'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
				'tab_effect' => Configuration::get($this->name . '_tab_effect'),
				'title' => Configuration::get($this->name . '_title', $this->context->language->id),
				'image_link' => Configuration::get($this->name . '_link', $this->context->language->id),
	
            ));
			
			$this->context->smarty->assign('productTabslider', $productTabslider);
			$this->context->smarty->assign('slideOptions', $options);
		return $this->display(__FILE__, 'producttabslider.tpl');
	}
	
    private function postProcess() {
		if (Tools::isSubmit('submitPosTabproductSlider'))
		{
			$languages = Language::getLanguages(false);
			$values = array();
			$update_images_values = false;
        
		
		foreach ($languages as $lang){
			if (isset($_FILES['postabproductslider_img_'.$lang['id_lang']])
					&& isset($_FILES['postabproductslider_img_'.$lang['id_lang']]['tmp_name'])
					&& !empty($_FILES['postabproductslider_img_'.$lang['id_lang']]['tmp_name']))
				{
					if ($error = ImageManager::validateUpload($_FILES['postabproductslider_img_'.$lang['id_lang']], 4000000))
						return $error;
					else
					{
						$ext = substr($_FILES['postabproductslider_img_'.$lang['id_lang']]['name'], strrpos($_FILES['postabproductslider_img_'.$lang['id_lang']]['name'], '.') + 1);
						$file_name = md5($_FILES['postabproductslider_img_'.$lang['id_lang']]['name']).'.'.$ext;

						if (!move_uploaded_file($_FILES['postabproductslider_img_'.$lang['id_lang']]['tmp_name'], dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$file_name))
							return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
						else
						{
							if (Configuration::hasContext('postabproductslider_img', $lang['id_lang'], Shop::getContext())
								&& Configuration::get('postabproductslider_img', $lang['id_lang']) != $file_name)
								@unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.Configuration::get('postabproductslider_img', $lang['id_lang']));

							$values['postabproductslider_img'][$lang['id_lang']] = $file_name;
							
						}
					}

					$update_images_values = true;
				}
				$values['postabproductslider_link'][$lang['id_lang']] = Tools::getValue('postabproductslider_link_'.$lang['id_lang']);
				$values['postabproductslider_title'][$lang['id_lang']] = Tools::getValue('postabproductslider_title_'.$lang['id_lang']);
		}
		
		if ($update_images_values)
				Configuration::updateValue($this->name . '_img', $values['postabproductslider_img']);

		Configuration::updateValue($this->name . '_link', $values['postabproductslider_link']);
		Configuration::updateValue($this->name . '_title', $values['postabproductslider_title']);
		
		Configuration::updateValue($this->name . '_show_new', Tools::getValue('postabproductslider_show_new'));
        Configuration::updateValue($this->name . '_show_sale', Tools::getValue('postabproductslider_show_sale'));
        Configuration::updateValue($this->name . '_show_feature', Tools::getValue('postabproductslider_show_feature'));
        Configuration::updateValue($this->name . '_show_best', Tools::getValue('postabproductslider_show_best'));
        Configuration::updateValue($this->name . '_limit', Tools::getValue('postabproductslider_limit'));
		Configuration::updateValue($this->name . '_row', Tools::getValue('postabproductslider_row'));
        Configuration::updateValue($this->name . '_speed_slide', Tools::getValue('postabproductslider_speed_slide'));
        Configuration::updateValue($this->name . '_pause_time', Tools::getValue('postabproductslider_pause_time'));
		Configuration::updateValue($this->name . '_auto_play', Tools::getValue('postabproductslider_auto_play'));
        Configuration::updateValue($this->name . '_show_arrow', Tools::getValue('postabproductslider_show_arrow'));
        Configuration::updateValue($this->name . '_show_ctr', Tools::getValue('postabproductslider_show_ctr'));
        Configuration::updateValue($this->name . '_number_item', Tools::getValue('postabproductslider_number_item'));
		
		return $this->displayConfirmation($this->l('The settings have been updated.'));
		}
		return '';
    }
	
	 
	public function getContent()
	{
		return $this->postProcess().$this->renderForm();
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
							'lang' => true,
							'label' => $this->l('Module title'),
							'name' => 'postabproductslider_title',
							'desc' => $this->l('This title will be displayed on front-office.')
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Show new products'),
							'name' => 'postabproductslider_show_new',
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
							'label' => $this->l('Show special Products:'),
							'name' => 'postabproductslider_show_sale',
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
							'label' => $this->l('Show Bestselling Products:'),
							'name' => 'postabproductslider_show_best',
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
							'label' => $this->l('Show Feature Products:'),
							'name' => 'postabproductslider_show_feature',
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
								'label' => $this->l('Rows'),
								'name' => 'postabproductslider_row',
								'class' => 'fixed-width-sm',
								'desc' => $this->l('Number rows of module')
						),
						array(
								'type' => 'text',
								'label' => $this->l('Number of Items:'),
								'name' => 'postabproductslider_number_item',
								'class' => 'fixed-width-sm',
								'desc' => $this->l('Show number of product visible.')
						),
						array(
								'type' => 'text',
								'label' => $this->l('Slide speed:'),
								'name' => 'postabproductslider_speed_slide',
								'class' => 'fixed-width-sm',
								'desc' => $this->l('')
						),
						
						array(
							'type' => 'switch',
							'label' => $this->l('Auto play'),
							'name' => 'postabproductslider_auto_play',
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
								'label' => $this->l('Time auto'),
								'name' => 'postabproductslider_pause_time',
								'class' => 'fixed-width-sm',
								'desc' => $this->l('This field only is value when auto play function is enable. Default is 3000ms.')
						),
						array(
							'type' => 'switch',
							'label' => $this->l('Show Next/Back control:'),
							'name' => 'postabproductslider_show_arrow',
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
							'name' => 'postabproductslider_show_ctr',
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
								'label' => $this->l('Products limit :'),
								'name' => 'postabproductslider_limit',
								'class' => 'fixed-width-sm',
								'desc' => $this->l('Set the number of products which you would like to see displayed in this module')
						),
						
						// array(
							// 'type' => 'file_lang',
							// 'label' => $this->l('Banner image'),
							// 'name' => 'postabproductslider_img',
							// 'desc' => $this->l('Upload an image for your banner. The recommended dimensions are 270 x 352px.'),
							// 'lang' => true,
						// ),
						// array(
							// 'type' => 'text',
							// 'lang' => true,
							// 'label' => $this->l('Banner Link'),
							// 'name' => 'postabproductslider_link',
							// 'desc' => $this->l('Enter the link associated to your banner. When clicking on the banner, the link opens in the same window.')
						// ),
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
		$helper->module = $this;
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitPosTabproductSlider';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'uri' => $this->getPathUri(),
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		$languages = Language::getLanguages(false);
		$fields = array();
		$fields['postabproductslider_show_new'] = Tools::getValue('postabproductslider_show_new', (int)Configuration::get($this->name . '_show_new'));
		$fields['postabproductslider_show_sale'] = Tools::getValue('postabproductslider_show_sale', (int)Configuration::get($this->name . '_show_sale'));
		$fields['postabproductslider_show_feature'] = Tools::getValue('postabproductslider_show_feature', (int)Configuration::get($this->name . '_show_feature'));
		$fields['postabproductslider_show_best'] = Tools::getValue('postabproductslider_show_best', (int)Configuration::get($this->name . '_show_best'));
		$fields['postabproductslider_number_item'] = Tools::getValue('postabproductslider_number_item', (int)Configuration::get($this->name . '_number_item'));
		$fields['postabproductslider_speed_slide'] = Tools::getValue('postabproductslider_speed_slide', (int)Configuration::get($this->name . '_speed_slide'));
		$fields['postabproductslider_pause_time'] = Tools::getValue('postabproductslider_pause_time', (int)Configuration::get($this->name . '_pause_time'));
		$fields['postabproductslider_auto_play'] = Tools::getValue('postabproductslider_auto_play', (int)Configuration::get($this->name . '_auto_play'));
		$fields['postabproductslider_show_arrow'] = Tools::getValue('postabproductslider_show_arrow', (int)Configuration::get($this->name . '_show_arrow'));
		$fields['postabproductslider_show_ctr'] = Tools::getValue('postabproductslider_show_ctr', (int)Configuration::get($this->name . '_show_ctr'));
		$fields['postabproductslider_limit'] = Tools::getValue('postabproductslider_limit', (int)Configuration::get($this->name . '_limit'));
		$fields['postabproductslider_row'] = Tools::getValue('postabproductslider_row', (int)Configuration::get($this->name . '_row'));
		
		foreach ($languages as $lang)
		{	
			$fields['postabproductslider_title'][$lang['id_lang']] = Tools::getValue('postabproductslider_title_'.$lang['id_lang'], Configuration::get($this->name . '_title', $lang['id_lang']));
			$fields['postabproductslider_img'][$lang['id_lang']] = Tools::getValue('postabproductslider_img_'.$lang['id_lang'], Configuration::get($this->name . '_img', $lang['id_lang']));
			$fields['postabproductslider_link'][$lang['id_lang']] = Tools::getValue('postabproductslider_link_'.$lang['id_lang'], Configuration::get($this->name . '_link', $lang['id_lang']));
		}
		
		return $fields;
	}
	
	public static function getBestSales($id_lang, $page_number = 0, $nb_products = 10, $order_by = null, $order_way = null)
	{
		if ($page_number < 0) $page_number = 0;
		if ($nb_products < 1) $nb_products = 10;
		$final_order_by = $order_by;
		$order_table = ''; 		
		if (is_null($order_by) || $order_by == 'position' || $order_by == 'price') $order_by = 'sales';
		if ($order_by == 'date_add' || $order_by == 'date_upd')
			$order_table = 'product_shop'; 				
		if (is_null($order_way) || $order_by == 'sales') $order_way = 'DESC';
		$groups = FrontController::getCurrentCustomerGroups();
		$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
		$interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;
		
		$prefix = '';
		if ($order_by == 'date_add')
			$prefix = 'p.';
		
		$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
					pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
					pl.`meta_keywords`, pl.`meta_title`, pl.`name`,
					m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
					MAX(image_shop.`id_image`) id_image, il.`legend`,
					ps.`quantity` AS sales, t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
					DATEDIFF(p.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.$interval.' DAY)) > 0 AS new
				FROM `'._DB_PREFIX_.'product_sale` ps
				LEFT JOIN `'._DB_PREFIX_.'product` p ON ps.`id_product` = p.`id_product`
				'.Shop::addSqlAssociation('product', 'p', false).'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				'.Product::sqlStock('p').'
				WHERE product_shop.`active` = 1
					AND product_shop.`visibility` != \'none\'
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sql_groups.'
					)
				GROUP BY product_shop.id_product
				ORDER BY '.(!empty($order_table) ? '`'.pSQL($order_table).'`.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).'
				LIMIT '.(int)($page_number * $nb_products).', '.(int)$nb_products;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		if ($final_order_by == 'price')
			Tools::orderbyPrice($result, $order_way);
		if (!$result)
			return false;
		return Product::getProductsProperties($id_lang, $result);
	}

}