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
		<style type="text/css">@import url(js/cal/calendar-blue.css);</style>
		<script type="text/javascript" src="js/dhtml.js"></script>
	</head>
	<body>
		<h1>{if $id}<a href="show.php?id={$id}">{/if}{$title}{if $id}</a>{/if}</h1>
		<p id="product_note"><a href="http://www.planster.net/">PLANster</a> {$version}</p>
		<div id="content">
{$body}
		</div>
		<p id="footer">
			&copy; 2006 <a href="http://www.desire.ch/">Stefan Ott</a>
{if $id}
			&middot; <a href="/rdf.php?id={$id}">RDF feed</a>
{/if}
		</p>
		<script language="JavaScript" type="text/javascript" src="js/wz_tooltip.js"></script>
	</body>
</html>
