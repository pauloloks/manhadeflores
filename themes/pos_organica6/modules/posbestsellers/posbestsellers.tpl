
<!-- MODULE Block best sellers -->
<div class="pos-bestsellers col-sm-4 col-md-4 col-xs-12">
	<div class="container">
		<div class="pos-title">
			<h2>
				<span>{l s='Mais Vendidos' mod='posbestsellers'} </span> {l s='Produtos' mod='posbestsellers'}
			</h2>
		</div>
		<div class="row pos-content">
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
								</div>
							<div class="product-contents">
									<h5 class="product-name"><a href="{$product.link|escape:'html'}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></h5>	
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
											<!-- {if $product.specific_prices.reduction_type == 'percentage'}
													<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
												{/if} -->
											{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
											
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
											</ul>
										</div>
									</div>
								</div>
							</div>	
						{if $smarty.foreach.myLoop.iteration % $row == 0 || $smarty.foreach.myLoop.last  }	
						</div>
						{/if}
					{/foreach}
				</div>
			{else}
				<p>{l s='No best sellers at this time' mod='posbestsellers'}</p>
			{/if}
		</div>
	</div>
</div>
<script type="text/javascript"> 
	$(document).ready(function() {
		var owl = $(".pos-topsellers");
		owl.owlCarousel({
		items :{$item},
		slideSpeed: 1000,
		pagination :false,
		navigation:true,
		itemsDesktop : [1199,1],
		itemsDesktopSmall : [991,1],
		itemsTablet: [767,1],
		itemsMobile : [479,1]
		});
	});
</script>
<!-- /MODULE Block best sellers -->
