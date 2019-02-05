{if $testimonials}
<div class="testiminoals">
	<div class="container">
		<div class="row">
			<div id="testimonials_block">
				<div class="testimonials_container container">
					<!-- <div class="pos-header_title">
						<div class="pos-title">
							<h2><span>{l s='testimonials' mod='postestimonials'}</span></h2>
						</div>
					</div> -->

					<div class="block-content row pos-content">
						<ul class="thumb">
						{$j=0} 
						  {foreach from=$testimonials name=myLoop item=testimonial}
							 {if $testimonial.active == 1}
									{if in_array($testimonial.media_type,$arr_img_type)}
										<li class="testithumb{$j}" onclick="testislider({$j})">
											<img src="{$mediaUrl}{$testimonial.media}" alt="Image Testimonial">
										</li>
									{/if}
							{/if}
							{$j = $j + 1} 
						  {/foreach}
						</ul>
						<div class="testimonialsSlide">
						  {foreach from=$testimonials name=myLoop item=testimonial}
							{if $testimonial.active == 1}
								{if $smarty.foreach.myLoop.index % 1 == 0 || $smarty.foreach.myLoop.first }
								<div class="item-testimonials">
								{/if}	
									<div class="item">
										<div class="box-test content_author">							
										
											<div class="content_test">
												<!-- <p class="des_email">{$testimonial.email}</p> -->
												<p class="des_testimonial">{$testimonial.content|escape:'html':'UTF-8'}</p>
												<p class="des_namepost"><span>{$testimonial.name_post}</span></p>
												
											</div>
										</div>
								
									</div>
								{if $smarty.foreach.myLoop.iteration % 1 == 0 || $smarty.foreach.myLoop.last  }
								</div>
								{/if}
							{/if}
						  {/foreach}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var owl = $(".testimonialsSlide");
		owl.owlCarousel({
		items : {$slider_options.number_item},
		itemsDesktop : [1199,{$slider_options.items_md}],
		itemsDesktopSmall : [991,{$slider_options.items_sm}],
		itemsTablet: [767,{$slider_options.items_xs}],
		itemsMobile : [480,{$slider_options.items_xxs}],
		autoPlay :  {if $slider_options.auto_play}{if $slider_options.auto_time}{$slider_options.auto_time}{else}3000{/if}{else} false{/if},
		stopOnHover: {if $slider_options.pausehover} true {else} false {/if},
		slideSpeed : {if $slider_options.speed_slide}{$slider_options.speed_slide}{else}1000{/if},
		addClassActive: true,
		scrollPerPage: {if $slider_options.move} false {else} true {/if},
		navigation : {if $slider_options.show_arrow} true {else} false {/if},
		pagination : {if $slider_options.show_pagination} true {else} false {/if},
		afterMove: function(){
			x = $( ".block-content .owl-item" ).index( $( ".block-content .testimonialsSlide .active" ));
			var testithumb = ".testithumb"+x;
			$(".block-content .thumb li").removeClass('active');
			$(testithumb).addClass('active');
		}
	});
	var owlslider = $(".block-content .testimonialsSlide").data('owlCarousel');
	function testislider(x)
	{
		owlslider.goTo(x)
	}
	var y= Math.round($('.testimonialsSlide .owl-item').length/2-1);
		owlslider.goTo(y)

</script>
 {/if}