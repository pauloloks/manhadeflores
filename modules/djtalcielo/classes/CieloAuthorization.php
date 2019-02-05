<?php
/**
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
*  @author    DJTAL
*  @copyright 2015 DJTAL
*  @version   1.0.0
*  @link      http://www.djtal.com.br/
*  @license
*/

class cieloAuthorization extends ObjectModel
{
    /** @var integer editorial id*/
    public $id;
    public $id_order;
    public $id_cart;
    public $paymentId;
    public $authorizationCode;
    public $type;
    public $provider;
    public $proofOfSale;
    public $tid;
    public $amount;
    public $capturedAmount;
    public $receivedDate;
    public $capturedDate;
    public $returnCode;
    public $returnMessage;
    public $status;
    public $customer_fisc;
    public $num_installment;
    public $url;
    public $secret_key;
    
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'cielo_authorization',
        'primary' => 'id_cielo_authorization',
        'multilang' => false,
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'required' => false),
            'id_cart' => array('type' => self::TYPE_INT, 'required' => false),
            'paymentId' => array('type' => self::TYPE_STRING, 'required' => false),
            'authorizationCode' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 160),
            'type' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 16),
            'provider' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 60),
            'proofOfSale' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 20),
            'tid' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 40),
            'amount' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 15),
            'capturedAmount' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 15),
            'receivedDate' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 30),
            'capturedDate' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 30),
            'returnCode' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 32),
            'returnMessage' => array('type' => self::TYPE_STRING, 'required' => false),
            'status' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 2),
            'customer_fisc' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 14),
            'num_installment' => array('type' => self::TYPE_INT, 'required' => false),
            'secret_key' => array('type' => self::TYPE_STRING, 'required' => false),
        )
    );
    
    public static function getCieloAuthByPaymentId ($paymentId) {
        $res = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_ .'cielo_authorization` WHERE paymentId='.(int)$paymentId);
        return count($res)>0?$res[0]:null;
    }

    public static function getCieloAuthByOrderId ($id_order) {
        $res = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_ .'cielo_authorization` WHERE id_order='.(int)$id_order);
        return count($res)>0?$res[0]:null;
    }
    
    public static function getCieloAuthByCartId ($id_cart) {
        $res = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_ .'cielo_authorization` WHERE id_cart='.(int)$id_cart);
        return count($res)>0?$res[0]:null;
    }
    
    public static function generateCieloAuthSecret ($id_cart = null, $id_order = null) {
        $res = null;
        $where = '';
        
        if($id_cart != null){
            $res = self::getCieloAuthByCartId((int)$id_cart);
            $where = 'id_cart = '.(int)$id_cart;
        } else if($id_order != null){
            $res = self::getCieloAuthByOrderId((int)$id_order);
            $where = 'id_order = '.(int)$id_order;
        }
        if($res != null && $res['secret_key'] == null){
            $key = $res['numpedido'].$res['date_auth'].$res['numautor'].$res['numautent'].$res['origembin'].time();
            $secretKey = md5($key);
            return Db::getInstance()->update('cielo_authorization', array('secret_key' => pSQL($secretKey)), $where);
        }
        return false;
    }
    
    public static function compareCieloAuthSecret ($secretKey, $id_cart = null, $id_order = null) {
        $res = null;
        if($id_cart != null){
            $res = self::getCieloAuthByCartId((int)$id_cart);
        } else if($id_order != null){
            $res = self::getCieloAuthByOrderId((int)$id_order);
        }
        if($res != null && $res['secret_key'] != null){
            if($res['secret_key'] == $secretKey){
                return true;
            }
        }
        return false;
    }
}

