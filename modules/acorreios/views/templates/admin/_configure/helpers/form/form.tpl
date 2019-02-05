{extends file="helpers/form/form.tpl"}

{block name='defaultForm'}
    <ul id="sp-config-tabs">
        {foreach $sptabs as $tabTitle => $tabClass}
            <li class="tab" data-tab="{$tabClass}">
                {$tabTitle}
            </li>
        {/foreach}
		
    </ul>
	
    {$smarty.block.parent}
	
	<div class="footer-section">
			<span>Desenvolvido por <a href="https://www.prestafy.com.br/" target="_blank">Prestafy</a></span>
	</div>
	
{/block}




{block name="input"}
	
  
    

    {$smarty.block.parent}

{/block}