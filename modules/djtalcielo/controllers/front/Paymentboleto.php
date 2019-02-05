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

class DjtalcieloPaymentboletoModuleFrontController extends ModuleFrontController {

    public function postProcess() {      

        $cart = Context::getContext()->cart;
        $total_order = $cart->getOrderTotal();

        $this->context->smarty->assign(array(
            'cart_id' => $cart->id,
            'total_order' => $total_order,
            'bank_name' => Configuration::get('DJTALCIELO_BOLETO_PROVIDER'),
            'secure_key' => Context::getContext()->customer->secure_key,
            'boleto_delay' => Configuration::get('DJTALCIELO_BOLETO_DELAY')
        ));

       return $this->setTemplate('payment-boleto.tpl');
    }
        
    /**
        * Set default medias for this controller
        */
    public function setMedia(){
        parent::setMedia();
        //$integration = Configuration::get('djtalcielo_WS_IFRAME_REDIRECT');
        //if($integration == 'ws'){
            $this->context->controller->addJS(array($this->module->getPath().'/views/js/jquery.mask.mod.js',
                $this->module->getPath().'/views/js/payment-boleto.js',
            ));
        //}
    }

}
