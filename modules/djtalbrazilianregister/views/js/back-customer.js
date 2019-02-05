/**
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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$(document).ready(function() {
	var html = '';
	html = html + getInputHtml('cpf', cpf_value.length>0?cpf_value:'', 'CPF');
	if(rg_value !== false) {
		html = html + getInputHtml('rg', rg_value.length>0?rg_value:'', 'RG');
	}
	html = html + getInputHtml('cnpj', cnpj_value.length>0?cnpj_value:'', 'CNPJ');
	if(ie_value !== false) {
		html = html + getInputHtml('ie', ie_value.length>0?ie_value:'', 'IE');
	}
	if(sr_value !== false) {
		html = html + getInputHtml('sr', sr_value.length>0?sr_value:'', 'Razão Social');
	}
	
	$('.form-wrapper').prepend(html);

	$('#cpf').mod_mask('000.000.000-00');
	$('#cnpj').mod_mask('00.000.000/0000-00');
	//$('#rg').mod_mask('00.000.000-0');
});

function getInputHtml(fieldName, fieldValue, displayName){
	return '<div class="form-group">' +
      '<label class="control-label col-lg-3">' +
        '<span class="label-tooltip" data-original-title="Digite o seu '+displayName+'">'+displayName+'</span>' +
      '</label>' +
      '<div class="col-lg-4">' +
        '<input type="text" name="'+fieldName+'" id="'+fieldName+'" class="js-added-field '+fieldName+'" value="'+fieldValue+'">' +
      '</div>' +
    '</div>';
}
