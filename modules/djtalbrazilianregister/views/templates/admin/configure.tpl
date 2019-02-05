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

<div class="panel">
    <h3><i class="icon icon-credit-card"></i> {l s='Registering rules for Brazil' mod='djtalbrazilianregister'}</h3>
    <p>
        <strong>{l s='Add CPF | CNPJ and adress search BY CEP to your Prestashop Store' mod='djtalbrazilianregister'}</strong><br />
    </p>
    {if isset($import_res)}
        <div style="height: 250px; overflow: auto;">
        <table>
        {foreach from=$import_res item=res}
            <tr>
                <td><strong>ID-Customer</strong>={$res['id_customer']|escape:'htmlall':'UTF-8'}</td> 
                
                <td>
                    {if isset($res['cpf'])}
                        <strong>CPF</strong>={$res['cpf']|escape:'htmlall':'UTF-8'} 
                    {/if}
                </td>
                <td>
                    {if isset($res['cnpj'])}
                        <strong>CNPJ</strong>={$res['cnpj']|escape:'htmlall':'UTF-8'} 
                    {/if}
                </td>
                <td>
                    {if isset($res['cpf_or_cnpj'])}
                        {if $res['cpf_or_cnpj']|count_characters == 11}
                            <strong>CPF</strong>={$res['cpf_or_cnpj']|escape:'htmlall':'UTF-8'}
                        {elseif $res['cpf_or_cnpj']|count_characters == 14}
                            <strong>CNPJ</strong>={$res['cpf_or_cnpj']|escape:'htmlall':'UTF-8'}
                        {else}
                            <strong>CPF or CNPJ ??? </strong>={$res['cpf_or_cnpj']|escape:'htmlall':'UTF-8'}
                        {/if}
                    {/if}
                </td>
                <td>
                    {if isset($res['rg'])}
                        <strong>RG</strong>={$res['rg']|escape:'htmlall':'UTF-8'}
                    {/if}
                </td>
                <td>
                    {if isset($res['ie'])}
                        <strong>IE</strong>={$res['ie']|escape:'htmlall':'UTF-8'}
                    {/if}
                </td>
                <td>
                    {if isset($res['sr'])}
                        <strong>Razão Social</strong>={$res['sr']|escape:'htmlall':'UTF-8'}
                    {/if}
                </td>
                <td>
                    {if isset($res['rg_or_ie'])}
                        <strong>RG or IE</strong>={$res['rg_or_ie']|escape:'htmlall':'UTF-8'}
                    {/if}
                </td>
            </tr>
        {/foreach}
        </table>
        </div>
    {/if}
</div>

