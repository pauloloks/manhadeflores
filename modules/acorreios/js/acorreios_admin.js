

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