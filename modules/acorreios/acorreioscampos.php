<?php

if (!defined('_PS_VERSION_')) exit;

include_once(dirname(__FILE__).'/models/ACorreiosServe.php');
 
class ACorreiosCampos extends Module 
{
    public function form_geral() 
    {

        $servicos[] = array(
            'id' => 'mao_propria',
            'name' => 'Mão Própria',
            'val' => 'on',
        );

         $servicos[] = array(
            'id' => 'valor_declarado',
            'name' => 'Valor Declarado',
            'val' => 'on',
        );

          $servicos[] = array(
            'id' => 'aviso_recebimento',
            'name' => 'Aviso de Recebimento',
            'val' => 'on',
        );

        $fields_form = array(
            $this->text ('acorreios_serial', 'Código de Licença','',false,false,'Informe seu código de licença/número serial.', ''),
           $this->separador('Parâmetros de Operação'),
           $this->field_onOff('acorreios_bloco_carrinho','Exibir simulador no carrinho de compras?', 'Marque SIM para que seja exibido um simulador de cálculo de frete na página do carrinho de compras.'),
           $this->field_onOff('acorreios_bloco_produto','Exibir simulador a página do produto?','Marque SIM para que seja exibido um simulador de cálculo de frete na página do carrinho de cada produto.'),
            $this->text('acorreios_meu_cep', 'CEP de Origem', 'input-large', false, 'Informe o CEP de onde você postará os seus pacotes', true),
            $this->textarea ('acorreios_cep_cidade', 'Faixa de CEP Local','',false,false,'Informe as faixas de CEP da sua cidade.', true),
            $this->text ('acorreios_tempo_preparacao', 'Prazo Adicional', 'input-large', false, 'Informe uma quantidade de dias para sempre ser adicionada ao prazo de entrega.', true),
            $this->separador('Serviços Adicionais'),
            $this->paragrafo('Ao ativar um serviço adicional, poderão haver cobranças extras. Por favor, consulte a tabela de tarifas dos Correios.'),
            $this->checkbox('acorreios', 'Selecionar Serviços', $servicos),
        );
        return $this->getFormSection($fields_form, 'Configurações Gerais');
    }

    public function get_campos_geral()
    {
        return array(
            'acorreios_bloco_carrinho',
            'acorreios_bloco_produto',
            'acorreios_meu_cep',
            'acorreios_cep_cidade',
            'acorreios_tempo_preparacao',
            'acorreios_servicos',
            'acorreios_mao_propria',
            'acorreios_aviso_recebimento',
            'acorreios_valor_declarado',
            );
    }


    public function form_pac() 
    {

        $id_servico = $this->get_id_servico('PAC');

        $fields_form = array(
           $this->separador('Configurações do serviço de PAC'),
           $this->field_onOff('acorreios_servicos_ativo_'.$id_servico,'Ativar', 'Marque SIM para exibir esta modalidade no seu site.'),

            $this->textarea('acorreios_servico_desativar_faixas_'.$id_servico, 'Faixas de CEP Excluídas','',false,false,'Informe as faixas de CEP para as quais este serviço não será oferecido.', false),

            $this->separador('Dados do Contrato (opcional)'),

            $this->text ('acorreios_espec_codigos_'.$id_servico, 'Código de Serviço', 'input-large', false, '', true),
            $this->text ('acorreios_espec_codigoa_'.$id_servico, 'Código Administrativo', 'input-large', false, '', false),
            $this->text ('acorreios_espec_senha_'.$id_servico, 'Senha', 'input-large', false, '', false),

            $this->separador('Opções avançadas (opcional)'),

            $this->paragrafo('DIMENSÕES E PESOS'),
            $this->text ('acorreios_espec_compmin_'.$id_servico, 'Comprimento Mínimo', 'input-large', false, ''),
            $this->text ('acorreios_espec_compmax_'.$id_servico, 'Comprimento Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramin_'.$id_servico, 'Largura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramax_'.$id_servico, 'Largura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamin_'.$id_servico, 'Altura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamax_'.$id_servico, 'Altura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_dimensoesmax_'.$id_servico, 'Perímetro Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesoemax_'.$id_servico, 'Peso Máximo - Estadual', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesonmax_'.$id_servico, 'Peso Máximo - Nacional', 'input-large', false, ''),
            $this->textarea('acorreios_espec_intervaloe_'.$id_servico, 'Intervalos de Pesos - Estadual','',false,false,'', false),
            $this->textarea('acorreios_espec_intervalon_'.$id_servico, 'Intervalos de Pesos - Nacional','',false,false,'', false),
            $this->paragrafo('VALORES PARA CÁLCULO OFFLINE'),
            $this->text ('acorreios_espec_cubagemi_'.$id_servico, 'Cubagem Máxima Isenta', 'input-large', false, ''),
            $this->text ('acorreios_espec_cubagemb_'.$id_servico, 'Cubagem para base do cálculo', 'input-large', false, ''),
            $this->text ('acorreios_espec_maopropria_'.$id_servico, 'Tarifa Mão Própria', 'input-large', false, ''),
            $this->text ('acorreios_espec_aviso_'.$id_servico, 'Tarifa Aviso de Recebimento', 'input-large', false, ''),
            $this->text ('acorreios_espec_valord_'.$id_servico, 'Valor Declarado (%)', 'input-large', false, ''),
            $this->text ('acorreios_espec_valordmax_'.$id_servico, 'Máximo valor declarado', 'input-large', false, ''),
            $this->text ('acorreios_espec_seguro_'.$id_servico, 'Seguro Automático', 'input-large', false, ''),

        );
        return $this->getFormSection($fields_form, 'PAC');

    }

    public function form_offline()
    {
        $fields_form = array(
           $this->separador('Consulta de Preços Offline'),
           $this->paragrafo('Para tornar mais rápida a consulta de fretes, este módulo permite que você armazene localmente os dados de preços e prazos do sistema dos Correios. Dessa forma, caso o sistema dos Correios fique fora do ar, seus clientes poderão continuar comprando normalmente no seu site. Recomendamos que você atualize estes dados sempre que houver algum anúncio de mudança de tarifas da parte dos correios.'),
           
           $this->html('<a href="#" onclick="javascript:acorreios_gera_dados_offline(2)">GERAR PAC</A>'),

        );
        return $this->getFormSection($fields_form, 'Dados Off-line');

    }

    public function form_debug()
    {
        $fields_form = array(
           $this->separador('Informações de Depuração'),
           $this->paragrafo('Encontre aqui informações que poderão lhe ajudar caso este módulo não esteja funcionando como deveria.'),

        );
        return $this->getFormSection($fields_form, 'Debug');

    }

    public function form_pgf() 
    {

        $id_servico = $this->get_id_servico('PAC-GF');

        $fields_form = array(
           $this->separador('Configurações do serviço de PAC Grandes Formatos'),
           $this->field_onOff('acorreios_servicos_ativo_'.$id_servico,'Ativar', 'Marque SIM para exibir esta modalidade no seu site.'),

            $this->textarea('acorreios_servico_desativar_faixas_'.$id_servico, 'Faixas de CEP Excluídas','',false,false,'Informe as faixas de CEP para as quais este serviço não será oferecido.', false),

            $this->separador('Dados do Contrato (opcional)'),

            $this->text ('acorreios_espec_codigos_'.$id_servico, 'Código de Serviço', 'input-large', false, '', true),
            $this->text ('acorreios_espec_codigoa_'.$id_servico, 'Código Administrativo', 'input-large', false, '', false),
            $this->text ('acorreios_espec_senha_'.$id_servico, 'Senha', 'input-large', false, '', false),

            $this->separador('Opções avançadas (opcional)'),

            $this->paragrafo('DIMENSÕES E PESOS'),
            $this->text ('acorreios_espec_compmin_'.$id_servico, 'Comprimento Mínimo', 'input-large', false, ''),
            $this->text ('acorreios_espec_compmax_'.$id_servico, 'Comprimento Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramin_'.$id_servico, 'Largura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramax_'.$id_servico, 'Largura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamin_'.$id_servico, 'Altura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamax_'.$id_servico, 'Altura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_dimensoesmax_'.$id_servico, 'Perímetro Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesoemax_'.$id_servico, 'Peso Máximo - Estadual', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesonmax_'.$id_servico, 'Peso Máximo - Nacional', 'input-large', false, ''),
            $this->textarea('acorreios_espec_intervaloe_'.$id_servico, 'Intervalos de Pesos - Estadual','',false,false,'', false),
            $this->textarea('acorreios_espec_intervalon_'.$id_servico, 'Intervalos de Pesos - Nacional','',false,false,'', false),
            $this->paragrafo('VALORES PARA CÁLCULO OFFLINE'),
            $this->text ('acorreios_espec_cubagemi_'.$id_servico, 'Cubagem Máxima Isenta', 'input-large', false, ''),
            $this->text ('acorreios_espec_cubagemb_'.$id_servico, 'Cubagem para base do cálculo', 'input-large', false, ''),
            $this->text ('acorreios_espec_maopropria_'.$id_servico, 'Tarifa Mão Própria', 'input-large', false, ''),
            $this->text ('acorreios_espec_aviso_'.$id_servico, 'Tarifa Aviso de Recebimento', 'input-large', false, ''),
            $this->text ('acorreios_espec_valord_'.$id_servico, 'Valor Declarado (%)', 'input-large', false, ''),
            $this->text ('acorreios_espec_valordmax_'.$id_servico, 'Máximo valor declarado', 'input-large', false, ''),
            $this->text ('acorreios_espec_seguro_'.$id_servico, 'Seguro Automático', 'input-large', false, ''),

        );
        return $this->getFormSection($fields_form, 'PAC GF');
    }


    public function form_sedex12() 
    {
        $id_servico = $this->get_id_servico('SEDEX 12');

        $fields_form = array(
           $this->separador('Configurações do serviço de Sedex 12'),
           $this->field_onOff('acorreios_servicos_ativo_'.$id_servico,'Ativar', 'Marque SIM para exibir esta modalidade no seu site.'),

            $this->textarea('acorreios_servico_desativar_faixas_'.$id_servico, 'Faixas de CEP Excluídas','',false,false,'Informe as faixas de CEP para as quais este serviço não será oferecido.', false),

            $this->separador('Dados do Contrato (opcional)'),

            $this->text ('acorreios_espec_codigos_'.$id_servico, 'Código de Serviço', 'input-large', false, '', true),
            $this->text ('acorreios_espec_codigoa_'.$id_servico, 'Código Administrativo', 'input-large', false, '', false),
            $this->text ('acorreios_espec_senha_'.$id_servico, 'Senha', 'input-large', false, '', false),

            $this->separador('Opções avançadas (opcional)'),

            $this->paragrafo('DIMENSÕES E PESOS'),
            $this->text ('acorreios_espec_compmin_'.$id_servico, 'Comprimento Mínimo', 'input-large', false, ''),
            $this->text ('acorreios_espec_compmax_'.$id_servico, 'Comprimento Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramin_'.$id_servico, 'Largura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramax_'.$id_servico, 'Largura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamin_'.$id_servico, 'Altura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamax_'.$id_servico, 'Altura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_dimensoesmax_'.$id_servico, 'Perímetro Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesoemax_'.$id_servico, 'Peso Máximo - Estadual', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesonmax_'.$id_servico, 'Peso Máximo - Nacional', 'input-large', false, ''),
            $this->textarea('acorreios_espec_intervaloe_'.$id_servico, 'Intervalos de Pesos - Estadual','',false,false,'', false),
            $this->textarea('acorreios_espec_intervalon_'.$id_servico, 'Intervalos de Pesos - Nacional','',false,false,'', false),
            $this->paragrafo('VALORES PARA CÁLCULO OFFLINE'),
            $this->text ('acorreios_espec_cubagemi_'.$id_servico, 'Cubagem Máxima Isenta', 'input-large', false, ''),
            $this->text ('acorreios_espec_cubagemb_'.$id_servico, 'Cubagem para base do cálculo', 'input-large', false, ''),
            $this->text ('acorreios_espec_maopropria_'.$id_servico, 'Tarifa Mão Própria', 'input-large', false, ''),
            $this->text ('acorreios_espec_aviso_'.$id_servico, 'Tarifa Aviso de Recebimento', 'input-large', false, ''),
            $this->text ('acorreios_espec_valord_'.$id_servico, 'Valor Declarado (%)', 'input-large', false, ''),
            $this->text ('acorreios_espec_valordmax_'.$id_servico, 'Máximo valor declarado', 'input-large', false, ''),
            $this->text ('acorreios_espec_seguro_'.$id_servico, 'Seguro Automático', 'input-large', false, ''),

        );
        return $this->getFormSection($fields_form, 'Sedex 12');
    }


    public function form_sedexhoje() 
    {
        $id_servico = $this->get_id_servico('SEDEX HOJE');

        $fields_form = array(
           $this->separador('Configurações do serviço de Sedex Hoje'),
           $this->field_onOff('acorreios_servicos_ativo_'.$id_servico,'Ativar', 'Marque SIM para exibir esta modalidade no seu site.'),

            $this->textarea('acorreios_servico_desativar_faixas_'.$id_servico, 'Faixas de CEP Excluídas','',false,false,'Informe as faixas de CEP para as quais este serviço não será oferecido.', false),

            $this->separador('Dados do Contrato (opcional)'),

            $this->text ('acorreios_espec_codigos_'.$id_servico, 'Código de Serviço', 'input-large', false, '', true),
            $this->text ('acorreios_espec_codigoa_'.$id_servico, 'Código Administrativo', 'input-large', false, '', false),
            $this->text ('acorreios_espec_senha_'.$id_servico, 'Senha', 'input-large', false, '', false),

            $this->separador('Opções avançadas (opcional)'),

            $this->paragrafo('DIMENSÕES E PESOS'),
            $this->text ('acorreios_espec_compmin_'.$id_servico, 'Comprimento Mínimo', 'input-large', false, ''),
            $this->text ('acorreios_espec_compmax_'.$id_servico, 'Comprimento Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramin_'.$id_servico, 'Largura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramax_'.$id_servico, 'Largura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamin_'.$id_servico, 'Altura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamax_'.$id_servico, 'Altura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_dimensoesmax_'.$id_servico, 'Perímetro Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesoemax_'.$id_servico, 'Peso Máximo - Estadual', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesonmax_'.$id_servico, 'Peso Máximo - Nacional', 'input-large', false, ''),
            $this->textarea('acorreios_espec_intervaloe_'.$id_servico, 'Intervalos de Pesos - Estadual','',false,false,'', false),
            $this->textarea('acorreios_espec_intervalon_'.$id_servico, 'Intervalos de Pesos - Nacional','',false,false,'', false),
            $this->paragrafo('VALORES PARA CÁLCULO OFFLINE'),
            $this->text ('acorreios_espec_cubagemi_'.$id_servico, 'Cubagem Máxima Isenta', 'input-large', false, ''),
            $this->text ('acorreios_espec_cubagemb_'.$id_servico, 'Cubagem para base do cálculo', 'input-large', false, ''),
            $this->text ('acorreios_espec_maopropria_'.$id_servico, 'Tarifa Mão Própria', 'input-large', false, ''),
            $this->text ('acorreios_espec_aviso_'.$id_servico, 'Tarifa Aviso de Recebimento', 'input-large', false, ''),
            $this->text ('acorreios_espec_valord_'.$id_servico, 'Valor Declarado (%)', 'input-large', false, ''),
            $this->text ('acorreios_espec_valordmax_'.$id_servico, 'Máximo valor declarado', 'input-large', false, ''),
            $this->text ('acorreios_espec_seguro_'.$id_servico, 'Seguro Automático', 'input-large', false, ''),

        );
        return $this->getFormSection($fields_form, 'Sedex Hoje');

    }

    public function form_embalagens()
    {
        $embalagens = $this->get_embalagens();

        $total_embalagens = 0;

        foreach($embalagens as $embalagem)
        {
            $fields_form[] = $this->separador('Caixa #'.$embalagem['id']);
            $fields_form[] = $this->text ('acorreios_caixa_nome_'.$embalagem['id'], 'Nome', 'input-large', false, '');
            $fields_form[] = $this->text ('acorreios_caixa_largura_'.$embalagem['id'], 'Largura', 'input-large', false, '');
            $fields_form[] = $this->text ('acorreios_caixa_comprimento_'.$embalagem['id'], 'Comprimento', 'input-large', false, '');
            $fields_form[] = $this->text ('acorreios_caixa_altura_'.$embalagem['id'], 'Altura', 'input-large', false, '');
            $fields_form[] = $this->text ('acorreios_caixa_peso_'.$embalagem['id'], 'Peso', 'input-large', false, '');
            $fields_form[] = $this->text ('acorreios_caixa_preco_'.$embalagem['id'], 'Preço de Custo', 'input-large', false, '');
            $fields_form[] =  $this->field_onOff('acorreios_caixa_ativo_'.$embalagem['id'],'Ativo', 'Marque SIM para usar essa caixa.');
            $fields_form[] =  $this->field_onOff('acorreios_caixa_excluir_'.$embalagem['id'],'Excluir', 'Marque SIM para excluir essa caixa ao salvar.');
          
            $total_embalagens=$embalagem['id'];
        }

        $fields_form[] = $this->separador('Adicionar Nova Caixa (opcional)');

        $fields_form[] = $this->separador('Nova Caixa');
            $fields_form[] = $this->text ('acorreios_caixa_nome_'.'nova', 'Nome', 'input-large', false, '');
            $fields_form[] = $this->text ('acorreios_caixa_largura_'.'nova', 'Largura', 'input-large', false, '');
            $fields_form[] = $this->text ('acorreios_caixa_comprimento_'.'nova', 'Comprimento', 'input-large', false, '');
            $fields_form[] = $this->text ('acorreios_caixa_altura_'.'nova', 'Altura', 'input-large', false, '');
            $fields_form[] = $this->text ('acorreios_caixa_peso_'.'nova', 'Peso', 'input-large', false, '');
            $fields_form[] = $this->text ('acorreios_caixa_preco_'.'nova', 'Preço de Custo', 'input-large', false, '');
            $fields_form[] =  $this->field_onOff('acorreios_caixa_ativo_'.'nova','Ativo', 'Marque SIM para usar essa caixa.');
            if(empty($total_embalagens))
                $total_embalagens=0;
            $fields_form[] = $this->field_hidden('acorreios_total_caixas', $total_embalagens); 

        return $this->getFormSection($fields_form, 'Caixas');

    }

    public function form_fretegratis()
    {
        
        $fields_form[]=$this->separador('Frete Grátis por Faixas de CEP');
        $fields_form[]= $this->paragrafo('Nesta página, você poderá informar as faixas de CEP para as quais deseja oferecer Frete Grátis. As faixas para frete grátis deverão ser informadas por intervalos de CEP. Para ter múltiplos intervalos, separe-os com uma barra. Exemplos:');
        $fields_form[]=$this->paragrafo('1 - Para ter frete grátis em pedidos de qualquer valor do cep 11111-111 até o 22222-222, escreva da seguinte forma: 11111111:22222222:0/');
        $fields_form[]=$this->paragrafo('2 - Para ter frete grátis do cep 11111-111 até o 22222-222, e também do CEP 33333-333 até o 44444-444 escreva da seguinte forma: 11111111:22222222:0/33333333:44444444:0/');
        $fields_form[]=$this->paragrafo('3 - Para ter frete grátis do cep 11111-111 até o 22222-222, para pedidos a partir de 50 reais, escreva assim: 11111111:22222222:50/');

        $faixas_gratis = $this->get_faixas_gratis();

        foreach($faixas_gratis as $fg)
        {
            $fields_form[]=$this->textarea('acorreios_fg_'.$fg['id_especificacao'], $fg['nome_regiao'],'',false,false,'Informe as faixas de CEP para oferecer Frete Grátis via '.$fg['nome_regiao'], false);
        }
       
        return $this->getFormSection($fields_form, 'FRETE GRÁTIS');
    }

    private function get_faixas_gratis()
    {
        $sql = 'SELECT id, nome_regiao, regiao_cep_excluido, ativo, id_especificacao FROM a_correios_frete_gratis
                WHERE id_shop='.$this->context->shop->id;

        return Db::getInstance()->ExecuteS($sql);
    }

    public function form_sedex10() 
    {

        $id_servico = $this->get_id_servico('SEDEX 10');

        $fields_form = array(
           $this->separador('Configurações do serviço de Sedex 10'),
           $this->field_onOff('acorreios_servicos_ativo_'.$id_servico,'Ativar', 'Marque SIM para exibir esta modalidade no seu site.'),

            $this->textarea('acorreios_servico_desativar_faixas_'.$id_servico, 'Faixas de CEP Excluídas','',false,false,'Informe as faixas de CEP para as quais este serviço não será oferecido.', false),

            $this->separador('Dados do Contrato (opcional)'),

            $this->text ('acorreios_espec_codigos_'.$id_servico, 'Código de Serviço', 'input-large', false, '', true),
            $this->text ('acorreios_espec_codigoa_'.$id_servico, 'Código Administrativo', 'input-large', false, '', false),
            $this->text ('acorreios_espec_senha_'.$id_servico, 'Senha', 'input-large', false, '', false),

            $this->separador('Opções avançadas (opcional)'),

            $this->paragrafo('DIMENSÕES E PESOS'),
            $this->text ('acorreios_espec_compmin_'.$id_servico, 'Comprimento Mínimo', 'input-large', false, ''),
            $this->text ('acorreios_espec_compmax_'.$id_servico, 'Comprimento Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramin_'.$id_servico, 'Largura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramax_'.$id_servico, 'Largura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamin_'.$id_servico, 'Altura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamax_'.$id_servico, 'Altura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_dimensoesmax_'.$id_servico, 'Perímetro Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesoemax_'.$id_servico, 'Peso Máximo - Estadual', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesonmax_'.$id_servico, 'Peso Máximo - Nacional', 'input-large', false, ''),
            $this->textarea('acorreios_espec_intervaloe_'.$id_servico, 'Intervalos de Pesos - Estadual','',false,false,'', false),
            $this->textarea('acorreios_espec_intervalon_'.$id_servico, 'Intervalos de Pesos - Nacional','',false,false,'', false),
            $this->paragrafo('VALORES PARA CÁLCULO OFFLINE'),
            $this->text ('acorreios_espec_cubagemi_'.$id_servico, 'Cubagem Máxima Isenta', 'input-large', false, ''),
            $this->text ('acorreios_espec_cubagemb_'.$id_servico, 'Cubagem para base do cálculo', 'input-large', false, ''),
            $this->text ('acorreios_espec_maopropria_'.$id_servico, 'Tarifa Mão Própria', 'input-large', false, ''),
            $this->text ('acorreios_espec_aviso_'.$id_servico, 'Tarifa Aviso de Recebimento', 'input-large', false, ''),
            $this->text ('acorreios_espec_valord_'.$id_servico, 'Valor Declarado (%)', 'input-large', false, ''),
            $this->text ('acorreios_espec_valordmax_'.$id_servico, 'Máximo valor declarado', 'input-large', false, ''),
            $this->text ('acorreios_espec_seguro_'.$id_servico, 'Seguro Automático', 'input-large', false, ''),

        );
        return $this->getFormSection($fields_form, 'Sedex 10');
    }

    private function get_id_servico($nome)
    {
        $id_especificacao = Db::getInstance()->getValue('SELECT id FROM a_correios_especificacoes WHERE servico="'.$nome.'" AND id_shop='.$this->context->shop->id); 

        $id_servico = Db::getInstance()->getValue('SELECT id FROM a_correios_servicos WHERE id_especificacao='.$id_especificacao.' AND id_shop='.$this->context->shop->id);

        return $id_servico;
    }


    public function form_sedex() 
    {
        $id_servico = $this->get_id_servico('SEDEX');

        $fields_form = array(
           $this->separador('Configurações do serviço de Sedex'),
           $this->field_onOff('acorreios_servicos_ativo_'.$id_servico,'Ativar', 'Marque SIM para exibir esta modalidade no seu site.'),

            $this->textarea('acorreios_servico_desativar_faixas_'.$id_servico, 'Faixas de CEP Excluídas','',false,false,'Informe as faixas de CEP para as quais este serviço não será oferecido.', false),

            $this->separador('Dados do Contrato (opcional)'),

            $this->text ('acorreios_espec_codigos_'.$id_servico, 'Código de Serviço', 'input-large', false, '', true),
            $this->text ('acorreios_espec_codigoa_'.$id_servico, 'Código Administrativo', 'input-large', false, '', false),
            $this->text ('acorreios_espec_senha_'.$id_servico, 'Senha', 'input-large', false, '', false),

            $this->separador('Opções avançadas (opcional)'),

            $this->paragrafo('DIMENSÕES E PESOS'),
            $this->text ('acorreios_espec_compmin_'.$id_servico, 'Comprimento Mínimo', 'input-large', false, ''),
            $this->text ('acorreios_espec_compmax_'.$id_servico, 'Comprimento Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramin_'.$id_servico, 'Largura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_larguramax_'.$id_servico, 'Largura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamin_'.$id_servico, 'Altura Mínima', 'input-large', false, ''),
            $this->text ('acorreios_espec_alturamax_'.$id_servico, 'Altura Máxima', 'input-large', false, ''),
            $this->text ('acorreios_espec_dimensoesmax_'.$id_servico, 'Perímetro Máximo', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesoemax_'.$id_servico, 'Peso Máximo - Estadual', 'input-large', false, ''),
            $this->text ('acorreios_espec_pesonmax_'.$id_servico, 'Peso Máximo - Nacional', 'input-large', false, ''),
            $this->textarea('acorreios_espec_intervaloe_'.$id_servico, 'Intervalos de Pesos - Estadual','',false,false,'', false),
            $this->textarea('acorreios_espec_intervalon_'.$id_servico, 'Intervalos de Pesos - Nacional','',false,false,'', false),
            $this->paragrafo('VALORES PARA CÁLCULO OFFLINE'),
            $this->text ('acorreios_espec_cubagemi_'.$id_servico, 'Cubagem Máxima Isenta', 'input-large', false, ''),
            $this->text ('acorreios_espec_cubagemb_'.$id_servico, 'Cubagem para base do cálculo', 'input-large', false, ''),
            $this->text ('acorreios_espec_maopropria_'.$id_servico, 'Tarifa Mão Própria', 'input-large', false, ''),
            $this->text ('acorreios_espec_aviso_'.$id_servico, 'Tarifa Aviso de Recebimento', 'input-large', false, ''),
            $this->text ('acorreios_espec_valord_'.$id_servico, 'Valor Declarado (%)', 'input-large', false, ''),
            $this->text ('acorreios_espec_valordmax_'.$id_servico, 'Máximo valor declarado', 'input-large', false, ''),
            $this->text ('acorreios_espec_seguro_'.$id_servico, 'Seguro Automático', 'input-large', false, ''),

        );
        return $this->getFormSection($fields_form, 'Sedex');
    }



    public function get_campos_sedex()
    {

    }

    public function getFormSection ($fields_form, $title, $icon = 'icon-cogs') {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $title,
                    'icon' => $icon
                ),
                'input' => $fields_form,
                'submit' => array(
                    'title' => $this->l('Save')
                )
            )
        );
    }

    protected function textarea ($name, $label, $class = '', $lang = false, $editor = true, $hint = '',
        $required='') {
        $field = array ();
        $field['type'] = 'textarea';
        $field['label'] = $this->l($label);
        $field['name'] = $name;
        if($class) $field['class'] = $class;
        if($lang) $field['lang'] = $lang;
        if($editor) $field['autoload_rte'] = $editor;
        if($hint) $field['hint'] = $this->l($hint);
        if($required) $field['required'] = $required;
        
        return $field;
    }
    protected function text ($name, $label, $class = '', $lang = false, $hint = '',$required='', $suffix = '') {
        $field = array ();
        $field['type'] = 'text';
        $field['label'] = $label;
        $field['name'] = $name;
        
        if($class) $field['class'] = $class;
        if($lang) $field['lang'] = $lang;
        if($hint) $field['hint'] = $this->l($hint);
        if($required) $field['required'] = $required;
        if (!empty($suffix)) $field['suffix'] = $suffix;
        
        return $field;
    }

    protected function separador($texto) 
    {
       return  array(
                    'type' => 'free',
                    'name' => 'SEPARADOR',
                    'desc' => '<H2>'.$texto.'</H2>',
                    'required' => false
                );
        
    }

    protected function html($html) 
    {
       return  array(
                    'type' => 'free',
                    'name' => 'SEPARADOR',
                    'desc' => $html,
                    'required' => false
                );
        
    }
	
    protected function paragrafo($texto) 
    {
       return  array(
                    'type' => 'free',
                    'name' => 'PARAGRAFO',
                    'desc' => '<p>'.$texto.'</p>',
                    'required' => false
                );
        
    }

    protected function field_onOff ($name, $label,$des ='') {
        return array(
            'type' => 'switch',
            'label' => $label,
            'name' => $name,
            'desc' => $des,
            'is_bool' => true,
            'required' => false,
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

    protected function field_hidden($nome, $valor='')
    {
        return array(
            'type' => 'hidden',
            'value' => $valor,
            'name' => $nome
          ); 
    }
    protected function checkbox($name, $label, $options,$desc='')
    {
       return array(
                'type'    => 'checkbox',                         
                'label'   => $label,               
                'desc'    => $desc, 
                'required' => false,      
                'name'    => $name,     
                'values'  => array(
                    'query' => $options,
                    'id'    => 'id',
                    'name'  => 'name',
                ));
    }

    private function get_embalagens()
    {
        $sql = "SELECT id, descricao, comprimento, altura, largura, peso, cubagem, custo, ativo
                FROM a_correios_embalagens
                WHERE id_shop = ".$this->context->shop->id;

        return Db::getInstance()->ExecuteS($sql);
    }

    private function recuperaServicosCorreios() {

        // Servicos dos Correios
        $sql = 'SELECT
                  a_correios_servicos.*,
                  a_correios_especificacoes.servico
                FROM a_correios_servicos
                  INNER JOIN a_correios_especificacoes
                    ON a_correios_servicos.id_especificacao = a_correios_especificacoes.id
                WHERE a_correios_servicos.id_shop = '.(int)$this->context->shop->id;

        return Db::getInstance()->ExecuteS($sql);
    }

   
}
