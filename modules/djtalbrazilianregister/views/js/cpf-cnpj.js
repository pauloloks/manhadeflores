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
    checkPageBrReg();
});

function checkPageBrReg(){
    if($('input[type=radio][name=id_cp_mode]').length > 0) { //cpf-or-cnpj
        if($('input[type=radio][name=id_cp_mode]:checked').length == 1 ) {
            doc_type = $('input[type=radio][name=id_cp_mode]:checked').val();
            setMask('br_document', doc_type);
            $('#br_document').attr('data-validate', 'is'+doc_type.toLocaleUpperCase());
            $('.br_document_rg').hide();
            $('.br_document_ie').hide();
            $('.br_document_sr').hide();
            if(doc_type == 'cpf'){
                $('.br_document_rg').show();
            }else if(doc_type == 'cnpj'){
                $('.br_document_ie').show();
                $('.br_document_sr').show();
            }
        }
        $('input[type=radio][name=id_cp_mode]').on('change', function(){
            doc_type = $('input[type=radio][name=id_cp_mode]:checked').val();
            setMask('br_document', doc_type);
            $('#br_document').attr('data-validate', 'is'+doc_type.toLocaleUpperCase());
            $('.br_document_rg').hide();
            $('.br_document_ie').hide();
            $('.br_document_sr').hide();
            if(doc_type == 'cpf'){
                $('.br_document_rg').show();
            }else if(doc_type == 'cnpj'){
                $('.br_document_ie').show();
                $('.br_document_sr').show();
            }
            if(typeof validate_field != 'undefined'){
                validate_field($('#br_document')[0]);
            }
        });
    } else {
        setMask('br_document_cpf', 'cpf');
        setMask('br_document_cnpj', 'cnpj');
    }
}

function setMask(id, doc_type){
    if(doc_type == 'cpf'){
        $('#'+id).mod_mask('000.000.000-00');
    } else if (doc_type == 'cnpj') {
        $('#'+id).mod_mask('00.000.000/0000-00');
    } else if (doc_type == 'cep') {
        $('#'+id).mod_mask('00000-000');
    } else if (doc_type == 'passport') {
        $('#'+id).mod_unmask();
    }
}

function validate_isCNPJ(s) {
    cnpj = s.replace(/[^\d]+/g,'');
 
    if(cnpj == '') {
        return false;
    }
     
    if (cnpj.length != 14) {
        return false;
    }
 
    // Invalidte known invalid CNPJ
    if (cnpj == '00000000000000' || 
        cnpj == '11111111111111' || 
        cnpj == '22222222222222' || 
        cnpj == '33333333333333' || 
        cnpj == '44444444444444' || 
        cnpj == '55555555555555' || 
        cnpj == '66666666666666' || 
        cnpj == '77777777777777' || 
        cnpj == '88888888888888' || 
        cnpj == '99999999999999') {
            return false;
    }
         
    // Validate DVs
    size = cnpj.length - 2
    numbers = cnpj.substring(0,size);
    digits = cnpj.substring(size);
    sum = 0;
    pos = size - 7;
    
    for (i = size; i >= 1; i--) {
      sum += numbers.charAt(size - i) * pos--;
      if (pos < 2){
        pos = 9;
      }
    }
    
    result = sum % 11 < 2 ? 0 : 11 - sum % 11;
    
    if (result != digits.charAt(0)) {
        return false;
    }
         
    size = size + 1;
    numbers = cnpj.substring(0,size);
    sum = 0;
    pos = size - 7;
    
    for (i = size; i >= 1; i--) {
      sum += numbers.charAt(size - i) * pos--;
      if (pos < 2) {
        pos = 9;
      }
    }
    
    result = sum % 11 < 2 ? 0 : 11 - sum % 11;
    
    if (result != digits.charAt(1)) {
      return false;
    }
           
    return true;   
}

function validate_isCPF(s) {
    //Check if it's a valid CPF
    var sum;
    var rest;
    sum = 0;   
    
    cpf = s.replace(/\.|-/g, '');
    
    if (cpf == '12345678909' || 
        cpf == '00000000000' ||
        cpf == '11111111111' ||
        cpf == '22222222222' ||
        cpf == '33333333333' ||
        cpf == '44444444444' ||
        cpf == '55555555555' ||
        cpf == '66666666666' ||
        cpf == '77777777777' ||
        cpf == '88888888888' ||
        cpf == '99999999999'){
        return false;
    }
    
    for (i=1; i<=9; i++) {
        sum = sum + parseInt(cpf.substring(i-1, i)) * (11 - i); 
    }
    
    rest = (sum * 10) % 11;
    
    if ((rest == 10) || (rest == 11)) {
        rest = 0;
    }
    
    if (rest != parseInt(cpf.substring(9, 10)) ){
        return false;
    }
    
    sum = 0;
    for (i = 1; i <= 10; i++){
       sum = sum + parseInt(cpf.substring(i-1, i)) * (12 - i);
    }
    
    rest = (sum * 10) % 11;
    
    if ((rest == 10) || (rest == 11))  {
        rest = 0;
    }
    
    if (rest != parseInt(cpf.substring(10, 11) ) ) {
        return false;
    }
    return true;
}

function validate_isRG(s) {
    return true;
}

function validate_isIE(s) {
    return true;
}

function validate_isSR(s) {
    return true;
}

function validate_isComp(s) {
    return true;
}

function validate_isPASSPORT(s) {
    var passport = s.replace(/ /g,'');
    if (passport.length >= 9) {
        return true;
    }
    return false;
}
