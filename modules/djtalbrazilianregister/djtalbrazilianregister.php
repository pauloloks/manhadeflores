<?php
/**
* 2007-2016 PrestaShop
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
*  @author    DJTAL
*  @copyright 2015 DJTAL
*  @version   1.0.0
*  @link      http://www.djtal.com.br/
*  @license
*/

if (!defined('_PS_VERSION_'))
    exit;

include_once dirname(__FILE__).'/models/BrazilianRegister.class.php';
class Djtalbrazilianregister extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'djtalbrazilianregister';
        $this->tab = 'administration';
        $this->version = '1.0.5';
        $this->author = 'Djtal';
        $this->need_instance = 0;
        $this->module_key = 'b24dba7da8e0320516dbcf5295d0af42';

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Registering rules for Brazil');
        $this->description = $this->l('This modules adds the expected informations for the client registering process in Brazil: CPF / CNPJ / CEP');

        $this->confirmUninstall = $this->l('Are you sure you want to make this big mistake ?');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
		//Global PS
        Configuration::updateValue('PS_TAX', false);
        Configuration::updateValue('PS_TAX_DISPLAY', false);
		
        Configuration::updateValue('DJTALBR_CPF_CNPJ_ACTIVE', false);
        Configuration::updateValue('DJTALBR_CPF_CNPJ_MODE', 'cpf-or-cnpj');
        Configuration::updateValue('DJTALBR_CPF_CNPJ_POSITION', 'bottom');
        Configuration::updateValue('DJTALBR_CPF_CNPJ_MANDATORY', true);
        Configuration::updateValue('DJTALBR_CUTOMER_PERMISSIONS', 'none');
        Configuration::updateValue('DJTALBR_STREET_NUM', 'yes_mand');
        Configuration::updateValue('DJTALBR_STREET_COMPL', 'yes');

        include(dirname(__FILE__).'/sql/install.php');
        
        
        //TAB
        $this->installTab('AdminDjtalBrazilianRegister', $this->l('Edit CPF / CNPJ'), 'AdminCustomers');
        
		$this->sendUsageStats();
		
        $brazil_id = Country::getByIso('BR');
        $tmp_addr_format = new AddressFormat($brazil_id);
        $tmp_addr_format->format =
'firstname
lastname
Country:name
postcode
address1
address2
city
State:name
phone
phone_mobile';
        $tmp_addr_format->id_country = $brazil_id;
        $tmp_addr_format->save();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayCustomerAccount') &&
            $this->registerHook('displayCustomerAccountForm') &&
            $this->registerHook('displayCustomerAccountFormTop') &&
            $this->registerHook('displayCustomerIdentityForm') &&
            $this->registerHook('actionCustomerAccountAdd') &&
            $this->registerHook('createAccount') &&
            $this->registerHook('actionObjectCustomerDeleteAfter') &&
            $this->registerHook('actionBeforeSubmitAccount') &&
            //$this->registerHook('actionAuthentication') &&
            //$this->registerHook('authentication') &&
            $this->registerHook('displayPDFInvoice') &&
            $this->registerHook('displayAdminCustomers') && 
            $this->registerHook('displayAdminOrderContentOrder') && 
            $this->registerHook('displayAdminOrderTabOrder') && 
			$this->registerHook('actionObjectCustomerAddAfter') && 
            $this->registerHook('actionObjectCustomerUpdateAfter') && 
            $this->registerHook('displayHeader');
    }
    
    public function installTab($class_name,$tab_name,$tab_parent_name=false) 
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $tab_name;

        if($tab_parent_name)
            $tab->id_parent = (int)Tab::getIdFromClassName($tab_parent_name);
        else
            $tab->id_parent = 0;
        
        $tab->module = $this->name;
        return $tab->add();
    }
    
    public function uninstall()
    {
        Configuration::deleteByName('DJTALBR_CPF_CNPJ_ACTIVE');
        Configuration::deleteByName('DJTALBR_CPF_CNPJ_MODE');
        Configuration::deleteByName('DJTALBR_CPF_CNPJ_POSITION');
        Configuration::deleteByName('DJTALBR_CPF_CNPJ_MANDATORY');

        //include(dirname(__FILE__).'/sql/uninstall.php');

        //TAB
        if ($id_tab = Tab::getIdFromClassName('AdminDjtalBrazilianRegister')) {
            $tab = new Tab($id_tab);
            if(!$tab->delete()) {
                $this->_errors[] = 'Tab deletion';
                return false;
            }
        }
        
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitDjtalBrazilianRegisterModule')) == true) {
            $output .= $this->postProcess();
        }
        
        $this->context->smarty->assign('module_dir', $this->_path);

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }
	
	public function sendUsageStats()
    {
        $postdata = http_build_query(array(
            'domain' => _PS_BASE_URL_ . __PS_BASE_URI__,
            'module' => $this->name,
            'version' => $this->version,
            'key' => 'root-260917',
            'stats' => ''
        ));
        $opts     = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context  = stream_context_create($opts);
        file_get_contents('http://djtal.com.br/getModStats.php', false, $context);
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        if(empty($allow_employee_form_lang)) {
            $allow_employee_form_lang = true;
        }
        $helper->allow_employee_form_lang = $allow_employee_form_lang;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDjtalBrazilianRegisterModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm(), $this->getConfigFormCEP(), $this->getOthersConfig(), $this->getCEPtest(), $this->getDataImport()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $version = Tools::substr(_PS_VERSION_, 0, 3);
        $switch = 'switch';
        if($version != '1.6') {
            $switch = 'radio';
        }
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('CPF and CNPJ Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => $switch,
                        'label' => $this->l('Live mode'),
                        'name' => 'DJTALBR_CPF_CNPJ_ACTIVE',
                        'is_bool' => true,
                        'class' => 't',
                        'desc' => $this->l('Active the CPF | CNPJ option on your store'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'lang' => false,
                        'label' => $this->l('Mode'),
                        'required' => false,
                        'desc' => $this->l('Choose the information you need from your clients'),
                        'options' => array(
                            'id' => 'id_mode',
                            'name' => 'name', 
                            'query' => array(
                                          array(
                                            'id_mode' => 'cpf-or-cnpj',
                                            'name' => $this->l('CPF or CNPJ'),
                                          ),
                                          array(
                                            'id_mode' => 'cpf-or-cnpj-or-passport', 
                                            'name' => $this->l('CPF or CNPJ, or Passport'),
                                          ),
                                          
                                          array(
                                            'id_mode' => 'cpf-and-cnpj', 
                                            'name' => $this->l('CPF and CNPJ'),
                                          ),
                                          array(
                                            'id_mode' => 'cpf-only',
                                            'name' => $this->l('CPF Only'),
                                          ),
                                          array(
                                            'id_mode' => 'cnpj-only',
                                            'name' => $this->l('CNPJ Only'),
                                          ),
                                        ),                                          
                          ),
                        'name' => 'DJTALBR_CPF_CNPJ_MODE',
                    ),
                    array(
                        'type' => 'select',
                        'lang' => false,
                        'label' => $this->l('Position'),
                        'required' => false,
                        'desc' => $this->l('Choose where the fields will appears. Top of the form or at the bottom'),
                        'options' => array(
                            'id' => 'id_pos',
                            'name' => 'name', 
                            'query' => array(
                                          array(
                                            'id_pos' => 'top', 
                                            'name' => $this->l('Top'),
                                          ),
                                          array(
                                            'id_pos' => 'bottom',
                                            'name' => $this->l('Bottom'),
                                          ),
                                        ),
                          ),
                        'name' => 'DJTALBR_CPF_CNPJ_POSITION',
                    ),
                    array(
                        'type' => $switch,
                        'class' => 't',
                        'label' => $this->l('Ask for RG with CPF'),
                        'name' => 'DJTALBR_CPF_CNPJ_RG',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => $switch,
                        'class' => 't',
                        'label' => $this->l('Ask for IE (Inscrição Estadual) with CNPJ'),
                        'name' => 'DJTALBR_CPF_CNPJ_IE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => $switch,
                        'class' => 't',
                        'label' => $this->l('Ask for Razão Social with CNPJ'),
                        'name' => 'DJTALBR_CNPJ_SR',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
					array(
                        'type' => $switch,
                        'label' => $this->l('Accept duplicate values'),
                        'name' => 'DJTALBR_ACCEPT_DUPLICATE_VALUES',
                        'is_bool' => true,
                        'class' => 't',
                        'desc' => $this->l('Accept duplicate values in CPF | CNPJ'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
					array(
                        'type' => $switch,
                        'class' => 't',
                        'label' => $this->l('Ask for a Complementary Question'),
                        'desc' => $this->l('In order to customise the question, The text should be translated in the Prestashop translation interface'),
                        'name' => 'DJTALBR_ASK_COMP',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => $switch,
                        'class' => 't',
                        'label' => $this->l('Mandatory'),
                        'name' => 'DJTALBR_CPF_CNPJ_MANDATORY',
                        'is_bool' => true,
                        'desc' => $this->l('Your clients will have to fill this information or not'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
					array(
                        'type' => 'select',
                        'lang' => false,
                        'label' => $this->l('The customer permission'),
                        'required' => false,
                        'desc' => $this->l('After the custumer registration, define if they can change or not the data of their fiscal information'),
                        'options' => array(
                            'id' => 'id_perm',
                            'name' => 'name', 
                            'query' => array(
                                          array(
                                            'id_perm' => 'none', 
                                            'name' => $this->l('None: Only the administrator can change or add fiscal information'),
                                          ),
                                          array(
                                            'id_perm' => 'add',
                                            'name' => $this->l('Add: The customer can add missing information'),
                                          ),
										  array(
                                            'id_perm' => 'edit',
                                            'name' => $this->l('Edit: The customer can change the registred information'),
                                          ),
                                        ),
                          ),
                        'name' => 'DJTALBR_CUTOMER_PERMISSIONS',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save All'),
                    'name' => 'submitDjtalBrazilianRegisterModule-save',
                ),
            ),
        );
    }
    
    /**
     * Create the structure of your form.
     */
    protected function getConfigFormCEP()
    {
        $version = Tools::substr(_PS_VERSION_, 0, 3);
        $switch = 'switch';
        if($version != '1.6') {
            $switch = 'radio';
        }
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('CEP Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('CEP Search mode'),
                        'name' => 'DJTALBR_CEP_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Choose the CEP search mode: It can  be manual, then the user have the option to click a button to initiate the search, or the search can start automatically when the CEP is digited. The automatic search can also prevent the user to change the adress information'),
                        'options' => array(
							'id' => 'id_mode',
							'name' => 'name', 
							'query' => array(
                                array(
									'id_mode' => 'none', 
									'name' => $this->l('None'),
									),
								array(
									'id_mode' => 'manual', 
									'name' => $this->l('Manually'),
									),
								array(
									'id_mode' => 'auto',
									'name' => $this->l('Automatic'),
									),
								array(
									'id_mode' => 'auto_prevent',
									'name' => $this->l('Automatic and prevent'),
									),
							),                                          
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'lang' => false,
                        'label' => $this->l('Choose a web service'),
                        'required' => false,
                        'hint' => $this->l('Select one of the Provider'),
                        'desc' => $this->l('More information:').
                            '<a target="blank" href="http://www.republicavirtual.com.br/" >www.republicavirtual.com.br</a> - 
                            <a target="blank" href="http://avisobrasil.com.br/" >www.avisobrasil.com.br</a> - 
                            <a target="blank" href="http://postmon.com.br/" >www.postmon.com.br</a> - 
                            <a target="blank" href="http://viacep.com.br/" >www.viacep.com.br</a><br />'.
                            $this->l('If you need to use another provider, feel free to ask us: ').'<a target="blank" href="http://djtal.com.br/contato/" >'.$this->l('Contact').'</a>',
                        'options' => array(
							'id' => 'id_mode',
							'name' => 'name', 
							'query' => array(
								array(
									'id_mode' => 'republicavirtual', 
									'name' => $this->l('Republica Virtual'),
									),
								array(
									'id_mode' => 'avisobrasil',
									'name' => $this->l('Aviso Brasil'),
									),
								array(
									'id_mode' => 'postmon',
									'name' => $this->l('Postmon'),
									),
								array(
									'id_mode' => 'viacep',
									'name' => $this->l('Viacep'),
									),
							),                                          
                        ),
                        'name' => 'DJTALBR_CEP_WEBSERVICE',
                    ),
                    array(
                        'type' => $switch,
                        'class' => 't',
                        'label' => $this->l('Don\'t know my CEP'),
                        'name' => 'DJTALBR_CEP_DK',
                        'is_bool' => true,
                        'desc' => $this->l('Show a "I Don\'t know my CEP" button. This button redirect\'s the user the correios page'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save All'),
                    'name' => 'submitDjtalBrazilianRegisterModule-save',
                ),
            ),
        );
    }
    
    
    protected function getOthersConfig()
    {
        $version = Tools::substr(_PS_VERSION_, 0, 3);
        $switch = 'switch';
        if($version != '1.6') {
            $switch = 'radio';
        }
        
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Others options'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => $switch,
                        'class' => 't',
                        'label' => $this->l('Show customer informations on the invoice'),
                        'name' => 'DJTALBR_CPF_CNPJ_PDF_INVOICE',
                        'is_bool' => true,
                        'desc' => $this->l('Show the CPF / CNPJ / RG / IE on the PDF invoice'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => $switch,
                        'class' => 't',
                        'label' => $this->l('Add mascara on the phone number'),
                        'name' => 'DJTALBR_CEP_PHONES',
                        'is_bool' => true,
                        'desc' => $this->l('Format the phones numbers: (xx) xxxx-xxxx e (xx) 9xxxx-xxxx for mobile'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show a specific field for the street number'),
                        'name' => 'DJTALBR_STREET_NUM',
                        'options' => array(
                                'id' => 'id_mode',
                                'name' => 'name', 
                                'query' => array(
                                              array(
                                                'id_mode' => 'no', 
                                                'name' => $this->l('No'),
                                              ),
                                              array(
                                                'id_mode' => 'yes',
                                                'name' => $this->l('Yes but not mandatory'),
                                              ),
                                              array(
                                                'id_mode' => 'yes_mand',
                                                'name' => $this->l('Yes, mandatory'),
                                              ),
                                            ),                                          
                              ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Show a specific field for the address complement'),
                        'name' => 'DJTALBR_STREET_COMPL',
                        'options' => array(
                                'id' => 'id_mode',
                                'name' => 'name', 
                                'query' => array(
                                              array(
                                                'id_mode' => 'no', 
                                                'name' => $this->l('No'),
                                              ),
                                              array(
                                                'id_mode' => 'yes',
                                                'name' => $this->l('Yes but not mandatory'),
                                              ),
                                              array(
                                                'id_mode' => 'yes_mand',
                                                'name' => $this->l('Yes, mandatory'),
                                              ),
                                            ),                                          
                              ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save All'),
                    'name' => 'submitDjtalBrazilianRegisterModule-save',
                ),
            ),
        );
    }
    
    /**
     * Create the structure of your form.
     */
    protected function getCEPtest()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('CEP Testing'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'lang' => false,
                        'label' => $this->l('Choose a web service for testing purpose'),
                        'required' => false,
                        'hint' => $this->l('Select one of the Provider'),
                        'desc' => $this->l('More information:').
                            '<a target="blank" href="http://www.republicavirtual.com.br/" >www.republicavirtual.com.br</a> - 
                            <a target="blank" href="http://avisobrasil.com.br/" >www.avisobrasil.com.br</a> - 
                            <a target="blank" href="http://postmon.com.br/" >www.postmon.com.br</a> - 
                            <a target="blank" href="http://viacep.com.br/" >www.viacep.com.br</a><br />'.
                            $this->l('If you need to use another provider, feel free to ask us: ').'<a target="blank" href="http://djtal.com.br/contato/" >'.$this->l('Contact').'</a>',
                        'options' => array(
                            'id' => 'id_mode',
                            'name' => 'name', 
                            'query' => array(
                                          array(
                                            'id_mode' => 'republicavirtual', 
                                            'name' => $this->l('Republica Virtual'),
                                          ),
                                          array(
                                            'id_mode' => 'avisobrasil',
                                            'name' => $this->l('Aviso Brasil'),
                                          ),
                                          array(
                                            'id_mode' => 'postmon',
                                            'name' => $this->l('Postmon'),
                                          ),
                                          array(
                                            'id_mode' => 'viacep',
                                            'name' => $this->l('Viacep'),
                                          ),
                                        ),                                          
                          ),
                        'name' => 'DJTALBR_CEP_TEST_WEBSERVICE',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('CEP to be tested'),
                        'name' => 'DJTALBR_CEP_TEST_NUM',
                        'desc' => $this->l('Choose one of the CEP provider, enter a CEP and click "TEST"').'<div id="resultCEP" dataurl="'.$this->context->link->getModuleLink('djtalbrazilianregister', 'cep', array(), true).'" ></div>',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('TEST'),
                    'icon' => 'icon-magic',
                    'name' => 'submitDjtalBrazilianRegisterModule-test',
                    'id' => 'ajax-test',
                ),
            ),
        );
    }
    
    protected function getDataImport()
    {
        $version = Tools::substr(_PS_VERSION_, 0, 3);
        $switch = 'switch';
        if($version != '1.6') {
            $switch = 'radio';
        }
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Import Customers data'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => $switch,
                        'class' => 't',
                        'label' => $this->l('Import Action'),
                        'name' => 'DJTALBR_IMPORT_ACTION',
                        'is_bool' => true,
                        'desc' => $this->l('Yes: Real Import | No: SQL Test only'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Import')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Test')
                            )
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Advanced Users ONLY - SQL Query'),
                        'name' => 'DJTALBR_IMPORT_QUERY',
                        'desc' => $this->l('The expected colums are: ').'id_customer | cpf | cnpj | cpf_or_cnpj | rg_or_ie . <div id="resultImport" ></div>',
                    ),
                ),
                'submit' => array(
                        'title' => $this->l('IMPORT'),
                        'icon' => 'icon-magic',
                        'name' => 'submitDjtalBrazilianRegisterModule-import',
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'DJTALBR_CPF_CNPJ_ACTIVE' => Configuration::get('DJTALBR_CPF_CNPJ_ACTIVE'),
            'DJTALBR_CPF_CNPJ_MODE' => Configuration::get('DJTALBR_CPF_CNPJ_MODE'),
            'DJTALBR_CPF_CNPJ_POSITION' => Configuration::get('DJTALBR_CPF_CNPJ_POSITION'),
            'DJTALBR_CPF_CNPJ_RG' => Configuration::get('DJTALBR_CPF_CNPJ_RG'),
            'DJTALBR_CPF_CNPJ_IE' => Configuration::get('DJTALBR_CPF_CNPJ_IE'),
            'DJTALBR_CNPJ_SR' => Configuration::get('DJTALBR_CNPJ_SR'),
			'DJTALBR_ACCEPT_DUPLICATE_VALUES' => Configuration::get('DJTALBR_ACCEPT_DUPLICATE_VALUES'),
            'DJTALBR_ASK_COMP' => Configuration::get('DJTALBR_ASK_COMP'),
            'DJTALBR_CPF_CNPJ_MANDATORY' => Configuration::get('DJTALBR_CPF_CNPJ_MANDATORY'),
            'DJTALBR_CUTOMER_PERMISSIONS' => Configuration::get('DJTALBR_CUTOMER_PERMISSIONS'),
            'DJTALBR_CEP_DK' => Configuration::get('DJTALBR_CEP_DK'),
            'DJTALBR_CEP_MODE' => Configuration::get('DJTALBR_CEP_MODE'),
            'DJTALBR_CEP_WEBSERVICE' => Configuration::get('DJTALBR_CEP_WEBSERVICE'),
            'DJTALBR_CEP_TEST_NUM' => Configuration::get('DJTALBR_CEP_TEST_NUM'),
            'DJTALBR_CEP_TEST_WEBSERVICE' => Configuration::get('DJTALBR_CEP_TEST_WEBSERVICE'),
            'DJTALBR_CPF_CNPJ_PDF_INVOICE' => Configuration::get('DJTALBR_CPF_CNPJ_PDF_INVOICE'),
            'DJTALBR_CEP_PHONES' => Configuration::get('DJTALBR_CEP_PHONES'),
            'DJTALBR_STREET_NUM' => Configuration::get('DJTALBR_STREET_NUM'),
            'DJTALBR_STREET_COMPL' => Configuration::get('DJTALBR_STREET_COMPL'),
            'DJTALBR_IMPORT_QUERY' => Configuration::get('DJTALBR_IMPORT_QUERY'),
            'DJTALBR_IMPORT_ACTION' => Configuration::get('DJTALBR_IMPORT_ACTION'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
		$this->sendUsageStats();
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key){
            Configuration::updateValue($key, Tools::getValue($key));
		}
            
        //import
        if (((bool)Tools::isSubmit('submitDjtalBrazilianRegisterModule-import')) == true){
            $query = Configuration::get('DJTALBR_IMPORT_QUERY');
            $import = Configuration::get('DJTALBR_IMPORT_ACTION');
            
            if (!empty($query) && $results = Db::getInstance()->ExecuteS($query)) {
                $results = BrazilianRegister::importByRawData($results, (bool)$import);
                if((bool)$import === false){
                    $this->context->smarty->assign('import_res', $results);
                }
            }
        } else {
            return $this->displayConfirmation($this->l('Settings updated'));
        }
    }
    
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        if (!$this->active)
            return;
        
        
        $registrationType= Configuration::get('PS_REGISTRATION_PROCESS_TYPE');
        //PS_REGISTRATION_PROCESS_TYPE = 0 : Only personal information
        //PS_REGISTRATION_PROCESS_TYPE = 1 : Personal information AND adress
        
        if((isset($this->context->controller->php_self) && $this->context->controller->php_self == 'address')
            || (isset($this->context->controller->php_self) && ($registrationType == 1 && $this->context->controller->php_self == 'authentication'))
            || (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'order-opc')) {
            $this->context->controller->addJS($this->_path.'/views/js/jquery.mask.mod.js');
            if (version_compare('1.6', _PS_VERSION_) > 0) { //version before 1.6
                $this->context->controller->addCSS($this->_path.'/views/css/front-15.css');
                $this->context->controller->addJS($this->_path.'/views/js/front-15.js');
            } else { //version after 1.6
                $this->context->controller->addCSS($this->_path.'/views/css/front.css');
                $this->context->controller->addJS($this->_path.'/views/js/front.js');
            }
            
            $jsDef = array(
                'cep_search_mode' => Configuration::get('DJTALBR_CEP_MODE'),
                'cep_dk_button' => (bool)Configuration::get('DJTALBR_CEP_DK'),
                'cep_ws_url' => $this->context->link->getModuleLink('djtalbrazilianregister', 'cep', array(), true),
                'phones_mask' => (bool)Configuration::get('DJTALBR_CEP_PHONES'),
                'street_num' => Configuration::get('DJTALBR_STREET_NUM'),
                'street_compl' => Configuration::get('DJTALBR_STREET_COMPL'),
                'translated_num' => $this->l('Number'),
                'translated_comp' => $this->l('Complement'));
                
            if(method_exists('Media','addJsDef')){
                Media::addJsDef($jsDef);
            } else {
                $this->smarty->assign($jsDef);
                return $this->display(__FILE__, 'views/templates/hook/header-no-jsdef.tpl');
            }
        } elseif(isset($this->context->controller->php_self) && $this->context->controller->php_self == 'my-account'){
			
		}
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name)
        {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path.'views/js/jquery.mask.mod.js');
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
		
		// Admin Customer Form: New Inputs
		if($this->context->controller->controller_type == 'admin' && $this->context->controller->controller_name == 'AdminCustomers') {
			$id_customer = (int)Tools::getValue('id_customer');
			$breg = BrazilianRegister::getByCustomerId($id_customer);
			
			$jsDef = array();
			
			$jsDef['cpf_value'] = true;
			if(Tools::getValue('cpf')){
				$jsDef['cpf_value'] = Tools::getValue('cpf');
			}elseif(!empty($breg['cpf'])) {
				$jsDef['cpf_value'] = $breg['cpf'];
			}
			
			$jsDef['cnpj_value'] = true;
			if(Tools::getValue('cnpj')){
				$jsDef['cnpj_value'] = Tools::getValue('cnpj');
			}elseif(!empty($breg['cnpj'])) {
				$jsDef['cnpj_value'] = $breg['cnpj'];
			}
			
			$jsDef['rg_value'] = false;
			if((bool)Configuration::get('DJTALBR_CPF_CNPJ_RG') == true) {
				$jsDef['rg_value'] = true;
				if(Tools::getValue('rg')){
					$jsDef['rg_value'] = Tools::getValue('rg');
				}elseif(!empty($breg['rg'])) {
					$jsDef['rg_value'] = $breg['rg'];
				}
            }
			
			$jsDef['ie_value'] = false;
			if((bool)Configuration::get('DJTALBR_CPF_CNPJ_IE') == true) {
				$jsDef['ie_value'] = true;
				if(Tools::getValue('ie')){
					$jsDef['ie_value'] = Tools::getValue('ie');
				}elseif(!empty($breg['ie'])) {
					$jsDef['ie_value'] = $breg['ie'];
				}
            }
            
			$jsDef['sr_value'] = false;
			if((bool)Configuration::get('DJTALBR_CNPJ_SR') == true) {
				$jsDef['sr_value'] = true;
				if(Tools::getValue('sr')){
					$jsDef['sr_value'] = Tools::getValue('sr');
				}elseif(!empty($breg['sr'])) {
					$jsDef['sr_value'] = $breg['sr'];
				}
            }
			
			Media::addJsDef($jsDef);
			$this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path.'views/js/jquery.mask.mod.js');
            $this->context->controller->addJS($this->_path.'views/js/back-customer.js');
		}
		
		// Admin Addresses Form: New Inputs
		if($this->context->controller->controller_type == 'admin' && $this->context->controller->controller_name == 'AdminAddresses') {
			$id_address = (int)Tools::getValue('id_address');
			
			$jsDef = array();
			$jsDef['street_num'] = Configuration::get('DJTALBR_STREET_NUM');
			$jsDef['street_compl'] = Configuration::get('DJTALBR_STREET_COMPL');
			
			Media::addJsDef($jsDef);
			$this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path.'views/js/back-address.js');
		}
    }
	
    public function hookActionBeforeSubmitAccount($params) {
        if (!$this->active)
            return;
        
        $mode = Configuration::get('DJTALBR_CPF_CNPJ_MODE');
        $mandatory = (bool)Configuration::get('DJTALBR_CPF_CNPJ_MANDATORY');
        $ask_rg = (bool)Configuration::get('DJTALBR_CPF_CNPJ_RG');
        $ask_ie = (bool)Configuration::get('DJTALBR_CPF_CNPJ_IE');
        $ask_sr = (bool)Configuration::get('DJTALBR_CNPJ_SR');
        $ask_comp = (bool)Configuration::get('DJTALBR_ASK_COMP');
		$accept_duplicate_values = (bool) Configuration::get('DJTALBR_ACCEPT_DUPLICATE_VALUES');
        
        if($mode == 'cpf-or-cnpj' || $mode == 'cpf-or-cnpj-or-passport') {
            $doc_type = Tools::getValue('id_cp_mode');
            $doc_value = Tools::getValue('br_document');
            $rg = Tools::getValue('br_document_rg');
            $ie = Tools::getValue('br_document_ie');
            $sr = Tools::getValue('br_document_sr');
            $comp = Tools::getValue('br_document_comp');
            
            if(!empty($doc_value)){
                if($doc_type == 'cpf') {
                    if(!self::validateCPF($doc_value)){
                        $this->context->controller->errors[] = '<b>CPF</b> '.$this->l('is invalid');
                    } elseif ($accept_duplicate_values == false) {
                        $this->validateDuplicateValues('cpf', $doc_value);
                    }
                } elseif($doc_type == 'cnpj') {
                    if(!self::validateCNPJ($doc_value)){
                        $this->context->controller->errors[] = '<b>CNPJ</b> '.$this->l('is invalid');
                    } elseif ($accept_duplicate_values == false) {
                        $this->validateDuplicateValues('cnpj', $doc_value);
                    }
                } elseif($doc_type == 'passport') {
                    if ($accept_duplicate_values == false) {
                        $this->validateDuplicateValues('passport', $doc_value);
                    }
                }
            }
            
            if($ask_comp && ($mandatory && empty($comp))) {
                $this->context->controller->errors[] = '<b>'.$this->l('The complementary question').'</b> '.$this->l('is necessary');
                return;
            }
			
            if($mode == 'cpf-or-cnpj'){
                if($mandatory && empty($doc_value)) {
                    $this->context->controller->errors[] = '<b>'.$this->l('CPF or CNPJ').'</b> '.$this->l('is necessary');
                    return;
                }
            } elseif($mode == 'cpf-or-cnpj-or-passport'){
                if($mandatory && empty($doc_value)) {
                    $this->context->controller->errors[] = '<b>'.$this->l('CPF or CNPJ, or Passport').'</b> '.$this->l('is necessary');
                    return;
                }
            }
            
            if($ask_rg && $mandatory && $doc_type == 'cpf' && empty($rg)) {
                $this->context->controller->errors[] = '<b>'.$this->l('RG').'</b> '.$this->l('is necessary');
                return;
            }
            if($ask_ie && $mandatory && $doc_type == 'cnpj' && empty($ie)) {
                $this->context->controller->errors[] = '<b>'.$this->l('IE').'</b> '.$this->l('is necessary');
                return;
            }
            if($ask_sr && $mandatory && $doc_type == 'cnpj' && empty($sr)) {
                $this->context->controller->errors[] = '<b>'.$this->l('Razão Social').'</b> '.$this->l('is necessary');
                return;
            }
            if($ask_sr && $mandatory && $doc_type == 'cnpj' && empty($sr)) {
                $this->context->controller->errors[] = '<b>'.$this->l('Razão Social').'</b> '.$this->l('is necessary');
                return;
            }
            
            
            if(!$mandatory && empty($doc_value)) {
                return;
            }
        } elseif($mode == 'cpf-and-cnpj') {
            $br_document_cpf = Tools::getValue('br_document_cpf');
            $br_document_cnpj = Tools::getValue('br_document_cnpj');
            $rg = Tools::getValue('br_document_rg');
            $ie = Tools::getValue('br_document_ie');
            $sr = Tools::getValue('br_document_sr');
			$comp = Tools::getValue('br_document_comp');
            
            if(!empty($br_document_cpf)){
                if(!self::validateCPF($br_document_cpf)){
                    $this->context->controller->errors[] = '<b>CPF</b> '.$this->l('is invalid');
                } elseif ($accept_duplicate_values == false) {
                    $this->validateDuplicateValues('cpf', $br_document_cpf);
                }
            }
            if(!empty($br_document_cnpj)){
                if(!self::validateCNPJ($br_document_cnpj)){
                    $this->context->controller->errors[] = '<b>CNPJ</b> '.$this->l('is invalid');
                } elseif ($accept_duplicate_values == false) {
                    $this->validateDuplicateValues('cnpj', $br_document_cnpj);
                }
            }
			
	    if($ask_comp && ($mandatory && empty($comp))) {
                $this->context->controller->errors[] = '<b>'.$this->l('The complementary question').'</b> '.$this->l('is necessary');
                return;
            }
            
            if($mandatory && empty($br_document_cpf)) {
                $this->context->controller->errors[] = '<b>CPF</b> '.$this->l('is necessary');
                return;
            }
            if($mandatory && empty($br_document_cnpj)) {
                $this->context->controller->errors[] = '<b>CNPJ</b> '.$this->l('is necessary');
                return;
            }
            if($ask_rg && $mandatory && empty($rg)) {
                $this->context->controller->errors[] = '<b>'.$this->l('RG').'</b> '.$this->l('is necessary');
                return;
            }
            if($ask_ie && $mandatory && empty($ie)) {
                $this->context->controller->errors[] = '<b>'.$this->l('IE').'</b> '.$this->l('is necessary');
                return;
            }
            if($ask_sr && $mandatory && empty($sr)) {
                $this->context->controller->errors[] = '<b>'.$this->l('Razão Social').'</b> '.$this->l('is necessary');
                return;
            }
        } elseif($mode == 'cpf-only') {
            $br_document_cpf = Tools::getValue('br_document_cpf');
            $rg = Tools::getValue('br_document_rg');
			$comp = Tools::getValue('br_document_comp');
            
            if(!empty($br_document_cpf)){
                if(!self::validateCPF($br_document_cpf)){
                    $this->context->controller->errors[] = '<b>CPF</b> '.$this->l('is invalid');
                } elseif ($accept_duplicate_values == false) {
                    $this->validateDuplicateValues('cpf', $br_document_cpf);
                }
            }
			
			if($ask_comp && ($mandatory && empty($comp))) {
                $this->context->controller->errors[] = '<b>'.$this->l('The complementary question').'</b> '.$this->l('is necessary');
                return;
            }
            
            if($mandatory && empty($br_document_cpf)) {
                $this->context->controller->errors[] = '<b>CPF</b> '.$this->l('is necessary');
                return;
            }
            if($ask_rg && $mandatory && empty($rg)) {
                $this->context->controller->errors[] = '<b>'.$this->l('RG').'</b> '.$this->l('is necessary');
                return;
            }
        } elseif($mode == 'cnpj-only') {
            $br_document_cnpj = Tools::getValue('br_document_cnpj');
            $ie = Tools::getValue('br_document_ie');
            $sr = Tools::getValue('br_document_sr');
			$comp = Tools::getValue('br_document_comp');
            
            if(!empty($br_document_cnpj)){
                if(!self::validateCNPJ($br_document_cnpj)){
                    $this->context->controller->errors[] = '<b>CNPJ</b> '.$this->l('is invalid');
                } elseif ($accept_duplicate_values == false) {
                    $this->validateDuplicateValues('cnpj', $br_document_cnpj);
                }
            }
			
			if($ask_comp && ($mandatory && empty($comp))) {
                $this->context->controller->errors[] = '<b>'.$this->l('The complementary question').'</b> '.$this->l('is necessary');
                return;
            }
            
            if($mandatory && empty($br_document_cnpj)) {
                $this->context->controller->errors[] = '<b>CNPJ</b> '.$this->l('is necessary');
                return;
            }
            if($ask_ie && $mandatory && empty($ie)) {
                $this->context->controller->errors[] = '<b>'.$this->l('IE').'</b> '.$this->l('is necessary');
                return;
            }
            if($ask_sr && $mandatory && empty($sr)) {
                $this->context->controller->errors[] = '<b>'.$this->l('Razão Social').'</b> '.$this->l('is necessary');
                return;
            }
        }
    }
	
	public function validateDuplicateValues($type, $value) {
        switch ($type) {
            case 'cpf':
                if (BrazilianRegister::cpfExist($value)) {
                    $this->context->controller->errors[] = $this->l('This').' <b>CPF</b> ' . $this->l('is already registered');
                }
                break;
            case 'cnpj':
                if (BrazilianRegister::cnpjExist($value)) {
                    $this->context->controller->errors[] = $this->l('This').' <b>CNPJ</b> ' . $this->l('is already registered');
                }
                break;
            case 'passport':
                if (BrazilianRegister::passportExist($value)) {
                    $this->context->controller->errors[] = $this->l('This').' <b>'.$this->l('Passport Number').'</b> ' . $this->l('is already registered');
                }
                break;
        }
    }
	
    public function hookCreateAccount($params)
    {
        if (!$this->active)
            return;
        
        $id_customer = $params['newCustomer']->id;
        $mode = Configuration::get('DJTALBR_CPF_CNPJ_MODE');
        
        if($mode == 'cpf-or-cnpj' || $mode == 'cpf-or-cnpj-or-passport') {
            $data = array();
            $doc_type = Tools::getValue('id_cp_mode');
            $doc_value = Tools::getValue('br_document');
            $rg = Tools::getValue('br_document_rg', null);
            $ie = Tools::getValue('br_document_ie', null);
            $sr = Tools::getValue('br_document_sr', null);
			$comp = Tools::getValue('br_document_comp', null);
            $doc_value = preg_replace('/[^0-9]/', '', $doc_value);
            
            $data[pSQL($doc_type)] = pSQL($doc_value);
            $data['rg'] = pSQL($rg);
            $data['ie'] = pSQL($ie);
            $data['sr'] = pSQL($sr);
            $data['comp'] = pSQL($comp);
            BrazilianRegister::insertByCustomerId($id_customer, $data);
        } elseif($mode == 'cpf-and-cnpj') {
            $br_document_cpf = Tools::getValue('br_document_cpf');
            $br_document_cnpj = Tools::getValue('br_document_cnpj');
            $rg = Tools::getValue('br_document_rg', null);
            $ie = Tools::getValue('br_document_ie', null);
            $sr = Tools::getValue('br_document_sr', null);
			$comp = Tools::getValue('br_document_comp', null);
            $br_document_cpf = preg_replace('/[^0-9]/', '', $br_document_cpf);
            $br_document_cnpj = preg_replace('/[^0-9]/', '', $br_document_cnpj);
            BrazilianRegister::insertByCustomerId($id_customer, array('cpf' => pSQL($br_document_cpf), 'cnpj' => pSQL($br_document_cnpj), 'rg' => $rg, 'ie' => $ie, 'sr' => $sr, 'comp' => pSQL($comp)));
        } elseif($mode == 'cpf-only') {
            $br_document_cpf = Tools::getValue('br_document_cpf');
            $rg = Tools::getValue('br_document_rg', null);
			$comp = Tools::getValue('br_document_comp', null);
            $br_document_cpf = preg_replace('/[^0-9]/', '', $br_document_cpf);
            BrazilianRegister::insertByCustomerId($id_customer, array('cpf' => pSQL($br_document_cpf), 'rg' => $rg, 'comp' => pSQL($comp)));
        } elseif($mode == 'cnpj-only') {
            $br_document_cnpj = Tools::getValue('br_document_cnpj');
            $ie = Tools::getValue('br_document_ie', null);
            $sr = Tools::getValue('br_document_sr', null);
			$comp = Tools::getValue('br_document_comp', null);
            $br_document_cnpj = preg_replace('/[^0-9]/', '', $br_document_cnpj);
            BrazilianRegister::insertByCustomerId($id_customer, array('cnpj' => pSQL($br_document_cnpj), 'ie' => $ie, 'sr' => $sr, 'comp' => pSQL($comp)));
        }
    }
    
    public function hookDisplayCustomerAccountFormTop($params) {
        if (!$this->active)
            return;
        
        $php_self = '';
        if(isset($this->context->controller->php_self)) {
             $php_self = $this->context->controller->php_self;
        }
		
		$registrationType= Configuration::get('PS_REGISTRATION_PROCESS_TYPE');
        //PS_REGISTRATION_PROCESS_TYPE = 0 : Only personal information
        //PS_REGISTRATION_PROCESS_TYPE = 1 : Personal information AND adress
        
        if(Configuration::get('DJTALBR_CPF_CNPJ_POSITION') == 'top' || $php_self == 'order-opc' || $registrationType == 1) {
            if (version_compare('1.6', _PS_VERSION_) > 0) { //version before 1.6
                return $this->cpfAndCnpjHook($params, 'authenticate-15');
            } else { //version after 1.6
                return $this->cpfAndCnpjHook($params, 'authenticate');
            }
        }
    }
    
    public function hookDisplayCustomerAccount($params) {
		if (!$this->active || Configuration::get('DJTALBR_CUTOMER_PERMISSIONS') == 'none')
        return;
	
		$this->context->controller->addCSS($this->_path.'/views/css/front-my-account.css');
		return $this->display(__FILE__, 'views/templates/hook/my-account-link.tpl');
	}
	
	
    public function hookDisplayCustomerAccountForm($params) {
        if (!$this->active)
            return;
        
        $php_self = '';
        if(isset($this->context->controller->php_self)) {
             $php_self = $this->context->controller->php_self;
        }
        
		$registrationType= Configuration::get('PS_REGISTRATION_PROCESS_TYPE');
        //PS_REGISTRATION_PROCESS_TYPE = 0 : Only personal information
        //PS_REGISTRATION_PROCESS_TYPE = 1 : Personal information AND adress
		
        if(Configuration::get('DJTALBR_CPF_CNPJ_POSITION') == 'bottom' && $php_self != 'order-opc' && $registrationType == 0) {
            if (version_compare('1.6', _PS_VERSION_) > 0) { //version before 1.6
                return $this->cpfAndCnpjHook($params, 'authenticate-15');
            } else { //version after 1.6
                return $this->cpfAndCnpjHook($params, 'authenticate');
            }
        }
    }
    public function hookDisplayCustomerIdentityForm($params)
    {
        if (!$this->active)
            return;
		
		$this->smarty->assign('customer_permissions', Configuration::get('DJTALBR_CUTOMER_PERMISSIONS'));
		
        return $this->cpfAndCnpjHook($params, 'identity');
    }

    public function hookDisplayHeader()
    {
        return $this->hookHeader();
    }
    
    public function hookDisplayPDFInvoice($params)
    {
        if (!$this->active)
            return;
       
        if((bool)Configuration::get('DJTALBR_CPF_CNPJ_PDF_INVOICE') == false)
            return;
            
        $order = new Order($params['object']->id_order);
        $breg = BrazilianRegister::getByCustomerId($order->id_customer);
        $br_document_cpf = $breg['cpf'];
        $br_document_cnpj = $breg['cnpj'];
        $br_document_rg = $breg['rg'];
        $br_document_ie = $breg['ie'];
        $br_document_sr = $breg['sr'];
        $br_document_comp = null;
		if((bool)Configuration::get('DJTALBR_ASK_COMP') == true){
			$br_document_comp = '<br />Comp: '.$breg['comp'];
		}
        return  'CPF: '.Djtalbrazilianregister::mascaraString('###.###.###-##', $br_document_cpf).
                '<br />CNPJ: '.Djtalbrazilianregister::mascaraString('##.###.###/####-##', $br_document_cnpj).
                '<br />RG: '.$br_document_rg.
                '<br />IE: '.$br_document_ie.
                '<br />SR: '.$br_document_ie.
				$br_document_comp;
    }
    
    public function hookDisplayAdminOrderTabOrder($params) {
        if (!$this->active)
        return;
    
        return $this->display(__FILE__, 'views/templates/hook/admin-order-tab.tpl');
    }
    
    public function hookDisplayAdminOrderContentOrder($params)
    {
        if (!$this->active)
        return;
    
        $customer = $params['customer'];
        $breg = BrazilianRegister::getByCustomerId($customer->id);
        $br_document_cpf = $breg['cpf'];
        $br_document_cnpj = $breg['cnpj'];
        $br_document_rg = $breg['rg'];
        $br_document_ie = $breg['ie'];
        $br_document_sr = $breg['sr'];
        
        $this->smarty->assign(array(
            'br_document_cpf' => $br_document_cpf,
            'br_document_cnpj' => $br_document_cnpj,
            'br_document_rg' => $br_document_rg,
            'br_document_ie' => $br_document_ie,
            'br_document_sr' => $br_document_sr,
        ));
            
        return $this->display(__FILE__, 'views/templates/hook/admin-order-content.tpl');
    }
	
	public function hookActionObjectCustomerDeleteAfter($params)
    {
		BrazilianRegister::deleteByCustomerId($params['object']->id);
	}
    
    public function hookDisplayAdminCustomers($params)
    {
        if (!$this->active)
        return ;

        $customer = new Customer((int)($params['id_customer']));
        $breg = BrazilianRegister::getByCustomerId($customer->id);
        $br_document_cpf = $breg['cpf'];
        $br_document_cnpj = $breg['cnpj'];
        $br_document_rg = $breg['rg'];
        $br_document_ie = $breg['ie'];
        $br_document_sr = $breg['sr'];
		$br_document_comp = $breg['comp'];
        
        $this->smarty->assign(array(
            'br_document_cpf' => $br_document_cpf,
            'br_document_cnpj' => $br_document_cnpj,
            'br_document_rg' => $br_document_rg,
            'br_document_ie' => $br_document_ie,
            'br_document_sr' => $br_document_sr,
            'br_document_comp' => $br_document_comp,
        ));
            
        return $this->display(__FILE__, 'views/templates/hook/admin-customer.tpl');
    }
    
    public function cpfAndCnpjHook($params, $display)
    {
        if((bool)Configuration::get('DJTALBR_CPF_CNPJ_ACTIVE') == true) {
            $mode = Configuration::get('DJTALBR_CPF_CNPJ_MODE');
            $mandatory = (bool)Configuration::get('DJTALBR_CPF_CNPJ_MANDATORY');
            $ask_rg = (bool)Configuration::get('DJTALBR_CPF_CNPJ_RG');
            $ask_ie = (bool)Configuration::get('DJTALBR_CPF_CNPJ_IE');
            $ask_sr = (bool)Configuration::get('DJTALBR_CNPJ_SR');
            $ask_comp = (bool)Configuration::get('DJTALBR_ASK_COMP');
            
            $breg = BrazilianRegister::getByCustomerId($params['cookie']->id_customer);
            $br_document_cpf = $breg['cpf'];
            $br_document_cnpj = $breg['cnpj'];
            $br_document_passport = $breg['passport'];
            $br_document_rg = $breg['rg'];
            $br_document_ie = $breg['ie'];
            $br_document_sr = $breg['sr'];
            $br_document_comp = $breg['comp'];
            $br_document = ''; //CPF or CNPJ value
            
            $id_cp_mode = 'cpf';
            if($mode == 'cpf-or-cnpj' || $mode == 'cpf-or-cnpj-or-passport') {
                if(!empty($br_document_cpf)){
                    $id_cp_mode = 'cpf';
                    $br_document = $br_document_cpf;
                } elseif(!empty($br_document_cnpj)) {
                    $id_cp_mode = 'cnpj';
                    $br_document = $br_document_cnpj;
                } elseif(!empty($br_document_passport)){
                    $id_cp_mode = 'passport';
                    $br_document = $br_document_passport;
                }
            }
            
            $this->context->controller->addJS($this->_path.'/views/js/jquery.mask.mod.js');
            $this->context->controller->addJS($this->_path.'/views/js/cpf-cnpj.js');
            $this->smarty->assign(array(
                'display' => $display,
                'mode' => $mode,
                'mandatory' => $mandatory,
                'ask_rg' => $ask_rg,
                'ask_ie' => $ask_ie,
                'ask_sr' => $ask_sr,
                'ask_comp' => $ask_comp,
                'id_cp_mode' => $id_cp_mode,
                'br_document' => $br_document,
                'br_document_cpf' => $br_document_cpf,
                'br_document_cnpj' => $br_document_cnpj,
                'br_document_rg' => $br_document_rg,
                'br_document_ie' => $br_document_ie,
                'br_document_sr' => $br_document_sr,
                'br_document_comp' => $br_document_comp,
            ));

            return $this->display(__FILE__, 'views/templates/hook/cpf-cnpj-'.$display.'.tpl');
        }
    }
	
	public function hookActionObjectCustomerAddAfter($params) {
		if($this->context->controller->controller_type == 'admin' && $this->context->controller->controller_name == 'AdminCustomers') {
			$this->customerAddEditInBackOffice($params['object']);
		}
	}

    public function hookActionObjectCustomerUpdateAfter($params) {
		if($this->context->controller->controller_type == 'admin' && $this->context->controller->controller_name == 'AdminCustomers') {
			$this->customerAddEditInBackOffice($params['object']);
		}
    }
	
	public function customerAddEditInBackOffice($customer) {
		$breg = BrazilianRegister::getByCustomerId($customer->id);
		if($breg == null){ // will be certainly the case if the customer is created in the BO
			$breg = array();
		}
		$cpf = pSQL(preg_replace('/[^0-9]/', '', Tools::getValue('cpf')));
		$rg = pSQL(preg_replace('/[^0-9]/', '', Tools::getValue('rg')));
		$cnpj = pSQL(preg_replace('/[^0-9]/', '', Tools::getValue('cnpj')));
		$ie = pSQL(preg_replace('/[^0-9]/', '', Tools::getValue('ie')));
		$sr = pSQL(preg_replace('/[^0-9]/', '', Tools::getValue('sr')));
		
		if(!empty($cpf)) {
			if(self::validateCPF($cpf)) {
				$breg['cpf'] = $cpf;
			} else {
				$this->context->controller->errors[] = '<b>CPF</b> '.$this->l('is invalid');
			}
		}
		if(!empty($cnpj)) {
			if(self::validateCNPJ($cnpj)) {
				$breg['cnpj'] = $cnpj;
			} else {
				$this->context->controller->errors[] = '<b>CNPJ</b> '.$this->l('is invalid');
			}
		}
		if(!empty($rg)) {
			$breg['rg'] = $rg;
		}
		if(!empty($ie)) {
			$breg['ie'] = $ie;
		}
		if(!empty($sr)) {
			$breg['sr'] = $sr;
		}
		
		if(!empty($breg['id_djtalbrazilianregister'])) {
			BrazilianRegister::updateByCustomerId($customer->id, $breg);
        } else {
			BrazilianRegister::insertByCustomerId($customer->id, $breg);
        }
	}
    
    public static function validateCPF($cpf)
    {
        
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        
        if (Tools::strlen($cpf) != 11)
            return false;
        
        if ($cpf == '12345678909' || 
        $cpf == '00000000000' || 
        $cpf == '11111111111' || 
        $cpf == '22222222222' || 
        $cpf == '33333333333' || 
        $cpf == '44444444444' || 
        $cpf == '55555555555' || 
        $cpf == '66666666666' || 
        $cpf == '77777777777' || 
        $cpf == '88888888888' || 
        $cpf == '99999999999') {
            return false;
        }
        
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }
        
        return true;
    }
    
    public static function validateCNPJ($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        // Validate Size
        if (Tools::strlen($cnpj) != 14)
            return false;
        // Validate first control digit 
        for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++)
        {
            $sum += $cnpj{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $rest = $sum % 11;
        if ($cnpj{12} != ($rest < 2 ? 0 : 11 - $rest))
            return false;
        // Validate second control digit
        for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++)
        {
            $sum += $cnpj{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $rest = $sum % 11;
        return $cnpj{13} == ($rest < 2 ? 0 : 11 - $rest);
    }
    
    public static function mascaraString($mascara,$string)
    {
         for($i=0;$i<Tools::strlen($string);$i++)
        {
             $mascara[Tools::strpos($mascara,'#')] = $string[$i];
        }
        return $mascara;
    }
	
	public function getPath()
    {
        return $this->_path;
    }
    
}
