<?php
class posaccessoriesproducts extends Module {
	private $token = '';
	private $_html = '';
	public static $sort_by = array(
        1 => array('id' =>1 , 'name' => 'Product Name'),
        2 => array('id' =>2 , 'name' => 'Price'),
        3 => array('id' =>3 , 'name' => 'Product ID'),       
        4 => array('id' =>4 , 'name' => 'Position'),
        5 => array('id' =>5 , 'name' => 'Date updated'),
        6 => array('id' =>6 , 'name' => 'Date added'),
        7 => array('id' =>7 , 'name' => 'Random'),
    );

    public static $order_by = array(
        1 => array('id' =>1 , 'name' => 'Descending'),
        2 => array('id' =>2 , 'name' => 'Ascending'),
    );
	public function __construct() {
		$this->name 		= 'posaccessoriesproducts';
		$this->tab 			= 'front_office_features';
		$this->version 		= '2.0';
		$this->author 		= 'posthemes';
		$this->bootstrap = true;
		$this->_html        = '';
		$this->displayName 	= $this->l('Pos Product Accessories module');
		$this->description 	= $this->l('Show accessories products on product page.');
		parent :: __construct();
       
	}
	
	public function install() {
        Configuration::updateValue($this->name . '_limit', 20);
        Configuration::updateValue($this->name . '_row', 1);
        Configuration::updateValue($this->name . '_items', 4);
		Configuration::updateValue($this->name . '_speed', 1000);
        Configuration::updateValue($this->name . '_auto', 0);
		Configuration::updateValue($this->name . '_pause', 3000);
        Configuration::updateValue($this->name . '_arrow', 1);
        Configuration::updateValue($this->name . '_pagi', 0);
        Configuration::updateValue($this->name . '_per_md', 3);
        Configuration::updateValue($this->name . '_per_sm', 2);
        Configuration::updateValue($this->name . '_per_xs', 2);
        Configuration::updateValue($this->name . '_per_xxs', 1);
        Configuration::updateValue($this->name . '_sort', 7);
        Configuration::updateValue($this->name . '_order', 1);
        Configuration::updateValue($this->name . '_move', 1);
        Configuration::updateValue($this->name . '_pausehover', 0);
		
		return parent :: install()
			&& $this->registerHook('productfooter')
			&& $this->registerHook('top')
			&& $this->registerHook('header')
			&& $this->installFixtures();
	}
	protected function installFixtures()
	{
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang){
			$this->installFixture((int)$lang['id_lang'], 'cms.png');
		}

		return true;
	}

	protected function installFixture($id_lang, $image = null)
	{	
		$values['posaccessoriesproducts_title'][(int)$id_lang] = 'Accessories';
		Configuration::updateValue($this->name . '_title', $values['posaccessoriesproducts_title']);

	}
	
    public function uninstall() {
        $this->_clearCache('posaccessoriesproducts.tpl');

		Configuration::deleteByName($this->name . '_limit');
        Configuration::deleteByName($this->name . '_row');
        Configuration::deleteByName($this->name . '_items');
		Configuration::deleteByName($this->name . '_speed');
        Configuration::deleteByName($this->name . '_auto');
		Configuration::deleteByName($this->name . '_pause');
        Configuration::deleteByName($this->name . '_arrow');
        Configuration::deleteByName($this->name . '_pagi');
        Configuration::deleteByName($this->name . '_per_lg');
        Configuration::deleteByName($this->name . '_per_md');
        Configuration::deleteByName($this->name . '_per_sm');
        Configuration::deleteByName($this->name . '_per_xs');
        Configuration::deleteByName($this->name . '_sort');
        Configuration::deleteByName($this->name . '_order');
		Configuration::deleteByName($this->name . '_move');
        Configuration::deleteByName($this->name . '_pausehover');
		
        return parent::uninstall();
    }

  
	public function psversion() {
		$version=_PS_VERSION_;
		$exp=$explode=explode(".",$version);
		return $exp[1];
	}
	
    private function postProcess() {
		if (Tools::isSubmit('submitposaccessoriesproducts'))
		{
			if($this->_postValidation()){
				$languages = Language::getLanguages(false);
				$values = array();
		        
				
				foreach ($languages as $lang){
						$values[$this->name . '_title'][$lang['id_lang']] = Tools::getValue('posas_title_'.$lang['id_lang']);
				}
				Configuration::updateValue($this->name . '_title', $values[$this->name . '_title']);

				Configuration::updateValue($this->name . '_row', Tools::getValue('posas_row'));
				Configuration::updateValue($this->name . '_items', Tools::getValue('posas_items'));
				Configuration::updateValue($this->name . '_speed', Tools::getValue('posas_speed'));
				Configuration::updateValue($this->name . '_auto', Tools::getValue('posas_auto'));
				Configuration::updateValue($this->name . '_pause', Tools::getValue('posas_pause'));
				Configuration::updateValue($this->name . '_arrow', Tools::getValue('posas_arrow'));
				Configuration::updateValue($this->name . '_pagi', Tools::getValue('posas_pagi'));
				Configuration::updateValue($this->name . '_move', Tools::getValue('posas_move'));
				Configuration::updateValue($this->name . '_pausehover', Tools::getValue('posas_pausehover'));
				Configuration::updateValue($this->name . '_limit', Tools::getValue('posas_limit'));
				Configuration::updateValue($this->name . '_sort', Tools::getValue('posas_sort'));
				Configuration::updateValue($this->name . '_order', Tools::getValue('posas_order'));
				Configuration::updateValue($this->name . '_per_md', Tools::getValue($this->name . '_per_md'));
				Configuration::updateValue($this->name . '_per_sm', Tools::getValue($this->name . '_per_sm'));
				Configuration::updateValue($this->name . '_per_xs', Tools::getValue($this->name . '_per_xs'));
				Configuration::updateValue($this->name . '_per_xxs', Tools::getValue($this->name . '_per_xxs'));
				
				
				return $this->displayConfirmation($this->l('The settings have been updated.'));
			}else{
				return $this->_html;
			}
		}
		
		return '';
    }
	
	public function getContent()
	{		
		return $this->postProcess().$this->renderForm();
	}

	protected function _postValidation()
	{
		$errors = array();
		if (Tools::isSubmit('submitposaccessoriesproducts'))
		{

			if (!Validate::isInt(Tools::getValue('posas_row')) || !Validate::isInt(Tools::getValue('posas_items')) ||
				!Validate::isInt(Tools::getValue('posas_speed')) || !Validate::isInt(Tools::getValue('posas_pause')) || !Validate::isInt(Tools::getValue('posas_limit'))
			)
				$errors[] = $this->l('Invalid values');
		} 
		/* Returns if validation is ok */
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));

			return false;
		}

		return true;
	}
	public function renderForm()
	{	
		
        $id_lang = (int) Context::getContext()->language->id;
        //echo '<pre>';print_r($test);die;
			$fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->l('Module Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
						array(
							'type' => 'text',
							'lang' => true,
							'label' => $this->l('Module title'),
							'name' => 'posas_title',
							'desc' => $this->l('This title will be displayed on front-office.')
						),
						array(
		                    'type' => 'select',
		                    'label' => $this->l('Sort by:'),
		                    'name' => 'posas_sort',
		                    'options' => array(
		                        'query' => self::$sort_by,
		                        'id' => 'id',
		                        'name' => 'name',
		                    ),
		                    'validation' => 'isUnsignedInt',
		                ),
		                array(
		                    'type' => 'select',
		                    'label' => $this->l('Order by:'),
		                    'name' => 'posas_order',
		                    'options' => array(
		                        'query' => self::$order_by,
		                        'id' => 'id',
		                        'name' => 'name',
		                    ),
		                    'validation' => 'isUnsignedInt',
		                ), 
						array(
							'type' => 'text',
							'label' => $this->l('Products limit :'),
							'name' => 'posas_limit',
							'class' => 'fixed-width-sm',
							'desc' => $this->l('Set the number of products which you would like to see displayed in this module')
						),
						
						
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			);
			$fields_form[1]['form'] = array(
				'legend' => array(
					'title' => $this->l('Slider configurations'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
							'type' => 'text',
							'label' => $this->l('Rows'),
							'name' => 'posas_row',
							'class' => 'fixed-width-sm',
							'desc' => $this->l('Number rows of module')
					),
					array(
							'type' => 'text',
							'label' => $this->l('Number of Items:'),
							'name' => 'posas_items',
							'class' => 'fixed-width-sm',
							'desc' => $this->l('Show number of product visible.')
					),
					array(
							'type' => 'text',
							'label' => $this->l('Slide speed:'),
							'name' => 'posas_speed',
							'class' => 'fixed-width-sm',
							'suffix' => 'milliseconds',
							'desc' => $this->l('')
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Auto play'),
						'name' => 'posas_auto',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Default is 1000ms'),
						'values' => array(
							array(
								'id' => 'posas_auto_on',
								'value' => 1,
								'label' => $this->l('Enabled')
								),
							array(
								'id' => 'posas_auto_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						)
					),
					array(
							'type' => 'text',
							'label' => $this->l('Time auto'),
							'name' => 'posas_pause',
							'class' => 'fixed-width-sm',
							'suffix' => 'milliseconds',
							'desc' => $this->l('This field only is value when auto play function is enable. Default is 3000ms.')
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Show Next/Back control:'),
						'name' => 'posas_arrow',
						'class' => 'fixed-width-xs',
						'desc' => $this->l(''),
						'values' => array(
							array(
								'id' => 'posas_arrow_on',
								'value' => 1,
								'label' => $this->l('Enabled')
								),
							array(
								'id' => 'posas_arrow_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						)
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Show pagination control:'),
						'name' => 'posas_pagi',
						'class' => 'fixed-width-xs',
						'desc' => $this->l(''),
						'values' => array(
							array(
								'id' => 'posas_pagi_on',
								'value' => 1,
								'label' => $this->l('Enabled')
								),
							array(
								'id' => 'posas_pagi_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						)
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Scroll number:'),
						'name' => 'posas_move',
	                    'default_value' => 0,
						'values' => array(
							array(
								'id' => 'posas_move_on',
								'value' => 1,
								'label' => $this->l('1 item')),
							array(
								'id' => 'posas_move_off',
								'value' => 0,
								'label' => $this->l('All visible items')),
						),
	                    'validation' => 'isBool',
					),
					 array(
						'type' => 'switch',
						'label' => $this->l('Pause On Hover:'),
						'name' => 'posas_pausehover',
	                    'default_value' => 1,
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'posas_pausehover_on',
								'value' => 1,
								'label' => $this->l('Yes')),
							array(
								'id' => 'posas_pausehover_off',
								'value' => 0,
								'label' => $this->l('No')),
						),
	                    'validation' => 'isBool',
					),
					'pos_fp_pro' => array(
	                    'type' => 'html',
	                    'id' => 'pos_fp_pro',
	                    'label'=> $this->l('Responsive:'),
	                    'name' => '',
	                ),
					
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)		
			);
		$fields_form[1]['form']['input']['pos_fp_pro']['name'] = $this->BuildDropListGroup($this->findCateProPer());
		
		$helper = new HelperForm();
		$helper->show_toolbar = true;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->module = $this;
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitposaccessoriesproducts';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$module = _PS_MODULE_DIR_ ;
		$helper->tpl_vars = array(
			'module' =>$module,
			'uri' => $this->getPathUri(),
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);

		return $helper->generateForm($fields_form);
	}
	
	public function getConfigFieldsValues()
	{
		$languages = Language::getLanguages(false);
		$fields = array(
			'posas_row'        => Configuration::get($this->name . '_row'),
			'posas_items'      => Configuration::get($this->name . '_items'),
			'posas_speed'      => Configuration::get($this->name . '_speed'),
			'posas_auto'       => Configuration::get($this->name . '_auto'),
			'posas_pause'      => Configuration::get($this->name . '_pause'),
			'posas_arrow'      => Configuration::get($this->name . '_arrow'),
			'posas_pagi'       => Configuration::get($this->name . '_pagi'),
			'posas_move'       => Configuration::get($this->name . '_move'),
			'posas_pausehover' => Configuration::get($this->name . '_pausehover'),
			'posas_sort'       => Configuration::get($this->name . '_sort'),
			'posas_order'      => Configuration::get($this->name . '_order'),
			'posas_limit'      => Configuration::get($this->name . '_limit'),

		);
		
		
		foreach ($languages as $lang)
		{	
			$fields['posas_title'][$lang['id_lang']] = Tools::getValue('posaccessoriesproducts_title_'.$lang['id_lang'], Configuration::get($this->name . '_title', $lang['id_lang']));
		}
		
		return $fields;
	}

	public function hookHeader($params){

    }
        
    // Hook Home
	
	public function hookproductfooter($params) {
	        if( Dispatcher::getInstance()->getController() != 'product' )
            return false;
            
	        $id_product = (int)Tools::getValue('id_product');
			if (!$id_product)
				return false;
			
	        $nb = Configuration::get($this->name . '_limit');
	        $random = false;
	        $sortby = Configuration::get($this->name . '_sort');
	        switch($sortby)
	        {
	        	case 1:
                $sortby = 'name';
           		break;
           		case 2:
                $sortby = 'price';
           		break;
           		case 3:
                $sortby = 'id_product';
           		break;
           		case 4:
                $sortby = 'position';
           		break;
           		case 5:
                $sortby = 'date_upd';
           		break;
           		case 6:
                $sortby = 'date_add';
           		break;
           		case 7:
                $sortby = null;
                $random = true;
           		break;
	        }
	        $orderby = Configuration::get($this->name . '_order');
	        if($orderby == 1) {
	        	$orderby = 'DESC';
	        } else {
	        	$orderby = 'ASC';
	        };
	        
			$id_lang =(int) Context::getContext()->language->id;
			$id_shop = (int) Context::getContext()->shop->id;

            $products = $this->getAccessories($id_lang,$id_product,($nb ? $nb : 8),$sortby,$orderby,$random,($nb ? $nb : 8));
			if(!$products) return false;
			$slider_options = array(
					'rows' => (int)Configuration::get($this->name . '_row'),
					'number_item' => (int)Configuration::get($this->name . '_items'),
					'speed_slide' => (int)Configuration::get($this->name . '_speed'),
					'auto_play' => (int)Configuration::get($this->name . '_auto'),
					'auto_time' => (int)Configuration::get($this->name . '_pause'),
					'show_arrow' => (int)Configuration::get($this->name . '_arrow'),
					'show_pagination' => (int)Configuration::get($this->name . '_pagi'),
					'move' => (int)Configuration::get($this->name . '_move'),
					'pausehover' => (int)Configuration::get($this->name . '_pausehover'),
					'items_md' => (int)Configuration::get($this->name . '_per_md'),	
					'items_sm' => (int)Configuration::get($this->name . '_per_sm'),	
					'items_xs' => (int)Configuration::get($this->name . '_per_xs'),	
					'items_xxs' => (int)Configuration::get($this->name . '_per_xxs'),		
				);

			$this->context->smarty->assign('slider_options', $slider_options);
			
            $this->smarty->assign(array(
				'products' => $products,
                'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
                'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
				'title' => Configuration::get($this->name . '_title', $this->context->language->id),			
            ));
		return $this->display(__FILE__, 'posaccessoriesproducts.tpl');
	}


	public static function getAccessories($id_lang, $id_product, $nb_products=10, $order_by = null, $order_way = null, $random = false, $random_number_products = 8, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'price';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`,
					pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
					image_shop.`id_image` id_image, il.`legend`, m.`name` as manufacturer_name, cl.`name` AS category_default, IFNULL(product_attribute_shop.id_product_attribute, 0) id_product_attribute,
					DATEDIFF(
						p.`date_add`,
						DATE_SUB(
							"'.date('Y-m-d').' 00:00:00",
							INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
						)
					) > 0 AS new
				FROM `'._DB_PREFIX_.'accessory`
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = `id_product_2`
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
					ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (
					product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (p.`id_manufacturer`= m.`id_manufacturer`)
				'.Product::sqlStock('p', 0).'
				WHERE `id_product_1` = '.(int)$id_product.'
                AND product_shop.`active` = 1 AND product_shop.`visibility` != \'none\'
				GROUP BY product_shop.id_product';

		if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT '.(int)$random_number_products;
        } else {
			$sql .= ' ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').pSQL($order_by).' '.pSQL($order_way).'
			LIMIT '.(int)$nb_products;
		}
				

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return false;
        }

        foreach ($result as &$row) {
            $row['id_product_attribute'] = Product::getDefaultAttribute((int)$row['id_product']);
        }

        return Product::getProductsProperties($id_lang, $result);
    }

	public function BuildDropListGroup($group)
    {
        if(!is_array($group) || !count($group))
            return false;

        $html = '<div class="row">';
        foreach($group AS $key => $k)
        {
             if($key==4)
                 $html .= '</div><div class="row">';

             $html .= '<div class="col-xs-4 col-sm-3">'.$k['label'].'</label>'.
             '<select name="'.$k['id'].'" 
             id="'.$k['id'].'" 
             class="'.(isset($k['class']) ? $k['class'] : 'fixed-width-md').'"'.
             (isset($k['onchange']) ? ' onchange="'.$k['onchange'].'"':'').' >';
            
            for ($i=1; $i < 7; $i++){
                $html .= '<option value="'.$i.'" '.(Configuration::get($k['id']) == $i ? ' selected="selected"':'').'>'.$i.'</option>';
            }
                                
            $html .= '</select></div>';
        }

        return $html.'</div>';
    }
    public function findCateProPer()
    {
        return array(
            array(
                'id' => 'posaccessoriesproducts_per_md',
                'label' => $this->l('Desktops (>991 pixels)'),
            ),
            array(
                'id' => 'posaccessoriesproducts_per_sm',
                'label' => $this->l('Tablets (>767 pixels)'),
            ),
            array(
                'id' => 'posaccessoriesproducts_per_xs',
                'label' => $this->l('Phones (>479 pixels)'),
            ),
            array(
                'id' => 'posaccessoriesproducts_per_xxs',
                'label' => $this->l('Small phones (>320 pixels)'),
            ),
        );
    }
}