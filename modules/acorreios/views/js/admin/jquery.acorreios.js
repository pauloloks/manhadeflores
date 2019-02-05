$(document).ready(function(){

    // Make the first tab active
    var $_firstTab = $('#sp-config-tabs .tab').first();
    $_firstTab.addClass('active');

    var firstTabContentID = '#' + $_firstTab.attr('data-tab');
    $('#configuration_form .panel').first().show();

    // On tab click
    $('#sp-config-tabs .tab').on('click', function()
    {
        var tabContentID =  $(this).data('tab');
        $('#configuration_form .panel').animate({ opacity: 0 }, 0).css("display","none");
        $('[id^="'+tabContentID+'"]').css("display","block").animate({ opacity: 1 }, 200);

        $('#sp-config-tabs .tab').removeClass('active');
        $(this).addClass('active');
    });
	
	$('.fontOptions').trigger('change');
    
	
});


var handle_font_change = function(that,systemFonts){
    var systemFontsArr = systemFonts.split(',');
    var selected_font = $(that).val();
    var identi = $(that).attr('id');
	
    if(!$('#'+identi+'_link').size())
        $('head').append('<link id="'+identi+'_link" rel="stylesheet" type="text/css" href="" />');
    if($.inArray(selected_font, systemFontsArr)<0)
        $('link#'+identi+'_link').attr({href:'http://fonts.googleapis.com/css?family=' + selected_font.replace(' ', '+')});
    $('#'+identi+'_example').css('font-family',selected_font);
    
};




//Executa estados um a um até chegar no último
function acorreios_gera_dados_offline(idEspCorreios, idTabOffline, tipo) 
{

    if(typeof idTabOffline == 'undefined')
    {
        idTabOffline = 1;
    }

    if(typeof tipo =='undefined')
    {
        tipo='capital';
    }
    //var urlFuncoes = $('#acorreios_url_funcoes').val();
    var urlFuncoes='https://localhost/prestafy/modules/acorreios/funcoes.php';
    var erro = false;

    // Chama funcoes.php
    $.ajax({
        type: "POST",
        async: false,
        url: urlFuncoes,
        data: {func: "1", idEspCorreios: idEspCorreios, idTabOffline: idTabOffline, tipo: tipo}
    }).done(function(retorno) {

        retorno = retorno.trim();

        if (retorno.substr(0, 4) != "erro") 
        {
            if(idTabOffline==30 && tipo =='capital')
            {
                idTabOffline=1; tipo='interior';
                acorreios_gera_dados_offline(idEspCorreios,idTabOffline,tipo);

            }
            if(idTabOffline ==30 && tipo=='interior')
            {
                alert('Todas as tabelas foram geradas com sucesso!');
            }
            else
            {
                idTabOffline++;
                acorreios_gera_dados_offline(idEspCorreios,idTabOffline,tipo);
            }
            
        }
    });
 
}