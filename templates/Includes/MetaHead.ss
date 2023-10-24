<% base_tag %>
$MetaTags

<meta name="msapplication-navbutton-color" content="#000000" />
<meta name="apple-mobile-web-app-status-bar-style" content="#000000" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="mobile-web-app-capable" content="yes" />

<%-- OpenGraph --%>
<meta property="og:url" content="{$AbsoluteBaseURL}" />
<meta property="og:site_name" content="{$SiteConfig.Title}" />
<meta property="og:locale" content="$ContentLocale.ATT" />

<meta name="theme-color" content="#ffffff">
<meta name="theme-color" media="(prefers-color-scheme: light)" content="#ffffff" />
<meta name="theme-color" media="(prefers-color-scheme: dark)" content="#000000" />

<% if $MetaTitle %>
	<meta property="og:title" content="{$MetaTitle} - {$SiteConfig.Title}" />
	<meta property="og:image:alt" content="{$MetaTitle}" />
<% else %>
	<meta property="og:title" content="{$Title} - {$SiteConfig.Title}" />
	<meta property="og:image:alt" content="{$Title}" />
<% end_if %>

<% if $MetaDescription %>
    <meta property="og:description" content="{$MetaDescription}" />
<% end_if %>

<% if $FeaturedImage %>
  <meta property="og:image" content="{$FeaturedImage.Link}" />
  <meta property="twitter:image" content="{$FeaturedImage.Link}" />
<% else_if $MetaImage %>
    <meta property="og:image" content="{$MetaImage.Link}" />
    <meta property="twitter:image" content="{$MetaImage.Link}" />
<% else %>
  <meta name="og:image" content="{$AbsoluteBaseURL}resources/app/client/dist/img/logo.png" />
  <meta name="twitter:image" content="{$AbsoluteBaseURL}resources/app/client/dist/img/logo.png" />
<% end_if %>

<% if $FacebookAppID %>
    <meta property="fb:app_id" content="{$FacebookAppID}" />
<% end_if %>
<meta property="og:type" content="<% if $OgType %>$OgType<% else %>article<% end_if %>" />
<%-- /OpenGraph --%>

<%-- Twitter --%>
<meta name="twitter:card" content="summary">
<%-- meta name="twitter:creator" content="" --%>
<meta name="twitter:title" content="<% if $MetaTitle %>{$MetaTitle}<% else %>$Title<% end_if %> - {$SiteConfig.Title}">
<meta name="twitter:description" content="{$Summary}">
<meta name="twitter:image" content="{$AbsoluteBaseURL}resources/app/client/dist/img/logo.png">
<%-- /Twitter --%>

<link rel="canonical" href="{$AbsoluteLink}" />

<link rel="author" type="text/plain" href="{$AbsoluteBaseURL}humans.txt" />
<link rel="sitemap" type="application/xml" title="Sitemap" href="{$AbsoluteBaseURL}sitemap.xml" />

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link rel="preconnect" href="https://maps.google.com" />
<link rel="preconnect" href="https://ajax.googleapis.com" />
<link rel="preconnect" href="https://csi.gstatic.com" />
<link rel="preconnect" href="https://maps.googleapis.com" />

<link rel="preconnect" href="https://www.youtube.com" />
<link rel="preconnect" href="https://i.ytimg.com" />
<link rel="preconnect" href="https://i9.ytimg.com" />
<link rel="preconnect" href="https://s.ytimg.com" />

<link rel="shortcut icon" type="image/x-icon" href="{$AbsoluteBaseURL}resources/app/client/dist/icons/favicon.ico" />
<link rel="apple-touch-icon" href="{$AbsoluteBaseURL}resources/app/client/dist/icons/apple-touch-icon.png" />
<link rel="apple-touch-icon-precomposed" href="{$AbsoluteBaseURL}resources/app/client/dist/icons/apple-touch-icon-precomposed.png" />

<meta name="application-name" content="{$SiteConfig.Title}" />
<meta name="msapplication-TileImage" content="{$AbsoluteBaseURL}resources/app/client/dist/icons/mstile-144x144.png" />
<meta name="msapplication-TileColor" content="#2F98F1" />

<meta content="yes" name="apple-mobile-web-app-capable" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5" />

<link rel="manifest" href="/manifest.json" />

<meta name="swversion" content="{$SWVersion}" />
<meta name="ping" content="/Security/ping" />
<meta name="api_url" content="{$AbsoluteBaseURL}graphql" />
<meta name="api_static_domain" content="{$AbsoluteBaseURL}" />
<meta name="http_method" content="{$HttpMethod}" />
