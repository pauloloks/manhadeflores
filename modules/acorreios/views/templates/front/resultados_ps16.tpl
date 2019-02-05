{if isset($acorreios['transportadoras'])}

                {foreach $acorreios['transportadoras'] as $transp}
<HR>
        <div class="row">
        <div class="col col-md-9 col-xs-12">
        <P style="text-align: right; font-family: Arial; Color: #000000; font-size:12px; padding-top:10px; padding-bottom:10px;"><b>{$transp['nomeTransportadora']} - {$transp['valorFrete']}</b>
                   <BR>
                    Prazo de Entrega: {$transp['prazoEntrega']} dias úteis
                    
                        </P>
        </div>

        <div class="col col-md-3 col-xs-12">

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