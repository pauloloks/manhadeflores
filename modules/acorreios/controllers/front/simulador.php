<?php

include_once(dirname(__FILE__).'/../../models/ACorreiosServe.php');

if(!class_exists('ACorreiosFrete'))
{
include_once(dirname(__FILE__).'/../../models/ACorreiosFrete.php');
}

class AcorreiosSimuladorModuleFrontController extends ModuleFrontController
{
	/**
	 * @see FrontController::postProcess()
	 */
   public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
	}

   public function postProcess()
   {
   		parent::init();
   		$origem = Tools::getValue('origem');
   		$cep = Tools::getValue('cep');

		if($origem == 'produto')
		{
			$id_produto = Tools::getValue('id_product');
	   		$produto = new Product($id_produto);

	   		$prod = array();

	   		$prod['product']['id_product'] = $id_produto;
	   		$prod['product']['id'] = $id_produto;
	   		$prod['product']['is_virtual'] = $produto->is_virtual;
	   		$prod['product']['price_tax_exc'] = $produto->price;
	   		$prod['product']['height'] = $produto->height;
	   		$prod['product']['width'] = $produto->width;
	   		$prod['product']['depth'] = $produto->depth;
	   		$prod['product']['weight'] = $produto->weight;
	   		$prod['product']['additional_shipping_cost'] = $produto->additional_shipping_cost;
	   		//var_dump($produto);
	   		$prod['product']['price_amount'] = $produto->price;
            //var_dump($prod);
	   		if (!$this->processaSimulador('produto', '1', $prod)) 
	        {
	            return false;
	        }
		}
   		else if ($origem == 'carrinho')
   		{
   			$params['cart'] = $this->context->cart;
   			$params['smarty'] = $this->context->smarty;
   			global $smarty;
   			$params['addresses'] = $this->context->customer->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));
   			
   			if (!$this->processaSimulador('carrinho', '', $params)) 
            {
             return false;
            }

   		}

   }


   private function processaSimulador($origem, $bloco, $params) 
   {
        if ($origem == 'produto') 
        {
            if (Configuration::get('ACORREIOS_BLOCO_PRODUTO') != 1 or !isset($params['product']) or 
                $params['product']['is_virtual'] == 1) 
            {
               
                return false;
            }
        }
        else if($origem == 'carrinho')
        {
            $virtual = true;

            foreach ($this->context->cart->getProducts() as $prod) {
                if ($prod['is_virtual'] == 0) {
                    $virtual = false;
                }
            }

            if(Configuration::get('ACORREIOS_BLOCO_CARRINHO') != 1 or !$params['cart'] or $virtual == true) 
            {
                return false;
            }
        }
        else
        {
        	exit;
        }

        $msgStatus = 'Aguardando CEP';
        $transpCorreios = array();
        $transpComplementos = array();
        $transportadoras = array();
              
        if ($origem == 'produto' or $origem == 'carrinho') 
        {
            $dadosBasicos = $this->recuperaDadosBasicosSimulador($origem, $params);

            if (!$dadosBasicos['status']) 
            {
                $msgStatus = $dadosBasicos['msgErro'];
            }
            else 
            { 
                $freteClass = new ACorreiosFrete();

                if ($freteClass->calculaFreteSimulador($origem, $dadosBasicos, $params)) 
                {
                    $transpCorreios = $freteClass->getTransportadoras();
                }

                $transportadoras = $transpCorreios;
                
                if (count($transportadoras) > 0) 
                {
                    usort($transportadoras, array($this, 'ordenaValor'));
                    $msgStatus = 'Frete Calculado';
                }
                else 
                {
                    $msgStatus = 'Não existem transportadoras disponíveis para o CEP de Destino. Favor entrar em contato com o Atendimento ao Cliente';
                }
            }
        }

       //var_dump($transportadoras);
       //var_dump($params);
        if ($origem == 'produto') 
        {
            $this->gravaDadosSmartyFrete($msgStatus, $params['product']['id_product'], $transportadoras, false);
        }
        else 
        {
            $this->gravaDadosSmartyFrete($msgStatus, null, $transportadoras, false);
        }

        return true;
    }

    private function recuperaDadosBasicosSimulador($origem, $params) 
    {
        $cepOrigem = trim(preg_replace("/[^0-9]/", "", Configuration::get('ACORREIOS_MEU_CEP')));
        $ufOrigem = '';
        $cepDestino = Tools::getValue('cep');;
        $ufDestino = '';
        $valorPedido = 0;
        $freteGratisValor = false;
        $transpFreteGratisValor = 0;

        if (Tools::getValue('posicao') == 'carrinho') 
        {
            $cepDestino = Tools::getValue('cep');
        }
        else 
        {
            if ( Tools::getValue('posicao') == 'produto' )
            {
                $cepDestino = Tools::getValue('cep');
            }
            else 
            {
                if ($origem == 'carrinho' && empty($cepDestino )) 
                {   
                    if ($this->context->customer->isLogged()) 
                    {

                        $enderecos = $params['addresses'];

                        foreach($enderecos as $endereco)
                        {
                            if(!empty($endereco['postcode']))
                            {
                                $cepDestino = $endereco['postcode'];
                            }
                        }
                        
                    }else {
                        if ($this->context->cookie->acorreios_cep_destino) {
                            $cepDestino = $this->context->cookie->acorreios_cep_destino;
                        }
                    }
                }
            }
        }

        // Valida CEP destino
        $cepDestino = trim(preg_replace("/[^0-9]/", "", $cepDestino));

        // Retorna erro se o CEP for invalido
        if (strlen($cepDestino) <> 8) {
            return array(
                'status'    => false,
                'msgErro'   => 'CEP Destino inválido',
            );
        }

        // Instancia ACorreiosServe
        $serve = new ACorreiosServe();

        // Recupera UF destino
        $ufDestino = $serve->recuperaUF($cepDestino);

        // Retorna erro se nao localizada a UF
        if (!$ufDestino) {
            return array(
                'status'    => false,
                'msgErro'   => 'UF Destino não localizada',
            );
        }

        // Recupera UF origem
        $ufOrigem = $serve->recuperaUF($cepOrigem);

        // Retorna erro se nao localizada a UF
        if (!$ufOrigem) {
            return array(
                'status'    => false,
                'msgErro'   => 'UF Origem não localizada',
            );
        }

        // Recupera valor do Pedido
        if ($origem == 'produto') {

            // Calcula valor do pedido (como esta no Detalhes do Produto e o valor do produto)
            $preco = $params['product']['price_tax_exc'];
            $impostos = 0;
            $valorPedido = $preco * (1 + ($impostos / 100));
        }else {
            // Recupera o valor do pedido

            $valorPedido = $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
        }

        // Verifica frete gratis por valor
        $freteGratis = $serve->filtroFreteGratisValor($valorPedido, $cepDestino, $ufDestino);

        if ($freteGratis['status']) {
            $freteGratisValor = true;
            $transpFreteGratisValor = $freteGratis['idCarrier'];
        }

        return array(
            'status'                    => true,
            'cepOrigem'                 => $cepOrigem,
            'ufOrigem'                  => $ufOrigem,
            'cepDestino'                => $cepDestino,
            'ufDestino'                 => $ufDestino,
            'valorPedido'               => $valorPedido,
            'freteGratisValor'          => $freteGratisValor,
            'transpFreteGratisValor'    => $transpFreteGratisValor
        );

    }

    private function gravaDadosSmartyFrete($msgStatus, $idProduto = null, $transportadoras, $lightBox) 
    {

        $msgTransp = '';
        foreach ($transportadoras as $transp) {

            if ($transp['mensagem'] != '') {
                $msgTransp = $transp['mensagem'];
                break;
            }

        }

        $this->context->smarty->assign(array(
            'acorreios' => array(
                'borda'             => Configuration::get('ACORREIOS_BORDA'),
                'raioBorda'         => Configuration::get('ACORREIOS_RAIO_BORDA'),
                'corFundo'          => Configuration::get('ACORREIOS_COR_FUNDO'),
                'corFonteTitulo'    => Configuration::get('ACORREIOS_COR_FONTE_TITULO'),
                'corBotao'          => Configuration::get('ACORREIOS_COR_BOTAO'),
                'corFonteBotao'     => Configuration::get('ACORREIOS_COR_FONTE_BOTAO'),
                'corFaixaMsg'       => Configuration::get('ACORREIOS_COR_FAIXA_MSG'),
                'corFonteMsg'       => Configuration::get('ACORREIOS_COR_FONTE_MSG'),
                'largura'           => Configuration::get('ACORREIOS_LARGURA'),
                'lightBox'          => $lightBox,
                'msgStatus'         => $msgStatus,
                'cepCookie'         => $this->context->cookie->acorreios_cep_destino,
                'msgTransp'         => $msgTransp,
                'idProduto'         => $idProduto,
                'transportadoras'   => $transportadoras,
            )
        ));

        if(version_compare(_PS_VERSION_, '1.7', '<') ==1)
        {
            if(!empty($idProduto))
            {
               echo $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/front/resultados_ps16.tpl');
               exit;
            }
            else
            {
              echo $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/front/resultados_ps162.tpl');
              exit;
            }

        }
        else
        {
            if(!empty($idProduto))
            {
                $this->setTemplate('module:acorreios/views/templates/front/resultados.tpl'); 
            }
            else
            {
                $this->setTemplate('module:acorreios/views/templates/front/resultados2.tpl'); 
            }

        }
        
    }

    static function ordenaValor($a, $b) 
    {

        if ($a['valorFrete'] == $b['valorFrete']) {
            return 0;
        }
        return ($a['valorFrete'] < $b['valorFrete']) ? -1 : 1;
    }
}
