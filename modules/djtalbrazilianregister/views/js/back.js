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
    $('form.AdminDjtalBrazilianRegister #cpf').mod_mask('000.000.000-00');
    $('#form-djtalbrazilianregister input[name=djtalbrazilianregisterFilter_cpf]').mod_mask('000.000.000-00');
	
    $('form.AdminDjtalBrazilianRegister #cnpj').mod_mask('00.000.000/0000-00');
    $('#form-djtalbrazilianregister input[name=djtalbrazilianregisterFilter_cnpj]').mod_mask('00.000.000/0000-00');
	
	$('#form-djtalbrazilianregister').on('submit', function(){
		$('#form-djtalbrazilianregister input[name=djtalbrazilianregisterFilter_cpf]').mod_unmask();
		$('#form-djtalbrazilianregister input[name=djtalbrazilianregisterFilter_cnpj]').mod_unmask();
	});
	
    $('#DJTALBR_CEP_TEST_NUM').mod_mask('00000-000');
	
    $('#ajax-test_2, #ajax-test').click(function( event ) {
        event.preventDefault();
        var cep = $('#DJTALBR_CEP_TEST_NUM').mod_cleanVal();
        var ws = $('#DJTALBR_CEP_TEST_WEBSERVICE').val();
        var wsURL = $('#resultCEP').attr('dataurl');
        
        var post = {};
        post['ws-name'] = ws;
        post['cep'] = cep;
        $('#resultCEP').html('');
        $('#resultCEP').hide();
        
        $.getJSON( wsURL, post, function( data ) {
            var html = '';
            if(data['found'] != '0'){
                html = html+'<strong>STATE:</strong> '+data['state']+'<br />';
                html = html+'<strong>CITY:</strong> '+data['city']+'<br />';
                html = html+'<strong>NEIGHBORHOOD:</strong> '+data['neighborhood']+'<br />';
                html = html+'<strong>ADDRESS:</strong> '+data['address']+'<br />';
            } else {
                html = html+'<strong>ERROR:</strong> Found = '+data['found']+'<br />';
            }
            $('#resultCEP').html(html);
            $('#resultCEP').show();
        }).fail(function() {
            var html = '<strong>ERROR...</strong> ';
            $('#resultCEP').html(html);
            $('#resultCEP').show();
        });
    });
});
