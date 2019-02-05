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

    {if !empty($br_document_cpf)}
        <div class="form-group">
            <label for="br_document_cpf">
                {l s='CPF' mod='djtalbrazilianregister' }
            </label>
            <input disabled="disabled" class="validate form-control" type="text" id="br_document_cpf" name="br_document_cpf" {if !empty($br_document_cpf)}value="{Djtalbrazilianregister::mascaraString('###.###.###-##', $br_document_cpf)|escape:'htmlall':'UTF-8'}"{/if} />
        </div>
    {/if}
    
    {if !empty($br_document_cnpj)}
        <div class="form-group">
            <label for="br_document_cnpj">
                {l s='CNPJ' mod='djtalbrazilianregister' }
            </label>
            <input disabled="disabled" class="validate form-control" type="text" id="br_document_cnpj" name="br_document_cnpj" {if !empty($br_document_cnpj)}value="{Djtalbrazilianregister::mascaraString('##.###.###/####-##', $br_document_cnpj)|escape:'htmlall':'UTF-8'}"{/if} />
        </div>
    {/if}
    
    {if !empty($br_document_passport)}
        <div class="form-group">
            <label for="br_document_passport">
                {l s='Passport' mod='djtalbrazilianregister' }
            </label>
            <input disabled="disabled" class="validate form-control" type="text" id="br_document_passport" name="br_document_passport" {if !empty($br_document_passport)}value="{$br_document_passport|escape:'htmlall':'UTF-8'}"{/if} />
        </div>
    {/if}
    
    {if !empty($br_document_rg)}
        <div class="form-group">
            <label for="br_document_rg">
                {l s='RG' mod='djtalbrazilianregister' }
            </label>
            <input disabled="disabled" class="validate form-control" type="text" id="br_document_rg" name="br_document_rg" {if !empty($br_document_rg)}value="{$br_document_rg|escape:'htmlall':'UTF-8'}"{/if} />
        </div>
    {/if}
    
    {if !empty($br_document_ie)}
        <div class="form-group">
            <label for="br_document_ie">
                {l s='IE' mod='djtalbrazilianregister' }
            </label>
            <input disabled="disabled" class="validate form-control" type="text" id="br_document_ie" name="br_document_ie" {if !empty($br_document_ie)}value="{$br_document_ie|escape:'htmlall':'UTF-8'}"{/if} />
        </div>
    {/if}
    
    {if !empty($br_document_sr)}
        <div class="form-group">
            <label for="br_document_sr">
                {l s='Razão Social' mod='djtalbrazilianregister' }
            </label>
            <input disabled="disabled" class="validate form-control" type="text" id="br_document_sr" name="br_document_sr" {if !empty($br_document_sr)}value="{$br_document_sr|escape:'htmlall':'UTF-8'}"{/if} />
        </div>
    {/if}
    
    {if !empty($br_document_comp)}
        <div class="form-group">
            <label for="br_document_comp">
                {l s='Complementary Question' mod='djtalbrazilianregister' }
            </label>
            <input disabled="disabled" class="validate form-control" type="text" id="br_document_comp" name="br_document_comp" {if !empty($br_document_comp)}value="{$br_document_comp|escape:'htmlall':'UTF-8'}"{/if} />
        </div>
    {/if}

	{if $customer_permissions == 'add'}
		<a style="float: left; margin-right: 42px;" href="{$link->getModuleLink('djtalbrazilianregister', 'fiscalinformation')|escape:'html':'UTF-8'}" class="btn btn-default button button-medium">
			<span><i class="icon-plus left"></i>{l s='Complete fiscal information' mod='djtalbrazilianregister'}</span>
		</a>
	{elseif $customer_permissions == 'edit'}
		<a style="float: left; margin-right: 42px;" href="{$link->getModuleLink('djtalbrazilianregister', 'fiscalinformation')|escape:'html':'UTF-8'}" class="btn btn-default button button-medium">
			<span><i class="icon-edit left"></i>{l s='Edit fiscal information' mod='djtalbrazilianregister'}</span>
		</a>
	{/if}