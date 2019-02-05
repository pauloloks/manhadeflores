<?php



class Customer extends CustomerCore {

    public $cpf;
    public $cnpj;
    public $rg;
    public $ie;
	
	public $cpf_or_cnpj;
    public $pf_or_pj;
	public $tipo;

    public function setWsBrazilianData() {
        include_once _PS_MODULE_DIR_ . 'djtalbrazilianregister/models/BrazilianRegister.class.php';
        $breg = BrazilianRegister::getByCustomerId($this->id);
        $this->cpf = $breg['cpf'];
        $this->cnpj = $breg['cnpj'];
        $this->rg = $breg['rg'];
        $this->ie = $breg['ie'];
        $this->sr = $breg['sr'];
		
		if(!empty($breg['cnpj'])){
			$this->cpf_or_cnpj = $breg['cnpj'];
			$this->pf_or_pj = 'pj';
			$this->tipo = 'J';
		}elseif(!empty($breg['cpf'])){
			$this->cpf_or_cnpj = $breg['cpf'];
			$this->pf_or_pj = 'pf';
			$this->tipo = 'F';
		}elseif(!empty($breg['passport'])){
			$this->cpf_or_cnpj = $breg['passport'];
			$this->pf_or_pj = 'es';
			$this->tipo = 'E';
		}
    }

    public function __construct($id = null) {

        parent::__construct($id);

        $this->webserviceParameters['fields']['cpf'] = array();
        $this->webserviceParameters['fields']['cnpj'] = array();
        $this->webserviceParameters['fields']['rg'] = array();
        $this->webserviceParameters['fields']['ie'] = array();
        $this->webserviceParameters['fields']['sr'] = array();
		
        $this->webserviceParameters['fields']['cpf_or_cnpj'] = array();
        $this->webserviceParameters['fields']['pf_or_pj'] = array();
		$this->webserviceParameters['fields']['tipo'] = array();

        $this->setWsBrazilianData();
    }
}
