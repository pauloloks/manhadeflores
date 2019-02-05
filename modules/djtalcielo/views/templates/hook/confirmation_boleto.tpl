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

<h3>{l s='Your order on %s is complete.' sprintf=$shop_name mod='djtalcielo'}</h3>
<p>    
    <br />
    - {l s='Amount' mod='djtalcielo'} : 
    <span class='price'><strong>{$total|escape:'htmlall':'UTF-8'}</strong></span>    
    <br />- {l s='Reference' mod='djtalcielo'} : 
    <span class='reference'><strong>{$reference|escape:'html':'UTF-8'}</strong></span>      
    <br /><br />{l s='An email has been sent with this information.' mod='djtalcielo'}       
    <br /><br />{l s='If you have questions, comments or concerns, please contact our' mod='djtalcielo'}
    <a href='{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}'>{l s='expert customer support team.' mod='djtalcielo'}</a>  
</p> 
<iframe id='boleto' width='640' height='1050' name='boleto' src="{$url|escape:'htmlall':'UTF-8'}{$cieloAuthorization.paymentId|escape:'htmlall':'UTF-8'}" ></iframe> 
    <br />- {l s='Reference' mod='djtalcielo'} <span class='reference'> <strong>{$reference|escape:'html':'UTF-8'}</strong></span>  
    <br /><br />{l s='Please, try to order again.' mod='djtalcielo'}    
    <br /><br />{l s='If you have questions, comments or concerns, please contact our' mod='djtalcielo'} <a href='{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}'>{l s='expert customer support team.' mod='djtalcielo'}</a>  
<hr />
