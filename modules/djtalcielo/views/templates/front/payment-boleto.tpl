{*
* 2007-2017 PrestaShop
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
*  @copyright 2017 DJTAL
*  @version   1.0.0
*  @link      http://www.djtal.com.br/
*  @license
*}

<div>
    <form id='djtalcielo_payment_form' action='{$link->getModuleLink('djtalcielo', 'Confirmationboleto', ['cart_id' => $cart_id, 'secure_key' => $secure_key], true)|escape:'htmlall':'UTF-8'}' method='POST'>
        <h3>{l s='Payment by Boleto' mod='djtalcielo'} - '{$bank_name|escape:'htmlall':'UTF-8'}':</h3>
        <div class='col-xs-12 col-sm-8'>
            <p>
                {l s='The valor total of your order is:' mod='djtalcielo' }
                <span class='amount'>{convertPrice price=$total_order}</span>
                <br /><br /><br />
                {l s='When confirming the payment of this order, you agree to pay the expected value in:' mod='djtalcielo' } 
                <span class='delay'>{$boleto_delay|escape:'htmlall':'UTF-8'} {l s='days.' mod='djtalcielo' }</span>
                <br /><br />
            </p>
            <p>
                {l s='Please confirm your order clicking on the `Confirm and get the boleto` button' mod='djtalcielo' }.
            </p> 
            <div class='form-item'>
                <div style='padding: 0px;' class='column col-xs-12 col-sm-12 col-md-12'>
                    <label for='customer_fisc' >{l s='CPF or CNPJ:' mod='djtalcielo'}</label>
                    <input size='14' type='text' id='customer_fisc' onkeyup='CpforCnpj()' name='customer_fisc'/>
                </div>
                <div class='clear'></div>
            </div>
        </div>
        <div class='col-xs-12 col-sm-4 boleto-logo'>
            <img src='{$modules_dir|escape:'htmlall':'UTF-8'}djtalcielo/views/img/{if $bank_name == 'Bradesco'}bradesco.png{else}banco-do-brasil.png {/if}'>
        </div>
        <p class='clearfix' style='float: left; padding: 24px 32px 0 0; width: 100%;'>
            <a href='{$link->getPageLink('order', true, NULL, 'step=3')|escape:'htmlall':'UTF-8'}' title='{l s='Other payment form' mod='djtalcielo' }' class='button-exclusive btn btn-default'>
                <i class='icon-chevron-left'></i>
                {l s='Other payment form' mod='djtalcielo' }
            </a>

            <a style='float: right;' onclick='return validateForm();'  href='{$link->getModuleLink('djtalcielo', 'Confirmationboleto', ['cart_id' => $cart_id, 'secure_key' => $secure_key], true)|escape:'htmlall':'UTF-8'}' class='button btn btn-default standard-checkout button-medium boleto-checkout' style=''>
                <span>{l s='Confirm and get the boleto' mod='djtalcielo'}<i class='icon-chevron-right right'></i></span>
            </a>
        </p>
    </form>
</div>