<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xml:lang="en">
	<head>
		<title>PLANster - {$title}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="{$smarty.const.base_url}style.css" type="text/css" media="screen" />
{if $ie}
		<link rel="stylesheet" href="{$smarty.const.base_url}style-ie.css" type="text/css" media="screen" />
{/if}
		<link rel="shortcut icon" href="{$smarty.const.base_url}favicon.ico" type="image/x-icon" />
{if $id}
		<link rel="alternate" title="News RSS" href="{$smarty.const.base_url}{$id}/feed" type="application/rss+xml" />
{/if}
		<script type="text/javascript" src="{$smarty.const.base_url}js/libdesire/libdesire.js"></script>
		<script type="text/javascript" src="{$smarty.const.base_url}js/planster.js"></script>
		<script type="text/javascript" src="{$smarty.const.base_url}js/json/json.js"></script>
		<script type="text/javascript" src="{$smarty.const.base_url}js/prototype/prototype.js"></script>
		<script type="text/javascript" src="{$smarty.const.base_url}js/scriptaculous/scriptaculous.js"></script>
		<script type="text/javascript" src="{$smarty.const.base_url}js/scriptaculous/effects.js"></script>
		<script type="text/javascript" src="{$smarty.const.base_url}js/scriptaculous/dragdrop.js"></script>
		<link rel="stylesheet" type="text/css" media="all" href="{$smarty.const.base_url}js/calendar/calendar-blue2.css" title="blue2" />
		<script type="text/javascript" src="{$smarty.const.base_url}js/calendar/calendar.js"></script>
		<script type="text/javascript" src="{$smarty.const.base_url}js/calendar/lang/calendar-en.js"></script>
		<script type="text/javascript" src="{$smarty.const.base_url}js/calendar/calendar-setup.js"></script>
	</head>
	<body>
		<div id="message"{if !$message} style="display: none"{/if}>
			<div class="corner tl"></div>
			<div class="corner tr"></div>
			<div class="corner bl"></div>
			<div class="corner br"></div>
			<div id="messageText">{$message}</div>
		</div>
		<h1 id="h1">{if $id}<a href="{$id}">{/if}{$title}{if $id}</a>{/if}</h1>
		<p id="product_note"><a href="http://www.planster.net/">PLANster</a> {$version}</p>
{$body}
		<p id="footer">
			&copy; 2004-2007 <a href="http://www.desire.ch/">Stefan Ott</a>
			&middot; <a href="{$smarty.const.base_url}about">About PLANster</a>
			&middot; <a href="http://validator.w3.org/check?uri=referer"><img src="{$smarty.const.base_url}img/xhtml.gif" alt="xhtml" title="Valid XHTML 1.0" /></a>
			&middot; <a href="http://jigsaw.w3.org/css-validator/validator?uri={$css_url}"><img src="{$smarty.const.base_url}img/css.gif" alt="css" title="Valid CSS" /></a>
		</p>
	</body>
</html>
