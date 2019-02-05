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
<div class='row'>
    <div class='col-xs-12 col-md-12'>
        <p class='payment_module' id='djtalcielo_payment_button'>
            <a href='{$link->getModuleLink('djtalcielo', 'Paymentcard', array(), true)|escape:'htmlall':'UTF-8'}' title='{l s='Pay with cielo' mod='djtalcielo'}'>
                <img class='logo' src='{$module_dir|escape:'htmlall':'UTF-8'}logo.png' alt='{l s='Pay with credit card' mod='djtalcielo'}' width='57' height='57' />
                {l s='Pay with cielo' mod='djtalcielo'}
                <img class='brands' src='{$module_dir|escape:'htmlall':'UTF-8'}views/img/all-cards.png' alt='{l s='Pay with cielo' mod='djtalcielo'}' />
            </a>
        </p>
    </div>
</div>
