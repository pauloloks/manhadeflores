
<div class="block-smartblog">
	<div class="container">
		<div class="pos-title">
		<h2><a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='blog posts' mod='smartbloghomelatestnews'}</a></h2>
		</div>
		<div class="row pos-content">
			<div class="sdsblog-box-content">
				{if isset($view_data) AND !empty($view_data)}
					{assign var='i' value=1}
					{foreach from=$view_data item=post name=post}
					   
							{assign var="options" value=null}
							{$options.id_post = $post.id}
							{$options.slug = $post.link_rewrite}
							<div class="sds_blog_post" >
								<div class="item-blog">
									<div class="news_module_image_holder">
										 <a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}"><img alt="{$post.title}" class="feat_img_small " src="{$modules_dir}smartblog/images/{$post.post_img}.jpg"></a>
											
									</div>
									<div class="blog_content">
										<div class="date-smart">
											<span class="date_form">{$post.date_added|date_format:"%e"}</span>
											<span class="month_form">{$post.date_added|date_format:"%b"}</span>
										</div>
										<div class="content_inner">
											<h4 class="sds_post_title"><a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">{$post.title|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></h4>
											<p>
												{$post.short_description|truncate:90:'...'|escape:'htmlall':'UTF-8'}
											</p>
											<a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}"  class="r_more"><span>{l s='Read More' mod='smartbloghomelatestnews'}</span></a>
										</div>
									</div>
								</div>
							</div>
						
						{$i=$i+1}
					{/foreach}
				{/if}
			</div>	
		</div>	 
	</div>
</div>
<script>


    $(document).ready(function() {
     
    var owl = $(".sdsblog-box-content");
     
    owl.owlCarousel({
	autoPlay : false,
	pagination :false,
	navigation :true,
	slideSpeed :1000,
    items : 3,
	addClassActive: true,
	itemsDesktop : [1199,3],
	itemsDesktopSmall : [991,2],
	itemsTablet: [767,2],
	itemsMobile : [480,1]
    });
    });
</script>