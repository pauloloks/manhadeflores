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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<html{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}>
	<head>
		{literal}
			<script async src="https://www.googletagmanager.com/gtag/js?id=UA-123138454-1"></script>
			<script>
			  window.dataLayer = window.dataLayer || [];
			  function gtag(){dataLayer.push(arguments);}
			  gtag('js', new Date());

			  gtag('config', 'UA-123138454-1');
			</script>
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<script>
			  (adsbygoogle = window.adsbygoogle || []).push({
				google_ad_client: "ca-pub-8621625935825377",
				enable_page_level_ads: true
			  });
			</script>
		{/literal}

		<meta charset="utf-8" />
		<title>{$meta_title|escape:'html':'UTF-8'}</title>
		{if isset($meta_description) AND $meta_description}
			<meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
		{/if}
		{if isset($meta_keywords) AND $meta_keywords}
			<meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
		{/if}
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="theme-color" content="#485f27">
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />

		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
		{if isset($css_files)}
			{foreach from=$css_files key=css_uri item=media}
				<link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
			{/foreach}
		{/if}
		{if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
			{$js_def}
			{foreach from=$js_files item=js_uri}
			<script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
			{/foreach}
		{/if}
		<script src="{$js_dir}owl.carousel.js" type="text/javascript"></script>
		{$HOOK_HEADER}
		<link rel="stylesheet" href="{$css_dir}material-design-iconic-font.min.css" type="text/css" />
		<link rel="stylesheet" href="{$css_dir}animate.css" type="text/css" />
		<link rel="stylesheet" href="http{if Tools::usingSecureMode()}s{/if}://fonts.googleapis.com/css?family=Open+Sans:300,600&amp;subset=latin,latin-ext" type="text/css" media="all" />
		<!--[if IE 8]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
		<script type="text/javascript"> //<![CDATA[
		var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.comodo.com/" : "http://www.trustlogo.com/");
		document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
		//]]>
		</script>
	</head>
	<body{if isset($page_name)} itemscope itemtype="http://schema.org/WebPage" id="{$page_name|escape:'html':'UTF-8'}"{/if} class="{if isset($page_name)}{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{else} show-left-column{/if}{if $hide_right_column} hide-right-column{else} show-right-column{/if}{if isset($content_only) && $content_only} content_only{/if} lang_{$lang_iso}">
	{if !isset($content_only) || !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
			<div id="restricted-country">
				<p>{l s='You cannot place a new order from your country.'}{if isset($geolocation_country) && $geolocation_country} <span class="bold">{$geolocation_country|escape:'html':'UTF-8'}</span>{/if}</p>
			</div>
		{/if}
		<div id="page"{if $page_name !="index"} class="sub-page"{/if}>

			<div class="header-container">
				<header id="header">
					{capture name='displayBanner'}{hook h='displayBanner'}{/capture}
					{if $smarty.capture.displayBanner}
						<div class="banner">
							<div class="container">
								<div class="row">
									{$smarty.capture.displayBanner}
								</div>
							</div>
						</div>
					{/if}
					{capture name='blockPosition6'}{hook h='blockPosition6'}{/capture}
					{if $smarty.capture.blockPosition6}
					{$smarty.capture.blockPosition6}
					{/if}
					<div class="container">
						<div class="row">
							<div class="header-middle">
								<div class="pos_logo col-lg-6 col-sm-4 col-md-4 col-xs-12">
									<a href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{$shop_name|escape:'html':'UTF-8'}">
										<img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
									</a>
								</div>
								<div class="header-middle-right col-lg-6 col-sm-8 col-md-8 col-xs-12">
									<div class="right-header">
										<div class="cart">{if isset($HOOK_TOP)}{$HOOK_TOP}{/if}</div>
										{capture name='displayNav'}{hook h='displayNav'}{/capture}
										{if $smarty.capture.displayNav}
											<div class="nav">
												<nav>{$smarty.capture.displayNav}</nav>
											</div>
										{/if}
									</div>
								</div>
							</div>
						</div>
					</div>
				</header>
				<div class="header-menu">
					<div class="container">
						<div class="row">
							<div class="col-md-3 col-xs-12">
								<div class="pos-vegamenu">
									{capture name='vegamenu'}{hook h='vegamenu'}{/capture}
									{if $smarty.capture.vegamenu}
									{$smarty.capture.vegamenu}
									{/if}
								</div>
							</div>
							<div class="col-md-9 col-xs-12">
								<div class="pos-megamenu">
									{capture name='megamenu'}{hook h='megamenu'}{/capture}
									{if $smarty.capture.megamenu}
									{$smarty.capture.megamenu}
									{/if}
								</div>
							</div>
						</div>
					</div>
				</div>
				{if $page_name =="index"}
					<div class="container">
						<div class="row">
							<div class="col-md-3 col-sm-12 col-xs-12"></div>
							<div class=" pos_bannerslide">
								{capture name='bannerSlide'}{hook h='bannerSlide'}{/capture}
								{if $smarty.capture.bannerSlide}
								{$smarty.capture.bannerSlide}
								{/if}
							</div>
						</div>
					</div>
					{capture name='blockPosition1'}{hook h='blockPosition1'}{/capture}
					{if $smarty.capture.blockPosition1}
					{$smarty.capture.blockPosition1}
					{/if}
					{capture name='blockPosition2'}{hook h='blockPosition2'}{/capture}
					{if $smarty.capture.blockPosition2}
					{$smarty.capture.blockPosition2}
					{/if}
					<div class="container">
						<div class="row">
							{capture name='blockPosition4'}{hook h='blockPosition4'}{/capture}
							{if $smarty.capture.blockPosition4}
							{$smarty.capture.blockPosition4}
							{/if}
						</div>
					</div>
					{capture name='blockPosition3'}{hook h='blockPosition3'}{/capture}
					{if $smarty.capture.blockPosition3}
					{$smarty.capture.blockPosition3}
					{/if}
					<div class="BrandSlider">
						<div class="container">
						{capture name='BrandSlider'}{hook h='BrandSlider'}{/capture}
						{if $smarty.capture.BrandSlider}
						{$smarty.capture.BrandSlider}
						{/if}
						</div>
					</div>
					<div class="social-newsletter">
						<div class="container">
							<div class="row">
								{capture name='blockPosition5'}{hook h='blockPosition5'}{/capture}
								{if $smarty.capture.blockPosition5}
								{$smarty.capture.blockPosition5}
								{/if}
							</div>
						</div>
					</div>
				{/if}
			</div>

			<div class="columns-container">
				{if $page_name !='index' && $page_name !='pagenotfound'}
				<div class="pos-breadcrumb">
					<div class="container">
					{include file="$tpl_dir./breadcrumb.tpl"}
					</div>
				</div>
				{/if}
				<div id="columns" class="container">
					<div id="slider_row" class="row">
						{capture name='displayTopColumn'}{hook h='displayTopColumn'}{/capture}
						{if $smarty.capture.displayTopColumn}
							<div id="top_column" class="center_column col-xs-12 col-sm-12">{$smarty.capture.displayTopColumn}</div>
						{/if}
					</div>
					<div class="row">
						{if isset($left_column_size) && !empty($left_column_size)}
						<div id="left_column" class="column col-xs-12 col-sm-{$left_column_size|intval}">{$HOOK_LEFT_COLUMN}</div>
						{/if}
						{if isset($left_column_size) && isset($right_column_size)}{assign var='cols' value=(12 - $left_column_size - $right_column_size)}{else}{assign var='cols' value=12}{/if}
						<div id="center_column" class="center_column col-xs-12 col-sm-{$cols|intval}">

						{if $page_name =="index"}

						{/if}
	{/if}
