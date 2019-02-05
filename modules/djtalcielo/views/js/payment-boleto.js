/**
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
*  @author    DJTAL
*  @copyright 2015 DJTAL
*  @version   1.0.0
*  @link      http://www.djtal.com.br/
*  @license
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$(document).ready(function(){
    var form = $('#djtalcielo_payment_form');
    form.submit(function(event) { // When the form is submited
        return validateForm();
    });
});

function CpforCnpj(){
    var customerFisc = $('#djtalcielo_payment_form #customer_fisc').val();
    customerFisc = customerFisc.replace(/\D/g,'');        
                    
    if(customerFisc.length > 11){
        $('#djtalcielo_payment_form #customer_fisc').mod_mask('00.000.000/0000-00');
    } else if (customerFisc.length == 11){
        $('#djtalcielo_payment_form #customer_fisc').mod_mask('000.000.000-00000');
    }
    
}

function validateForm() {
    var form = $('#djtalcielo_payment_form');
    
    var customerFisc = $('#djtalcielo_payment_form #customer_fisc').val();
    customerFisc = customerFisc.replace(/\D/g,'');    
    var fieldErrors = {};

    if(customerFisc == ''){
        fieldErrors['customer_fisc'] = 'O CPF ou o CNPJ tem que ser preenchido';
    }
    if(customerFisc.length == 14){
        if(!isCNPJ(customerFisc)){
            fieldErrors['customer_fisc'] = 'O CNPJ está errado';
        }

    } else if (customerFisc.length == 11){
        if(!isCPF(customerFisc)){
            fieldErrors['customer_fisc'] = 'O CPF está errado';
        }
    } else {
        fieldErrors['customer_fisc'] = 'O CPF ou o CNPJ está mal preenchido';
    }

    //Verifica se há erros
    var hasErrors = false;
    for(var field in fieldErrors) { hasErrors = true; break; }

    $('#djtalcielo_payment_form input').removeClass('error');
    $('#djtalcielo_payment_form #field_errors').html('');
    $('#djtalcielo_payment_form #field_errors').hide();
    
    if(hasErrors) {
        // realiza o tratamento de errors
        var errorText = '';
        var count = 0;
        for(var key in fieldErrors) {
            count++;
            $('#djtalcielo_payment_form #'+key).addClass('error');
            errorText = errorText + fieldErrors[key] + '<br />';
        }
        if(count == 1) {
            errorText = '<strong> Um erro foi detectado: </strong> <br /><br />' + errorText;
        } else {
            errorText = '<strong> Varios erros foram detectados: </strong> <br /><br />' + errorText;
        }
        $('#djtalcielo_payment_form #field_errors').html(errorText);
        $('#djtalcielo_payment_form #field_errors').show();
        
    } else {
        form.get(0).submit();
    }
    return false;
}

function isCNPJ(s) {
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

function isCPF(s) {
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