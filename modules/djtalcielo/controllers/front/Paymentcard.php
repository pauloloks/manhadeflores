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

class DjtalcieloPaymentcardModuleFrontController extends ModuleFrontController {

    public function postProcess() {      

        $creditcard = false;
        $debitcard = false;

        $cart = Context::getContext()->cart;
        $total_order = $cart->getOrderTotal();

        $integration = Configuration::get('DJTALCIELO_TYPE_INTEGRATION');
        $pay_methods = explode(',',Configuration::get('DJTALCIELO_PAYMENT_METHODS'));
        $max_installments = Configuration::get('DJTALCIELO_INSTALLMENT_MAX_NUMBER');
        $installment_min_value = Configuration::get('DJTALCIELO_INSTALLMENT_MIN_VALUE');
        
        if(in_array('credit_card', $pay_methods)){
            $creditcard = true;
        }
        if(in_array('debit_card', $pay_methods)){
            $debitcard = true;
        }

        $this->context->smarty->assign(array(
            'cart_id' => $cart->id,
            'total_order' => $total_order,
            'integration' => $integration,
            'creditcard' => $creditcard,
            'debitcard' => $debitcard,
            'max_installments' => $max_installments,
            'installment_min_value' => $installment_min_value,
            'secure_key' => Context::getContext()->customer->secure_key,
        ));

       return $this->setTemplate('payment-card.tpl');
    }
        
    /**
        * Set default medias for this controller
        */
    public function setMedia(){
        parent::setMedia();
        //$integration = Configuration::get('djtalcielo_WS_IFRAME_REDIRECT');
        //if($integration == 'ws'){
            $this->context->controller->addJS(array($this->module->getPath().'/views/js/jquery.mask.mod.js',
                $this->module->getPath().'/views/js/payment-card.js',
            ));
        //}
    }

}
