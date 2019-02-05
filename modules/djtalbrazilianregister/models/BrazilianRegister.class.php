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

class BrazilianRegister extends ObjectModel
{
    public $id;
    public $id_customer;
    public $cpf;
    public $cnpj;
    public $rg;
    public $ie;
    public $sr;
    public $passport;
    public $comp;
    
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'djtalbrazilianregister',
        'primary' => 'id_djtalbrazilianregister',
        'multilang' => false,
        'fields' => array(
            'id_customer' =>            array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'cpf' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false, 'size' => 11),
            'cnpj' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false, 'size' => 14),
            'passport' =>               array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false, 'size' => 255),
            'rg' =>                     array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false, 'size' => 255),
            'ie' =>                     array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false, 'size' => 255),
            'sr' =>                     array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false, 'size' => 255),
            'comp' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => false, 'size' => 255),
        )
    );
    
    public static function getByCustomerId($id_customer) {
        $res = Db::getInstance()->executeS('SELECT * FROM`'._DB_PREFIX_ .'djtalbrazilianregister` WHERE id_customer='.(int)$id_customer);
        if(count($res) > 0){
            return $res[0];
        }
        return null;
    }
	
	public static function getFormatedCPFByCustomerId($id_customer) {
        $breg = self::getByCustomerId($id_customer);
		if($breg != null && !empty($breg['cpf'])){
			return Djtalbrazilianregister::mascaraString('###.###.###-##', $breg['cpf']);
		}
        return null;
    }
	
	public static function getFormatedCNPJByCustomerId($id_customer) {
        $breg = self::getByCustomerId($id_customer);
		if($breg != null && !empty($breg['cnpj'])){
			return Djtalbrazilianregister::mascaraString('##.###.###/####-##', $breg['cnpj']);
		}
        return null;
    }
    
    public static function getFormatedPassportByCustomerId($id_customer) {
        $breg = self::getByCustomerId($id_customer);
		if($breg != null && !empty($breg['passport'])){
			return $breg['passport'];
		}
        return null;
    }
    
    public static function insertByCustomerId($id_customer, $data) {
        $data['id_customer'] = (int)$id_customer;
        Db::getInstance()->insert('djtalbrazilianregister', $data);
    }
	
	public static function updateByCustomerId($id_customer, $data) {
        $where = 'id_customer='.(int)$id_customer;
        Db::getInstance()->update('djtalbrazilianregister', $data, $where);
    }
    
    public static function importByRawData($rawData, $doImport = false) {
        $returnData = array();
        foreach($rawData as $customerData){
            if(isset($customerData['id_customer']) && !empty($customerData['id_customer'])){
                $okData = array();
                $okData['id_customer'] = (int)$customerData['id_customer'];
                $has_cpf = false;
                $has_cnpj = false;
                if(isset($customerData['cpf']) && !empty($customerData['cpf'])){
                    $okData['cpf'] = $customerData['cpf'];
                    $okData['cpf'] = pSQL(preg_replace('/\D/', '', $okData['cpf']));
                    $has_cpf = true;
                }
                if(isset($customerData['cnpj']) && !empty($customerData['cnpj'])){
                    $okData['cnpj'] = $customerData['cnpj'];
                    $okData['cnpj'] = pSQL(preg_replace('/\D/', '', $okData['cnpj']));
                    $has_cnpj = true;
                }
                if(isset($customerData['cpf_or_cnpj']) && !empty($customerData['cpf_or_cnpj'])){
                    $customerData['cpf_or_cnpj'] = preg_replace('/\D/', '', $customerData['cpf_or_cnpj']);
                    $l = Tools::strlen($customerData['cpf_or_cnpj']);
                    if($l == 11){
                        $okData['cpf'] = pSQL($customerData['cpf_or_cnpj']);
                        $has_cpf = true;
                    } elseif ($l == 14){
                        $okData['cnpj'] = pSQL($customerData['cpf_or_cnpj']);
                        $has_cnpj = true;
                    }
                }
                if(isset($customerData['rg_or_ie']) && !empty($customerData['rg_or_ie'])){
                    if($has_cpf){
                        $okData['rg'] = pSQL($customerData['rg_or_ie']);
                    } else if($has_cnpj){
                        $okData['ie'] = pSQL($customerData['rg_or_ie']);
                    }
                }
                if($doImport) {
                    if(self::getByCustomerId($okData['id_customer']) == null){
                        self::insertByCustomerId($okData['id_customer'], $okData);
                    }
                } else {
                    $returnData[] = $okData;
                }
            }
        }
        return $returnData;
    }
	
	public static function deleteByCustomerId($id_customer) {
        $res = Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'djtalbrazilianregister WHERE id_customer = '.(int)$id_customer);
    }
    
    public static function passportExist($passport){
        $passport = pSql(trim($passport));
        $res = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_ .'djtalbrazilianregister WHERE passport='.$passport);
        if(count($res) > 0){
            return true;
        }
        return false;
    }
	
	public static function cpfExist($cpf){
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $res = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_ .'djtalbrazilianregister WHERE cpf='.$cpf);
        if(count($res) > 0){
            return true;
        }
        return false;
    }
    
    public static function cnpjExist($cnpj){
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        $res = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_ .'djtalbrazilianregister WHERE cnpj='.$cnpj);
        if(count($res) > 0){
            return true;
        }
        return false;
    }
}
