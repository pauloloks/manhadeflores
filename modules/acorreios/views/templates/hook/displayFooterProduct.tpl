
{if $is_virtual!=1}
<HR>

<input id="acorreios_simulador_url" type="hidden" value="{$simulador}">
<input id="acorreios_id_product" type="hidden" value="{$id_product}">
<P style="font-family:Arial; font-size:12px; color:#000000; text-align: right;">Informe o CEP do endereço de entrega para calcular o valor do frete: 
<input class="acorreios-mask-cep acorreios-col-lg-15" type="text" id="acorreios_cep" placeholder="" value="{$acorreios['cepCookie']}" style="padding:5px; width: 80px"/>
            <input class="btn-primary" style="padding:5px; " type="button" onclick="acorreios_enviar_produto();" name="" value="{l s='Calcular' mod='acorreios'}"/> </P>

<div id="acorreios_carregando" style="display: none;">
<div id="fountainG">
    <div id="fountainG_1" class="fountainG"></div>
    <div id="fountainG_2" class="fountainG"></div>
    <div id="fountainG_3" class="fountainG"></div>
    <div id="fountainG_4" class="fountainG"></div>
    <div id="fountainG_5" class="fountainG"></div>
    <div id="fountainG_6" class="fountainG"></div>
    <div id="fountainG_7" class="fountainG"></div>
    <div id="fountainG_8" class="fountainG"></div>
</div>
</div>

<div id="acorreios_resultado_frete">

{if isset($acorreios['transportadoras'])}

                {foreach $acorreios['transportadoras'] as $transp}
<HR>
        <div class="row">
        <div class="col col-md-10 col-xs-12">
        <P style="text-align: right; font-family: Arial; Color: #000000; font-size:12px; padding-top:10px; padding-bottom:10px;"><b>{$transp['nomeTransportadora']} - {$transp['valorFrete']}</b>
                   <BR>
                    Prazo de Entrega: {$transp['prazoEntrega']} dias úteis
                    
                        </P>
        </div>

        <div class="col col-md-2 col-xs-12">

            <img src="{$transp['url_logo']}" style="padding:15px; float:right" />
        </div>
                 </div>
                {/foreach}
         

            {if $acorreios['msgTransp'] != ''}
                <div class="acorreios-msg-transp">
                    {$acorreios['msgTransp']}
                </div>
            {/if}


    {/if}

</div>

{/if}

<style>


#fountainG{
    position:relative;
    width:168px;
    height:20px;
    margin:auto;
}

.fountainG{
    position:absolute;
    top:0;
    background-color:rgba(163,163,163,0.97);
    width:20px;
    height:20px;
    animation-name:bounce_fountainG;
        -o-animation-name:bounce_fountainG;
        -ms-animation-name:bounce_fountainG;
        -webkit-animation-name:bounce_fountainG;
        -moz-animation-name:bounce_fountainG;
    animation-duration:1.235s;
        -o-animation-duration:1.235s;
        -ms-animation-duration:1.235s;
        -webkit-animation-duration:1.235s;
        -moz-animation-duration:1.235s;
    animation-iteration-count:infinite;
        -o-animation-iteration-count:infinite;
        -ms-animation-iteration-count:infinite;
        -webkit-animation-iteration-count:infinite;
        -moz-animation-iteration-count:infinite;
    animation-direction:normal;
        -o-animation-direction:normal;
        -ms-animation-direction:normal;
        -webkit-animation-direction:normal;
        -moz-animation-direction:normal;
    transform:scale(.3);
        -o-transform:scale(.3);
        -ms-transform:scale(.3);
        -webkit-transform:scale(.3);
        -moz-transform:scale(.3);
    border-radius:13px;
        -o-border-radius:13px;
        -ms-border-radius:13px;
        -webkit-border-radius:13px;
        -moz-border-radius:13px;
}

#fountainG_1{
    left:0;
    animation-delay:0.496s;
        -o-animation-delay:0.496s;
        -ms-animation-delay:0.496s;
        -webkit-animation-delay:0.496s;
        -moz-animation-delay:0.496s;
}

#fountainG_2{
    left:21px;
    animation-delay:0.6125s;
        -o-animation-delay:0.6125s;
        -ms-animation-delay:0.6125s;
        -webkit-animation-delay:0.6125s;
        -moz-animation-delay:0.6125s;
}

#fountainG_3{
    left:42px;
    animation-delay:0.739s;
        -o-animation-delay:0.739s;
        -ms-animation-delay:0.739s;
        -webkit-animation-delay:0.739s;
        -moz-animation-delay:0.739s;
}

#fountainG_4{
    left:63px;
    animation-delay:0.8655s;
        -o-animation-delay:0.8655s;
        -ms-animation-delay:0.8655s;
        -webkit-animation-delay:0.8655s;
        -moz-animation-delay:0.8655s;
}

#fountainG_5{
    left:84px;
    animation-delay:0.992s;
        -o-animation-delay:0.992s;
        -ms-animation-delay:0.992s;
        -webkit-animation-delay:0.992s;
        -moz-animation-delay:0.992s;
}

#fountainG_6{
    left:105px;
    animation-delay:1.1085s;
        -o-animation-delay:1.1085s;
        -ms-animation-delay:1.1085s;
        -webkit-animation-delay:1.1085s;
        -moz-animation-delay:1.1085s;
}

#fountainG_7{
    left:126px;
    animation-delay:1.235s;
        -o-animation-delay:1.235s;
        -ms-animation-delay:1.235s;
        -webkit-animation-delay:1.235s;
        -moz-animation-delay:1.235s;
}

#fountainG_8{
    left:147px;
    animation-delay:1.3615s;
        -o-animation-delay:1.3615s;
        -ms-animation-delay:1.3615s;
        -webkit-animation-delay:1.3615s;
        -moz-animation-delay:1.3615s;
}



@keyframes bounce_fountainG{
    0%{
    transform:scale(1);
        background-color:rgb(163,163,163);
    }

    100%{
    transform:scale(.3);
        background-color:rgb(255,255,255);
    }
}

@-o-keyframes bounce_fountainG{
    0%{
    -o-transform:scale(1);
        background-color:rgb(163,163,163);
    }

    100%{
    -o-transform:scale(.3);
        background-color:rgb(255,255,255);
    }
}

@-ms-keyframes bounce_fountainG{
    0%{
    -ms-transform:scale(1);
        background-color:rgb(163,163,163);
    }

    100%{
    -ms-transform:scale(.3);
        background-color:rgb(255,255,255);
    }
}

@-webkit-keyframes bounce_fountainG{
    0%{
    -webkit-transform:scale(1);
        background-color:rgb(163,163,163);
    }

    100%{
    -webkit-transform:scale(.3);
        background-color:rgb(255,255,255);
    }
}

@-moz-keyframes bounce_fountainG{
    0%{
    -moz-transform:scale(1);
        background-color:rgb(163,163,163);
    }

    100%{
    -moz-transform:scale(.3);
        background-color:rgb(255,255,255);
    }
}

</style>