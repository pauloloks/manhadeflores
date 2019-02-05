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

class DjtalbrazilianregisterCepModuleFrontController extends ModuleFrontController
{
    
    public function __construct() {
        parent::__construct();
        $this->display_header = false;
        $this->display_header_javascript = false;
        $this->display_footer = false;
        if (Configuration::get('PS_SSL_ENABLED') && array_key_exists('HTTPS', $_SERVER))
            $this->ssl = true;
    }
     
    public function postProcess()
    {
        /**
         * If the module is not active anymore, no need to process anything.
         */
        if ($this->module->active == false)
            die('This module is not active');
        
        if (Tools::isSubmit('cep') == false)
            die('No CEP sent');
        
        $wsName = Configuration::get('DJTALBR_CEP_WEBSERVICE');
        if (Tools::isSubmit('ws-name') == true) {
            $wsName = Tools::getValue('ws-name');
        }
        if (empty($wsName))
            die('No WebService configured');
        
        $cep = Tools::getValue('cep');
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        $res = array();
        $res['state'] = '';
        $res['city'] = '';
        $res['neighborhood'] = '';
        $res['address'] = '';
        
        if($wsName == 'republicavirtual') {
            $res = @$this->checkFromRepublicavirtual($cep);
        } elseif($wsName == 'avisobrasil') {
            $res = @$this->checkFromAvisobrasil($cep);
        } elseif($wsName == 'postmon') {
            $res = @$this->checkFromPostmon($cep);
        } elseif($wsName == 'viacep') {
            $res = @$this->checkFromViacep($cep);
        }
        
        if($res['found'] == '1'){
            $res['state_id'] = $this->getIDfromUF($res['state']);
        }
        
        die(Tools::jsonEncode($res));
    }
    
    public function checkFromRepublicavirtual($cep)
    {
        $wsURL = 'http://cep.republicavirtual.com.br/web_cep.php?cep='.$cep.'&formato=json';
        $json =  Tools::jsonDecode(Tools::file_get_contents($wsURL));
        if(isset($json->uf) &&
        isset($json->cidade) &&
        isset($json->bairro) &&
        isset($json->tipo_logradouro) &&
        isset($json->logradouro)) {
            return array(
                'found' => '1',
                'state' => trim($json->uf),
                'city' => trim($json->cidade),
                'neighborhood' => trim($json->bairro),
                'address' => trim($json->tipo_logradouro.' '.$json->logradouro),
            );
        } else {
            return array(
                'found' => '0',
            );
        }
        
    }
    
    public function checkFromAvisobrasil($cep)
    {
        $wsURL = 'http://cep.correiocontrol.com.br/'.$cep.'.json';
        $json = Tools::jsonDecode(Tools::file_get_contents($wsURL));
        if(isset($json->uf) &&
        isset($json->localidade) &&
        isset($json->bairro) &&
        isset($json->logradouro)) {
            return array(
                'found' => '1',
                'state' => trim($json->uf),
                'city' => trim($json->localidade),
                'neighborhood' => trim($json->bairro),
                'address' => trim($json->logradouro),
            );
        } else {
            return array(
                'found' => '0',
            );
        }
    }
    
    public function checkFromPostmon($cep)
    {
        $wsURL = 'http://api.postmon.com.br/v1/cep/'.$cep;
        $json = Tools::jsonDecode(Tools::file_get_contents($wsURL));
        if(isset($json->estado) &&
        isset($json->cidade) &&
        isset($json->bairro) &&
        isset($json->logradouro)) {
            return array(
                'found' => '1',
                'state' => trim($json->estado),
                'city' => trim($json->cidade),
                'neighborhood' => trim($json->bairro),
                'address' => trim($json->logradouro),
            );
        } else {
            return array(
                'found' => '0',
            );
        }
    }
    
    public function checkFromViacep($cep)
    {
        $wsURL = 'http://viacep.com.br/ws/'.$cep.'/json/';
        $json = Tools::jsonDecode(Tools::file_get_contents($wsURL));
        if(isset($json->uf) &&
        isset($json->localidade) &&
        isset($json->bairro) &&
        isset($json->logradouro)) {
            return array(
                'found' => '1',
                'state' => trim($json->uf),
                'city' => trim($json->localidade),
                'neighborhood' => trim($json->bairro),
                'address' => trim($json->logradouro),
            );
        } else {
            return array(
                'found' => '0',
            );
        }
    }
    
    public function getIDfromUF($uf)
    {
        $id_country = Country::getByIso('BR');
        $id_state = State::getIdByIso($uf, $id_country);
        return $id_state;
    }
}
