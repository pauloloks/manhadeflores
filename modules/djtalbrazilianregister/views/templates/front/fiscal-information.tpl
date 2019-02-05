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




{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
        {l s='My account' mod='djtalbrazilianregister'}
    </a>
    <span class="navigation-pipe">
        {$navigationPipe}
    </span>
    <span class="navigation_page">
       {l s='Brazilian fiscal information' mod='djtalbrazilianregister'}
    </span>
{/capture}
<div class="box">
    <h1 class="page-subheading">
        {l s='Brazilian fiscal information' mod='djtalbrazilianregister'}
    </h1>

    {include file="$tpl_dir./errors.tpl"}

    {if isset($confirmation) && $confirmation}
        <p class="alert alert-success">
            {l s='Your Brazilian fiscal information has been successfully updated.' mod='djtalbrazilianregister'}
        </p>
    {/if}
	
	<p class="info-title">
		{l s='Please be sure to update your personal information if it has changed.'}
	</p>
	<form id="fiscalInformationForm" action="{$link->getModuleLink('djtalbrazilianregister', 'fiscal-information')|escape:'html':'UTF-8'}" method="post" class="std">
		<fieldset>
			{if $br_show_cpf}
				<div class="form-group">
					<label for="br_document_cpf">
						{l s='CPF' mod='djtalbrazilianregister' }
					</label>
					<input {if $customer_permissions == 'none' || ($customer_permissions == 'add' && !empty($br_document_cpf))}disabled="disabled"{/if} class="validate form-control" type="text" id="br_document_cpf" name="br_document_cpf" data-validate="isCPF" {if !empty($br_document_cpf)}value="{Djtalbrazilianregister::mascaraString('###.###.###-##', $br_document_cpf)|escape:'htmlall':'UTF-8'}"{/if} />
				</div>
			{/if}
			
			{if $br_show_cnpj}
				<div class="form-group">
					<label for="br_document_cnpj">
						{l s='CNPJ' mod='djtalbrazilianregister' }
					</label>
					<input {if $customer_permissions == 'none' || ($customer_permissions == 'add' && !empty($br_document_cnpj))}disabled="disabled"{/if} class="validate form-control" type="text" id="br_document_cnpj" name="br_document_cnpj" data-validate="isCNPJ" {if !empty($br_document_cnpj)}value="{Djtalbrazilianregister::mascaraString('##.###.###/####-##', $br_document_cnpj)|escape:'htmlall':'UTF-8'}"{/if} />
				</div>
			{/if}
			
			{if $br_show_passport}
				<div class="form-group">
					<label for="br_document_passport">
						{l s='Passport' mod='djtalbrazilianregister' }
					</label>
					<input {if $customer_permissions == 'none' || ($customer_permissions == 'add' && !empty($br_document_passport))}disabled="disabled"{/if} class="validate form-control" type="text" id="br_document_passport" name="br_document_passport" data-validate="isPassport" {if !empty($br_document_passport)}value="{$br_document_passport|escape:'htmlall':'UTF-8'}"{/if} />
				</div>
			{/if}
			
			{if $br_show_rg}
				<div class="form-group">
					<label for="br_document_rg">
						{l s='RG' mod='djtalbrazilianregister' }
					</label>
					<input {if $customer_permissions == 'none' || ($customer_permissions == 'add' && !empty($br_document_rg))}disabled="disabled"{/if} class="validate form-control" type="text" id="br_document_rg" name="br_document_rg" data-validate="isRG" {if !empty($br_document_rg)}value="{$br_document_rg|escape:'htmlall':'UTF-8'}"{/if} />
				</div>
			{/if}
			
			{if $br_show_ie}
				<div class="form-group">
					<label for="br_document_ie">
						{l s='IE' mod='djtalbrazilianregister' }
					</label>
					<input {if $customer_permissions == 'none' || ($customer_permissions == 'add' && !empty($br_document_ie))}disabled="disabled"{/if} class="validate form-control" type="text" id="br_document_ie" name="br_document_ie" data-validate="isIE" {if !empty($br_document_ie)}value="{$br_document_ie|escape:'htmlall':'UTF-8'}"{/if} />
				</div>
			{/if}
			
			{if $br_show_sr}
				<div class="form-group">
					<label for="br_document_sr">
						{l s='Razão Social' mod='djtalbrazilianregister' }
					</label>
					<input {if $customer_permissions == 'none' || ($customer_permissions == 'add' && !empty($br_document_sr))}disabled="disabled"{/if} class="validate form-control" type="text" id="br_document_sr" name="br_document_sr" data-validate="isSR" {if !empty($br_document_sr)}value="{$br_document_sr|escape:'htmlall':'UTF-8'}"{/if} />
				</div>
			{/if}
			
			{if $br_show_comp}
				<div class="form-group">
					<label for="br_document_comp">
						{l s='Complementary Question' mod='djtalbrazilianregister' }
					</label>
					<input {if $customer_permissions == 'none' || ($customer_permissions == 'add' && !empty($br_document_comp))}disabled="disabled"{/if} class="validate form-control" type="text" id="br_document_comp" name="br_document_comp" {if !empty($br_document_comp)}value="{$br_document_comp|escape:'htmlall':'UTF-8'}"{/if} />
				</div>
			{/if}

			<div class="form-group">
				<button type="submit" name="submitFiscalInformation" class="btn btn-default button button-medium">
					<span>{l s='Save' mod='djtalbrazilianregister'}<i class="icon-chevron-right right"></i></span>
				</button>
			</div>
		</fieldset>
	</form> <!-- .std -->
</div>



<ul class="footer_links clearfix">
	<li>
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)}">
            <span>
                <i class="icon-chevron-left"></i>{l s='Back to your account'}
            </span>
        </a>
    </li>
	<li>
        <a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">
            <span>
                <i class="icon-chevron-left"></i>{l s='Home'}
            </span>
        </a>
    </li>
</ul>
