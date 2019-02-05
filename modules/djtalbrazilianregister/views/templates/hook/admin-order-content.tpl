{*
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
*}

<div class="tab-pane" id="cpf-cnpj">
    <h4 class="visible-print">{l s='CPF - CNPJ' mod='djtalbrazilianregister' } </h4>
    <div>
        <strong>CPF:</strong> {Djtalbrazilianregister::mascaraString('###.###.###-##', $br_document_cpf)|escape:'htmlall':'UTF-8'} <br />
        <strong>RG:</strong> {$br_document_rg|escape:'htmlall':'UTF-8'} <br />
        <strong>CNPJ:</strong> {Djtalbrazilianregister::mascaraString('##.###.###/####-##', $br_document_cnpj)|escape:'htmlall':'UTF-8'} <br />
        <strong>IE:</strong> {$br_document_ie|escape:'htmlall':'UTF-8'}
        <strong>Razão Social:</strong> {$br_document_sr|escape:'htmlall':'UTF-8'}
    </div>
</div>


