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

<script type="text/javascript">
    {strip}
        var cep_search_mode = "{$cep_search_mode|escape:'htmlall':'UTF-8'}";
        var cep_dk_button = {if $cep_dk_button == 0}false{else}true{/if};
        var phones_mask = {if $phones_mask == 0}false{else}true{/if};
        var cep_ws_url = "{$cep_ws_url|escape:'htmlall':'UTF-8'}";
        var street_num = "{$street_num|escape:'htmlall':'UTF-8'}";
        var street_compl = "{$street_compl|escape:'htmlall':'UTF-8'}";
    {/strip}
</script>


