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
<fieldset class="account_creation customer-br-infos">
    <h3>{l s='Brazilian register' mod='djtalbrazilianregister' }</h3>
    {if $mode == 'cpf-or-cnpj'}
        <p class="radio required">
            <span>{l s='CPF or CNPJ' mod='djtalbrazilianregister' }</span>
            
            <input type="radio" name="id_cp_mode" id="id_cp_mode_cpf" value="cpf" {if $id_cp_mode == 'cpf'}checked="checked"{/if} />
            <label for="id_cp_mode_cpf" class="top">{l s='CPF' mod='djtalbrazilianregister' }</label>
            
            <input type="radio" name="id_cp_mode" id="id_cp_mode_cnpj" value="cnpj" {if $id_cp_mode == 'cnpj'}checked="checked"{/if} />
            <label for="id_cp_mode_cnpj" class="top">{l s='CNPJ' mod='djtalbrazilianregister' }</label>
        </p>
        <p class="{if $mandatory}required{/if} text">
            <label for="br_document" >
                {l s='Document' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input data-validate="" type="text" id="br_document" name="br_document" value="{if isset($smarty.post.br_document)}{$smarty.post.br_document|escape:'htmlall':'UTF-8'}{/if}" />
        </p>
    {elseif $mode == 'cpf-and-cnpj'}
        <p class="{if $mandatory}required{/if} text">
            <label for="br_document_cpf">
                {l s='CPF' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input data-validate="isCPF" type="text" id="br_document_cpf" name="br_document_cpf" value="{if isset($smarty.post.br_document_cpf)}{$smarty.post.br_document_cpf|escape:'htmlall':'UTF-8'}{/if}" />
        </p>
        <p class="{if $mandatory}required{/if} text">
            <label for="br_document_cnpj" >
                {l s='CNPJ' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input data-validate="isCNPJ" type="text" id="br_document_cnpj" name="br_document_cnpj" value="{if isset($smarty.post.br_document_cnpj)}{$smarty.post.br_document_cnpj|escape:'htmlall':'UTF-8'}{/if}" />
        </p>
    {elseif $mode == 'cpf-only'}
        <p class="{if $mandatory}required{/if} text">
            <label for="br_document_cpf" >
                {l s='CPF' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input data-validate="isCPF" type="text" id="br_document_cpf" name="br_document_cpf" value="{if isset($smarty.post.br_document_cpf)}{$smarty.post.br_document_cpf|escape:'htmlall':'UTF-8'}{/if}" />
        </p>
    {elseif $mode == 'cnpj-only'}
        <p class="{if $mandatory}required{/if} text">
            <label for="br_document_cnpj" >
                {l s='CNPJ' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input data-validate="isCNPJ" type="text" id="br_document_cnpj" name="br_document_cnpj" value="{if isset($smarty.post.br_document_cnpj)}{$smarty.post.br_document_cnpj|escape:'htmlall':'UTF-8'}{/if}" />
        </p>
    {/if}
</fieldset>

<script>
    checkPageBrReg();
</script>












