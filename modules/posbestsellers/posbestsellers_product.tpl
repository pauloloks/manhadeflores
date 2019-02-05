
<!-- MODULE Block best sellers -->
<div class="pos-bestsellers-product ">
	<div class="pos-title">
		<h2>
			<a href="{$link->getPageLink('best-sales')|escape:'html'}" title="{l s='View a top sellers products' mod='posbestsellers'}">
				{l s='Top sellers' mod='posbestsellers'}
			</a>
		</h2>
	</div>
    <div class="row">
        {if $best_sellers && $best_sellers|@count > 0}
            <div class="pos-topsellers">
                {foreach from=$best_sellers item=product name=myLoop}
					{if $smarty.foreach.myLoop.index % 3 == 0 || $smarty.foreach.myLoop.first }
                    <div class="item-bestseller {if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} clearfix">
					{/if}
						<div class="item-product">
							<div class="products-inner">
								<a href="{$product.link|escape:'html'}" title="{$product.legend|escape:'html':'UTF-8'}">
									<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html'}"
										 alt="{$product.legend|escape:'html':'UTF-8'}"/>

								</a>
							</div>	
							<div class="product-contents">
								<div class="contents-inner">
									{if !$PS_CATALOG_MODE}
										<h5 class="product-name">
											<a href="{$product.link|escape:'html'}" title="{$product.legend|escape:'html':'UTF-8'}">
												{$product.name|strip_tags:'UTF-8'|escape:'html':'UTF-8'}<br/>
											</a>
										</h5>
									
									{/if}
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
											{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
												{hook h="displayProductPriceBlock" product=$product type="old_price"}
												<span class="old-price product-price">
													{displayWtPrice p=$product.price_without_reduction}
												</span>
												{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
												{if $product.specific_prices.reduction_type == 'percentage'}
													<span class="price-percent-reduction"><span>-{$product.specific_prices.reduction * 100}%</span></span>
												{/if}
											{/if}
											{hook h="displayProductPriceBlock" product=$product type="price"}
											{hook h="displayProductPriceBlock" product=$product type="unit_price"}
											{hook h="displayProductPriceBlock" product=$product type='after_price'}
										{/if}
										</div>
									{/if}
								</div>	
							</div>	
						</div>		
					{if $smarty.foreach.myLoop.iteration % 3 == 0 || $smarty.foreach.myLoop.last  }	
					</div>
					{/if}
                {/foreach}
            </div>
			<div class="boxprevnext">
				<a class="prev prevsellers"><i class="icon-angle-left"></i></a>
				<a class="next nextsellers"><i class="icon-angle-right"></i></a>
			</div>
        {else}
            <p>{l s='No best sellers at this time' mod='posbestsellers'}</p>
        {/if}
    </div>
</div>


<script type="text/javascript"> 
	$(document).ready(function() {
		var owl = $(".pos-topsellers");
		owl.owlCarousel({
		items :1,
		slideSpeed: 1000,
		 pagination :false,
		itemsDesktop : [1199,1],
		itemsDesktopSmall : [991,1],
		itemsTablet: [767,1],
		itemsMobile : [480,1]
		});
		$(".nextsellers").click(function(){
		owl.trigger('owl.next');
		})
		$(".prevsellers").click(function(){
		owl.trigger('owl.prev');
		})  
	});
</script>
<!-- /MODULE Block best sellers -->
