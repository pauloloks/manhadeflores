
	<div class="pos-accessories block_product">
		{if $title}
		<div class="pos-title">
			<h2>{$title}</h2>
		</div>
		{/if}
		<div class=" row pos-content">
			<div class="accessories_carousel">
				{foreach from=$products item=product name=myLoop}
					{if $smarty.foreach.myLoop.index % $slider_options.rows == 0 || $smarty.foreach.myLoop.first }
					<div class="item_featured">
					{/if}
						<div class="item-product">
							<div class="products-inner">
								<a class ="bigpic_{$product.id_product}_tabcategory product_image" href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}">
									<img class="img-responsive lazyOwl" data-src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="{$product.name|escape:html:'UTF-8'}" />
									{hook h="rotatorImg" product=$product}									
								</a>
								{if isset($product.new) && $product.new == 1}
									<a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
										<span class="new-label">{l s='New' mod='posaccessoriesproducts'}</span>
									</a>
								{/if}
								{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
									<a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
										<span class="sale-label">{l s='Sale!' mod='posaccessoriesproducts'}</span>
									</a>
								{/if}
								<div class="actions">							
									<div class="actions-inner">
										<ul class="add-to-links">
											<li class="cart">
												{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
												{if ($product.allow_oosp || $product.quantity > 0)}
												{if isset($static_token)}
													<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow"  title="{l s='Add to cart' mod='posaccessoriesproducts'}" data-id-product="{$product.id_product|intval}">
														<span>{l s='Add to cart' mod='posaccessoriesproducts'}</span>
														
													</a>
												{else}
												<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='posaccessoriesproducts'}" data-id-product="{$product.id_product|intval}">
													<span>{l s='Add to cart' mod='posaccessoriesproducts'}</span>
												</a>
												   {/if}      
												{else}
												<span class="button ajax_add_to_cart_button btn btn-default disabled" >
													<span>{l s='Add to cart' mod='posaccessoriesproducts'}</span>
												</span>
												{/if}
												{/if}
											</li>
											<li>
												{hook h='displayProductListFunctionalButtons' product=$product}
											</li>
											<li>
											{if isset($comparator_max_item) && $comparator_max_item}
											  <a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare' mod='posaccessoriesproducts'}">{l s='Compare' mod='posaccessoriesproducts'}
											
											  </a>
											 {/if}
											</li>
											<li>
												{if isset($quick_view) && $quick_view}
													<a class="quick-view" title="{l s='Quick view' mod='posaccessoriesproducts'}" href="{$product.link|escape:'html':'UTF-8'}">
														<span>{l s='Quick view' mod='posaccessoriesproducts'}</span>
													</a>
												{/if}
											</li>
										</ul>
									</div>
								</div>
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
										{if $product.specific_prices.reduction_type == 'percentage'}
											<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
										{/if}
									{/if}
									{hook h="displayProductPriceBlock" product=$product type="price"}
									{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									{hook h="displayProductPriceBlock" product=$product type='after_price'}
								{/if}
								</div>
								{/if}					
							</div>
							<div class="product-contents">

								<h5 class="product-name"><a href="{$product.link|escape:'html'}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:25:'...'|escape:'htmlall':'UTF-8'}</a></h5>
								
								{capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
								{if $smarty.capture.displayProductListReviews}
									<div class="hook-reviews">
									{hook h='displayProductListReviews' product=$product}
									</div>
								{/if}
							</div>								
						</div>
					{if $smarty.foreach.myLoop.iteration % $slider_options.rows == 0 || $smarty.foreach.myLoop.last}
					</div>
					{/if}
				{/foreach}
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			var accessories_carousel = $(".accessories_carousel");
			accessories_carousel.owlCarousel({
				items : {$slider_options.number_item},
				itemsDesktop : [1199,{$slider_options.items_md}],
				itemsDesktopSmall : [991,{$slider_options.items_sm}],
				itemsTablet: [767,{$slider_options.items_xs}],
				itemsMobile : [479,{$slider_options.items_xxs}],
				autoPlay :  {if $slider_options.auto_play}{if $slider_options.auto_time}{$slider_options.auto_time}{else}3000{/if}{else} false{/if},
				stopOnHover: {if $slider_options.pausehover} true {else} false {/if},
				slideSpeed : {if $slider_options.speed_slide}{$slider_options.speed_slide}{else}1000{/if},
				addClassActive: true,
				scrollPerPage: {if $slider_options.move} false {else} true {/if},
				navigation : {if $slider_options.show_arrow} true {else} false {/if},
				pagination : {if $slider_options.show_pagination} true {else} false {/if},
				lazyLoad: true,
			});
		});
	</script>