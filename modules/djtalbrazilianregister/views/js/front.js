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

var id_country_br = 58;

$(document).on('ready', function(){
    var initiated = false;
	
	
	//check for OPC module
	var idPrefix = [''];
	if($('#onepagecheckoutps_step_one_container').length == 1){ //onepagecheckoutps: https://addons.prestashop.com/pt/processo-pedido/8503-one-page-checkout-ps-easy-fast-intuitive.html
		if($('#delivery_postcode').length == 1){
			idPrefix.push('delivery_');
		}
		if($('#invoice_postcode').length == 1){
			idPrefix.push('invoice_');
		}
	}
	
	idPrefix.forEach(function(prefix){
		var id_country = $('#'+prefix+'id_country option:selected').val();
		
		if($('#'+prefix+'postcode').length == 1 && (parseInt(id_country) == id_country_br  || $('#'+prefix+'id_country').length == 0)) {
			initiated = true;
			initAdressBehavior(prefix);
		} else {
			var inter = setInterval(function(){
				if(!initiated && $('#'+prefix+'postcode').length == 1 && $('#'+prefix+'id_country option:selected').length == 1){
					if(parseInt($('#'+prefix+'id_country option:selected').val()) == id_country_br) {
						initiated = true;
						initAdressBehavior(prefix);
						clearInterval(inter);
					}
				}
			}, 200);
		}
		
		$('#'+prefix+'id_country').on('change', function(){
			var id_country = $(this).val();
			if(parseInt(id_country) == id_country_br){
				initiated = true;
				initAdressBehavior(prefix);
			} else {
				cleanAdressBehavior(prefix);
			}
		});
		
	});
	

    
    if(typeof validate_field != 'function'){
        window.validate_field = function(that){
            if ($(that).hasClass('is_required') || $(that).val().length)
            {
                if ($(that).attr('data-validate') == 'isPostCode')
                {
                    var selector = '#id_country';
                    if ($(that).attr('name') == 'postcode_invoice')
                        selector += '_invoice';

                    var id_country = $(selector + ' option:selected').val();

                    if (typeof(countriesNeedZipCode[id_country]) != 'undefined' && typeof(countries[id_country]) != 'undefined')
                        var result = window['validate_'+$(that).attr('data-validate')]($(that).val(), countriesNeedZipCode[id_country], countries[id_country]['iso_code']);
                }
                else if($(that).attr('data-validate'))
                    var result = window['validate_' + $(that).attr('data-validate')]($(that).val());

                if (result)
                    $(that).parent().removeClass('form-error').addClass('form-ok');
                else
                    $(that).parent().addClass('form-error').removeClass('form-ok');
            }
        };
    }
    
});

function cleanAdressBehavior(prefix = '') {
    var postCode = $('#'+prefix+'postcode');
    if(typeof postCode.mod_unmask == 'function') {
        postCode.mod_unmask();
    }
    postCode.parent().find('#'+prefix+'cepSearchButton').remove();
    postCode.parent().find('#'+prefix+'cepDontKnowButton').remove();
    postCode.attr('data-validate', 'isPostCode');
    $('#'+prefix+'phone').mod_unmask();
    $('#'+prefix+'phone_mobile').mod_unmask();
    if(street_compl != 'no'){
        $('#'+prefix+'complement_js').parent().remove();
    }
    if(street_num != 'no'){
        $('#'+prefix+'number_js').parent().remove();
    }
}

function initAdressBehavior(prefix = '') {
    var postCode = $('#'+prefix+'postcode');
    
    if(typeof postCode.mod_unmask == 'function') {
        postCode.mod_unmask();
    }
    postCode.mod_mask('00000-000');
    
    //Search button
    if(cep_search_mode == 'manual'){
        postCode.parent().append('<a id="'+prefix+'cepSearchButton" class="btn btn-default button button-small"><span><i class="icon-search"></i></span></a>');
    }
    //Don't know my CEP button
    if(cep_dk_button){
        postCode.parent().append('<a class="btn btn-default button button-small" target="blank" href="http://www.buscacep.correios.com.br/servicos/dnec/index.do" id="'+prefix+'cepDontKnowButton"><span>Não sei meu CEP</span></a>');
    }
    
    postCode.attr('data-validate', 'isCEP');
    
    //Validate events:
	var postVal = '';
	if(typeof postCode != 'undefined'){
		if(typeof postCode.mod_cleanVal == 'function') {
			postVal = postCode.mod_cleanVal();
		} else {
			postVal = postCode.val();
		}
	} else {
		postVal = $('#'+prefix+'postcode').val();
	}
    if(cep_search_mode == 'manual'){
        $('#'+prefix+'cepSearchButton').on('click', function() {
			postVal = $('#'+prefix+'postcode').val();
            validate_isCEP(postVal, true, prefix);
        });
    } else {
		if(postVal.length > 0){
			validate_isCEP(postVal, true, prefix);
		}
	}
	//register the keyboard "Enter" event too
	if(cep_search_mode != 'manual' && cep_search_mode != 'none'){
		$(postCode).keyup(function(event){
			if(event.keyCode == 13){
				var postVal = $(this).val();
				if(postVal.length > 0){
					validate_isCEP(postVal, true, prefix);
				}
			}
		});
	}
    
    //Phone Mask
    if(phones_mask === true){
        $('#'+prefix+'phone').mod_mask('(00) 0000-0000');
        $('#'+prefix+'phone_mobile').mod_mask('(00) 00000-0000');
    }
    
    
    //See if adress has Number and Or Complement
    var address = $('#'+prefix+'address1').val();
    var num = '';
    var compl = '';
    var address_final = '';
    
    if(address && address != ''){
        //var numAddressArray = address.match(/\d+$/);
        var numAddressArray = address.split(',');
        if(numAddressArray.length == 2){ // Adress AND Num or Complement
            if(!isNaN(numAddressArray[1])){
                num = numAddressArray[1].trim();
            } else {
                compl = numAddressArray[1].trim();
            }
            address_final = numAddressArray[0].trim();
            
        } else if (numAddressArray.length == 3){ // Adress AND Num AND Complement
            if(!isNaN(numAddressArray[1])){
                num = numAddressArray[1].trim();
                compl = numAddressArray[2].trim();
                address_final = numAddressArray[0].trim();
            } else if(!isNaN(numAddressArray[2])){
                num = numAddressArray[2].trim();
                compl = numAddressArray[1].trim();
                address_final = numAddressArray[0].trim();
            }
            
        } else {
            address_final = address;
        }
		
		$('#'+prefix+'address1').val(address_final);
		if(street_num == 'no' && street_compl == 'no'){
			('#'+prefix+'address1').val(address);
		}
		if(street_num == 'no'){
			('#'+prefix+'address1').val(address_final+', '+num);
		}
		if(street_compl == 'no'){
			('#'+prefix+'address1').val(address_final+', '+compl);
		}
    }
	
    // Street Complement add
    if(street_compl != 'no'){
        if($('.form-group:has(#'+prefix+'address1)').length > 0){
            var html = $('.form-group:has(#'+prefix+'address1)').prop('outerHTML').replace(/address1/g, 'complement_js');
            $(html).insertAfter('.form-group:has(#'+prefix+'address1)');
            var star = '';
            if(street_compl == 'yes_mand'){
                star = ' <sup>*</sup>';
            }
            $('label[for='+prefix+'complement_js]').html(translated_comp + star);
            $('#'+prefix+'complement_js').val('');
            $('#'+prefix+'complement_js').attr('data-validate', 'isAddressComplement');
            $('#'+prefix+'complement_js').parent().removeClass('form-error').removeClass('form-ok');
            
            $('#'+prefix+'complement_js').val(compl);
        }
    }
    
    // Street number add
    if(street_num != 'no'){
        if($('.form-group:has(#'+prefix+'address1)').length > 0){
            var html = $('.form-group:has(#'+prefix+'address1)').prop('outerHTML').replace(/address1/g, 'number_js');
            $(html).insertAfter('.form-group:has(#'+prefix+'address1)');
            var star = '';
            if(street_num == 'yes_mand'){
                star = ' <sup>*</sup>';
            }
            $('label[for='+prefix+'number_js]').html(translated_num + star);
            $('#'+prefix+'number_js').val('');
            $('#'+prefix+'number_js').attr('data-validate', 'isAddressNumber');
            $('#'+prefix+'number_js').parent().removeClass('form-error').removeClass('form-ok');
            
            $('#'+prefix+'number_js').val(num);
        }
    }
    
	//#btn_place_order: onepagecheckoutps case
    $('#add_address, #account-creation_form, #btn_place_order').on('submit', function(event){
        submitAddressAccount(event, prefix);
    });
	
	//Order OPC
	$('#submitAccount, #submitGuestAccount').on('click', function(event){
		submitAddressAccount(event, prefix);
	});
}

function submitAddressAccount(event, prefix = '') {
    var address1 = $('#'+prefix+'address1').val();
    address1 = address1.replace(/,/g, '');
    var num = '';
    var compl = '';
    
    if(street_num != 'no'){
        num = $('#'+prefix+'number_js').val().trim();
        num = num.replace(/,/g, '');

        if(validate_isAddressNumber(num) || (street_num == 'yes' && num == '')){
            $('#'+prefix+'number_js').parent().removeClass('form-error');
            $('#'+prefix+'number_js').parent().addClass('form-ok');
        } else {
            $('#'+prefix+'number_js').parent().removeClass('form-ok');
            $('#'+prefix+'number_js').parent().addClass('form-error');
            $('html, body').animate({
                scrollTop: $('#'+prefix+'number_js').offset().top - 122
            }, 500);
			event.preventDefault();
			event.stopPropagation();
            return false;
        }
    }
    
    if(street_compl != 'no'){
        compl = $('#'+prefix+'complement_js').val().trim();
        compl = compl.replace(/,/g, '');
        
        if(validate_isAddressComplement(compl) || (street_compl == 'yes' && compl == '')){
            $('#'+prefix+'complement_js').parent().removeClass('form-error');
            $('#'+prefix+'complement_js').parent().addClass('form-ok');
        } else {
            $('#'+prefix+'complement_js').parent().removeClass('form-ok');
            $('#'+prefix+'complement_js').parent().addClass('form-error');
            $('html, body').animate({
                scrollTop: $('#'+prefix+'complement_js').offset().top - 30
            }, 500);
			event.preventDefault();
			event.stopPropagation();
            return false;
        }
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
         $('#'+prefix+'address1').val(address1+', '+adressInfos);
    }
    
    if(cep_search_mode == 'auto_prevent'){
        $('#'+prefix+'address1').attr('disabled', false);
        $('#'+prefix+'address2').attr('disabled', false);
        $('#'+prefix+'city').attr('disabled', false);
        $('#'+prefix+'id_state').attr('disabled', false);
    }
    
    return true;
}

function validate_isAddressNumber(s)
{
    return s != null && s != '' && !isNaN(s);
}

function validate_isAddressComplement(s)
{
    return s != null && s != '';
}

function validate_isAddressNumberAndComplement(s)
{
    return s != null && s != '';
}

function validate_isCEP(s, do_search, prefix = '')
{
    var cep = s.replace(/[^\d]+/g,'');
    
    if(cep.length == 8) {
        if(do_search === true || (cep_search_mode != 'manual' && cep_search_mode != 'none')){
            
            $('#'+prefix+'postcode').addClass('wait-check');
            $.getJSON( cep_ws_url, {cep: cep}, function( data ) {
                $('#'+prefix+'postcode').removeClass('wait-check');
                if(data['found'] == 1){
                    
                    $('#'+prefix+'address1').val(data['address']);
                    if(!!$('#address1').attr('data-validate')){
                        validate_field($('#'+prefix+'address1'));
						if(cep_search_mode == 'auto_prevent' && $('#'+prefix+'address1').parent().hasClass('form-ok')){
							$('#'+prefix+'address1').attr('disabled', 'disabled');
						}
                    }
                    
                    $('#'+prefix+'address2').val(data['neighborhood']);
                    if(!!$('#'+prefix+'address2').attr('data-validate')){
                        validate_field($('#'+prefix+'address2'));
						if(cep_search_mode == 'auto_prevent' && $('#'+prefix+'address2').parent().hasClass('form-ok')){
							$('#'+prefix+'address2').attr('disabled', 'disabled');
						}
                    }
                    
                    $('#'+prefix+'city').val(data['city']);
                    if(!!$('#'+prefix+'city').attr('data-validate')){
                        validate_field($('#'+prefix+'city'));
						if(cep_search_mode == 'auto_prevent' && $('#'+prefix+'city').parent().hasClass('form-ok')){
							$('#'+prefix+'city').attr('disabled', 'disabled');
						}
                    }
                    
                    $('#'+prefix+'id_state').val(data['state_id']);
                    var state_name = $('#'+prefix+'id_state option[value='+data['state_id']+']').text();
                    $('#'+prefix+'id_state').parent().find('span').html(state_name);
					if(cep_search_mode == 'auto_prevent' && typeof state_name !== 'undefined' && state_name.length !== 0){
						$('#'+prefix+'id_state').attr('disabled', 'disabled');
					}
					
                    $('#'+prefix+'postcode').parent().removeClass('form-error');
                    $('#'+prefix+'postcode').parent().addClass('form-ok');
                }else{
                    $('#'+prefix+'postcode').parent().removeClass('form-ok');
                    $('#'+prefix+'postcode').parent().addClass('form-error');
					if(cep_search_mode == 'auto_prevent'){
						$('#'+prefix+'address1').attr('disabled', false);
						$('#'+prefix+'address2').attr('disabled', false);
						$('#'+prefix+'city').attr('disabled', false);
						$('#'+prefix+'id_state').attr('disabled', false);
					}
                }
            });
        }
        return true;
    }   
}
