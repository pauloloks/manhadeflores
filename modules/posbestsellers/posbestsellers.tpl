
<!-- MODULE Block best sellers -->
<div class="pos-bestsellers pos-content">
	<div class="pos-title">
		<h2>
			<a href="{$link->getPageLink('best-sales')|escape:'html'}" title="{l s='View a top sellers products' mod='posbestsellers'}">
				{l s='Top sellers' mod='posbestsellers'}
			</a>
		</h2>
	</div>
    <div class="row_edit">
        {if $best_sellers && $best_sellers|@count > 0}
            <div class="pos-topsellers">
                {foreach from=$best_sellers item=product name=myLoop}
					{if $smarty.foreach.myLoop.index % $row == 0 || $smarty.foreach.myLoop.first }
                    <div class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} clearfix">
					{/if}
								<div class="item-product">
							<div class="products-inner">
								<a href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}" class="product_image"><img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="{$product.name|escape:html:'UTF-8'}" />
								
								</a>
								{if isset($product.new) && $product.new == 1}
									<a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
										<span class="new-label">{l s='New' mod='posbestsellers'}</span>
									</a>
								{/if}
								{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
									<a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
										<span class="sale-label">{l s='Sale!' mod='posbestsellers'}</span>
									</a>
								{/if}
						
								<div class="actions">															
									<div class="actions-inner">
										<div class="cart">
											{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
											{if ($product.allow_oosp || $product.quantity > 0)}
											{if isset($static_token)}
												<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow"  title="{l s='Add to cart' mod='posbestsellers'}" data-id-product="{$product.id_product|intval}">
													<span>{l s='Add to cart' mod='posbestsellers'}</span>
													
												</a>
											{else}
											<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='posbestsellers'}" data-id-product="{$product.id_product|intval}">
												<span>{l s='Add to cart' mod='posbestsellers'}</span>
											</a>
											   {/if}      
											{else}
											<span class="button ajax_add_to_cart_button btn btn-default disabled" >
												<span>{l s='Add to cart' mod='posbestsellers'}</span>
											</span>
											{/if}
											{/if}
										</div>
										<ul class="add-to-links">
											<li>
												{hook h='displayProductListFunctionalButtons' product=$product}	
											</li>
											<li>
											{if isset($comparator_max_item) && $comparator_max_item}
											  <a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare' mod='posbestsellers'}">{l s='Compare' mod='posbestsellers'}
											
											  </a>
											 {/if}
											</li>
											<li>
												{if isset($quick_view) && $quick_view}
													<a class="quick-view" title="{l s='Quick view' mod='posbestsellers'}" href="{$product.link|escape:'html':'UTF-8'}">
														<span>{l s="Quick view" mod='posbestsellers'}</span>
													</a>
												{/if}
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="product-contents">
								<div class="content-inner">
									<h5 class="product-name"><a href="{$product.link|escape:'html'}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></h5>							
									{capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
									{if $smarty.capture.displayProductListReviews}
										<div class="hook-reviews">
										{hook h='displayProductListReviews' product=$product}
										</div>
									{/if}
								</div>
								<!-- <div class="product_desc"><a href="{$product.link|escape:'html'}" title="{l s='More' mod='posbestsellers'}">{$product.description_short|strip_tags|truncate:65:'...'}</a></div> -->
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
										{if $product.specific_prices.reduction_type == 'percentage'}
												<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
											{/if}
										{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
										
									{/if}
									{hook h="displayProductPriceBlock" product=$product type="price"}
									{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									{hook h="displayProductPriceBlock" product=$product type='after_price'}
								{/if}
								</div>
								{/if}
							</div>
						</div>	
					{if $smarty.foreach.myLoop.iteration % $row == 0 || $smarty.foreach.myLoop.last  }	
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
		items :5,
		slideSpeed: 1000,
		 pagination :false,
		itemsDesktop : [1199,4],
		itemsDesktopSmall : [991,3],
		itemsTablet: [767,2],
		itemsMobile : [479,1]
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
