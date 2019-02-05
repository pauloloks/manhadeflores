<?php

include_once(dirname(__FILE__).'/models/ACorreiosServe.php');

if(!class_exists('ACorreiosFrete'))
{
include_once(dirname(__FILE__).'/models/ACorreiosFrete.php');
}
include_once(dirname(__FILE__) . '/acorreioscampos.php');

class ACorreios extends CarrierModule 
{

    public $id_carrier;
    private $tab_select = '';
    private $postErrors = array();
    private $prazoEntrega = array();
    private $html = '';
    private $SPConfigCore;
    private $_output;
    public $resultado_simulador;

    public function __construct() 
    {
        
        $this->name     = 'acorreios';
        $this->tab      = 'shipping_logistics';
        $this->version  = '1.7.2';
        $this->author   = 'Prestafy';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Correios do Brasil');
        $this->description = $this->l('Transportadora e Simulador de frete dos Correios, com várias opções de Sedex e PAC.');

    
        $this->_tabClassName['principal'] = array('className' => 'AdminAcorreios', 'name' => 'AdminAcorreios');

        Configuration::updateValue('ACORREIOS_URL_IMG', Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/img/');
        Configuration::updateValue('ACORREIOS_URL_FUNCOES', Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/funcoes.php');
        Configuration::updateValue('ACORREIOS_URL_FUNCOES_RASTREIO', __PS_BASE_URI__.'modules/'.$this->name.'/funcoes.php');
        Configuration::updateValue('ACORREIOS_URI_LOGO_PS', _PS_SHIP_IMG_DIR_);
        Configuration::updateValue('ACORREIOS_URI_LOGO_PS_TMP', _PS_TMP_IMG_DIR_.'carrier_mini_');
        
        // Atualiza cookie do CEP
        if (Tools::getValue('origem') == 'adicCarrinho') {
            $this->context->cookie->acorreios_cep_destino = Tools::getValue('cep');
        }else {
            if (Tools::isSubmit('btnSubmit')) {
                $this->context->cookie->acorreios_cep_destino = Tools::getValue('acorreios_cep');
            }
        }

    }

    public function install() 
    {
        
        if(version_compare(_PS_VERSION_, '1.7', '<') ==1)
        {
            if (!parent::install()
            Or !$this->registerHook('actionCarrierUpdate')
            Or !$this->registerHook('displayBeforeCarrier')
            Or !$this->registerHook('displayRightColumnProduct')
            Or !$this->registerHook('displayShoppingCartFooter')
            or !$this->registerHook('actionFrontControllerSetMedia')
            or !$this->registerHook('displayBeforeCarrier')
            or !$this->registerHook('displayOrderDetail'))
            {

                return false;
            }

        }
        else
        {
            if (!parent::install()
                Or !$this->registerHook('actionCarrierUpdate')
                Or !$this->registerHook('displayBeforeCarrier')
                Or !$this->registerHook('displayProductButtons')
                Or !$this->registerHook('displayShoppingCartFooter')
                or !$this->registerHook('actionFrontControllerSetMedia')
                or !$this->registerHook('displayOrderDetail'))
            {

                return false;
            }
        }

        

        Configuration::updateValue('ACORREIOS_MEU_CEP', '');
        Configuration::updateValue('ACORREIOS_EXCLUIR_CONFIG', '');
        Configuration::updateValue('ACORREIOS_MSG_CORREIOS', 'on');
        Configuration::updateValue('ACORREIOS_BLOCO_CARRINHO', 'on');
        Configuration::updateValue('ACORREIOS_BLOCO_PRODUTO', 'on');
        Configuration::updateValue('ACORREIOS_FRETE_GRATIS_DEMAIS_TRANSP', 'on');
        Configuration::updateValue('ACORREIOS_OFFLINE', '');
        Configuration::updateValue('ACORREIOS_EMBALAGEM', '2');
        Configuration::updateValue('ACORREIOS_TEMPO_PREPARACAO', '0');
        Configuration::updateValue('ACORREIOS_AVISO_RECEBIMENTO', '');
        Configuration::updateValue('ACORREIOS_VALOR_DECLARADO', '');
        Configuration::updateValue('ACORREIOS_MAO_PROPRIA', '');
        Configuration::updateValue('ACORREIOS_CEP_CIDADE', '');

        Configuration::updateValue('ACORREIOS_URL_WS_CORREIOS', 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx?WSDL');
        Configuration::updateValue('ACORREIOS_URL_RASTREIO_CORREIOS', 'http://websro.correios.com.br/sro_bin/txect01%24.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=@');


        $this->gera_tabelas_db();
        $this->gera_ceps();
        $this->gera_carriers();
        $this->gera_especificacoes();
        $this->gera_frete_gratis();
        $this->gera_caixas_padrao();
        $this->gera_servicos();
        $this->gera_frete_gratis();
        $this->gera_dados_offline();

        return true;

    }

    public function hookdisplayOrderDetail($params)
    {
        
       
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        
        $url_loja = Tools::getHttpHost(true).__PS_BASE_URI__;   
        $formulario = $url_loja.'modules/acorreios/views/js/front/form.js';
        $masked_input = $url_loja.'modules/acorreios/views/js/front/jquery.maskedinput.js';
             
        if(version_compare(_PS_VERSION_, '1.7', '<') ==1)
        {
       
            $this->context->controller->addJS( $formulario );
            $this->context->controller->addJS( $masked_input );
        }
        else
        {

             $this->context->controller->registerJavascript('acorreio-fmlr', $formulario, array('server' => 'remote', 'position' => 'footer', 'priority' => 20));
        $this->context->controller->registerJavascript('acorreio-mi', $masked_input, array('server' => 'remote', 'position' => 'footer', 'priority' => 20));
        }
    }

    public function uninstall() 
    {
        
        $servicos = $this->recuperaServicosCorreios();
        $serve = new ACorreiosServe();
        $serve->desinstalaCarrier($servicos);

        $this->excluir_tabelas_db();

        return parent::uninstall();

    }
    
    public function refaz_lista($lista)
    {
        
        global $smarty;

        foreach($lista as $j=> $carrier)
        {
                $lista[$j]['delay'] = 'Entrega em '.$smarty->tpl_vars['entrega_'.$carrier['id']].' dias úteis'; 
                $lista[$j]['price'] =  Tools::displayPrice($lista[$j]['price_without_tax']);         
        }
           
        return $lista;

    }
    public function hookdisplayShoppingCartFooter($params) 
    {


        if (!$this->processaSimulador('carrinho', '', $params)) {
            return false;
        }

        $url_loja = Tools::getHttpHost(true).__PS_BASE_URI__;

        $this->smarty->assign( array(
                              'simulador' => $this->context->link->getModuleLink('acorreios', 'simulador', array(), true),
                             
                              'url_loja' => $url_loja
                     ));

        return $this->display(__FILE__, 'views/templates/hook/displayShoppingCartFooter.tpl');

    }

    public function hookactionCarrierUpdate($params) 
    {
        

        $atualizado = false;
        $sql = 'SELECT *
                FROM a_correios_servicos
                WHERE id_carrier = '.(int)$params['id_carrier'];

        $servicos = Db::getInstance()->getRow($sql);

        if(!empty($servicos))
        {
            if ((int)$servicos['id_carrier'] != (int)$params['carrier']->id) {
            $novoId = $params['carrier']->id;
            $atualizado = true;
        }else {
            $novoId = $servicos['id_carrier'];
        }

        if ((int)$servicos['grade'] != (int)$params['carrier']->grade) {
            $novaGrade = $params['carrier']->grade;
            $atualizado = true;
        }else {
            $novaGrade = $servicos['grade'];
        }


        if ($servicos['ativo'] != $params['carrier']->active) 
        {
            $novoAtivo = $params['carrier']->active;
            $atualizado = true;
        }else {
            $novoAtivo = $servicos['ativo'];
        }
        if ($atualizado == true) 
        {

            $dados = array(
                'id_carrier'    => $novoId,
                'grade'         => $novaGrade,
                'ativo'         => $novoAtivo
            );

            $sql = 'UPDATE a_correios_servicos SET id_carrier = '.$dados['id_carrier'].', grade = '.$dados['grade']. ' , ativo = '.$dados['ativo'] .' 

            WHERE id_carrier='.(int)$servicos['id_carrier'].'
             ' ;

            Db::getInstance()->Execute($sql);

            $SQL = 'UPDATE a_correios_frete_gratis set id_carrier='.$dados['id_carrier'].'WHERE id_carrier='.(int) $servicos['id_carrier'];

              Db::getInstance()->Execute($sql);
        }

        }

        

       

    }

    public function hookdisplayProductButtons($params) 
    {
        

        
        if (!$this->processaSimulador('produto', '1', $params)) 
        {
            return false;
        }

        $url_loja = Tools::getHttpHost(true).__PS_BASE_URI__;

        if(version_compare(_PS_VERSION_, '1.7', '<') ==1)
        {
            $id_product = $params['product']->id;
            $is_virtual = $params['product']->is_virtual;
        }
        else
        {
            $id_product = $params['product']['id_product'];
            $is_virtual = $params['product']['is_virtual'];
        }

        $this->smarty->assign( array(
                              'simulador' => $this->context->link->getModuleLink('acorreios', 'simulador', array(), true),
                              'is_virtual' => $is_virtual,
                              'id_product' => $id_product,
                              'url_loja' => $url_loja
                     ));

        return $this->display(__FILE__, 'views/templates/hook/displayFooterProduct.tpl');
    }

    public function hookdisplayRightColumnProduct($params) 
    {
        
        #print_r($params);exit;
        if(version_compare(_PS_VERSION_, '1.7', '<') ==1)
        {
            $params['product'] = $this->context->controller->getProduct();
        }
        
        if (!$this->processaSimulador('produto', '1', $params)) 
        {
            return false;
        }

        $url_loja = Tools::getHttpHost(true).__PS_BASE_URI__;

        if(version_compare(_PS_VERSION_, '1.7', '<') ==1)
        {
            $id_product = $params['product']->id;
            $is_virtual = $params['product']->is_virtual;
        }
        else
        {
            $id_product = $params['product']['id_product'];
            $is_virtual = $params['product']['is_virtual'];
        }

        $this->smarty->assign( array(
                              'simulador' => $this->context->link->getModuleLink('acorreios', 'simulador', array(), true),
                              'is_virtual' => $is_virtual,
                              'id_product' => $id_product,
                              'url_loja' => $url_loja
                     ));

        return $this->display(__FILE__, 'views/templates/hook/displayFooterProduct.tpl');
    }


    public function getContent()
    {
        
        $this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/views/css/admin/sp-admin.css');
        $this->context->controller->addJqueryPlugin('acorreios', _MODULE_DIR_ . $this->name . '/views/js/admin/');

         if (Tools::isSubmit('submitacorreios')) 
         {
            $this->postValidation();
           
         }
            
        $id_shop = $this->context->shop->id;
        $languages = $this->context->language->getLanguages();
        $errors = array();
 
        return $this->_output.$this->info_modulo().$this->getFormHTML();
    }

    private function getFormHTML()
    {
        
        $id_default_lang = $this->context->language->id;
        $languages = $this->context->language->getLanguages();
        $id_shop = $this->context->shop->id;

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $id_default_lang;
        $helper->allow_employee_form_lang = $id_default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            )
        );

        //Carrega as configurações de cada uma das abas separadamente
        $paineis = new ACorreiosCampos();
        $configs = $paineis->get_campos_geral();

        //Configurações da aba Geral
        foreach($configs as $config)
        {
            $helper->fields_value[$config] = Configuration::get(strtoupper($config));
        }
        $helper->fields_value['SEPARADOR']='';
        $helper->fields_value['PARAGRAFO']='';

        //Configurações da aba Faixas de Frete Grátis

        $sql = "SELECT * FROM a_correios_frete_gratis
                WHERE id_shop=".$this->context->shop->id;

        $frete_gratis = Db::getInstance()->ExecuteS($sql);

        foreach($frete_gratis as $fg)
        {
            $helper->fields_value['acorreios_fg_'.$fg['id_especificacao']] = $fg['regiao_cep'];
        }

        //Configurações da aba Caixas

        $sql = "SELECT id, descricao, comprimento, altura, largura, peso, cubagem, custo, ativo
                FROM a_correios_embalagens
                WHERE id_shop = ".$this->context->shop->id;

        $caixas = Db::getInstance()->ExecuteS($sql);

        foreach($caixas as $caixa)
        {
            $helper->fields_value['acorreios_caixa_nome_'.$caixa['id']] = $caixa['descricao'];
            $helper->fields_value['acorreios_caixa_largura_'.$caixa['id']] = $caixa['largura'];
            $helper->fields_value['acorreios_caixa_comprimento_'.$caixa['id']] = $caixa['comprimento'];
            $helper->fields_value['acorreios_caixa_altura_'.$caixa['id']] = $caixa['altura'];
            $helper->fields_value['acorreios_caixa_peso_'.$caixa['id']] = $caixa['peso'];
            $helper->fields_value['acorreios_caixa_preco_'.$caixa['id']] = $caixa['custo'];
            $helper->fields_value['acorreios_caixa_ativo_'.$caixa['id']] = $caixa['ativo'];
            $helper->fields_value['acorreios_caixa_excluir_'.$caixa['id']] = 0;
        }
        #Campos da faixa nova
        $helper->fields_value['acorreios_caixa_nome_nova'] = '';
        $helper->fields_value['acorreios_caixa_largura_nova'] = '';
        $helper->fields_value['acorreios_caixa_comprimento_nova'] = '';
        $helper->fields_value['acorreios_caixa_altura_nova'] = '';
        $helper->fields_value['acorreios_caixa_peso_nova'] = '';
        $helper->fields_value['acorreios_caixa_preco_nova'] = '';
        $helper->fields_value['acorreios_caixa_ativo_nova'] = 0;
        $helper->fields_value['acorreios_caixa_excluir_nova'] = 0;
        $helper->fields_value['acorreios_serial'] = Configuration::get('ACORREIOS_SERIAL');
        
         $helper->fields_value['acorreios_total_caixas'] = count($caixas);
        // Custom variables     
        $helper->tpl_vars = array(
            'sptabs' => $this->get_abas(),
            'versions' => '',
            'controller_url' => $this->context->link->getAdminLink('AdminSPConfig'),
            'shopId' => $id_shop
        );


        /**

            COMEÇA A SELECIONAR OS DADOS DOS SERVIÇOS
            LISTA COM OS SERVIÇOS QUE PODEM SER SELECIONADOS

        **/
        $servs = array('SEDEX', 'PAC-GF', 'PAC', 'SEDEX 10', 'SEDEX 12', 'SEDEX HOJE');

        foreach($servs as $serv)
        {
            $id_servico = $this->get_id_servico($serv);

            //Seleciona os dados dos campos dos serviços
            $sql = 'SELECT id_especificacao, regiao_cep_excluido, ativo FROM  a_correios_servicos
                    WHERE id='.$id_servico.' AND id_shop='.$this->context->shop->id;

            $sv = Db::getInstance()->ExecuteS($sql);
            
            $id_especificacao=$sv[0]['id_especificacao'];
            $ceps_excluidos = $sv[0]['regiao_cep_excluido'];
            $ativo=$sv[0]['ativo'];

            $helper->fields_value['acorreios_servicos_ativo_'.$id_servico] = $ativo;
            $helper->fields_value['acorreios_servico_desativar_faixas_'.$id_servico] = $ceps_excluidos;

            //Seleciona os dados da tabela de especificações

            $sql = 'SELECT * FROM  a_correios_especificacoes WHERE id='.$id_especificacao.'
            AND id_shop='.$this->context->shop->id;

            $esp = Db::getInstance()->ExecuteS($sql);

            $helper->fields_value['acorreios_espec_codigos_'.$id_servico] = $esp[0]['cod_servico'];
            $helper->fields_value['acorreios_espec_codigoa_'.$id_servico] = $esp[0]['cod_administrativo'];
            $helper->fields_value['acorreios_espec_senha_'.$id_servico] = $esp[0]['senha'];
            $helper->fields_value['acorreios_espec_compmin_'.$id_servico] = $esp[0]['comprimento_min'];
            $helper->fields_value['acorreios_espec_compmax_'.$id_servico] = $esp[0]['comprimento_max'];
            $helper->fields_value['acorreios_espec_larguramin_'.$id_servico] = $esp[0]['largura_min'];
            $helper->fields_value['acorreios_espec_larguramax_'.$id_servico] = $esp[0]['largura_max'];
            $helper->fields_value['acorreios_espec_alturamin_'.$id_servico] = $esp[0]['altura_min'];
            $helper->fields_value['acorreios_espec_alturamax_'.$id_servico] = $esp[0]['altura_max'];
            $helper->fields_value['acorreios_espec_dimensoesmax_'.$id_servico] = $esp[0]['somatoria_dimensoes_max'];
            $helper->fields_value['acorreios_espec_pesoemax_'.$id_servico] = $esp[0]['peso_estadual_max'];
            $helper->fields_value['acorreios_espec_pesonmax_'.$id_servico] = $esp[0]['peso_nacional_max'];
            $helper->fields_value['acorreios_espec_intervaloe_'.$id_servico] = $esp[0]['intervalo_pesos_estadual'];
            $helper->fields_value['acorreios_espec_intervalon_'.$id_servico] = $esp[0]['intervalo_pesos_nacional'];
            $helper->fields_value['acorreios_espec_cubagemi_'.$id_servico] = $esp[0]['cubagem_max_isenta'];
            $helper->fields_value['acorreios_espec_cubagemb_'.$id_servico] = $esp[0]['cubagem_base_calculo'];
            $helper->fields_value['acorreios_espec_maopropria_'.$id_servico] = $esp[0]['mao_propria_valor'];
            $helper->fields_value['acorreios_espec_aviso_'.$id_servico] = $esp[0]['aviso_recebimento_valor'];
            $helper->fields_value['acorreios_espec_valord_'.$id_servico] = $esp[0]['valor_declarado_percentual'];
            $helper->fields_value['acorreios_espec_valordmax_'.$id_servico] = $esp[0]['valor_declarado_max'];
            $helper->fields_value['acorreios_espec_seguro_'.$id_servico] = $esp[0]['seguro_automatico_valor'];

        }
        
       //Mostra verdadeiramente o formulário
       return ($helper->generateForm(array(
            'general'   => $paineis->form_geral(),
            'sedexn'  => $paineis->form_sedex(),
            'sedex10'  => $paineis->form_sedex10(),
            'sedex12'  => $paineis->form_sedex12(),
            'sedexhoje'  => $paineis->form_sedexhoje(),
            'pac'  => $paineis->form_pac(),
            'pgf'  => $paineis->form_pgf(),
            'faixasgratis' =>$paineis->form_fretegratis(),
            'embalagens' =>$paineis->form_embalagens(),
            'offline' =>$paineis->form_offline(),
            'debug' => $paineis->form_debug()
            
        )));
    }

    private function info_modulo()
    {
        
        return $this->display(__FILE__, '/views/hook/infos.tpl');
    }

    protected function field_onOff ($name, $label,$des ='') {
        return array(
            'type' => 'switch',
            'label' => $label,
            'name' => $name,
            'desc' => $des,
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => $name.'_ON',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => $name.'_OFF',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            )
        );
    }
    
    public function get_abas()
    {
        $tabArray = array(
            'Geral'               => 'fieldset_general',
            'Faixas de Frete Grátis'       => 'fieldset_faixasgratis',
            'Caixas'          => 'fieldset_embalagens',
            'Sedex'               => 'fieldset_sedexn',
            'Sedex 10'            => 'fieldset_sedex10',
            'Sedex 12'            => 'fieldset_sedex12',
            'Sedex Hoje'          => 'fieldset_sedexhoje',
            'PAC'                 => 'fieldset_pac',
            'PAC-GF'              => 'fieldset_pgf',
            'Dados Off-Line'      => 'fieldset_offline',
            'Debug'               => 'fieldset_debug'
        );
        return $tabArray;
    }

    private function postValidation()
    {
        //Verifica se os dados da aba Geral foram informados corretamente

        if (Trim(Tools::getValue('acorreios_meu_cep')) == '') {
            $this->postErrors[] = $this->l('O campo CEP DE ORIGEM é Obrigatório.');
        }

        if (Trim(Tools::getValue('acorreios_cep_cidade')) == '') {
            $this->postErrors[] = $this->l('O campo Faixa de CEP Local é Obrigatório.');
        }

        if (Trim(Tools::getValue('acorreios_tempo_preparacao')) == '') {
            $this->postErrors[] = $this->l('O campo Prazo Adicional é Obrigatório.');
        }else {
            if (!is_numeric(Tools::getValue('acorreios_tempo_preparacao'))) {
                $this->postErrors[] = $this->l('O campo Prazo Adicional precisa ser um número inteiro.');
            }else {
                if (Tools::getValue('acorreios_tempo_preparacao') < 0) {
                    $this->postErrors[] = $this->l('O campo Prazo Adicional não pode ser menor que zero.');
                }
            }
        }

        if (!$this->postErrors) {
            $this->salvar_geral();
        }
        else
        {
            $this->postErrors[] = $this->l('Por favor, verifique os campos da aba Geral e tente novamente.');
        }
        $this->excluiCache();
        //Verifica se as faixas de CEP da aba de faixas de frete grátis foram informadas corretamente
        $this->salvar_faixas();
        $this->salvar_caixas();
        $this->salvar_servicos();

    }

    protected function salvar_servicos()
    {
        $servicos = array('SEDEX', 'PAC-GF', 'PAC', 'SEDEX 10', 'SEDEX 12', 'SEDEX HOJE');

        foreach($servicos as $serv)
        {
             $id_servico = $this->get_id_servico($serv);

             $ativo = Tools::getValue('acorreios_servicos_ativo_'.$id_servico);
             $ceps_excluidos = Tools::getValue('acorreios_servico_desativar_estados_'.$id_servico);

             $id_especificacao = $this->get_id_especificacao($serv);

              Db::getInstance()->Execute('UPDATE a_correios_servicos SET ativo='.$ativo.', 
                regiao_cep_excluido="'.$ceps_excluidos.'" WHERE
                id='.$id_servico.' AND id_shop='.$this->context->shop->id.' LIMIT 1'); 

              $cod_servico = Tools::getValue('acorreios_espec_codigos_'.$id_servico);
              $codigo_administrativo = Tools::getValue('acorreios_espec_codigoa_'.$id_servico);
              $senha = Tools::getValue('acorreios_espec_senha_'.$id_servico);
              $comprimento_min = Tools::getValue('acorreios_espec_compmin_'.$id_servico);
              $comprimento_max = Tools::getValue('acorreios_espec_compmax_'.$id_servico);
              $largura_min = Tools::getValue('acorreios_espec_larguramin_'.$id_servico);
              $largura_max = Tools::getValue('acorreios_espec_larguramax_'.$id_servico);
              $altura_min = Tools::getValue('acorreios_espec_alturamin_'.$id_servico);
              $altura_max = Tools::getValue('acorreios_espec_alturamax_'.$id_servico);
              $dimensoes_max = Tools::getValue('acorreios_espec_dimensoesmax_'.$id_servico);
              $peso_emax = Tools::getValue('acorreios_espec_pesoemax_'.$id_servico);
              $peso_nmax = Tools::getValue('acorreios_espec_pesonmax_'.$id_servico);
              $peso_intervaloe = Tools::getValue('acorreios_espec_intervaloe_'.$id_servico);
              $peso_intervalon = Tools::getValue('acorreios_espec_intervalon_'.$id_servico);
              $cubagemi = Tools::getValue('acorreios_espec_cubagemi_'.$id_servico);
              $cubagemb = Tools::getValue('acorreios_espec_cubagemb_'.$id_servico);
              $mao = Tools::getValue('acorreios_espec_maopropria_'.$id_servico);
              $aviso = Tools::getValue('acorreios_espec_aviso_'.$id_servico);
              $valord = Tools::getValue('acorreios_espec_valord_'.$id_servico);
              $valordmax = Tools::getValue('acorreios_espec_valordmax_'.$id_servico);
              $ativo = Tools::getValue('acorreios_servicos_ativo_'.$id_servico);
              $seguro = Tools::getValue('acorreios_espec_seguro_'.$id_servico);

              $sql = 'UPDATE a_correios_especificacoes
                SET 
                cod_servico = "'.$cod_servico.'",
                cod_administrativo = "'.$codigo_administrativo.'",
                senha = "'.$senha.'",
                comprimento_min = '.$comprimento_min.',
                comprimento_max = '.$comprimento_max.',
                largura_min = '.$largura_min.',
                largura_max = '.$largura_max.',
                altura_min = '.$altura_min.',
                altura_max = '.$altura_max.',
                somatoria_dimensoes_max = '.$dimensoes_max.',
                peso_estadual_max = '.$peso_emax.',
                peso_nacional_max = '.$peso_nmax.',
                intervalo_pesos_estadual = "'.$peso_intervaloe.'",
                intervalo_pesos_nacional = "'.$peso_intervalon.'",
                cubagem_max_isenta = '.$cubagemi.',
                cubagem_base_calculo = '.$cubagemb.',
                mao_propria_valor = '.$mao.',
                aviso_recebimento_valor = '.$aviso.',
                valor_declarado_percentual = '.$valord.',
                valor_declarado_max = '.$valordmax.',
                seguro_automatico_valor = '.$seguro.'

                WHERE id = '.$id_especificacao.'
                AND id_shop = '.$this->context->shop->id.' LIMIT 1
                ';
    
              DB::getInstance()->Execute($sql);
        }

        $this->_output.= $this->displayConfirmation($this->l('Os dados dos serviços postais foram atualizados com sucesso!'));
    }

    private function get_id_especificacao($nome)
    {
        $id_especificacao = Db::getInstance()->getValue('SELECT id FROM a_correios_especificacoes WHERE servico="'.$nome.'" AND id_shop='.$this->context->shop->id); 

        return $id_especificacao;

    }

    private function get_id_servico($nome)
    {
        $id_especificacao = Db::getInstance()->getValue('SELECT id FROM a_correios_especificacoes WHERE servico="'.$nome.'" AND id_shop='.$this->context->shop->id); 

        $id_servico = Db::getInstance()->getValue('SELECT id FROM a_correios_servicos WHERE id_especificacao='.$id_especificacao.' AND id_shop='.$this->context->shop->id);

        return $id_servico;
    }

    private function gera_servicos() 
    {
        $serve = new ACorreiosServe();

        $sql = 'SELECT id, servico
                FROM a_correios_especificacoes
                WHERE id_shop = '.(int)$this->context->shop->id;

        $eps = Db::getInstance()->ExecuteS($sql);

        foreach ($eps as $reg) 
        {
            $parm = array(
                'name'                  => $reg['servico'],
                'id_tax_rules_group'    => 0,
                'active'                => false,
                'deleted'               => false,
                'shipping_handling'     => false,
                'range_behavior'        => true,
                'is_module'             => true,
                'shipping_external'     => true,
                'shipping_method'       => 0,
                'external_module_name'  => $this->name,
                'need_range'            => true,
                'url'                   => Configuration::get('ACORREIOS_URL_RASTREIO_CORREIOS'),
                'is_free'               => false,
                'grade'                 => 0,
            );

            $idCarrier = $serve->instalaCarrier($parm);
            $dados = array(
                'id_shop'               => $this->context->shop->id,
                'id_especificacao'      => $reg['id'],
                'id_carrier'            => $idCarrier,
                'filtro_regiao_uf'      => 1,
                'grade'                 => 0,
                'ativo'                 => 0,
                'percentual_desconto'   => 0,
                'valor_pedido_desconto' => 0
            );

            Db::getInstance()->Execute('INSERT INTO a_correios_servicos 
                (id_shop, id_especificacao, id_carrier, filtro_regiao_uf, grade, ativo,
                percentual_desconto, valor_pedido_desconto) VALUES 
                ('.$dados['id_shop'].', '.$dados['id_especificacao'].', '.$dados['id_carrier'].',
                '.$dados['filtro_regiao_uf'].', '.$dados['grade'].','.$dados['ativo'].',
                '.$dados['percentual_desconto'].', '.$dados['valor_pedido_desconto'].')');
        }

    }

    private function salvar_caixas()
    {  
       Db::getInstance()->Execute('TRUNCATE TABLE a_correios_embalagens');
       $total_caixas = Tools::getValue('acorreios_total_caixas');
      
        for($i=1; $i <= $total_caixas; $i++ ) 
        {
            $ativo = Tools::getValue('acorreios_caixa_ativo_'.$i);
            $altura = str_replace(',', '.', Tools::getValue('acorreios_caixa_altura_'.$i));
            $comprimento = str_replace(',', '.', Tools::getValue('acorreios_caixa_comprimento_'.$i));
            $largura = str_replace(',', '.', Tools::getValue('acorreios_caixa_largura_'.$i));
            $peso = str_replace(',', '.', Tools::getValue('acorreios_caixa_peso_'.$i));
            $custo = str_replace(',', '.', Tools::getValue('acorreios_caixa_preco_'.$i));
            $descricao = str_replace(',', '.', Tools::getValue('acorreios_caixa_nome_'.$i));
           
            if(Tools::getValue('acorreios_caixa_excluir_'.$i) == 0
                && !empty($comprimento) && !empty($altura) && !empty($largura) && !empty($peso) && !empty($custo)
            )
            {
            $cubagem = ($comprimento * $altura * $largura);

            Db::getInstance()->Execute('INSERT INTO a_correios_embalagens (id_shop, descricao, comprimento, altura, largura, peso, cubagem,custo, ativo) VALUES ('.$this->context->shop->id.', "'.$descricao.'", '.$comprimento.', '.$altura.', '.$largura.','.$peso.', '.$cubagem.', '.$custo.', '.$ativo.')');
            }
        }

        //Recebe os dados da nova caixa
        $ativo = Tools::getValue('acorreios_caixa_ativo_'.'nova');
        $largura = str_replace(',', '.', Tools::getValue('acorreios_caixa_largura_'.'nova'));
        $comprimento = str_replace(',', '.', Tools::getValue('acorreios_caixa_comprimento_'.'nova'));
        $altura = str_replace(',', '.', Tools::getValue('acorreios_caixa_altura_'.'nova'));
        $peso = str_replace(',', '.', Tools::getValue('acorreios_caixa_peso_'.'nova'));
        $custo = str_replace(',', '.', Tools::getValue('acorreios_caixa_preco_'.'nova'));
        $descricao = str_replace(',', '.', Tools::getValue('acorreios_caixa_nome_'.'nova'));
        $cubagem = ($comprimento * $altura * $largura);

        if(!empty($comprimento) && !empty($altura) && !empty($largura) && !empty($peso) && !empty($custo)
            )
        {
            #Os dados de uma nova caixa foram preenchidos, portanto, insere ela
            Db::getInstance()->Execute('INSERT INTO a_correios_embalagens (id_shop, descricao, comprimento, altura, largura, peso, cubagem,custo, ativo) VALUES ('.$this->context->shop->id.', "'.$descricao.'", '.$comprimento.', '.$altura.', '.$largura.','.$peso.', '.$cubagem.', '.$custo.', '.$ativo.')');
        }

        $this->_output.= $this->displayConfirmation($this->l('Os dados da aba Caixas foram salvos com sucesso!'));

    }


    private function salvar_faixas()
    {
        #Apaga todas as faixas de frete grátis que já existem
        $sql = "DELETE FROM a_correios_frete_gratis
                WHERE id_shop = ".$this->context->shop->id;

        Db::getInstance()->Execute($sql);

        $sql= "SELECT id_especificacao, id_carrier FROM  a_correios_servicos
               WHERE id_shop= ".$this->context->shop->id;


        $servicos = Db::getInstance()->ExecuteS($sql);

        //Seleciona as especificações, que é onde estão os nomes de cada serviço

        $sql='SELECT id, servico FROM a_correios_especificacoes WHERE id_shop='.$this->context->shop->id;
        $especs = Db::getInstance()->ExecuteS($sql);
        $nomes = array();

        foreach($especs as $espec)
        {
            $nomes[$espec['id']] = $espec['servico'];
        }

        foreach($servicos as $servico)
        {
            $id_especificacao = $servico['id_especificacao'];
            $campo = 'acorreios_fg_'.$id_especificacao;

            $sql = "INSERT INTO a_correios_frete_gratis
                (nome_regiao,id_shop, id_carrier, filtro_regiao_uf, regiao_cep, ativo, id_especificacao)
                VALUES
                ('".$nomes[$id_especificacao]."', ".$this->context->shop->id.", ".$servico['id_carrier'].", 0, '".$_POST[$campo]."',1, '".$id_especificacao."')
                 ";

             Db::getInstance()->Execute($sql);

        }

        $this->_output.= $this->displayConfirmation($this->l('Os dados da aba Faixas de Frete Grátis foram salvos com sucesso!'));

    }

    private function salvar_geral()
    {
        Configuration::updateValue('ACORREIOS_MEU_CEP', Trim(Tools::getValue('acorreios_meu_cep')));
        Configuration::updateValue('ACORREIOS_SERIAL', Trim(Tools::getValue('acorreios_serial')));
        Configuration::updateValue('ACORREIOS_BLOCO_CARRINHO', Trim(Tools::getValue('acorreios_bloco_carrinho')));
        Configuration::updateValue('ACORREIOS_BLOCO_PRODUTO', Trim(Tools::getValue('acorreios_bloco_produto')));
        Configuration::updateValue('ACORREIOS_CEP_CIDADE', Trim(Tools::getValue('acorreios_cep_cidade')));
        Configuration::updateValue('ACORREIOS_TEMPO_PREPARACAO', Trim(Tools::getValue('acorreios_tempo_preparacao')));
        Configuration::updateValue('ACORREIOS_MAO_PROPRIA', Trim(Tools::getValue('acorreios_mao_propria')));
        Configuration::updateValue('ACORREIOS_VALOR_DECLARADO', Trim(Tools::getValue('acorreios_valor_declarado')));
        Configuration::updateValue('ACORREIOS_AVISO_RECEBIMENTO', Trim(Tools::getValue('acorreios_aviso_recebimento')));

        $this->_output.= $this->displayConfirmation($this->l('Os dados da aba Geral foram salvos com sucesso!'));
        
    }


    public function getOrderShippingCostExternal($params) 
    { 
        return $this->getOrderShippingCost($params, 0);
    }

    private function recuperaCadastroCep() 
    {
        

        $sql = 'SELECT *
                FROM a_correios_cadastro_cep
                ORDER BY estado';

        return Db::getInstance()->executeS($sql);
    }

    private function gera_ceps() 
    {
        

        #Cadastramento de CEPs iniciais
        $sql = "INSERT INTO `a_correios_cadastro_cep` (`estado`, `capital`, `cep_estado`, `cep_capital`, `cep_base_capital`, `cep_base_interior`) VALUES
            ('AC', 'Rio Branco',        '69900000:69999999',                        '69900001:69923999',                    '69900-001', '69985-000'),
            ('AL', 'Maceió',            '57000000:57999999',                        '57000001:57099999',                    '57000-001', '57770-000'),
            ('AM', 'Manaus',            '69000000:69299999/69400000:69899999',      '69000001:69099999',                    '69000-001', '69158-000'),
            ('AP', 'Macapá',            '68900000:68999999',                        '68900001:68911999',                    '68900-001', '68950-000'),
            ('BA', 'Salvador',          '40000000:48999999',                        '40000001:42599999',                    '40000-001', '44500-000'),
            ('CE', 'Fortaleza',         '60000000:63999999',                        '60000001:61599999',                    '60000-001', '62750-000'),
            ('DF', 'Brasília',          '70000000:72799999/73000000:73699999',      '70000001:72799999/73000001:73699999',  '70000-001', '70000-001'),
            ('ES', 'Vitória',           '29000000:29999999',                        '29000001:29099999',                    '29000-001', '29700-001'),
            ('GO', 'Goiãnia',           '72800000:72999999/73700000:76799999',      '74000001:74899999',                    '74000-001', '75000-001'),
            ('MA', 'São Luiz',          '65000000:65999999',                        '65000001:65109999',                    '65000-001', '65250-000'),
            ('MG', 'Belo Horizonte',    '30000000:39999999',                        '30000001:31999999',                    '30000-001', '37130-000'),
            ('MS', 'Campo Grande',      '79000000:79999999',                        '79000001:79124999',                    '79000-001', '79300-001'),
            ('MT', 'Cuiabá',            '78000000:78899999',                        '78000001:78099999',                    '78000-001', '78200-000'),
            ('PA', 'Belém',             '66000000:68899999',                        '66000001:66999999',                    '66000-001', '68370-001'),
            ('PB', 'João Pessoa',       '58000000:58999999',                        '58000001:58099999',                    '58000-001', '58930-000'),
            ('PE', 'Recife',            '50000000:56999999',                        '50000001:52999999',                    '50000-001', '53690-000'),
            ('PI', 'Teresina',          '64000000:64999999',                        '64000001:64099999',                    '64000-001', '64235-000'),
            ('PR', 'Curitiba',          '80000000:87999999',                        '80000001:82999999',                    '80000-001', '86800-001'),
            ('RJ', 'Rio de Janeiro',    '20000000:28999999',                        '20000001:23799999',                    '20000-001', '27300-001'),
            ('RN', 'Natal',             '59000000:59999999',                        '59000001:59139999',                    '59000-001', '59780-000'),
            ('RO', 'Porto Velho',       '76800000:76999999',                        '76800001:76834999',                    '76800-001', '76870-001'),
            ('RR', 'Boa Vista',         '69300000:69399999',                        '69300001:69339999',                    '69300-001', '69343-000'),
            ('RS', 'Porto Alegre',      '90000000:99999999',                        '90000001:91999999',                    '90000-001', '97540-001'),
            ('SC', 'Florianópolis',     '88000000:89999999',                        '88000001:88099999',                    '88000-001', '89245-000'),
            ('SE', 'Aracajú',           '49000000:49999999',                        '49000001:49098999',                    '49000-001', '49500-000'),
            ('SP', 'São Paulo',         '01000000:19999999',                        '01000001:05999999/08000000:08499999',  '01000-001', '17800-000'),
            ('TO', 'Palmas',            '77000000:77999999',                        '77000001:77249999',                    '77000-001', '77645-000');";

        Db::getInstance()->execute($sql);

    }

    private function recuperaCadastroEmbalagens() 
    {
        

        $sql = 'SELECT *
                FROM a_correios_embalagens
                WHERE id_shop = '.(int)$this->context->shop->id.'
                Order By cubagem';

        return Db::getInstance()->ExecuteS($sql);
    }

    private function gera_caixas_padrao() 
    {
        

        $sql = "INSERT INTO a_correios_embalagens(id_shop, descricao, comprimento, altura, largura, peso, cubagem, custo, ativo) VALUES
            (".$this->context->shop->id.", 'Caixa 1', 16.00, 2.00,  11.00, 0.20, 352.000000,  0.00, 1),
            (".$this->context->shop->id.", 'Caixa 2', 16.00, 4.00,  11.00, 0.25, 704.000000,  0.00, 1),
            (".$this->context->shop->id.", 'Caixa 3', 16.00, 6.00,  11.00, 0.30, 1056.000000, 0.00, 1),
            (".$this->context->shop->id.", 'Caixa 4', 16.00, 8.00,  11.00, 0.35, 1408.000000, 0.00, 1),
            (".$this->context->shop->id.", 'Caixa 5', 16.00, 10.00, 11.00, 0.40, 1760.000000, 0.00, 1);";

        Db::getInstance()->execute($sql);
    }

    private function recuperaEspCorreios() {

        $sql = 'SELECT *
                FROM a_correios_especificacoes
                WHERE id_shop = '.(int)$this->context->shop->id;

        return Db::getInstance()->ExecuteS($sql);
    }

    private function gera_especificacoes() {

        $sql = "INSERT INTO `a_correios_especificacoes` (`id_shop`, `tabela_offline`, `servico`, `cod_servico`, `cod_administrativo`, `senha`, `comprimento_min`, `comprimento_max`, `largura_min`, `largura_max`, `altura_min`, `altura_max`, `somatoria_dimensoes_max`, `peso_estadual_max`, `peso_nacional_max`, `intervalo_pesos_estadual`, `intervalo_pesos_nacional`, `cubagem_max_isenta`, `cubagem_base_calculo`, `mao_propria_valor`, `aviso_recebimento_valor`, `valor_declarado_percentual`, `valor_declarado_max`, `seguro_automatico_valor`) VALUES
            ('".$this->context->shop->id."', '0', 'E-SEDEX',    '81019', '', '', '16', '105', '11', '105', '2', '105', '200', '15', '15', '0.3/1/2/3/4/5/6/7/8/9/10/11/12/13/14/15',                                                '0.3/1/2/3/4/5/6/7/8/9/10/11/12/13/14/15',                                              '60000',    '6000',     '5.90', '4.30', '1.5', '10000', '50'),
            ('".$this->context->shop->id."', '1', 'PAC',        '04510', '', '', '16', '105', '11', '105', '2', '105', '200', '30', '30', '1/2/3/4/5/6/7/8/9/10/11/12/13/14/15/16/17/18/19/20/21/22/23/24/25/26/27/28/29/30',       '1/2/3/4/5/6/7/8/9/10/11/12/13/14/15/16/17/18/19/20/21/22/23/24/25/26/27/28/29/30',     '0',        '6000',     '5.90', '4.30', '1.5', '10000', '50'),
            ('".$this->context->shop->id."', '0', 'PAC-GF',     '41300', '', '', '16', '150', '11', '150', '2', '150', '300', '30', '30', '1/2/3/4/5/6/7/8/9/10/11/12/13/14/15/16/17/18/19/20/21/22/23/24/25/26/27/28/29/30',       '1/2/3/4/5/6/7/8/9/10/11/12/13/14/15/16/17/18/19/20/21/22/23/24/25/26/27/28/29/30',     '0',        '6000',     '5.90', '4.30', '1.5', '10000', '50'),
            ('".$this->context->shop->id."', '1', 'SEDEX',      '04014', '', '', '16', '105', '11', '105', '2', '105', '200', '30', '30', '0.3/1/2/3/4/5/6/7/8/9/10/11/12/13/14/15/16/17/18/19/20/21/22/23/24/25/26/27/28/29/30',   '0.3/1/2/3/4/5/6/7/8/9/10/11/12/13/14/15/16/17/18/19/20/21/22/23/24/25/26/27/28/29/30', '60000',    '6000',     '5.90', '4.30', '1.5', '10000', '50'),
            ('".$this->context->shop->id."', '0', 'SEDEX 10',   '40215', '', '', '16', '105', '11', '105', '2', '105', '200', '10', '10', '0.3/1/2/3/4/5/6/7/8/9/10',                                                               '0.3/1/2/3/4/5/6/7/8/9/10',                                                             '60000',    '6000',     '5.90', '4.30', '1.5', '10000', '75'),
            ('".$this->context->shop->id."', '0', 'SEDEX 12',   '40169', '', '', '16', '105', '11', '105', '2', '105', '200', '10', '10', '0.3/1/2/3/4/5/6/7/8/9/10',                                                               '0.3/1/2/3/4/5/6/7/8/9/10',                                                             '60000',    '6000',     '5.90', '4.30', '1.5', '10000', '75'),
            ('".$this->context->shop->id."', '0', 'SEDEX HOJE', '40290', '', '', '16', '105', '11', '105', '2', '105', '200', '10', '10', '0.3/1/2/3/4/5/6/7/8/9/10',                                                               '0.3/1/2/3/4/5/6/7/8/9/10',                                                             '60000',    '6000',     '5.90', '4.30', '1.5', '10000', '75');";

        Db::getInstance()->execute($sql);

    }

    private function recuperaServicosCorreios() 
    {
        $sql = 'SELECT
                  a_correios_servicos.*,
                  a_correios_especificacoes.servico
                FROM a_correios_servicos
                  INNER JOIN a_correios_especificacoes
                    ON a_correios_servicos.id_especificacao = a_correios_especificacoes.id
                WHERE a_correios_servicos.id_shop = '.(int)$this->context->shop->id;

        return Db::getInstance()->ExecuteS($sql);
    }

    private function gera_carriers() 
    {

        // Instacia FKcorreiosClass
        $serve = new ACorreiosServe();

        // Recupera dados da tabela de especificacoes dos Correios
        $sql = 'SELECT id, servico
                FROM a_correios_especificacoes
                WHERE id_shop = '.(int)$this->context->shop->id;

        $espCorreios = Db::getInstance()->ExecuteS($sql);

        foreach ($espCorreios as $reg) {

            // Inclui Carrier no Prestashop
            $parm = array(
                'name'                  => $reg['servico'],
                'id_tax_rules_group'    => 0,
                'active'                => false,
                'deleted'               => false,
                'shipping_handling'     => false,
                'range_behavior'        => true,
                'is_module'             => true,
                'shipping_external'     => true,
                'shipping_method'       => 0,
                'external_module_name'  => $this->name,
                'need_range'            => true,
                'url'                   => Configuration::get('ACORREIOS_URL_RASTREIO_CORREIOS'),
                'is_free'               => false,
                'grade'                 => 0,
            );

            $idCarrier = $serve->instalaCarrier($parm);

            // Insere os registros na tabela de servicos
            $dados = array(
                'id_shop'               => $this->context->shop->id,
                'id_especificacao'      => $reg['id'],
                'id_carrier'            => $idCarrier,
                'filtro_regiao_uf'      => 1,
                'grade'                 => 0,
                'ativo'                 => 0,
                'percentual_desconto'   => 0,
                'valor_pedido_desconto' => 0
            );

            $sql = 'INSERT INTO a_correios_servicos (id_shop, id_especificacao, id_carrier, 
            filtro_regiao_uf, grade, ativo, percentual_desconto, valor_pedido_desconto)
            VALUES ('.$dados['id_shop'].', '.$dados['id_especificacao'].', '.$dados['id_carrier'].',
            '.$dados['filtro_regiao_uf'].', '.$dados['grade'].', '.$dados['ativo'].',
            '.$dados['percentual_desconto'].', '.$dados['valor_pedido_desconto'].')';

            Db::getInstance()->Execute($sql);
        }

    }

    private function recuperaRegioesFreteGratis() 
    {

        $sql = 'SELECT *
                FROM a_correios_frete_gratis
                WHERE id_shop = '.(int)$this->context->shop->id;

        return Db::getInstance()->ExecuteS($sql);
    }

    private function recuperaTranspFreteGratis() 
    {

        $transportadoras = array();

        // Servicos dos Correios
        $sql = "SELECT
                    a_correios_servicos.id_carrier,
                    a_correios_especificacoes.servico AS transportadora
                FROM a_correios_servicos
                  INNER JOIN a_correios_especificacoes
                    ON a_correios_servicos.id_especificacao = a_correios_especificacoes.id
                WHERE a_correios_servicos.id_shop = ".(int)$this->context->shop->id;

        $transpCorreios = Db::getInstance()->ExecuteS($sql);

        // Recupera transportadoras dos Complementos
        $transpComplementos = array();

        $complementos = $this->recuperaComplementosFrete();

        foreach ($complementos as $reg) {

            // Cria path da classe do Complemento
            $path = _PS_MODULE_DIR_.$reg['modulo'].'/models/'.strtoupper(substr($reg['modulo'],0,2)).substr($reg['modulo'],2).'FreteClass.php';

            // Verifica se a classe existe
            if (file_exists($path)) {

                // Include da classe
                include_once($path);

                // Instancia a classe de frete do complemento
                $funcao = strtoupper(substr($reg['modulo'],0,2)).substr($reg['modulo'],2).'FreteClass';
                $freteClass = new $funcao;

                $transp = $freteClass->recuperaTranspFreteGratis();

                // Merge dos array
                $transpComplementos = array_merge($transpComplementos, $transp);
            }
        }

        // Merge dos arrays dos Correios e dos Complementos
        $transportadoras = array_merge($transpCorreios, $transpComplementos);

        return $transportadoras;
    }

    private function gera_frete_gratis() 
    {
        
        $sql = 'SELECT id, servico FROM a_correios_especificacoes WHERE id_shop='.$this->context->shop->id;

        $faixas = Db::getInstance()->ExecuteS($sql);

        foreach($faixas as $fx)
        {
            $dados = array(
                'id_shop'           => $this->context->shop->id,
                'nome_regiao'       => $fx['servico'],
                'filtro_regiao_uf'  => 1,
                'valor_pedido'      => 0,
                'ativo'             => 1,
                'id_especificacao'  =>$fx['id']
            );

            Db::getInstance()->Execute('INSERT INTO a_correios_frete_gratis 
                (id_shop, nome_regiao, filtro_regiao_uf, valor_pedido, ativo, id_especificacao) VALUES
                ('.$dados['id_shop'].', "'.$dados['nome_regiao'].'", '.$dados['filtro_regiao_uf'].',
                '.$dados['valor_pedido'].', '.$dados['ativo'].', '.$dados['id_especificacao'].')');
        }
        return true;
    }

    private function recuperaEspCorreiosTabOffline() 
    {

        $sql = "SELECT id, servico
                FROM a_correios_especificacoes
                WHERE tabela_offline = '1' AND id_shop = ".(int)$this->context->shop->id;

        return Db::getInstance()->ExecuteS($sql);
    }

    private function recuperaTabOffline($minhaCidade = false) 
    {

        if ($minhaCidade) {
            $sql = "SELECT *
                    FROM a_correios_tabelas_offline
                    WHERE minha_cidade = '1' AND id_shop = ".(int)$this->context->shop->id;
        }else {
            $sql = "SELECT
                        a_correios_tabelas_offline.*,
                        a_correios_cadastro_cep.estado,
                        a_correios_cadastro_cep.capital
                    FROM a_correios_tabelas_offline
                        INNER JOIN a_correios_cadastro_cep
                            ON a_correios_tabelas_offline.id_cadastro_cep = a_correios_cadastro_cep.id
                    WHERE a_correios_tabelas_offline.id_shop = ".(int)$this->context->shop->id;
        }

        return Db::getInstance()->ExecuteS($sql);
    }

    private function gera_dados_offline() 
    {

        $espCorreios = $this->recuperaEspCorreiosTabOffline();

        $cadCep = $this->recuperaCadastroCep();

        foreach ($espCorreios as $especificacao) 
        {

            $dados = array(
                'id_shop'           => $this->context->shop->id,
                'id_especificacao'  => $especificacao['id'],
                'id_cadastro_cep'   => '0',
                'minha_cidade'      => '1'
            );
            $sql = 'INSERT INTO a_correios_tabelas_offline (id_shop, id_especificacao, 
            id_cadastro_cep, minha_cidade) VALUES 
            ('.$dados['id_shop'].', '.$dados['id_especificacao'].', '.$dados['id_cadastro_cep'].','.$dados['minha_cidade'].')';
            
            Db::getInstance()->Execute($sql);

            foreach ($cadCep as $estado) {

                $dados = array(
                    'id_shop'           => $this->context->shop->id,
                    'id_especificacao'  => $especificacao['id'],
                    'id_cadastro_cep'   => $estado['id'],
                    'minha_cidade'      => '0'
                );

               $sql = 'INSERT INTO a_correios_tabelas_offline (id_shop, id_especificacao, 
            id_cadastro_cep, minha_cidade) VALUES 
            ('.$dados['id_shop'].', '.$dados['id_especificacao'].', '.$dados['id_cadastro_cep'].','.$dados['minha_cidade'].')';
            
            Db::getInstance()->Execute($sql);
            }
        }

    }


    private function verificaModuloInstalado($modulo) 
    {

        $sql = "SELECT *
                FROM "._DB_PREFIX_."module
                WHERE name = '".$modulo."'";

        $moduloInstalado = Db::getInstance()->getRow($sql);

        if ($moduloInstalado) {
            return true;
        }

        return false;
    }

    private function excluiCache() 
    {
        Db::getInstance()->Execute('TRUNCATE TABLE a_correios_cache;');
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

        $this->smarty->assign(array(
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
    }

    private function gravaDadosSmartyRastreio() 
    {

        $this->smarty->assign(array(
            'acorreios_rastreio' => array(
                'urlFuncoesRastreio'   => Configuration::get('ACORREIOS_URL_FUNCOES_RASTREIO'),
            )
        ));
    }

    private function processaSimulador($origem, $bloco, $params) 
    {
       # print_r($params);exit;
        if(version_compare(_PS_VERSION_, '1.7', '<') ==1)
        {
            //1.6

            if($origem == 'produto')
            {
                #print_r($params);exit;
                if(Configuration::get('ACORREIOS_BLOCO_PRODUTO') != 1 or !isset($params['product']) or 
                    $params['product']->is_virtual == 1)
                {
                    return false;
                }
            }
            else
            {

                $virtual = true;

                foreach ($this->context->cart->getProducts() as $prod) 
                {
                    if ($prod['is_virtual'] == 0) {
                        $virtual = false;
                    }
                }

                if(Configuration::get('ACORREIOS_BLOCO_CARRINHO') != 1 or !$params['cart'] or $virtual == true) 
                {
                    return false;
                }
           }

        }
        else
        {
            if ($origem == 'produto') 
            {   
                if (Configuration::get('ACORREIOS_BLOCO_PRODUTO') != 1 or !isset($params['product']) or 
                    $params['product']['is_virtual'] == 1) 
                {
                    return false;
                }

            }
            else
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

        }
        

        $msgStatus = 'Aguardando CEP';
        $transpCorreios = array();
        $transpComplementos = array();
        $transportadoras = array();
              
        if ($origem == 'produto' and 
            (Tools::isSubmit('btnSubmit') or Tools::getValue('origem') == 'adicCarrinho' or !empty($_POST['acorreios_cep'])) or
            $origem == 'carrinho' and (Tools::isSubmit('btnSubmit') or $this->context->customer->isLogged() or isset($this->context->cookie->acorreios_cep_destino))) {


           
            $dadosBasicos = $this->recuperaDadosBasicosSimulador($origem, $params);


            if (!$dadosBasicos['status']) {
                $msgStatus = $dadosBasicos['msgErro'];
            }else {
                
                $freteClass = new ACorreiosFrete();

                if ($freteClass->calculaFreteSimulador($origem, $dadosBasicos, $params)) {
                    $transpCorreios = $freteClass->getTransportadoras();
                    
                }

                $transportadoras = $transpCorreios;
                
                if (count($transportadoras) > 0) {
                    
                    usort($transportadoras, array($this, 'ordenaValor'));

                    $msgStatus = 'Frete Calculado';
                }else {
                    $msgStatus = $this->l('Não existem transportadoras disponíveis para o CEP de Destino. Favor entrar em contato com o Atendimento ao Cliente');
                }
            }
        }

        $this->resultado_simulador = $transportadoras;

        if ($origem == 'produto') {
            $product =(object) $params['product'];

            $this->gravaDadosSmartyFrete($msgStatus, $product->id, $transportadoras, false);
        }else {
            $this->gravaDadosSmartyFrete($msgStatus, null, $transportadoras, false);
        }

        return true;
    }

    private function recuperaDadosBasicosSimulador($origem, $params) 
    {
        global $smarty;

        $cepOrigem = trim(preg_replace("/[^0-9]/", "", Configuration::get('ACORREIOS_MEU_CEP')));
        $ufOrigem = '';
        $cepDestino = '';
        $ufDestino = '';
        $valorPedido = 0;
        $freteGratisValor = false;
        $transpFreteGratisValor = 0;

        if (Tools::getValue('origem') == 'adicCarrinho') 
        {
            $cepDestino = Tools::getValue('cep');
        }else {
            if (Tools::isSubmit('btnSubmit') or !empty(Tools::getValue('acorreios_cep'))){

                $cepDestino = Tools::getValue('acorreios_cep');

            }else {
                if ($origem == 'carrinho') 
                {
                   
                    if ($this->context->customer->isLogged()) 
                    {
                        #['tpl_vars']['cart']['customer']['addresses']
                        $enderecos = $this->context->customer->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));
                       
                        foreach($enderecos as $endereco)
                        {
                            if(!empty($endereco['postcode']))
                            {
                                $cepDestino = $endereco['postcode'];
                            }
                        }

                        if(!empty($params['checkout']))
                        {
                            //Solicitação veio já do checkout
                            //Usar id_address_delivery do carrinho
                            foreach($enderecos as $endereco)
                            {
                                if($endereco['id_address'] == $this->context->cart->id_address_delivery)
                                {
                                    $cepDestino = $endereco['postcode'];
                                }
                            }
                        }
                        
                    }else {
                        // Recupera CEP do cookie
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

    
    private function gera_tabelas_db() 
    {

        $db = Db::getInstance();

        $sql = 'CREATE TABLE IF NOT EXISTS `a_correios_cadastro_cep` (
                `id`                int(10)     NOT NULL AUTO_INCREMENT,
                `estado`            varchar(2),
                `capital`           varchar(50),
                `cep_estado`        varchar(150),
                `cep_capital`       varchar(150),
                `cep_base_capital`  varchar(9),
                `cep_base_interior` varchar(9),
                PRIMARY KEY  (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS `a_correios_embalagens` (
                `id`            int(10)         NOT NULL AUTO_INCREMENT,
                `id_shop`       int(10),
                `descricao`     varchar(50),
                `comprimento`   decimal(20,2),
                `altura`        decimal(20,2),
                `largura`       decimal(20,2),
                `peso`          decimal(20,2),
                `cubagem`       decimal(20,6),
                `custo`         decimal(20,2),
                `ativo`         tinyint(1),
                PRIMARY KEY (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        // Cria tabela com as Especificacoes dos Correios
        $sql = 'CREATE TABLE IF NOT EXISTS `a_correios_especificacoes` (
                `id`                            int(10)         NOT NULL AUTO_INCREMENT,
                `id_shop`                       int(10),
                `tabela_offline`                tinyint(1),
                `servico`                       varchar(50),
                `cod_servico`                   varchar(50),
                `cod_administrativo`            varchar(50),
                `senha`                         varchar(10),
                `comprimento_min`               decimal(20,2),
                `comprimento_max`               decimal(20,2),
                `largura_min`                   decimal(20,2),
                `largura_max`                   decimal(20,2),
                `altura_min`                    decimal(20,2),
                `altura_max`                    decimal(20,2),
                `somatoria_dimensoes_max`       decimal(20,2),
                `peso_estadual_max`             decimal(20,2),
                `peso_nacional_max`             decimal(20,2),
                `intervalo_pesos_estadual`      varchar(250),
                `intervalo_pesos_nacional`      varchar(250),
                `cubagem_max_isenta`            decimal(20,5),
                `cubagem_base_calculo`          decimal(20,5),
                `mao_propria_valor`             decimal(20,2),
                `aviso_recebimento_valor`       decimal(20,2),
                `valor_declarado_percentual`    decimal(20,2),
                `valor_declarado_max`           decimal(20,2),
                `seguro_automatico_valor`       decimal(20,2),
                PRIMARY KEY (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        // Cria tabela com os servicos dos correios
        $sql = 'CREATE TABLE IF NOT EXISTS `a_correios_servicos` (
                `id`                    int(10)     NOT NULL AUTO_INCREMENT,
                `id_shop`               int(10),
                `id_especificacao`      int(10),
                `id_carrier`            int(10),
                `filtro_regiao_uf`      int(10),
                `regiao_uf`             varchar(100),
                `regiao_cep`            text,
                `regiao_cep_excluido`   text,
                `grade`                 int(10),
                `percentual_desconto`   decimal(20,2),
                `valor_pedido_desconto` decimal(20,2),
                `ativo`                 tinyint(1),
                PRIMARY KEY (`id`),
                INDEX (`id_carrier`, `id_shop`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        // Cria tabela com as configuracoes do frete gratis
        $sql = 'CREATE TABLE IF NOT EXISTS `a_correios_frete_gratis` (
                `id`                    int(10)         NOT NULL AUTO_INCREMENT,
                `id_shop`               int(10),
                `id_carrier`            int(10),
                `id_especificacao`            int(10),
                `nome_regiao`           varchar(100),
                `filtro_regiao_uf`      int(10),
                `regiao_uf`             varchar(100),
                `regiao_cep`            text,
                `regiao_cep_excluido`   text,
                `valor_pedido`          decimal(20,2),
                `id_produtos`           text,
                `ativo`                 tinyint(1),
                INDEX (`id_carrier`),
                PRIMARY KEY (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        // Cria a tabela de precos offline dos correios
        $sql = 'CREATE TABLE IF NOT EXISTS `a_correios_tabelas_offline` (
                `id`                        int(10)     NOT NULL AUTO_INCREMENT,
                `id_shop`                   int(10),
                `id_especificacao`          int(10),
                `id_cadastro_cep`           int(10),
                `prazo_entrega_cidade`      int(10),
                `prazo_entrega_capital`     int(10),
                `prazo_entrega_interior`    int(10),
                `tabela_cidade`             text,
                `tabela_capital`            text,
                `tabela_interior`           text,
                `minha_cidade`              tinyint(1),
                INDEX (`id_especificacao`),
                PRIMARY KEY  (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);


        // Cria a tabela de cache
        $sql = 'CREATE TABLE IF NOT EXISTS `a_correios_cache` (
                `id`            int(10)     NOT NULL AUTO_INCREMENT,
                `hash`          varchar(32),
                `valor_frete`   decimal(20,2),
                `prazo_entrega` int(10),
                `msg_correios`  text,
                INDEX (`hash`),
                PRIMARY KEY  (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        return true;

    }

    private function excluir_tabelas_db() 
    {

        $sql = "DROP TABLE IF EXISTS `a_correios_cadastro_cep`;";
        Db::getInstance()->execute($sql);

        $sql = "DROP TABLE IF EXISTS `a_correios_embalagens`;";
        Db::getInstance()->execute($sql);

        $sql = "DROP TABLE IF EXISTS `a_correios_especificacoes`;";
        Db::getInstance()->execute($sql);

        $sql = "DROP TABLE IF EXISTS `a_correios_servicos`;";
        Db::getInstance()->execute($sql);

        $sql = "DROP TABLE IF EXISTS `a_correios_frete_gratis`;";
        Db::getInstance()->execute($sql);

        $sql = "DROP TABLE IF EXISTS `a_correios_tabelas_offline`;";
        Db::getInstance()->execute($sql);

        $sql = "DROP TABLE IF EXISTS `a_correios_cache`;";
        Db::getInstance()->execute($sql);

    }

    static function ordenaValor($a, $b) 
    {

        if ($a['valorFrete'] == $b['valorFrete']) {
            return 0;
        }
        return ($a['valorFrete'] < $b['valorFrete']) ? -1 : 1;
    }

    public function getOrderShippingCost($params, $shipping_cost) 
    {
        
        global $smarty;
        $freteClass = new ACorreiosFrete();

        if (!$freteClass->calculaFretePS($params, $this->id_carrier)) 
        {
            return false;
        }

        $frete = $freteClass->getFreteCarrier();

        $this->prazoEntrega[$this->id_carrier] = $frete['prazoEntrega'];

        $this->context->smarty->assign(array('entrega_'.$this->id_carrier => $frete['prazoEntrega'] ));

        return (float)$frete['valorFrete'];
    }

    function refazDeliveryOptionList($delivery_option_list)
    {
        $params['cart'] = $this->context->cart;
        $params['smarty'] = $this->context->smarty;
        $params['checkout'] = 1;
        global $smarty;
        $params['addresses'] = $this->context->customer->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));

        if (!$this->processaSimulador('carrinho', '', $params)) 
        {
             return false;
        }

        $fretes = $this->resultado_simulador;


        foreach ($delivery_option_list as $i => $id_address) 
        {

            foreach ($id_address as $j => $key) 
            {

                foreach ($key['carrier_list'] as $k => $calist) 
                {

                    foreach($calist['instance']->delay as $l => $prazo)
                    {

                        foreach($fretes as $frete)
                        {
                            if($frete['nomeTransportadora'] == $calist['instance']->name )
                            {
                                //Muda prazo

                                switch($frete['prazoEntrega'])
                                {
                                    case 0:
                                        $msg='Entrega no mesmo dia';
                                    break;

                                    case 1:
                                        $msg = 'Entrega em até 1 dia útil';
                                    break;

                                    default:
                                        $msg = 'Entrega em '. $frete['prazoEntrega'] . ' dias úteis';
                                    break;
                                }

                                $delivery_option_list[$i][$j]['carrier_list'][$k]['instance']->delay[$l] = $msg;
                            }
                        }

                        
                    }
                }
            }
        }

        return $delivery_option_list;
    }

     public function hookdisplayBeforeCarrier($params) 
     {


        if (!isset($this->context->smarty->tpl_vars['delivery_option_list'])) {
            return;
        }
        global $smarty;
        $delivery_option_list = $this->context->smarty->tpl_vars['delivery_option_list'];
        

        foreach ($delivery_option_list->value as $i => $id_address) {

            foreach ($id_address as $j => $key) {

                foreach ($key['carrier_list'] as $k => $calist) 
                {
                   
                    foreach($calist['instance']->delay as $l => $prazo)
                    {

                        if (isset($this->prazoEntrega[$calist['instance']->id]) 
                            && is_numeric($this->prazoEntrega[$calist['instance']->id])) 
                        {
                            if ($this->prazoEntrega[$calist['instance']->id] == 0) 
                            {
                                $msg = $this->l('Entrega no mesmo dia');
                            }
                            else
                            {
                                if ($this->prazoEntrega[$calist['instance']->id] > 1) 
                                {

                                    $msg = 'Entrega em até '.$this->prazoEntrega[$calist['instance']->id].$this->l(' dias úteis');
                                }else {
                                    $msg = 'Entrega em '.$this->prazoEntrega[$calist['instance']->id].$this->l(' dia útil');
                                }
                            }

                            $delivery_option_list->value[$i][$j]['carrier_list'][$k]['instance']->delay[$l] = $msg;
                        
                            
                        }
                        
                    }

                    /*if (isset($this->prazoEntrega[$id_carrier['instance']->id])) {

                        if (i) 
                        {
                           

                             echo '<PRE>';
                        print_r($key);
                        echo '</pre>';exit;

                            
                        }else {
                            $msg = $this->prazoEntrega[$id_carrier['instance']->id];
                        }

                       
                        echo $id_carrier['instance']->delay[$this->context->cart->id_lang];exit;
                        $id_carrier['instance']->delay[$this->context->cart->id_lang] = $msg;
                        }*/ 
                    
                }
            }
        }
         
    }

}