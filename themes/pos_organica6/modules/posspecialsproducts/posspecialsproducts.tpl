
	{if isset($products) AND $products}

<div class="col-md-4 col-xs-12 special-right">
	<div id="special_products_home" class="products_block">
			<div class="pos-title">
				<h2>
					 <span>{l s='Deals of the day' mod='posspecialsproducts'}</span>
				</h2>
			</div>
			<div class="block_content">
				{assign var='liHeight' value=260}
				{assign var='nbItemsPerLine' value=3}
				{assign var='nbLi' value=$products|@count}
				{assign var='nbLines' value=ceil($nbLi/$nbItemsPerLine)}
				{assign var='ulHeight' value=$nbLines*$liHeight}
				{assign var='nub' value=$row*$item}
				
				<div class="pos-content">
					<div id="pos-special-products">
					{foreach from=$products item=product name=myLoop}
					{if $smarty.foreach.myLoop.index % 1 == 0 || $smarty.foreach.myLoop.first }
						<div class="item-special">
					{/if}
						<div class="item-product">
						
							<div class="products-inner">
								<a href="{$product.link|escape:'html'}" title="{$product.legend|escape:'html':'UTF-8'}" class="content_img clearfix">
									<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}"
										class="img-responsive" alt="{$product.legend|escape:'html':'UTF-8'}"/>

								</a>
								<!-- {if isset($product.new) && $product.new == 1}
									<a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
										<span class="new">{l s='New' mod='posspecialsproducts'}</span>
									</a>
								{/if}
								{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
									<a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
										<span class="sale">{l s='Sale!' mod='posspecialsproducts'}</span>
									</a>
								{/if} -->
							</div>
							<div class="product-contents">
								<h5 class="product-name">
									<a href="{$product.link|escape:'html'}" title="{$product.legend|escape:'html':'UTF-8'}">
									{$product.name|strip_tags:'UTF-8'|escape:'html':'UTF-8'}<br/>
									</a>
								</h5>
								{capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
								{if $smarty.capture.displayProductListReviews}
									<div class="hook-reviews">
									{hook h='displayProductListReviews' product=$product}
									</div>
								{/if}
								{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
								<div  class="price-box">
									{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
						
										<span class="price product-price">
											{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
										</span>
										{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
											{hook h="displayProductPriceBlock" product=$product type="old_price"}
											<span class="old-price product-price">
												{displayWtPrice p=$product.price_without_reduction}
											</span>
											<!-- {hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
											{if $product.specific_prices.reduction_type == 'percentage'}
												<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
											{/if} -->
										{/if}
										{hook h="displayProductPriceBlock" product=$product type="price"}
										{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									{/if}
								
								</div>	
								{/if}
								<!-- <div class="actions">
									<div class="actions-inner">
										<ul class="add-to-links">
											<div class="cart">
												{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
												{if ($product.allow_oosp || $product.quantity > 0)}
												{if isset($static_token)}
													<a class="ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow"  title="{l s='Add to cart' mod='posspecialsproducts'}" data-id-product="{$product.id_product|intval}">
														<span>{l s='Add to cart' mod='posspecialsproducts'}</span>
														
													</a>
												{else}
												<a class="ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='posspecialsproducts'}" data-id-product="{$product.id_product|intval}">
													<span>{l s='Add to cart' mod='posspecialsproducts'}</span>
												</a>
												   {/if}      
												{else}
												<span class="ajax_add_to_cart_button btn btn-default disabled" >
													<span>{l s='Add to cart' mod='posspecialsproducts'}</span>
												</span>
												{/if}
												{/if}
											</div>
										</ul>
									</div>
								</div> -->
								<!-- <p class="product-desc" itemprop="description">
									{$product.description_short|strip_tags:'UTF-8'|truncate:125:'...'}
								</p> -->
							</div>	
							<div class="countdown">
								{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction && $product.specific_prices.to|date_format:"%Y" !=0 }
									{hook h='timecountdown' product=$product }  
									<span id="future_date_{$product.id_category_default}_{$product.id_product}" data-time ='
								   {$product.specific_prices.to|date_format:"%Y"} ,{$product.specific_prices.to|date_format:"%m"}-1 ,{$product.specific_prices.to|date_format:"%d"},{$product.specific_prices.to|date_format:"%H"}, {$product.specific_prices.to|date_format:"%M"}, {$product.specific_prices.to|date_format:"%S"}' class="id_countdown">  </span>
								{/if}

							</div>
						</div>
						{if $smarty.foreach.myLoop.iteration % 1 == 0 || $smarty.foreach.myLoop.last  }
						</div>
					{/if}
					{/foreach}
					</div>
				</div>
				
			</div>
	</div>
</div>

<script>


    $(document).ready(function() {
     
    var owl = $("#pos-special-products");
     
    owl.owlCarousel({
	autoPlay : false,
	 pagination :false,
    items : 1,
			slideSpeed: 1000,
		itemsDesktop : [1199,1],
		itemsDesktopSmall : [991,2],
		itemsTablet: [767,2],
		itemsMobile : [479,1],
		 afterAction: function(el){
			this.$owlItems.removeClass('first-active')
			this.$owlItems .eq(this.currentItem).addClass('first-active')  
		 },
		navigation : true,
    });    
    });
</script>
	{/if}