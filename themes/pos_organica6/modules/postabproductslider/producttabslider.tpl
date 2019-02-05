
<script type="text/javascript">

$(document).ready(function() {

	$(".tabslider_content").hide();
	$(".tabslider_content:first").show(); 

	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		$(".tabslider_content").hide();
		$(".tabslider_content").removeClass("animate1 {$tab_effect}");
		var activeTab = $(this).attr("rel"); 
		$("#"+activeTab) .addClass("animate1 {$tab_effect}");
		$("#"+activeTab).fadeIn().addClass("animatetab");  
	});
});

</script>
<div class="product-tabs-container-slider">
<div class="container">
	<div class="pos-title">
		<!-- <h2>{$title}</h2> -->
		<ul class="tabs"> 
		{$count=0}
		{foreach from=$productTabslider item=productTab name=posTabProduct}
			<li class="{if $smarty.foreach.posTabProduct.first}first_item{elseif $smarty.foreach.posTabProduct.last}last_item{else}{/if} item {if $count==0} active {/if}" rel="tab_{$productTab.id}"  >
				{$productTab.name}
			</li>
				{$count= $count+1}
		{/foreach}	
		</ul>
	</div>
	<div class="pos-content">
		<div class="tab_container"> 
		{foreach from=$productTabslider item=productTab name=posTabProduct}
			<div id="tab_{$productTab.id}" class="tabslider_content">
				<div class="productTabContent">
				{foreach from=$productTab.productInfo item=product name=myLoop}
					{if $smarty.foreach.myLoop.index % $slideOptions.rows == 0 || $smarty.foreach.myLoop.first }
					<div class="slider_item">
					{/if}
						<div class="item-product">
							<div class="products-inner">
								<a class ="bigpic_{$product.id_product}_tabcategory product_image" href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}">
									<img class="img-responsive " src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="{$product.name|escape:html:'UTF-8'}" />
									{hook h="rotatorImg" product=$product}									
								</a>
								{if isset($product.new) && $product.specific_prices} 
								{if $product.specific_prices}<span class="sale-box">{l s='Oferta'}</span>{/if}
							{else}
							{if isset($product.new) && $product.new == 1}<span class="new-box">{l s='New'} </span>{/if}
							{if $product.specific_prices}<span class="sale-box">{l s='Oferta'}</span>{/if}
							{/if}	
								<div class="quick-views">
									{if isset($quick_view) && $quick_view}
										<a class="quick-view" title="{l s='Quick view' mod='postabproductslider'}" href="{$product.link|escape:'html':'UTF-8'}">
											<span>{l s='Quick view' mod='postabproductslider'}</span>
										</a>
									{/if}
								</div>								
							</div>
							<div class="product-contents">
								<div class="manufacturer">{$product.manufacturer_name}</div>
								<h5 class="product-name"><a href="{$product.link|escape:'html'}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:25:'...'|escape:'htmlall':'UTF-8'}</a></h5>
								{capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
								{if $smarty.capture.displayProductListReviews}
									<div class="hook-reviews">
									{hook h='displayProductListReviews' product=$product}
									</div>
								{/if}
								{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
								<div class="price-box">
									{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
									{hook h="displayProductPriceBlock" product=$product type='before_price'}
									<span class="price product-price">
										{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
									</span>
									{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
										{hook h="displayProductPriceBlock" product=$product type="old_price"}
										<span class="old-price product-price">
											{displayWtPrice p=$product.price_without_reduction}
										</span>
										{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
										<!-- {if $product.specific_prices.reduction_type == 'percentage'}
											<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
										{/if} -->
									{/if}
									{hook h="displayProductPriceBlock" product=$product type="price"}
									{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									{hook h="displayProductPriceBlock" product=$product type='after_price'}
								{/if}
								</div>
								{/if}	
								<div class="actions">							
									<div class="actions-inner">
										<ul class="add-to-links">
											<li>
												{hook h='displayProductListFunctionalButtons' product=$product}
											</li>
											<li class="cart">
												{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
												{if ($product.allow_oosp || $product.quantity > 0)}
												{if isset($static_token)}
													<a class="ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow"  title="{l s='Add to cart' mod='postabproductslider'}" data-id-product="{$product.id_product|intval}">
														<span>{l s='Add to cart' mod='postabproductslider'}</span>
														
													</a>
												{else}
												<a class="ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='postabproductslider'}" data-id-product="{$product.id_product|intval}">
													<span>{l s='Add to cart' mod='postabproductslider'}</span>
												</a>
												   {/if}      
												{else}
												<span class="ajax_add_to_cart_button btn btn-default disabled" >
													<span>{l s='Add to cart' mod='postabproductslider'}</span>
												</span>
												{/if}
												{/if}
											</li>
											<li>
											{if isset($comparator_max_item) && $comparator_max_item}
											  <a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare' mod='postabproductslider'}">{l s='Compare' mod='postabproductslider'}
											
											  </a>
											 {/if}
											</li>
										</ul>
									</div>
								</div>
							</div>								
						</div>
					{if $smarty.foreach.myLoop.iteration % $slideOptions.rows == 0 || $smarty.foreach.myLoop.last  }	
					</div>
					{/if}
				{/foreach}
				</div>
			</div>
		{/foreach}		
		</div> <!-- .tab_container -->
	</div>
</div>
</div>
<script type="text/javascript"> 
	$(document).ready(function() {
		var owl = $(".productTabContent");
		owl.owlCarousel({
			autoPlay: {if $slideOptions.auto_play}{if $slideOptions.auto_time}{$slideOptions.auto_time}{else}3000{/if}{else}false{/if},
			items : {if $slideOptions.number_item}{$slideOptions.number_item}{else}4{/if},
			slideSpeed : {if $slideOptions.speed_slide}{$slideOptions.speed_slide}{else}1000{/if},
			navigation : {if $slideOptions.show_arrow} true {else} false {/if},
			pagination : {if $slideOptions.show_pagination}true{else}false{/if},
			stopOnHover : true,
			addClassActive: true,
			 afterAction: function(el){
			this.$owlItems.removeClass('first-active')
			this.$owlItems .eq(this.currentItem).addClass('first-active')  
			},
			itemsDesktop : [1199,3], 
			itemsDesktopSmall : [991,3], 
			itemsTablet: [767,2],
			itemsMobile : [479,1]
		}); 
	});			  
</script>
