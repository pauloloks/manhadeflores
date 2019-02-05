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


class DjtalcieloReturnDebitoModuleFrontController extends ModuleFrontController {

    public function __construct() {
        parent::__construct();
        $this->display_header = false;
        $this->display_header_javascript = false;
        $this->display_footer = false;
    }

    public function postProcess() {

        $paymentId = Tools::getValue('PaymentId');

        $auth = cieloAuthorization::getCieloAuthByPaymentId($paymentId);
        
        $order = new Order($auth['id_order']);
        $orderHistory = new OrderHistory();
        $orderHistory->id_order = (int)$order->id;
        $orderHistory->changeIdOrderState($order->current_state, $order->id);


        //status cielo
        $id_order_approved = Configuration::get('DJTALCIELO_STATE_APPROVED');
        $id_order_refused = Configuration::get('DJTALCIELO_STATE_REFUSED');
       
        if(!$paymentId)
            return false;

        $MerchantId = Configuration::get('DJTALCIELO_MERCHANTID');
        $MerchantKey = Configuration::get('DJTALCIELO_MERCHANTKEY');

        $merchant = new Merchant($MerchantId, $MerchantKey);

        if(Configuration::get('DJTALCIELO_SANDBOX'))
            $environment = $environment = Environment::sandbox();
        else
            $environment = $environment = Environment::production();
        
        $transaction = (new CieloEcommerce($merchant, $environment))->getSale($paymentId);  
        $status = $transaction->getPayment()->getStatus();


        if ($status == 1) {
            $transaction = (new CieloEcommerce($merchant, $environment))->captureSale($paymentId, $transaction->getPayment()->getAmount(), 0);
            $orderHistory->changeIdOrderState($id_order_approved, $order->id);
            $order->current_state = $id_order_approved;
        } else if ($status != 2) {
            $orderHistory->changeIdOrderState($id_order_refused, $order->id);
            $order->current_state = $id_order_refused;
        }

        $transaction = (new CieloEcommerce($merchant, $environment))->getSale($paymentId);

        $order->save();
        $orderHistory->add();
        
        $this->updateAuthorization($auth['id_cielo_authorization'], $transaction);

        Tools::redirect('index.php?controller=history');
    }

    protected function updateAuthorization($id_cielo_authorization, $transaction){

        $cieloAuthorization = new cieloAuthorization($id_cielo_authorization);
        $cieloAuthorization->paymentId = $transaction->getPayment()->getPaymentId();
        $cieloAuthorization->type = $transaction->getPayment()->getType();
        $cieloAuthorization->authorizationCode = $transaction->getPayment()->getAuthorizationCode();
        $cieloAuthorization->tid = $transaction->getPayment()->getTid();
        $cieloAuthorization->proofOfSale = $transaction->getPayment()->getProofOfSale();
        $cieloAuthorization->returnMessage = $transaction->getPayment()->getReturnMessage();
        $cieloAuthorization->returnCode = $transaction->getPayment()->getReturnCode();
        $cieloAuthorization->num_installment = $transaction->getPayment()->getInstallments();
        $cieloAuthorization->provider = $transaction->getPayment()->getProvider();
        $cieloAuthorization->amount = $transaction->getPayment()->getAmount();
        $cieloAuthorization->capturedAmount = $transaction->getPayment()->getCapturedAmount();
        $cieloAuthorization->capturedDate = $transaction->getPayment()->getCapturedDate();
        $cieloAuthorization->receivedDate = $transaction->getPayment()->getReceivedDate();
        $cieloAuthorization->status = $transaction->getPayment()->getStatus();
        $cieloAuthorization->save();

    }
}
