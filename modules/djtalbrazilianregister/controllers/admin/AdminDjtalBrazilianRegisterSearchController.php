<?php

include_once dirname(__FILE__).'../../../models/BrazilianRegister.class.php';

class AdminDjtalBrazilianRegisterSearchController extends ModuleAdminController
{
	
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initFieldSearchCPF()
    {
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Search by CPF or by E-mail'),
                'icon' => 'icon-link'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('CPF'),
                    'name' => 'search_cpf',
                ),
				array(
                    'type' => 'text',
                    'label' => $this->l('E-mail'),
                    'name' => 'search_email',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Search')
            )
        );

        $this->fields_value['search_cpf'] = '';
    }

    public function renderForm()
    {
        $this->initFieldSearchCPF();

        // Reindex fields
        $this->fields_form = array_values($this->fields_form);

        // Activate multiple fieldset
        $this->multiple_fieldsets = true;

        return parent::renderForm();
    }

    public function initContent()
    {
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->display = '';
        $this->content .= $this->renderForm();

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
            'title' => $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    public function postProcess()
    {
		if(Tools::isSubmit('search_cpf') || Tools::isSubmit('search_email')){
			$search_cpf = preg_replace('/[^0-9]/', '', Tools::getValue('search_cpf'));
			$search_email =Tools::getValue('search_email');
			if(!empty($search_cpf)) {
				$this->content = $this->getByCpf($search_cpf);
			}
			if(!empty($search_email)) {
				$this->content = $this->getByEmail($search_email);
			}
		}
    }
	
	public function getByCpf($cpf) {
		$data = BrazilianRegister::getByCpf($cpf);
		if($data != null){
			$customer = new Customer($data['id_customer']);
			if(!empty($customer->id)){
				$link = new Link();
				$data_br = BrazilianRegister::getByCustomerId($data['id_customer']);
				$cpf = $data_br['cpf'];
				return '<div style="background: #0FBC00; padding: 12px; margin: 12px 0; ">
						'.$this->l('Client found').' ID=[<strong>'.$data['id_customer'].'</strong>], CPF=[<strong>'.$cpf.'</strong>], E-mail=[<strong>'.$customer->email.'</strong>]
						<a style="color: black;" href="'.$link->getAdminLink('AdminCustomers').'&id_customer='.$customer->id.'&viewcustomer" >
							'.$customer->firstname.' '.$customer->lastname.'
						</a>
						 | 
						<a style="color: black;" href="'.$link->getAdminLink('AdminDjtalBrazilianRegister').'&updatedjtalbrazilianregister&id_djtalbrazilianregister='.$data_br['id_djtalbrazilianregister'].'" >
							'.$this->l('Edit fiscal infirmation').'
						</a>
					</div>';
			} else {
				return $this->l('No client found for the CPF').' "'.$cpf.'"';
			}
		} else {
			return $this->l('No client found for the CPF').' "'.$cpf.'"';
		}
	}
	
	public function getByEmail($email) {
		$customers = Customer::getCustomersByEmail($email);
		if($customers != null && count($customers) > 0){
			$link = new Link();
			$html = '';
			foreach($customers as $c) {
				$customerObj = new Customer($c['id_customer']);
				$data_br = BrazilianRegister::getByCustomerId($c['id_customer']);
				$cpf = $data_br['cpf'];
				$html = $html.'
					<div style="background: #0FBC00; padding: 12px; margin: 12px 0; ">
						'.$this->l('Client found').' ID=[<strong>'.$customerObj->id.'</strong>], CPF=[<strong>'.$cpf.'</strong>], E-mail=[<strong>'.$email.'</strong>]
						<a style="color: black;" href="'.$link->getAdminLink('AdminCustomers').'&id_customer='.$customerObj->id.'&viewcustomer" >
							'.$customerObj->firstname.' '.$customerObj->lastname.'
						</a>
						 | 
						<a style="color: black;" href="'.$link->getAdminLink('AdminDjtalBrazilianRegister').'&updatedjtalbrazilianregister&id_djtalbrazilianregister='.$data_br['id_djtalbrazilianregister'].'" >
							'.$this->l('Edit fiscal infirmation').'
						</a>
					</div>';
			}
			return $html;
		} else {
			return $this->l('No client found for the E-mail').' "'.$email.'"';
		}
	}
}