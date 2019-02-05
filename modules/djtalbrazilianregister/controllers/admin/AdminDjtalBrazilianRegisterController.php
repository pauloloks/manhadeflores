<?php

include_once dirname(__FILE__).'../../../models/BrazilianRegister.class.php';

class AdminDjtalBrazilianRegisterController extends ModuleAdminController
{
    public function __construct()
    {        
        $this->table = 'djtalbrazilianregister';
        $this->className = 'BrazilianRegister';
        $this->identifier = 'id_djtalbrazilianregister';
        
        $this->lang = false;
        $this->addRowAction('edit');
        $this->bootstrap = true;
                
        $this->fields_list['name'] = array('title' => $this->l('ID'), 'align' => 'left', 'width' => 'auto');
        $this->fields_list['firstname'] = array('title' => $this->l('Name'),  'align' => 'left', 'type' => 'text', 'orderby' => false, 'callback' => 'getCompleteName');
        $this->fields_list['email'] = array('title' => $this->l('E-mail'), 'align' => 'left', 'type' => 'text', 'orderby' => false);
        $this->fields_list['cpf'] = array('title' => $this->l('CPF'), 'align' => 'left', 'width' => 'auto', 'callback' => 'getCpf');
        $this->fields_list['rg'] = array('title' => $this->l('RG'), 'align' => 'left', 'width' => 'auto');
		$this->fields_list['cnpj'] = array('title' => $this->l('CNPJ'), 'align' => 'left', 'width' => 'auto', 'callback' => 'getCnpj');
        $this->fields_list['ie'] = array('title' => $this->l('IE'), 'align' => 'left', 'width' => 'auto');
        $this->fields_list['sr'] = array('title' => $this->l('Razão Social'), 'align' => 'left', 'width' => 'auto');

        parent::__construct();
		$this->list_no_link = true;
		
		//Db::getInstance()->execute('SET SQL_BIG_SELECTS=1');
		
		$this->_join = 'RIGHT JOIN '._DB_PREFIX_.'customer c ON (a.id_customer = c.id_customer) 
		LEFT JOIN '._DB_PREFIX_.'gender_lang gl ON (c.id_gender = gl.id_gender AND gl.id_lang = '.(int)$this->context->language->id.')';
		$this->_select = 'c.id_customer as id_customer, c.id_customer as name, c.firstname as firstname, c.lastname as lastname, c.email as email, gl.name as title';
    }
	
	public function displayEditLink($token = null, $id, $name = null)
	{
		if($id != null) {
			$tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
			$tpl->assign(array(
					'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&update'.$this->table.'&token='.($token != null ? $token : $this->token),
					'action' => $this->l('Edit Data'),
			));
		} elseif( (int)$name != 0 ) { //Specific case: the name is the id_customer, not very clean but working
			$tpl = $this->createTemplate('helpers/list/list_action_add.tpl');
			$tpl->assign(array(
					'href' => self::$currentIndex.'&'.$this->identifier.'='.$id.'&id_customer='.(int)$name.'&update'.$this->table.'&token='.($token != null ? $token : $this->token),
					'action' => $this->l('Add Data'),
			));
		}
	
		return $tpl->fetch();
	}
	
	public function initToolbar()
    {
		parent::initToolbar();
		unset($this->toolbar_btn['new']);
	}
    
	public function getCompleteName($echo, $row) {
		$link = new Link();
		return '<a href="'.$link->getAdminLink('AdminCustomers').'&id_customer='.$row['id_customer'].'&viewcustomer" >'.$row['title'].' '.$echo.' '.$row['lastname'].'</a>';
    }
	
    public function getCpf($echo, $row) {
		return empty($echo)?'':Djtalbrazilianregister::mascaraString('###.###.###-##', $echo);
    }
	
    public function getCnpj($echo, $row) {
		return empty($echo)?'':Djtalbrazilianregister::mascaraString('##.###.###/####-##', $echo);
    }
        
    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd'.$this->table)){
			$id_djtalbrazilianregister = (int) Tools::getValue('id_djtalbrazilianregister');
			if($id_djtalbrazilianregister != 0){
				$register_form = new BrazilianRegister($id_djtalbrazilianregister);
			}else{
				$register_form = new BrazilianRegister();
                $id_customer = (int) Tools::getValue('id_customer');
				$register_form->id_customer = $id_customer;
			}

			$cpf = Tools::getValue('cpf');
			$cnpj = Tools::getValue('cnpj');
			
			if(!empty($cpf)){
				if(Djtalbrazilianregister::validateCPF($cpf) !== false){
					$cpf = preg_replace('/[^0-9]/', '', $cpf);
					$register_form->cpf = pSQL($cpf);
				}else{
					return $this->context->controller->errors[] = '<b>'.$this->l('CPF').'</b> '.$this->l('is invalid');
				}
			}
			if(!empty($cnpj)){
				if(Djtalbrazilianregister::validateCNPJ($cnpj) !== false){
					$cnpj = preg_replace('/[^0-9]/', '', $cnpj);
					$register_form->cnpj = pSQL($cnpj);
				}else{
					return $this->context->controller->errors[] = '<b>'.$this->l('CNPJ').'</b> '.$this->l('is invalid');
				}
			}
			
			$register_form->rg = pSQL(Tools::getValue('rg'));
			$register_form->ie = pSQL(Tools::getValue('ie'));
			$register_form->sr = pSQL(Tools::getValue('sr'));
			$register_form->comp = pSQL(Tools::getValue('comp'));
			if($register_form->save()){
				$this->context->controller->confirmations[] = $this->l('Customer data updated');
			}
		} else {
			parent::postProcess();
		}
    }
    public function renderForm()
    {
        $this->fields_form = array(
                'tinymce' => true,
                'legend' => array(
                    'title' => $this->l('Edit User'),
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_customer'
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('CPF'),
                        'required' => false,
                        'name' => 'cpf',
                        'size' => 80,
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('CNPJ'),
                        'required' => false,
                        'name' => 'cnpj',
                        'size' => 80,
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('RG'),
                        'required' => false,
                        'name' => 'rg',
                        'size' => 80,
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('IE'),
                        'required' => false,
                        'name' => 'ie',
                        'size' => 80,
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Razão Social'),
                        'required' => false,
                        'name' => 'sr',
                        'size' => 80,
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('COMP'),
                        'required' => false,
                        'name' => 'comp',
                        'size' => 80,
                    ),
                ),
                
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button btn btn-default pull-right'
                ),
        );
 
        if (!$this->loadObject(true))
            return;
        
        
        return parent::renderForm();
        
	}

    public function getFieldsValue($obj) {
        $id = (int) Tools::getValue('id_'.$this->table);
		$register_form = new BrazilianRegister($id);
		$values = array('cpf'=>'', 'cnpj' => '', 'rg' => '', 'ie' => '', 'sr' => '', 'comp' => '');
		if($register_form != null){
			$values = array(
				'cpf'=> $register_form->cpf,
				'cnpj' => $register_form->cnpj,
				'rg' => $register_form->rg,
				'ie' => $register_form->ie,
				'sr' => $register_form->sr,
				'comp' => $register_form->comp,
			);
		}
		$values['id_customer'] = (int) Tools::getValue('id_customer');
		return $values;
    }
    
    public function setMedia()
    {
        parent::setMedia();
        $this->addJs(__PS_BASE_URI__.'modules/djtalbrazilianregister/views/js/jquery.mask.mod.js');
        $this->addJs(__PS_BASE_URI__.'modules/djtalbrazilianregister/views/js/back.js');
    }

}