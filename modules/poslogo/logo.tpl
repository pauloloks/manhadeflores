<div class="pos-logo-container pos-content">
	<div class="pos-logo">
		<div class="pos-title"><h2><span>{l s='Our Brands' mod='poslogo'}</span></h2></div>
			<div class="container-inner">
				<div class="row">
					<div class="logo-slider">
						{foreach from=$logos item=logo name=posLogo}
							<div class="item-banklogo">
								<a href ="{$logo.link}">
									<img class="replace-2x img-responsive" src ="{$logo.image}" alt ="{l s='Logo' mod='poslogo'}" />
								</a>
							</div>
						{/foreach}
					
					</div>
					<div class="boxprevnext">
						<a class="prev prevlogo"><i class="icon-chevron-left"></i></a>
						<a class="next nextlogo"><i class="icon-chevron-right"></i></a>
					</div> 
				</div>	
			</div>				
	</div> 
</div>
<script type="text/javascript"> 
		$(document).ready(function() {
			var owl = $(".logo-slider");
			owl.owlCarousel({
			items :5,
			addClassActive: true,
			pagination : false,
			itemsDesktop : [1199,5],
			itemsDesktopSmall : [911,4],
			itemsTablet: [767,3],
			itemsMobile : [360,2]
			});
			$(".nextlogo").click(function(){
			owl.trigger('owl.next');
			})
			$(".prevlogo").click(function(){
			owl.trigger('owl.prev');
			})  
		});
</script>