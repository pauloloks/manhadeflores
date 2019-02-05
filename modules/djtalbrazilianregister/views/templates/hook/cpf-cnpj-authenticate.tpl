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
{if $mode == 'cpf-or-cnpj' || $mode == 'cpf-or-cnpj-or-passport'}
    <div class="clearfix form-group">
        <label>{l s='CPF or CNPJ' mod='djtalbrazilianregister' }</label>
        <br />
        <div class="radio-inline">
            <label for="id_cp_mode_cpf" class="top">
                <input type="radio" name="id_cp_mode" id="id_cp_mode_cpf" value="cpf" {if $id_cp_mode == 'cpf'}checked="checked"{/if} />
                {l s='CPF' mod='djtalbrazilianregister' }
            </label>
        </div>
        <div class="radio-inline">
            <label for="id_cp_mode_cnpj" class="top">
                <input type="radio" name="id_cp_mode" id="id_cp_mode_cnpj" value="cnpj" {if $id_cp_mode == 'cnpj'}checked="checked"{/if} />
                {l s='CNPJ' mod='djtalbrazilianregister' }
            </label>
        </div>
        {if $mode == 'cpf-or-cnpj-or-passport'}
            <div class="radio-inline">
                <label for="id_cp_mode_passport" class="top">
                    <input type="radio" name="id_cp_mode" id="id_cp_mode_passport" value="passport" {if $id_cp_mode == 'passport'}checked="checked"{/if} />
                    {l s='Passport' mod='djtalbrazilianregister' }
                </label>
            </div>
        {/if}
    </div>
    <div class="{if $mandatory}required{/if} form-group">
        <label for="br_document" class="{if $mandatory && $display=='identity'}required{/if}">
            {l s='Document' mod='djtalbrazilianregister' }
            {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
        </label>
        <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="" type="text" id="br_document" name="br_document" value="{if isset($smarty.post.br_document)}{$smarty.post.br_document|escape:'htmlall':'UTF-8'}{/if}" />
    </div>
    {if $ask_rg}
        <div class="{if $mandatory}required{/if} form-group br_document_rg" style="display: none;">
            <label for="br_document_rg" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='RG' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isRG" type="text" id="br_document_rg" name="br_document_rg" value="{if isset($smarty.post.br_document_rg)}{$smarty.post.br_document_rg|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
    {if $ask_ie}
        <div class="{if $mandatory}required{/if} form-group br_document_ie" style="display: none;">
            <label for="br_document_ie" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='IE (Inscrição Estadual)' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isIE" type="text" id="br_document_ie" name="br_document_ie" value="{if isset($smarty.post.br_document_ie)}{$smarty.post.br_document_ie|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
    {if $ask_sr}
        <div class="{if $mandatory}required{/if} form-group br_document_sr" style="display: none;">
            <label for="br_document_sr" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='Razão Social' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isSR" type="text" id="br_document_sr" name="br_document_sr" value="{if isset($smarty.post.br_document_sr)}{$smarty.post.br_document_sr|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
	{if $ask_comp}
        <div class="{if $mandatory}required{/if} form-group br_document_comp">
            <label for="br_document_comp" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='Complementary Question' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" type="text" id="br_document_comp" name="br_document_comp" value="{if isset($smarty.post.br_document_comp)}{$smarty.post.br_document_comp|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
{elseif $mode == 'cpf-and-cnpj'}
    <div class="{if $mandatory}required{/if} form-group">
        <label for="br_document_cpf" class="{if $mandatory && $display=='identity'}required{/if}">
            {l s='CPF' mod='djtalbrazilianregister' }
            {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
        </label>
        <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isCPF" type="text" id="br_document_cpf" name="br_document_cpf" value="{if isset($smarty.post.br_document_cpf)}{$smarty.post.br_document_cpf|escape:'htmlall':'UTF-8'}{/if}" />
    </div>
    {if $ask_rg}
        <div class="{if $mandatory}required{/if} form-group br_document_rg">
            <label for="br_document_rg" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='RG' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isRG" type="text" id="br_document_rg" name="br_document_rg" value="{if isset($smarty.post.br_document_rg)}{$smarty.post.br_document_rg|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
    <div class="{if $mandatory}required{/if} form-group">
        <label for="br_document_cnpj" class="{if $mandatory && $display=='identity'}required{/if}">
            {l s='CNPJ' mod='djtalbrazilianregister' }
            {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
        </label>
        <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isCNPJ" type="text" id="br_document_cnpj" name="br_document_cnpj" value="{if isset($smarty.post.br_document_cnpj)}{$smarty.post.br_document_cnpj|escape:'htmlall':'UTF-8'}{/if}" />
    </div>
    {if $ask_ie}
        <div class="{if $mandatory}required{/if} form-group br_document_ie">
            <label for="br_document_ie" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='IE (Inscrição Estadual)' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isIE" type="text" id="br_document_ie" name="br_document_ie" value="{if isset($smarty.post.br_document_ie)}{$smarty.post.br_document_ie|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
    {if $ask_sr}
        <div class="{if $mandatory}required{/if} form-group br_document_sr">
            <label for="br_document_sr" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='Razão Social' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isSR" type="text" id="br_document_sr" name="br_document_sr" value="{if isset($smarty.post.br_document_sr)}{$smarty.post.br_document_sr|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}   
	{if $ask_comp}
        <div class="{if $mandatory}required{/if} form-group br_document_comp">
            <label for="br_document_comp" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='Complementary Question' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" type="text" id="br_document_comp" name="br_document_comp" value="{if isset($smarty.post.br_document_comp)}{$smarty.post.br_document_comp|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
{elseif $mode == 'cpf-only'}
    <div class="{if $mandatory}required{/if} form-group">
        <label for="br_document_cpf" class="{if $mandatory && $display=='identity'}required{/if}">
            {l s='CPF' mod='djtalbrazilianregister' }
            {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
        </label>
        <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isCPF" type="text" id="br_document_cpf" name="br_document_cpf" value="{if isset($smarty.post.br_document_cpf)}{$smarty.post.br_document_cpf|escape:'htmlall':'UTF-8'}{/if}" />
    </div>
    {if $ask_rg}
        <div class="{if $mandatory}required{/if} form-group br_document_rg">
            <label for="br_document_rg" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='RG' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isRG" type="text" id="br_document_rg" name="br_document_rg" value="{if isset($smarty.post.br_document_rg)}{$smarty.post.br_document_rg|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
	{if $ask_comp}
        <div class="{if $mandatory}required{/if} form-group br_document_comp">
            <label for="br_document_comp" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='Complementary Question' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" type="text" id="br_document_comp" name="br_document_comp" value="{if isset($smarty.post.br_document_comp)}{$smarty.post.br_document_comp|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
{elseif $mode == 'cnpj-only'}
    <div class="{if $mandatory}required{/if} form-group">
        <label for="br_document_cnpj" class="{if $mandatory && $display=='identity'}required{/if}">
            {l s='CNPJ' mod='djtalbrazilianregister' }
            {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
        </label>
        <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isCNPJ" type="text" id="br_document_cnpj" name="br_document_cnpj" value="{if isset($smarty.post.br_document_cnpj)}{$smarty.post.br_document_cnpj|escape:'htmlall':'UTF-8'}{/if}" />
    </div>
    {if $ask_ie}
        <div class="{if $mandatory}required{/if} form-group br_document_ie">
            <label for="br_document_ie" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='IE (Inscrição Estadual)' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isIE" type="text" id="br_document_ie" name="br_document_ie" value="{if isset($smarty.post.br_document_ie)}{$smarty.post.br_document_ie|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
    {if $ask_sr}
        <div class="{if $mandatory}required{/if} form-group br_document_sr">
            <label for="br_document_sr" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='Razão Social' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isSR" type="text" id="br_document_sr" name="br_document_sr" value="{if isset($smarty.post.br_document_sr)}{$smarty.post.br_document_sr|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}      
	{if $ask_comp}
        <div class="{if $mandatory}required{/if} form-group br_document_comp">
            <label for="br_document_comp" class="{if $mandatory && $display=='identity'}required{/if}">
                {l s='Complementary Question' mod='djtalbrazilianregister' }
                {if $mandatory && $display=='authenticate'} <sup>*</sup>{/if}
            </label>
            <input class="{if $mandatory}is_required{/if} validate form-control" data-validate="isComp" type="text" id="br_document_comp" name="br_document_comp" value="{if isset($smarty.post.br_document_comp)}{$smarty.post.br_document_comp|escape:'htmlall':'UTF-8'}{/if}" />
        </div>
    {/if}
{/if}

<script>
checkPageBrReg();
</script>












