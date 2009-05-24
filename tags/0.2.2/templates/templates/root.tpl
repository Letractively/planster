<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xml:lang="en">
	<head>
		<title>PLANster - {$title}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
{if $id}
		<link rel="alternate" title="News RSS" href="/rdf.php?id={$id}" type="application/rss+xml" />
{/if}
		<script type="text/javascript" src="js/dhtml.js"></script>
		<link rel="stylesheet" type="text/css" media="all" href="js/calendar/calendar-blue2.css" title="blue2" />
		<script type="text/javascript" src="js/calendar/calendar.js"></script>
		<script type="text/javascript" src="js/calendar/lang/calendar-en.js"></script>
		<script type="text/javascript" src="js/calendar/calendar-setup.js"></script>
	</head>
	<body>
		<h1>{if $id}<a href="show.php?id={$id}">{/if}{$title}{if $id}</a>{/if}</h1>
		<p id="product_note"><a href="http://www.planster.net/">PLANster</a> {$version}</p>
		<div id="content">
{$body}
		</div>
		<p id="footer">
			&copy; 2006 <a href="http://www.desire.ch/">Stefan Ott</a>
			&middot; <a href="http://validator.w3.org/check?uri=referer">XHTML</a>
			&middot; <a href="http://jigsaw.w3.org/css-validator/validator?uri={$css_url}">CSS</a>
{if $id}
			&middot; <a href="/rdf.php?id={$id}">RDF feed</a>
{/if}
		</p>
		<script type="text/javascript" src="js/wz_tooltip.js"></script>
	</body>
</html>
