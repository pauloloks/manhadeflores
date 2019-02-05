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
$(document).on('ready', function(){
    var initiated = false;
    if($('#postcode').length == 1) {
        initiated = true;
        initAdressBehavior();
    } else {
        var inter = setInterval(function(){
            if(!initiated && $('#postcode').length == 1){
                initiated = true;
                initAdressBehavior();
                clearInterval(inter);
            }
        }, 200);
    }
});

function initAdressBehavior() {
    var postCode = $('#postcode');
    
    if(typeof postCode.unmask == 'function') {
        postCode.unmask();
    }
    postCode.mod_mask('00000-000');
    
    //Search button
    if(cep_search_mode == 'manual'){
        postCode.parent().append('<a id="cepSearchButton" class="btn btn-default button button-small"><span><i class="icon-search"></i></span></a>');
    }
    //Don't know my CEP button
    if(cep_dk_button){
        postCode.parent().append('<a class="btn btn-default button button-small" target="blank" href="http://www.buscacep.correios.com.br/servicos/dnec/index.do" id="cepDontKnowButton"><span>Não sei meu CEP</span></a>');
    }
    
    postCode.attr('data-validate', 'isCEP');
    
    //Validate events:
    if(cep_search_mode == 'manual'){
        $('#cepSearchButton').on('click', function() {
            var postVal = '';
            if(typeof postCode != 'undefined'){
                if(typeof postCode.mod_cleanVal == 'function') {
                    postVal = postCode.mod_cleanVal();
                } else {
                    postVal = postCode.val();
                }
            } else {
                postVal = $('#postcode').val();
            }
            validate_isCEP(postVal, true);
        });
    } else {
        postCode.on('keyup', function() {
            if(typeof postCode.mod_cleanVal == 'function') {
                postVal = postCode.mod_cleanVal();
            } else {
                postVal = postCode.val();
            }
            if(postVal.length == 8){
                validate_isCEP(postVal, true);
            }
        });
    }
    
    if(phones_mask === true){
        $('#phone').mod_mask('(00) 0000-0000');
        $('#phone_mobile').mod_mask('(00) 00000-0000');
    }
    
    if(street_num === true){
        if($('.form-group:has(#address1)').length > 0){
            var html = $('.form-group:has(#address1)').prop('outerHTML').replace(/address1/g, 'number_js');
            $(html).insertAfter('.form-group:has(#address1)');
            $('label[for=number_js]').html('Número <sup>*</sup>');
            $('#number_js').val('');
            $('#add_address').on('submit', function(){
                var address1 = $('#address1').val();
                var num = $('#number_js').val();
                $('#address1').val(address1+' '+num);
            });
        }
    }
}

function validate_isCEP(s, do_search)
{
    var cep = s.replace(/[^\d]+/g,'');
    console.log('validate_isCEP: CEP='+cep);
    if(cep.length == 8) {
        if(do_search === true || cep_search_mode != 'manual'){
            
            $('#postcode').addClass('wait-check');
            $.getJSON( cep_ws_url, {cep: cep}, function( data ) {
                $('#postcode').removeClass('wait-check');
                if(data['found'] == 1){
                    $('#address1').val(data['address']);
                    $('#address2').val(data['neighborhood']);
                    $('#city').val(data['city']);
                    $('#id_state').val(data['state_id']);
                    $('#id_state').parent().find('span').html(data['state']);
                    $('#postcode').parent().removeClass('form-error');
                    $('#postcode').parent().addClass('form-ok');
                }else{
                    $('#postcode').parent().removeClass('form-ok');
                    $('#postcode').parent().addClass('form-error');
                }
            });
        }
        return true;
    }
        
}