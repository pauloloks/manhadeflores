{*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="contact-link">
{if $telnumber}
	<div class="shop-phone{if isset($is_logged) && $is_logged} is_logged{/if}">
		<span>{l s='Order online or call us : ' mod='blockcontact'} : <strong>{$telnumber}</strong></span>
	</div>
{/if}
{if $email != ''}
<div class="shop-email"><span>{l s='Email' mod='blockcontact'} : <a href="mailto:{$email|escape:'html':'UTF-8'}"> {$email}</a></span></div>
{/if}
</div>
