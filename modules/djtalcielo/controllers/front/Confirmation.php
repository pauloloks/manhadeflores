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
use Cielo\API30\Ecommerce\DebitCard;

use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\CieloSerializable;
//use Cielo\API30\Ecommerce\Address;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Request\AbstractSaleRequest;
use Cielo\API30\Ecommerce\Request\UpdateSaleRequest;
use Cielo\API30\Ecommerce\Request\CieloError;
use Cielo\API30\Ecommerce\Request\CieloRequestException;


class DjtalcieloConfirmationModuleFrontController extends ModuleFrontController {

    public function __construct() {
        parent::__construct();
    }

    public function postProcess() {

        if ((Tools::isSubmit('cart_id') == false) || (Tools::isSubmit('secure_key') == false))
            return false;

        if ((Tools::isSubmit('card_number') == false) || (Tools::isSubmit('card_expiration_month') == false) || (Tools::isSubmit('card_expiration_year') == false) || (Tools::isSubmit('card_cvv') == false) || (Tools::isSubmit('customer_fisc') == false) || (Tools::isSubmit('card_holder_name') == false) || (Tools::isSubmit('type_payment') == false))
            return false;

        $cart_id = Tools::getValue('cart_id');
        $secure_key = Tools::getValue('secure_key');
        $url_return = $this->context->link->getModuleLink('djtalcielo','returnDebito');
        
        $cart = new Cart((int) $cart_id);
        $customer = new Customer((int) $cart->id_customer);
        $delivery_address = new Address($cart->id_address_delivery);
        $state = new State($delivery_address->id_state);
        $country = new Country($delivery_address->id_country);

        $type_payment = Tools::getValue('type_payment');

        $card_number = preg_replace('/[^0-9]/', '', Tools::getValue('card_number'));
        $card_expiration_month = preg_replace('/[^0-9]/', '', Tools::getValue('card_expiration_month'));
        $card_expiration_year = preg_replace('/[^0-9]/', '', Tools::getValue('card_expiration_year'));
        $card_cvv = preg_replace('/[^0-9]/', '', Tools::getValue('card_cvv'));
        $card_holder_name = Tools::getValue('card_holder_name');
        $customer_fisc = preg_replace('/[^0-9]/', '', Tools::getValue('customer_fisc'));
        $card_installment = (int) Tools::getValue('card_installment');

        $identity_type = 'CPF';
        if (count($customer_fisc) > 11)
            $identity_type = 'CNPJ';

        if ((int) $card_installment == 0)
            $card_installment = 1;

		$total_order_default = $cart->getOrderTotal();
        $total_order = preg_replace('/[^0-9]/', '', number_format($cart->getOrderTotal(),2,'.',','));

        $data_transaction = array();
        $data_transaction['MerchantOrderId'] = $cart->id;
        $data_transaction['Customer'] = array(
            'Name' => $customer->firstname,
            'Identity' => $customer_fisc,
            'IdentityType' => $identity_type,
            'Email' => $customer->email,
            // 'Birthdate' => '1991-01-02',
            'Address' => array(
                'Street' => $delivery_address->address1,
                //'Number' => '123',
                'Complement' => $delivery_address->address2,
                'ZipCode' => $delivery_address->postcode,
                'City' => $delivery_address->city,
                'State' => $state->iso_code,
                'Country' => $country->iso_code
            ),
            'DeliveryAddress' => array(
                'Street' => $delivery_address->address1,
                //'Number' => '123',
                'Complement' => $delivery_address->address2,
                'ZipCode' => $delivery_address->postcode,
                'City' => $delivery_address->city,
                'State' => $state->iso_code,
                'Country' => $country->iso_code
            )
        );
        $name_payment = $this->module->l('Card');
        if ($type_payment == 'credit_card') {

            $name_payment = $this->module->l('Cielo - Credit card');
            $data_transaction['Payment'] = array(
                'Type' => 'CreditCard',
                'Amount' => $total_order,
                // 'ServiceTaxAmount' => 0,
                'Installments' => $card_installment,
                //'Interest' => 'ByMerchant',
                'Capture' => true, //Identifies if authorization must be with automatic capture.
                'Authenticate' => false,
                //'SoftDescriptor' => 'tst',
                'CreditCard' => array(
                    'CardNumber' => $card_number,
                    'Holder' => $card_holder_name,
                    'ExpirationDate' => $card_expiration_month . '/' . $card_expiration_year,
                    'SecurityCode' => $card_cvv,
                    'SaveCard' => 'false', //save for generate Card Token
                    'Brand' => $this->cardType($card_number)
                )
            );
        } else if ($type_payment == 'debit_card') {

            $name_payment =$this->module->l('Cielo - Debit card');
            $data_transaction['Payment'] = array(
                'Type' => 'DebitCard',
                'Amount' => $total_order,
                'ReturnUrl' => $url_return,
                // 'ServiceTaxAmount' => 0,
                'Installments' => $card_installment,
                //'Interest' => 'ByMerchant',
                'Capture' => true, //Identifies if authorization must be with automatic capture.
                'Authenticate' => true,
                //'SoftDescriptor' => 'tst',
                'DebitCard' => array(
                    'CardNumber' => $card_number,
                    'Holder' => $card_holder_name,
                    'ExpirationDate' => $card_expiration_month . '/' . $card_expiration_year,
                    'SecurityCode' => $card_cvv,
                    'SaveCard' => 'false', //save for generate Card Token
                    'Brand' => $this->cardType($card_number)
                )
            );
        }

        $transaction = $this->createTransaction($data_transaction, $type_payment);

        if (!$transaction) {
            return $this->setTemplate('error.tpl');
        }
        $status = $transaction->getPayment()->getStatus();

        $id_order_waiting = Configuration::get('DJTALCIELO_STATE_WAITING');
        $id_order_approved = Configuration::get('DJTALCIELO_STATE_APPROVED');
        $id_order_refused = Configuration::get('DJTALCIELO_STATE_REFUSED');
        $id_order_refunded = Configuration::get('DJTALCIELO_STATE_REFUNDED');
        $id_order_cancel = Configuration::get('DJTALCIELO_STATE_CANCEL');

        $payment_status = $id_order_refused;

        if ($status == 0) 
            $payment_status = $id_order_cancel;
        else if ($status == 2) 
            $payment_status = $id_order_approved;
        else if ($status == 3) 
            $payment_status = $id_order_refused;
        else if ($status == 10)
            $payment_status = $id_order_cancel;
        else if ($status == 11)
            $payment_status = $id_order_refunded;
        else if ($status == 12)
            $payment_status = $id_order_waiting;
        else if ($status == 13)
            $payment_status = $id_order_cancel;

        if($type_payment == 'debit_card')
            $payment_status = $id_order_waiting;

        $currency_id = (int) Context::getContext()->currency->id;
        if ($this->module->validateOrder($cart_id, $payment_status, $total_order_default, $name_payment, null, array(), $currency_id, false, $secure_key)) {
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

            if($type_payment == 'debit_card')
                Tools::redirect($transaction->getPayment()->getAuthenticationUrl());
            
            Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart_id . '&id_module=' . $module_id . '&id_order=' . $order_id . '&key=' . $secure_key);
        }
    }

    protected function createTransaction($data_transaction, $type_payment) {

        $MerchantId = Configuration::get('DJTALCIELO_MERCHANTID');
        $MerchantKey = Configuration::get('DJTALCIELO_MERCHANTKEY');

        if(Configuration::get('DJTALCIELO_SANDBOX'))
            $environment = $environment = Environment::sandbox();
        else
            $environment = $environment = Environment::production();
        

        $merchant = new Merchant($MerchantId, $MerchantKey);

        $sale = new Sale($data_transaction['MerchantOrderId']);

        $sale->customer($data_transaction['Customer']['Name'])
                ->setEmail($data_transaction['Customer']['Email'])
                ->setIdentity($data_transaction['Customer']['Identity'])
                ->setIdentityType($data_transaction['Customer']['IdentityType'])
                ->setAddress($data_transaction['Customer']['Address'])
                ->setDeliveryAddress($data_transaction['Customer']['DeliveryAddress']);


        if($type_payment == 'credit_card'){
            //creditCard
            $payment = $sale->payment($data_transaction['Payment']['Amount'], $data_transaction['Payment']['Installments'])->setCapture(true);
            $payment->creditCard($data_transaction['Payment']['CreditCard']['SecurityCode'], $data_transaction['Payment']['CreditCard']['Brand'])
                    ->setExpirationDate($data_transaction['Payment']['CreditCard']['ExpirationDate'])
                    ->setCardNumber($data_transaction['Payment']['CreditCard']['CardNumber'])
                    ->setHolder($data_transaction['Payment']['CreditCard']['Holder']);
            
        }else{
            //debitCard
            $payment = $sale->payment($data_transaction['Payment']['Amount'], $data_transaction['Payment']['Installments'])->setCapture(true)->setReturnUrl($data_transaction['Payment']['ReturnUrl']);
            $payment->debitCard($data_transaction['Payment']['DebitCard']['SecurityCode'], $data_transaction['Payment']['DebitCard']['Brand'])
            ->setExpirationDate($data_transaction['Payment']['DebitCard']['ExpirationDate'])
            ->setCardNumber($data_transaction['Payment']['DebitCard']['CardNumber'])
            ->setHolder($data_transaction['Payment']['DebitCard']['Holder']);
        }
        try {
            
            $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);
          
            return $sale;
        } catch (CieloRequestException $e) {
            $error = $e->getCieloError();
            $this->errors[] = $this->module->l($error->getMessage() . ' - Code: ' . $error->getCode());

            return false;
        }
    }

	public function cardType($number) {
        $number = preg_replace('/[^\d]/', '', $number);
        if (preg_match('/^3[47][0-9]{13}$/', $number)) {
            return 'Amex';
        } elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/', $number)) {
            return 'Diners';
        } elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/', $number)) {
            return 'Discover';
        } elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/', $number)) {
            return 'JCB';
        } elseif (preg_match('/^5[1-5][0-9]{14}$/', $number)) {
            return 'Master';
        } elseif (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $number)) {
            return 'Visa';
        } elseif (preg_match('/^(5078\d{2})(\d{2})(\d{11})$/', $number)) {
            return 'Aura';
        }else {
            return 'Unknown';
        }
    }

}
