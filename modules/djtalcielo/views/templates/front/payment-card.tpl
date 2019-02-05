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



{capture name=path}{l s='Payment by Credit Card' mod='djtalcielo'}{/capture}

<div>
    <h3>{l s='Redirect your customer' mod='djtalcielo'}:</h3>
    <p>
        {l s='The valor total of your order is:' mod='djtalcielo' }
        <span class='amount {$currency->id|escape:'htmlall':'UTF-8'}'>{convertPrice price=$total_order}</span>
    </p>
    <form id='djtalcielo_payment_form' action='{$link->getModuleLink('djtalcielo', 'Confirmation', ['cart_id' => $cart_id, 'secure_key' => $secure_key], true)|escape:'htmlall':'UTF-8'}' method='POST'>
        
        <div class='top-choice' >
            <img id='img_master' src='{$modules_dir|escape:'htmlall':'UTF-8'}djtalcielo/views/img/selos_cielo_credito_debito.png' alt='{l s='Credit Card' mod='djtalcielo' }' />
        </div>
        
        <div id='field_errors'></div>
        
        <div class='card'>
        <div class='form-item'>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <label for='card_installment' >{l s='Method Payment:' mod='djtalcielo'}</label>
                </div>
                <div class='column col-xs-12 col-sm-6 col-md-4' style='margin-left: 23px;'>
                    {if $creditcard}
                        <label class='radio-inline'>
                          <input type='radio' value='credit_card' checked='checked' name='type_payment'>{l s='Credit Card' mod='djtalcielo'}
                        </label>
                    {/if}
                    {if $debitcard}
                        <label class='radio-inline'>
                          <input type='radio' value='debit_card' name='type_payment' {if !$creditcard} checked='checked' {/if}>{l s='Debit Card' mod='djtalcielo'}
                        </label>
                    {/if}
                </div>
                <div class='clear'></div>
            </div>
            <div class='form-item'>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <label  for='card_number' >{l s='Credit card number:' mod='djtalcielo'}</label> 
                </div>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <input size='32' type='text' id='card_number' name='card_number' onkeyup='getCreditCardLabel($(this).val())' placeholder='0000 0000 0000 0000'/>
                </div>
                <div class='clear'></div>
            </div>
            <div class='form-item'>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <label for='card_holder_name' >{l s='Name (Like on the credit card)' mod='djtalcielo'}</label>
                </div>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <input size='32' type='text' id='card_holder_name' name='card_holder_name' placeholder='{l s='Name (Like on the credit card)' mod='djtalcielo'}'/>
                </div>
                <div class='clear'></div>
            </div>
            <div class='form-item'>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <label for='card_expiration_month' >{l s='Month / Year of experation' mod='djtalcielo'}</label>
                </div>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                   
                    <select name='card_expiration_month' id='card_expiration_month' onchange='' size='1'>
                        <option value='01'>{l s='January' mod='djtalcielo'}</option>
                        <option value='02'>{l s='February' mod='djtalcielo'}</option>
                        <option value='03'>{l s='March' mod='djtalcielo'}</option>
                        <option value='04'>{l s='April' mod='djtalcielo'}</option>
                        <option value='05'>{l s='May' mod='djtalcielo'}</option>
                        <option value='06'>{l s='June' mod='djtalcielo'}</option>
                        <option value='07'>{l s='July' mod='djtalcielo'}</option>
                        <option value='08'>{l s='August' mod='djtalcielo'}</option>
                        <option value='09'>{l s='September' mod='djtalcielo'}</option>
                        <option value='10'>{l s='October' mod='djtalcielo'}</option>
                        <option value='11'>{l s='November' mod='djtalcielo'}</option>
                        <option value='12'>{l s='December' mod='djtalcielo'}</option>
                    </select> / <select id='card_expiration_year' name='card_expiration_year'  size='1'></select>
                    
                </div>
                <div class='clear'></div>
            </div>
            <div class='form-item'>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <label for='card_cvv' >{l s='Safety code:' mod='djtalcielo'}</label>
                </div>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <input size='4' type='text' id='card_cvv' name='card_cvv' placeholder='000'/>
                </div>
                <div class='clear'></div>
            </div>
            {if $max_installments >= 2}
            <div class='form-item'>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <label for='card_installment' >{l s='Installment:' mod='djtalcielo'}</label>
                </div>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <select id='card_installment' name='card_installment'>
                        <option value='1' >{l s='Cash' mod='djtalcielo'} {convertPrice price=$total_order}</option>
                        {for $var=2 to $max_installments}
                            {if ($total_order / $var) > $installment_min_value}
                                <option value='{$var|escape:'htmlall':'UTF-8'}' >{$var|escape:'htmlall':'UTF-8'} {l s='Parcelas' mod='djtalcielo'} ({convertPrice price=$total_order / $var})</option>
                            {/if}
                        {/for}
                    <select>
                </div>
                <div class='clear'></div>
            </div>
            {/if}
            <div class='form-item'>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <label for='customer_fisc' >{l s='CPF or CNPJ:' mod='djtalcielo'}</label>
                </div>
                <div class='column col-xs-12 col-sm-6 col-md-4'>
                    <input size='14' type='text' id='customer_fisc' onkeyup='CpforCnpj()' name='customer_fisc'/>
                </div>
                <div class='clear'></div>
            </div>
            
        </div>
    </form>
    
    <p class='clearfix' style='float: left; padding: 24px 32px 0 0; width: 100%;'>
        <a href='{$link->getPageLink('order', true, NULL, 'step=3')|escape:'htmlall':'UTF-8'}' title='{l s='Other payment form' mod='djtalcielo' }' class='button-exclusive btn btn-default'>
            <i class='icon-chevron-left'></i>
            {l s='Other payment form' mod='djtalcielo' }
        </a>
        
        <a style='float: right' href='#pagarme_payment_form' onclick='validateForm();' class='button btn btn-default standard-checkout button-medium'>
            <span>{l s='Confirm' mod='djtalcielo'}<i class='icon-chevron-right right'></i></span>
        </a>
    </p>
</div>
