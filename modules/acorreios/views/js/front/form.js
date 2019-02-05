
jQuery(function() {
    $('.acorreios-mask-cep').mask('99999-999');
});


$(document).ready(function(){

    var cepAtual = $('#acorreios_cep_adic_carrinho').val();

    if (typeof cepAtual != 'undefined') {
        cepAtual = cepAtual.replace(/[^0-9]/g,'');

        if (cepAtual.length == 8) {

            var linkAtual = $('#acorreios_link_cep_adic_carrinho').attr('href');
            var novoLink = linkAtual + '&cep=' + cepAtual;

            $('#acorreios_link_cep_adic_carrinho').attr('href', novoLink);

        }
    }

});

function acorreios_enviar_produto()
{
   $('#acorreios_resultado_frete').hide();
   var baseUri = $('#acorreios_simulador_url').val();
   var id_product = $('#acorreios_id_product').val();
   var cep = $('#acorreios_cep').val();
   $('#acorreios_carregando').show();

   if(baseUri.indexOf("?") == -1)
   {
        url_req = baseUri + '?rand=' + new Date().getTime();
   }
   else
   {
        url_req = baseUri + '&rand=' + new Date().getTime();
   }

   $.ajax({
    type: 'POST',
    headers: { "cache-control": "no-cache" },
    url: url_req,
    async: true,
    cache: false,
    dataType : "html",
    data: 'cep='+cep+'&id_product='+id_product+'&origem=produto',
    success: function(retorno)
    {
      $('#acorreios_carregando').hide();
      $('#acorreios_resultado_frete').html(retorno);
      $('#acorreios_resultado_frete').show();
    }
  });
}

function acorreios_enviar_carrinho()
{
  $('#acorreios_lista_frete').hide();
   var baseUri = $('#acorreios_simulador_url').val();
   //var id_product = $('#acorreios_id_product').val();
   var cep = $('#acorreios_cep').val();
   $('#acorreios_carregando').show();

   if(baseUri.indexOf("?") == -1)
   {
        url_req = baseUri + '?rand=' + new Date().getTime();
   }
   else
   {
        url_req = baseUri + '&rand=' + new Date().getTime();
   }

   $.ajax({
    type: 'POST',
    headers: { "cache-control": "no-cache" },
    url: url_req,
    async: true,
    cache: false,
    dataType : "html",
    data: 'cep='+cep+'&origem=carrinho',
    success: function(retorno)
    {
      $('#acorreios_carregando').hide();
      $('#acorreios_lista_frete').html(retorno);
      $('#acorreios_lista_frete').show();
    }
  });

}

jQuery(document).ready(function(){

    jQuery(document).on('keyup', 'input[name=acorreios_cep_adic_carrinho]', function(e) {

        var cep = jQuery(this).val();

        if (typeof cep != 'undefined') {
            cep = cep.replace(/[^0-9]/g, '');

            if (cep.length == 8) {

                var linkAtual = jQuery('#acorreios_link_cep_adic_carrinho').attr('href');
                var novoLink = linkAtual + '&cep=' + cep;

                jQuery('#acorreios_link_cep_adic_carrinho').attr('href', novoLink);

            }
        }
    });

});



