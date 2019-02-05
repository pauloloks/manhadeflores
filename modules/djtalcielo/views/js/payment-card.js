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
    $('#djtalcielo_payment_form #card_number').mod_mask('0000-0000-0000-0000000');
    //$('#djtalcielo_payment_form #card_expiration_month').mod_mask('00');
    //$('#djtalcielo_payment_form #card_expiration_year').mod_mask('00');
    $('#djtalcielo_payment_form #card_cvv').mod_mask('000');
  
    var myDate = new Date();
    var year = myDate.getFullYear();
    var options = "";
    for(var i = year; i < year+20; i++){
        options += '<option value="'+i+'">'+i+'</option>';
    }
    $('#card_expiration_year').append(options);
  
    var form = $('#djtalcielo_payment_form');

    form.submit(function(event) { // When the form is submited
        return validateForm();
    });
});

function getCreditCardLabel(cardNumber){
    
  cardNumber = cardNumber.replace(/\D/g,'');
  count  = cardNumber.length;
  $('#djtalcielo_payment_form #card_cvv').mod_mask('0000');

  var regexVisa = /^4[0-9]{12}(?:[0-9]{3})?/;
  var regexMaster = /^5[1-5][0-9]{14}/;
  var regexAmex = /^3[47][0-9]{13}/;
  var regexDiners = /^3(?:0[0-5]|[68][0-9])[0-9]{11}/;
  var regexDiscover = /^6(?:011|5[0-9]{2})[0-9]{12}/;
  var regexJCB = /^(?:2131|1800|35\d{3})\d{11}/;
  var regexHiperCard = /^(606282\d{10}(\d{3})?)|(3841\d{15})$/;
  var regexAura = /^(5078\d{2})(\d{2})(\d{11})$/;
    
 /* if(regexJCB.test(cardNumber)){
    hideFlags('jcb');
  }
  if(regexAmex.test(cardNumber)){
    console.log('amex');
  }
  */
    
  if(regexHiperCard.test(cardNumber) && count <= 16){
   //hideFlags('hipercard');
  }else if(regexAura.test(cardNumber) && count > 16){
    //hideFlags('aura');
  }else if(regexVisa.test(cardNumber) && count <= 16){
    //hideFlags('visa');
  }else if(regexMaster.test(cardNumber) && count <= 16){
    //hideFlags('master');
  }else if(regexDiners.test(cardNumber) && count <= 16){
    //hideFlags('diners');
  }else if(regexDiscover.test(cardNumber) && count <= 16){
    //hideFlags('discover');
    $('#djtalcielo_payment_form #card_cvv').mod_mask('0000');
  }else{
      //hideFlags('');
  }

  return '';

}

function hideFlags(name){
    
    var flags = ["master", "visa", "hipercard", "diners","discover","aura"];
    if(name.length > 0){
        for(var i = 0;i < flags.length;i++) {
            if(flags[i] == name){
                $('#img_'+flags[i]).show();
            }else{
                $('#img_'+flags[i]).hide();
            }
        }
    }else{
        for(var i = 0;i < flags.length;i++) {
            $('#img_'+flags[i]).show();
        }
    }
}

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
    
    var cardHolderName = $('#djtalcielo_payment_form #card_holder_name').val();
    var cardExpirationMonth = $('#djtalcielo_payment_form #card_expiration_month').val();
    var cardExpirationYear = $('#djtalcielo_payment_form #card_expiration_year').val();
    var cardNumber = $('#djtalcielo_payment_form #card_number').val();
    if(cardNumber != ''){
        cardNumber = cardNumber.replace(/-/g, '');
    }
    var cardCVV = $('#djtalcielo_payment_form #card_cvv').val();
    var customerFisc = $('#djtalcielo_payment_form #customer_fisc').val();
    customerFisc = customerFisc.replace(/\D/g,'');    
    
    // pega os erros de validação nos campos do form
    var fieldErrors = {};
    if(cardNumber == ''){
        fieldErrors['card_number'] = 'O número do cartão tem que ser preenchido';
    }
    if(cardHolderName == ''){
        fieldErrors['card_holder_name'] = 'O nome do portador do cartão tem que ser preenchido';
    }
    if(cardExpirationMonth == ''){
        fieldErrors['card_expiration_month'] = 'O mês de vencimento do cartão tem que ser preenchido';
    }
    if(cardExpirationYear == ''){
        fieldErrors['card_expiration_year'] = 'O ano de vencimento do cartão tem que ser preenchido';
    }
    if(cardCVV == ''){
        fieldErrors['card_cvv'] = 'O número do código verificador tem que ser preenchido';
    }
    if(customerFisc == ''){
        fieldErrors['customer_fisc'] = 'O CPF ou o CNPJ tem que ser preenchido';
    }
    if(!valid_credit_card(cardNumber)){
        fieldErrors['card_number'] = 'O número do cartão não é valido';
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

// takes the form field value and returns true on valid number
function valid_credit_card(value) {
  // accept only digits, dashes or spaces
    if (/[^0-9-\s]+/.test(value)) return false;

    // The Luhn Algorithm. It's so pretty.
    var nCheck = 0, nDigit = 0, bEven = false;
    value = value.replace(/\D/g, "");

    for (var n = value.length - 1; n >= 0; n--) {
        var cDigit = value.charAt(n),
              nDigit = parseInt(cDigit, 10);

        if (bEven) {
            if ((nDigit *= 2) > 9) nDigit -= 9;
        }

        nCheck += nDigit;
        bEven = !bEven;
    }

    return (nCheck % 10) == 0;
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