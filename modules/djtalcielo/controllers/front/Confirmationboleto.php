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

include dirname(__FILE__) . '/../../Cielo/autoload.php';

use Cielo\API30\Merchant;
use Cielo\API30\Ecommerce\Payment;
use Cielo\API30\Ecommerce\Sale;
//use Cielo\API30\Ecommerce\Customer as CieloCustumer;
use Cielo\API30\Ecommerce\CreditCard;
use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\CieloSerializable;
//use Cielo\API30\Ecommerce\Address;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Request\AbstractSaleRequest;
use Cielo\API30\Ecommerce\Request\UpdateSaleRequest;
use Cielo\API30\Ecommerce\Request\CieloError;
use Cielo\API30\Ecommerce\Request\CieloRequestException;

class DjtalcieloConfirmationboletoModuleFrontController extends ModuleFrontController {

    public function __construct() {
        parent::__construct();
    }

    public function postProcess() {

        if ((Tools::isSubmit('cart_id') == false) || (Tools::isSubmit('secure_key') == false))
            return false;


        $cart_id = Tools::getValue('cart_id');
        $secure_key = Tools::getValue('secure_key');

        $cart = new Cart((int) $cart_id);
        $customer = new Customer((int) $cart->id_customer);
        $delivery_address = new Address($cart->id_address_delivery);
        $state = new State($delivery_address->id_state);
        $country = new Country($delivery_address->id_country);
        $customer_fisc = preg_replace('/[^0-9]/', '', Tools::getValue('customer_fisc'));

        $identity_type = 'CPF';
        if (count($customer_fisc) > 11)
            $identity_type = 'CNPJ';

		$total_order_default = $cart->getOrderTotal();
        $total_order = preg_replace('/[^0-9]/', '', number_format($cart->getOrderTotal(),2,'.',','));

        $data_transaction = array();
        $data_transaction['MerchantOrderId'] = $cart->id;
        $data_transaction['Customer'] = array(
            'Name' => $customer->firstname,
            'Identity' => $customer_fisc,
            'IdentityType' => $identity_type,
            'Address' => array(
                'Street' => $delivery_address->address1,
                'Number' => '123',
                'District' => $delivery_address->address2,
                'ZipCode' => $delivery_address->postcode,
                'City' => $delivery_address->city,
                'State' => $state->iso_code,
                'Country' => $country->iso_code
            ),
        );

        $data_transaction['Payment'] = array(
            'Type' => 'Boleto',
            'Amount' => (int) $total_order,
            'Provider' => Configuration::get('DJTALCIELO_BOLETO_PROVIDER'),
            //'Address' => 'Rua Teste',
            //'BoletoNumber' => '123',
            'Assignor' => Configuration::get('PS_SHOP_NAME'),
            'Demonstrative' => Configuration::get('DJTALCIELO_BOLETO_DEMONSTRATIVE'),
            //'ExpirationDate' => '2015-01-05',
            // 'Identification' => '11884926754',
            'Instructions' => Configuration::get('DJTALCIELO_BOLETO_INSTRUCTIONS')//'Aceitar somente até a data de vencimento, após essa data juros de 1% dia.'
        );

        $transaction = $this->createTransactionBoleto($data_transaction);
        if (!$transaction) {

            return $this->setTemplate('error.tpl');
        }

        $id_order_waiting = Configuration::get('DJTALCIELO_STATE_WAITING');
        $payment_status = $id_order_waiting;
        $currency_id = (int) Context::getContext()->currency->id;

        if ($this->module->validateOrder($cart_id, $payment_status, $total_order_default, 'Cielo - Boleto', null, array(), $currency_id, false, $secure_key)) {
            $order_id = Order::getOrderByCartId((int) $cart_id);
            $module_id = $this->module->id;

            $cieloAuthorization = new cieloAuthorization();
            $cieloAuthorization->paymentId = $transaction->getPayment()->getPaymentId();
            $cieloAuthorization->type = $transaction->getPayment()->getType();
            $cieloAuthorization->id_order = $order_id;
            $cieloAuthorization->id_cart = $cart_id;
            $cieloAuthorization->authorizationCode = $transaction->getPayment()->getAuthorizationCode();
            $cieloAuthorization->tid = $transaction->getPayment()->getTid();
            $cieloAuthorization->proofOfSale = $transaction->getPayment()->getProofOfSale();
            $cieloAuthorization->returnMessage = $transaction->getPayment()->getReturnMessage();
            $cieloAuthorization->returnCode = $transaction->getPayment()->getReturnCode();
            $cieloAuthorization->num_installment = $transaction->getPayment()->getInstallments();
            $cieloAuthorization->customer_fisc = $customer_fisc;
            $cieloAuthorization->provider = $transaction->getPayment()->getProvider();
            $cieloAuthorization->amount = $transaction->getPayment()->getAmount();
            $cieloAuthorization->capturedAmount = $transaction->getPayment()->getCapturedAmount();
            $cieloAuthorization->capturedDate = $transaction->getPayment()->getCapturedDate();
            $cieloAuthorization->receivedDate = $transaction->getPayment()->getReceivedDate();
            $cieloAuthorization->status = $transaction->getPayment()->getStatus();
            $cieloAuthorization->secret_key = $secure_key;

            $cieloAuthorization->save();

            Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart_id . '&id_module=' . $module_id . '&id_order=' . $order_id . '&key=' . $secure_key);
        }
    }

    protected function createTransactionBoleto($data_transaction) {

        $MerchantId = Configuration::get('DJTALCIELO_MERCHANTID');
        $MerchantKey = Configuration::get('DJTALCIELO_MERCHANTKEY');

        if(Configuration::get('DJTALCIELO_SANDBOX')){
            $environment = $environment = Environment::sandbox();
        }else{
            $environment = $environment = Environment::production();
        }

        $merchant = new Merchant($MerchantId, $MerchantKey);

        $sale = new Sale($data_transaction['MerchantOrderId']);

        $sale->customer($data_transaction['Customer']['Name'])
                ->setAddress($data_transaction['Customer']['Address'])
                ->setIdentity($data_transaction['Customer']['Identity'])
                ->setIdentityType($data_transaction['Customer']['IdentityType']);

        $payment = $sale->payment($data_transaction['Payment']['Amount']);

        $payment->boleto($data_transaction['Payment']['Provider'])
                ->setExpirationDate($data_transaction['Payment']['ExpirationDate'])
                ->setBoletoNumber($data_transaction['Payment']['BoletoNumber'])
                ->setAssignor($data_transaction['Payment']['Assignor'])
                ->setDemonstrative($data_transaction['Payment']['Demonstrative'])
                ->setIdentification($data_transaction['Payment']['Identification'])
                ->setInstructions($data_transaction['Payment']['Instructions']);

        try {
           
            $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

            return $sale;
        } catch (CieloRequestException $e) {
            
            $error = $e->getCieloError();
            $this->errors[] = $this->module->l($error->getMessage() . ' - Code: ' . $error->getCode());
            return false;
        }
    }

}
