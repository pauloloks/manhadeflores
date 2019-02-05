<?php
/**
 * 2007-2017 PrestaShop
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
 *  @copyright 2017 DJTAL
 *  @version   1.0.0
 *  @link      http://www.djtal.com.br/
 *  @license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/classes/CieloAuthorization.php';

class Djtalcielo extends PaymentModule{

    public function __construct() {
        $this->name = 'djtalcielo';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.1';
        $this->author = 'Djtal';
        $this->need_instance = 0;
        $this->module_key = '8614306ca7fd27436a1379f8b3a1abbc';

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Cielo Payment');
        $this->description = $this->l('Module to integrate the cielo - Cielo payment solution');

        $this->confirmUninstall = $this->l('Are you sure you wan to uninstall this module');

        $this->limited_currencies = array('BRL');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install() {
		$image = _PS_ROOT_DIR_ . '/modules/'.$this->name.'/img/logo-x16.gif';

        $id_order_waiting = Configuration::get('DJTALCIELO_STATE_WAITING');
        $id_order_approved = Configuration::get('DJTALCIELO_STATE_APPROVED');
        $id_order_refused = Configuration::get('DJTALCIELO_STATE_REFUSED');
        $id_order_refunded = Configuration::get('DJTALCIELO_STATE_REFUNDED');
        $id_order_cancel = Configuration::get('DJTALCIELO_STATE_CANCEL');
        
        $stateWaiting = new OrderState($id_order_waiting);
        $stateApproved = new OrderState($id_order_approved);
        $stateRefused = new OrderState($id_order_refused);
        $stateRefunded = new OrderState($id_order_refunded);
        $stateCancel = new OrderState($id_order_cancel);

        if (!Validate::isLoadedObject($stateWaiting)) {
            $order_state_waiting = new OrderState();
            $order_state_waiting->module_name = $this->name;
            $order_state_waiting->color = '#e4ff54';
			$order_state_waiting->send_email = false;
            $order_state_waiting->hidden = false;
            $order_state_waiting->delivery = false;
            $order_state_waiting->logable = false;
            $order_state_waiting->invoice = false;
            $order_state_waiting->unremovable = false;
            $order_state_waiting->shipped = false;
            $order_state_waiting->paid = false;
			foreach (Language::getLanguages(false) as $language) {
				$order_state_waiting->name[(int) $language['id_lang']] = $this->l('Cielo - Awaiting payment'); //Aguardando Pagamento
			}
			if ($order_state_waiting->add()) {
				$file = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state_waiting->id . '.gif';
				copy($image, $file);
            }
            Configuration::updateValue('DJTALCIELO_STATE_WAITING', $order_state_waiting->id);
        }
        if (!Validate::isLoadedObject($stateApproved)) {
            $order_state_approved = new OrderState();
            $order_state_approved->module_name = $this->name;
            $order_state_approved->color = '#4169E1';
			$order_state_approved->send_email = false;
            $order_state_approved->hidden = false;
            $order_state_approved->delivery = false;
            $order_state_approved->logable = true;
            $order_state_approved->invoice = true;
            $order_state_approved->unremovable = false;
            $order_state_approved->shipped = false;
            $order_state_approved->paid = true;
			foreach (Language::getLanguages(false) as $language) {
				$order_state_approved->name[(int) $language['id_lang']] = $this->l('Cielo - Approved payment'); //Pagamento Aprovado
			}
			if ($order_state_approved->add()) {
				$file = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state_approved->id . '.gif';
				copy($image, $file);
            }
            Configuration::updateValue('DJTALCIELO_STATE_APPROVED', $order_state_approved->id);
        }
        if (!Validate::isLoadedObject($stateRefused)) {
            $order_state_refused = new OrderState();
            $order_state_refused->module_name = $this->name;
            $order_state_refused->color = '#ff4057';
			$order_state_refused->send_email = false;
            $order_state_refused->hidden = false;
            $order_state_refused->delivery = false;
            $order_state_refused->logable = false;
            $order_state_refused->invoice = false;
            $order_state_refused->unremovable = false;
            $order_state_refused->shipped = false;
            $order_state_refused->paid = false;
			foreach (Language::getLanguages(false) as $language) {
				$order_state_refused->name[(int) $language['id_lang']] = $this->l('Cielo - Refused payment'); //Pagamento Recusado
			}
			if ($order_state_refused->add()) {
				$file = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state_refused->id . '.gif';
				copy($image, $file);
            }
            Configuration::updateValue('DJTALCIELO_STATE_REFUSED', $order_state_refused->id);
        }
        if (!Validate::isLoadedObject($stateRefunded)) {
            $order_state_refuned = new OrderState();
            $order_state_refuned->module_name = $this->name;
            $order_state_refuned->color = '#ffeddb';
			$order_state_refuned->send_email = false;
            $order_state_refuned->hidden = false;
            $order_state_refuned->delivery = false;
            $order_state_refuned->logable = false;
            $order_state_refuned->invoice = false;
            $order_state_refuned->unremovable = false;
            $order_state_refuned->shipped = false;
            $order_state_refuned->paid = false;
			foreach (Language::getLanguages(false) as $language) {
				$order_state_refuned->name[(int) $language['id_lang']] = $this->l('Cielo - Refunded payment'); //Transação Estornada
			}
			if ($order_state_refuned->add()) {
				$file = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state_refuned->id . '.gif';
				copy($image, $file);
            }
            Configuration::updateValue('DJTALCIELO_STATE_REFUNDED', $order_state_refuned->id);
        }
        
        if (!Validate::isLoadedObject($stateCancel)) {
            $order_state_cancel = new OrderState();
            $order_state_cancel->module_name = $this->name;
            $order_state_cancel->color = '#c30005';
			$order_state_cancel->send_email = false;
            $order_state_cancel->hidden = false;
            $order_state_cancel->delivery = false;
            $order_state_cancel->logable = false;
            $order_state_cancel->invoice = false;
            $order_state_cancel->unremovable = false;
            $order_state_cancel->shipped = false;
            $order_state_cancel->paid = false;
			foreach (Language::getLanguages(false) as $language) {
				$order_state_cancel->name[(int) $language['id_lang']] = $this->l('Cielo - Canceled order'); //Pedido Cancelado
			}
			if ($order_state_cancel->add()) {
				$file = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state_cancel->id . '.gif';
				copy($image, $file);
            }
            Configuration::updateValue('DJTALCIELO_STATE_CANCEL', $order_state_cancel->id);
        }
        
        include(dirname(__FILE__).'/sql/install.php');
        
        return parent::install() &&
                $this->registerHook('header') &&
                $this->registerHook('backOfficeHeader') &&
                $this->registerHook('displayHeader') &&
                $this->registerHook('payment') &&
                $this->registerHook('paymentReturn') &&
                $this->registerHook('actionPaymentCCAdd') &&
                $this->registerHook('actionPaymentConfirmation') &&
                $this->registerHook('displayBackOfficeHeader') &&
                $this->registerHook('displayHeader') &&
                $this->registerHook('displayPayment') &&
                $this->registerHook('displayPaymentReturn') &&
                $this->registerHook('displayPaymentTop');
    }

    public function uninstall() {
        return parent::uninstall();
    }

    public function getContent() {
		$errors = '';
		$confirmation = '';
	
        if (((bool) Tools::isSubmit('submitDjtalcieloModule')) == true){
            if ((bool)Tools::getValue('DJTALCIELO_LIVE_MODE') !== false) {
				if(empty(Tools::getValue('DJTALCIELO_MERCHANTID'))){
					$errors .= $this->displayError($this->l('The Merchant ID have to be filled'));
				}
				if(empty(Tools::getValue('DJTALCIELO_MERCHANTKEY'))){
					$errors .= $this->displayError($this->l('The Merchant KEY have to be filled'));
				}
				if((int)Tools::getValue('DJTALCIELO_INSTALLMENT_MAX_NUMBER') > 1 && Tools::strlen(Tools::getValue('DJTALCIELO_INSTALLMENT_MIN_VALUE')) == 0){
					$errors .= $this->displayError($this->l('The minimum installment value cannot be empty'));
				}
				if((int)Tools::getValue('DJTALCIELO_INSTALLMENT_MAX_NUMBER') > 1 && (int)Tools::getValue('DJTALCIELO_INSTALLMENT_MIN_VALUE') < 0){
					$errors .= $this->displayError($this->l('The minimum installment value have to be a positive number'));
				}
				
				if(Tools::strlen(Tools::getValue('DJTALCIELO_BOLETO_DELAY')) == 0){
					$errors .= $this->displayError($this->l('The boleto delay value cannot be empty'));
				}
				if((int)Tools::getValue('DJTALCIELO_BOLETO_DELAY') <= 0){
					$errors .= $this->displayError($this->l('The boleto delay value have to be a positive number, and greater than 0'));
				}
			}
            
			$this->postProcess();
			
			if (empty($errors)) {
				$confirmation = $this->displayConfirmation($this->l('The configuration has been saved'));
			}
		}

        $this->context->smarty->assign(array(
            'url_boleto_notification' => $this->context->link->getModuleLink('djtalcielo','notificationBoleto'),
            )
        );
        $url_boleto_notification = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/url_boleto_notification.tpl');
        return $confirmation.$errors.$url_boleto_notification.$this->renderForm();
    }

    protected function renderForm() {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDjtalcieloModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $helper->tpl_vars['fields_value']['DJTALCIELO_PAYMENT_METHODS[]'] = explode(',', Configuration::get('DJTALCIELO_PAYMENT_METHODS'));


        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm() {

        $options = array();
        $options[] = array(
            'id' => 'credit_card',
            'name' => $this->l('Credit card')
        );
        $options[] = array(
            'id' => 'debit_card',
            'name' => $this->l('Debit card')
        );
        $options[] = array(
            'id' => 'ticket',
            'name' => $this->l('Ticket')
        );

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings Cielo'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'DJTALCIELO_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Merchant Id'),
                        'name' => 'DJTALCIELO_MERCHANTID',
                        'col' => 4,
                        'desc' => $this->l('Provided by cielo'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Merchant Key'),
                        'name' => 'DJTALCIELO_MERCHANTKEY',
                        'col' => 4,
                        'desc' => $this->l('Provided by cielo'),
                    ),
                    array(
                        'type' => 'select',
                        'lang' => false,
                        'label' => $this->l('Integration mode'),
                        'required' => false,
                        'desc' => $this->l('Choose integration mode for your store'),
                        'options' => array(
                            'id' => 'id_mode',
                            'name' => 'name',
                            'query' => array(
                                /*array(
                                    'id_mode' => 'redirect',
                                    'name' => $this->l('Chekout Redirect')
                                ),*/
                                array(
                                    'id_mode' => 'transparent',
                                    'name' => $this->l('Chekout Transparent')
                                ),
                            ),
                        ),
                        'name' => 'DJTALCIELO_TYPE_INTEGRATION',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Max parcel number'),
                        'name' => 'DJTALCIELO_INSTALLMENT_MAX_NUMBER',
                        'desc' => $this->l('Maximum installment number'),
                        'options' => array(
                            'id' => 'id_max',
                            'name' => 'name',
                            'query' => array(
                                array('id_max' => '1', 'name' => $this->l('Without installment')),
                                array('id_max' => '2', 'name' => '2 ' . $this->l('Times')),
                                array('id_max' => '3', 'name' => '3 ' . $this->l('Times')),
                                array('id_max' => '4', 'name' => '4 ' . $this->l('Times')),
                                array('id_max' => '5', 'name' => '5 ' . $this->l('Times')),
                                array('id_max' => '6', 'name' => '6 ' . $this->l('Times')),
                                array('id_max' => '7', 'name' => '7 ' . $this->l('Times')),
                                array('id_max' => '8', 'name' => '8 ' . $this->l('Times')),
                                array('id_max' => '9', 'name' => '9 ' . $this->l('Times')),
                                array('id_max' => '10', 'name' => '10 ' . $this->l('Times')),
                                array('id_max' => '11', 'name' => '11 ' . $this->l('Times')),
                                array('id_max' => '12', 'name' => '12 ' . $this->l('Times')),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'prefix' => 'R$',
                        'label' => $this->l('Installment Min Value'),
                        'name' => 'DJTALCIELO_INSTALLMENT_MIN_VALUE',
                        'desc' => $this->l('The smaller isntallment value'),
                        'col' => 4
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Payment methods '),
                        'name' => 'DJTALCIELO_PAYMENT_METHODS',
                        'desc' => $this->l('Choice payment methods for your store. Use CTRL + Click to select multiple methods'),
                        'multiple' => true,
                        'options' => array(
                            'id' => 'id',
                            'name' => 'name',
                            'query' => $options,
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Boleto Provider'),
                        'name' => 'DJTALCIELO_BOLETO_PROVIDER',
                        'desc' => $this->l('boleto provider'),
                        'options' => array(
                            'id' => 'id',
                            'name' => 'name',
                            'query' => array(
                                array('id' => 'Bradesco', 'name' => 'Bradesco'),
                                array('id' => 'Banco do Brasil', 'name' => 'Banco Do Brasil'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Boleto Delay'),
                        'name' => 'DJTALCIELO_BOLETO_DELAY',
                        'desc' => $this->l('boleto delay'),
                        'col' => 4
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Boleto Demonstrative'),
                        'name' => 'DJTALCIELO_BOLETO_DEMONSTRATIVE',
                        'desc' => $this->l('Optional,').' '.$this->l('Boleto Demonstrative (max caracter: 450)'),
                        'col' => 4
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Boleto Instruction'),
                        'name' => 'DJTALCIELO_BOLETO_INSTRUCTIONS',
                        'desc' => $this->l('Optional,').' '.$this->l('Boleto Instruction (max caracter: 450)'),
                        'col' => 4
                    ),
                    /*array(
                        'type' => 'switch',
                        'class' => 't',
                        'label' => $this->l('Sell with card token'),
                        'name' => 'DJTALCIELO_CARD_TOKEN',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),*/
                    array(
                        'type' => 'switch',
                        'class' => 't',
                        'label' => $this->l('Sandbox Mode'),
                        'name' => 'DJTALCIELO_SANDBOX',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            )
        );
    }

    protected function getConfigFormValues() {
        return array(
            'DJTALCIELO_LIVE_MODE' => Configuration::get('DJTALCIELO_LIVE_MODE'),
            'DJTALCIELO_MERCHANTID' => Configuration::get('DJTALCIELO_MERCHANTID'),
            'DJTALCIELO_MERCHANTKEY' => Configuration::get('DJTALCIELO_MERCHANTKEY'),
            'DJTALCIELO_TYPE_INTEGRATION' => Configuration::get('DJTALCIELO_TYPE_INTEGRATION'),
            //'DJTALCIELO_PAYMENT_METHODS[]' => Configuration::get('DJTALCIELO_PAYMENT_METHODS[]'),
            'DJTALCIELO_INSTALLMENT_MAX_NUMBER' => Configuration::get('DJTALCIELO_INSTALLMENT_MAX_NUMBER'),
            'DJTALCIELO_INSTALLMENT_MIN_VALUE' => Configuration::get('DJTALCIELO_INSTALLMENT_MIN_VALUE'),
            'DJTALCIELO_BOLETO_PROVIDER' => Configuration::get('DJTALCIELO_BOLETO_PROVIDER'),
            'DJTALCIELO_BOLETO_DELAY' => Configuration::get('DJTALCIELO_BOLETO_DELAY'),

            'DJTALCIELO_BOLETO_DEMONSTRATIVE' => Configuration::get('DJTALCIELO_BOLETO_DEMONSTRATIVE'),
            'DJTALCIELO_BOLETO_INSTRUCTIONS' => Configuration::get('DJTALCIELO_BOLETO_INSTRUCTIONS'),

            'DJTALCIELO_SANDBOX' => Configuration::get('DJTALCIELO_SANDBOX'),
            'DJTALCIELO_CARD_TOKEN' => Configuration::get('DJTALCIELO_CARD_TOKEN'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess() {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
		}

		$payment_methods = Tools::getValue('DJTALCIELO_PAYMENT_METHODS');
		if(!empty($payment_methods)) {
			Configuration::updateValue('DJTALCIELO_PAYMENT_METHODS', implode(',', $payment_methods));
		}
    }

public function hookPayment($params) {

        if ($this->active == false)
            return;
        if (Configuration::get('DJTALCIELO_LIVE_MODE') == false)
            return;

        $html = null;
        $currency_id = $params['cart']->id_currency;
        $currency = new Currency((int) $currency_id);

        if (in_array($currency->iso_code, $this->limited_currencies) == false)
            return false;

        $this->smarty->assign('module_dir', $this->_path);

        $payment_methods = explode(',',Configuration::get('DJTALCIELO_PAYMENT_METHODS'));

        if(in_array('credit_card',$payment_methods) || in_array('debit_card',$payment_methods)){
            $html .= $this->display(__FILE__, 'views/templates/hook/payment.tpl');
        }

        if(in_array('ticket',$payment_methods)){
            $html .= $this->display(__FILE__, 'views/templates/hook/payment_boleto.tpl');
        }

        return $html;
    }
    
    public function hookPaymentReturn($params)
    {
        if ($this->active == false)
            return;

        $order = $params['objOrder'];
        $confirmation = 'confirmation';

        if ($order->getCurrentOrderState()->id == Configuration::get('DJTALCIELO_STATE_APPROVED')) {
            $this->smarty->assign('status', 'ok');
		}

        $this->smarty->assign(array(
            'id_order' => $order->id,
            'reference' => $order->reference,
            'params' => $params,
            'total' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
        ));

        $url = 'https://sandbox.pagador.com.br/post/pagador/reenvia.asp/';
        if($order->payment == 'Cielo - Boleto'){
            $cieloAuthorization = cieloAuthorization::getCieloAuthByOrderId($order->id);
            $this->smarty->assign(array(
                'url' => $url,
                'cieloAuthorization' => $cieloAuthorization
            ));

            $confirmation = 'confirmation_boleto';
        }
            
        return $this->display(__FILE__, 'views/templates/hook/'. $confirmation .'.tpl');
    }
    
    public function hookDisplayPaymentReturn($params)
    {
        return $this->hookPaymentReturn($params);
    }

    public function hookHeader() {
        //$this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function getPath() {
        return $this->_path;
    }

}
