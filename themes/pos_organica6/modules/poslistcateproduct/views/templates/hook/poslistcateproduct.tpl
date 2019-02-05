{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{$count=0}
{foreach from=$productCates item=productCate name=poslistcateproduct}
<div class="container">
<div class="row">
<div class="poslistcateproduct pos-content poslistcateproduct_{$count} block_product">
	<div class="col-md-2 col-sm-2 col-xs-12 category-left">
			{if $productCate.image}
			<div class="thumb-category">
				{if $productCate.url}<a href="{$productCate.url}">{/if}<img src="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`poslistcateproduct/images/`$productCate.image|escape:'htmlall':'UTF-8'`")}" alt="" />{if $productCate.url}</a>{/if}
			</div>
			{/if}
	</div>
	<div class="col-md-10 col-sm-10 col-xs-12 category-right">
		<div class="row_edit">
			<div class="pos-title">		
				<h2>
					<span>{$productCate.category_name}</span>
				</h2>
			</div>
			<div class="block_content">		
				{foreach from=$productCate.product item=product name=myLoop}
					{if $smarty.foreach.myLoop.index % $configure['row'] == 0 || $smarty.foreach.myLoop.first }
						<div class="item_out" style="height: 274px;">
					{/if}
						<div class="item-product" style="height: 274px;">
							<div class="products-inner">
								<a class="product_img_link"	href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
									<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}"
									alt="{$product.legend|escape:'html':'UTF-8'}"
									class="img-responsive"/>
								</a>
								{if isset($product.new) && $product.specific_prices} 
								{if $product.specific_prices}<span class="sale-box">{l s='Oferta'}</span>{/if}
							{else}
							{if isset($product.new) && $product.new == 1}<span class="new-box">{l s='New'} </span>{/if}
							{if $product.specific_prices}<span class="sale-box">{l s='Oferta'}</span>{/if}
							{/if}	
								<!-- <div class="actions">													
									<div class="actions-inner">	
										<ul class="add-to-links">
											<li>
												{hook h='displayProductListFunctionalButtons' product=$product}
											</li>
											<li>
												{if isset($quick_view) && $quick_view}
													<a class="quick-view" title="{l s='Quick view' mod='poslistcateproduct'}" href="{$product.link|escape:'html':'UTF-8'}">
														<span>{l s='Quick view' mod='poslistcateproduct'}</span>
													</a>
												{/if}
											</li>
											<li>
											{if isset($comparator_max_item) && $comparator_max_item}
											  <a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare' mod='poslistcateproduct'}">{l s='Compare' mod='poslistcateproduct'}
											
											  </a>
											 {/if}
											</li>
											
										</ul>
									</div>
								</div> -->
							</div>
							<div class="product-contents">
								
								<h5 class="product-name"><a href="{$product.link|escape:'html'}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:25:'...'|escape:'htmlall':'UTF-8'}</a></h5>
								
								{capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
								<!-- {if $smarty.capture.displayProductListReviews}
									<div class="hook-reviews">
									{hook h='displayProductListReviews' product=$product}
									</div>
								{/if}  -->
								
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
								<!-- <div class="cart">
									{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
									{if ($product.allow_oosp || $product.quantity > 0)}
									{if isset($static_token)}
										<a class="ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow"  title="{l s='Add to cart' mod='poslistcateproduct'}" data-id-product="{$product.id_product|intval}">
											<span>{l s='Add to cart' mod='poslistcateproduct'}</span>
											
										</a>
									{else}
									<a class="ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='poslistcateproduct'}" data-id-product="{$product.id_product|intval}">
										<span>{l s='Add to cart' mod='poslistcateproduct'}</span>
									</a>
									   {/if}      
									{else}
									<span class="ajax_add_to_cart_button btn btn-default disabled" >
										<span>{l s='Add to cart' mod='poslistcateproduct'}</span>
									</span>
									{/if}
									{/if}
								</div> -->
							</div>
						</div>
					{if $smarty.foreach.myLoop.iteration %  $configure['row'] == 0 || $smarty.foreach.myLoop.last}
						</div>
					{/if}
				{/foreach}
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			var owl = $(".poslistcateproduct_{$count} .block_content");
			owl.owlCarousel({
				items : {if $configure['items']} {$configure['items']} {else} 5 {/if},
				itemsDesktop : [1199,3],
				itemsDesktopSmall : [991,3],
				itemsTablet: [767,2],
				itemsMobile : [479,1],
				autoPlay :  {if $configure['auto']}{if $configure['delay']}{$configure['delay']}{else}3000{/if}{else}false{/if},
				navigation : {if $configure.arrow} true {else} false {/if},
				pagination : {if $slideOptions.show_pagination}true{else}false{/if},
				slideSpeed: {if $configure['speed']}{$configure['speed']}{else}200{/if},
				stopOnHover: true,
				addClassActive: true,
				afterAction: function(el){
					this.$owlItems.removeClass('first-active')
					this.$owlItems .eq(this.currentItem).addClass('first-active')  
				 }
			});
			$(".next_{$count}").click(function(){
			owl.trigger('owl.next');
			});
			$(".prev_{$count}").click(function(){
			owl.trigger('owl.prev');
			})  
		});
	</script>
	</div>		
	</div>		
</div>
{$count= $count+1}
{/foreach}


