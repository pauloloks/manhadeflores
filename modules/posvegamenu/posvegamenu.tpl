
<div class="navleft-container visible-lg visible-md ">
	<div class="pt_vmegamenu_title">
		<h2><i class="icon-reorder"></i>{l s='All categories' mod='posvegamenu'}</h2>
	</div>
    <div id="pt_vmegamenu" class="pt_vmegamenu">
        {$megamenu}
    </div>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
//<![CDATA[
var VMEGAMENU_POPUP_EFFECT = {$effect};
//]]>

$(document).ready(function(){
    $("#pt_ver_menu_link ul li").each(function(){
        var url = document.URL;
        $("#pt_ver_menu_link ul li a").removeClass("act");
        $('#pt_ver_menu_link ul li a[href="'+url+'"]').addClass('act');
    }); 
        
    $('.pt_menu').hover(function(){
        if(VMEGAMENU_POPUP_EFFECT == 0) $(this).find('.popup').stop(true,true).slideDown('slow');
        if(VMEGAMENU_POPUP_EFFECT == 1) $(this).find('.popup').stop(true,true).fadeIn('slow');
        if(VMEGAMENU_POPUP_EFFECT == 2) $(this).find('.popup').stop(true,true).show('slow');
    },function(){
        if(VMEGAMENU_POPUP_EFFECT == 0) $(this).find('.popup').stop(true,true).slideUp('fast');
        if(VMEGAMENU_POPUP_EFFECT == 1) $(this).find('.popup').stop(true,true).fadeOut('fast');
        if(VMEGAMENU_POPUP_EFFECT == 2) $(this).find('.popup').stop(true,true).hide('fast');
    })
	var count_block = $('.pt_menu_block').length; 
	var number_blocks = 9 ;
	 if(count_block < number_blocks) return false; 
	$('.pt_menu_block').each(function(i,n){
		if(i == number_blocks) {
		$(this).parent().append('<div class="view_more"><i class="icon-plus-circle"></i> More Categories</div>');
		}
		if(i> number_blocks) {
		  $(this).addClass('hide_menu_block');
		}
		
	})
	$('.hide_menu_block').hide();
	$('.view_more').click(function() {
			$(this).toggleClass('show');
			if($(this).hasClass('show')){
				$(this).addClass('open_menu');
				$(this).html('<em class="closed-menu"><i class="icon-minus-circle"></i> Close Categories</em>');
				$('.hide_menu_block').slideDown();	
			}
			else
			{
				$(this).removeClass('open_menu').addClass('close_menu');
				$(this).html('<em class="closed-menu"><i class="icon-plus-circle"></i> More Categories</em>'); 
				$('.hide_menu_block').slideUp();
				
			}
	
	});
	
});
   	$(function(){
		$('.pt_vmegamenu_title').click(function(){
			$(this).parent().find('.pt_vmegamenu').slideToggle('fast');
		
			});	
		});
</script>
