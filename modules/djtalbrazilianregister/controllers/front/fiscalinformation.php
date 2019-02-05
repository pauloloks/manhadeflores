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

class DjtalbrazilianregisterFiscalInformationModuleFrontController extends ModuleFrontController
{
	
	public $popup_mode = false;
	public $customer_permissions = 'none';
    
    public function __construct() {
        parent::__construct();
		
		if(Tools::isSubmit('popup_mode')){
			$this->popup_mode = true;
			$this->display_header = false;
			$this->display_header_javascript = false;
			$this->display_footer = false;
		}
		
		$this->customer_permissions = Configuration::get('DJTALBR_CUTOMER_PERMISSIONS');
		
        if (Configuration::get('PS_SSL_ENABLED') && array_key_exists('HTTPS', $_SERVER)){
            $this->ssl = true;
		}
    }
	
	public function setMedia()
    {
		parent::setMedia();
		$this->addCSS($this->module->getPath().'/views/css/front-my-account.css');
		$this->addJS($this->module->getPath().'/views/js/jquery.mask.mod.js');
		$this->addJS($this->module->getPath().'/views/js/cpf-cnpj-my-account.js');
	}
     
    public function postProcess()
    {
        /**
         * If the module is not active anymore, no need to process anything.
         */
        if ($this->module->active == false) {
            die('This module is not active');
		}
		
		if(Tools::isSubmit('submitFiscalInformation')) {
			$this->saveFiscalInformation();
		}
		
		$breg = BrazilianRegister::getByCustomerId($this->context->customer->id);
        $br_document_cpf = $breg['cpf'];
        $br_document_cnpj = $breg['cnpj'];
        $br_document_passport = $breg['passport'];
        $br_document_rg = $breg['rg'];
        $br_document_ie = $breg['ie'];
        $br_document_sr = $breg['sr'];
		$br_document_comp = $breg['comp'];
		
		$cpf_cnpj_mode = Configuration::get('DJTALBR_CPF_CNPJ_MODE');
		
		$br_show_comp = false;
		if((bool)Configuration::get('DJTALBR_show_COMP') == true){
			$br_show_comp = true;
		}
		
		$br_show_passport = false;
		if($cpf_cnpj_mode == 'cpf-or-cnpj-or-passport'){
			$br_show_passport = true;
		}
		
		$br_show_ie = false;
		if(((bool)Configuration::get('DJTALBR_CPF_CNPJ_IE') == true) && ($cpf_cnpj_mode == 'cpf-or-cnpj-or-passport' || $cpf_cnpj_mode == 'cpf-and-cnpj' || $cpf_cnpj_mode == 'cpf-or-cnpj' || $cpf_cnpj_mode == 'cnpj-only')){
			$br_show_ie = true;
		}
		
		$br_show_sr = false;
		if(((bool)Configuration::get('DJTALBR_CNPJ_SR') == true) && ($cpf_cnpj_mode == 'cpf-or-cnpj-or-passport' || $cpf_cnpj_mode == 'cpf-and-cnpj' || $cpf_cnpj_mode == 'cpf-or-cnpj' || $cpf_cnpj_mode == 'cnpj-only')){
			$br_show_sr = true;
		}
		
		$br_show_rg = false;
		if(((bool)Configuration::get('DJTALBR_CPF_CNPJ_RG') == true) && ($cpf_cnpj_mode == 'cpf-or-cnpj-or-passport' || $cpf_cnpj_mode == 'cpf-and-cnpj' || $cpf_cnpj_mode == 'cpf-or-cnpj' || $cpf_cnpj_mode == 'cpf-only')){
			$br_show_rg = true;
		}
		
		$br_show_cpf = false;
		if($cpf_cnpj_mode == 'cpf-or-cnpj-or-passport' || $cpf_cnpj_mode == 'cpf-and-cnpj' || $cpf_cnpj_mode == 'cpf-or-cnpj' || $cpf_cnpj_mode == 'cpf-only'){
			$br_show_cpf = true;
		}
		
		$br_show_cnpj = false;
		if($cpf_cnpj_mode == 'cpf-or-cnpj-or-passport' || $cpf_cnpj_mode == 'cpf-and-cnpj' || $cpf_cnpj_mode == 'cpf-or-cnpj' || $cpf_cnpj_mode == 'cnpj-only'){
			$br_show_cnpj = true;
		}
        
		
		$this->context->smarty->assign(array(	
			'popup_mode' => $this->popup_mode,
			'customer_permissions' => $this->customer_permissions,
			'br_document_cpf' => $br_document_cpf,
			'br_document_cnpj' => $br_document_cnpj,
			'br_document_passport' => $br_document_passport,
			'br_document_rg' => $br_document_rg,
			'br_document_ie' => $br_document_ie,
			'br_document_sr' => $br_document_sr,
			'br_document_comp' => $br_document_comp,
			'br_show_comp' => $br_show_comp,
			'br_show_passport' => $br_show_passport,
			'br_show_ie' => $br_show_ie,
			'br_show_sr' => $br_show_sr,
			'br_show_rg' => $br_show_rg,
			'br_show_cpf' => $br_show_cpf,
			'br_show_cnpj' => $br_show_cnpj
		));
												
        return $this->setTemplate('fiscal-information.tpl');
    }
	
	public function saveFiscalInformation()
    {
		if($this->customer_permissions == 'none'){
			$this->errors[] = $this->l('You cannot edit or add any information');
			return false;
		}
		
		$register_form = null;
		$djtalbrazilianregister = BrazilianRegister::getByCustomerId((int)$this->context->customer->id);
		if($djtalbrazilianregister != null){
			$register_form = new BrazilianRegister($djtalbrazilianregister['id_djtalbrazilianregister']);
		}else{
			$register_form = new BrazilianRegister();
			$register_form->id_customer = (int)$this->context->customer->id;
		}
		

		$cpf = Tools::getValue('br_document_cpf');
		$cnpj = Tools::getValue('br_document_cnpj');
		$passport = Tools::getValue('br_document_passport');
		$rg = Tools::getValue('br_document_rg');
		$ie = Tools::getValue('br_document_ie');
		$sr = Tools::getValue('br_document_sr');
		$comp = Tools::getValue('br_document_comp');
		
		if(!empty($cpf) && (($this->customer_permissions == 'add' && empty($register_form->cpf)) || $this->customer_permissions == 'edit')){
			if(Djtalbrazilianregister::validateCPF($cpf) !== false){
				$cpf = preg_replace('/[^0-9]/', '', $cpf);
				$register_form->cpf = pSQL($cpf);
			}else{
				$this->errors[] = '<b>'.$this->l('CPF').'</b> '.$this->l('is invalid');
			}
		}
		if(!empty($cnpj) && (($this->customer_permissions == 'add' && empty($register_form->cnpj)) || $this->customer_permissions == 'edit')){
			if(Djtalbrazilianregister::validateCNPJ($cnpj) !== false){
				$cnpj = preg_replace('/[^0-9]/', '', $cnpj);
				$register_form->cnpj = pSQL($cnpj);
			}else{
				$this->errors[] = '<b>'.$this->l('CNPJ').'</b> '.$this->l('is invalid');
			}
		}
		
		if(!empty($passport) && (($this->customer_permissions == 'add' && empty($register_form->passport)) || $this->customer_permissions == 'edit')){
			$register_form->passport = pSQL($passport);
		}
		
		if(!empty($rg) && (($this->customer_permissions == 'add' && empty($register_form->rg)) || $this->customer_permissions == 'edit')){
			$register_form->rg = pSQL($rg);
		}
		
		if(!empty($ie) && (($this->customer_permissions == 'add' && empty($register_form->ie)) || $this->customer_permissions == 'edit')){
			$register_form->ie = pSQL($ie);
		}
		
		if(!empty($comp) && (($this->customer_permissions == 'add' && empty($register_form->comp)) || $this->customer_permissions == 'edit')){
			$register_form->comp = pSQL($comp);
		}
		
		if(count($this->errors) > 0){
			return false;
		}
		
		if($register_form->save()){
			$this->context->smarty->assign(	'confirmation', true);
		}
	}
}










