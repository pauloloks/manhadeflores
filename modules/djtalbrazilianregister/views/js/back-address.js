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
	
	var num_value = '';
	var compl_value = '';
	var complete_address = $('#address1').val();
	var final_address = $('#address1').val();
    
    if(complete_address && complete_address != ''){
        //var numAddressArray = address.match(/\d+$/);
        var numAddressArray = complete_address.split(',');
        if(numAddressArray.length == 2){ // Adress AND Num or Complement
            if(!isNaN(numAddressArray[1])){
                num_value = numAddressArray[1].trim();
            } else {
                compl_value = numAddressArray[1].trim();
            }
            final_address = numAddressArray[0].trim();
            
        } else if (numAddressArray.length == 3){ // Adress AND Num AND Complement
            if(!isNaN(numAddressArray[1])){
                num_value = numAddressArray[1].trim();
                compl_value = numAddressArray[2].trim();
                final_address = numAddressArray[0].trim();
            } else if(!isNaN(numAddressArray[2])){
                num_value = numAddressArray[2].trim();
                compl_value = numAddressArray[1].trim();
                final_address = numAddressArray[0].trim();
            }
            
        } else {
            final_address = complete_address;
        }
		$('#address1').val(final_address);
	}
	
	
	var html = '';
	if(street_num != 'no') {
		html = html + getInputHtml('number_js', num_value, 'Número');
	}
	if(street_compl != 'no') {
		html = html + getInputHtml('complement_js', compl_value, 'Complemento');
	}
	
	$('.form-wrapper #address1').parent().parent().after(html);
	
	$('#address_form').on('submit', function(event){
        submitAddressAccount(event);
    });
	
});

function submitAddressAccount(event) {
    var address1 = $('#address1').val();
    address1 = address1.replace(/,/g, '');
    var num = '';
    var compl = '';
    
    if(street_num != 'no'){
        num = $('#number_js').val().trim();
        num = num.replace(/,/g, '');
    }
    
    if(street_compl != 'no'){
        compl = $('#complement_js').val().trim();;
        compl = compl.replace(/,/g, '');
    }
    
    var adressInfos = '';
    if(num != ''){
        adressInfos = num;
        if(compl != '') {
            adressInfos = num+', '+compl;
        }
    } else if(compl != '') {
        adressInfos = compl;
    }
    
    if(adressInfos != '') {
         $('#address1').val(address1+', '+adressInfos);
    }
    
    return true;
}

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
