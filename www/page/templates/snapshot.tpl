<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "//www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="//www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="en-US" />
		<meta http-equiv="language" content="en-US" />
		<meta name="description" content="{$CONFIG.DOMAIN} - The finest in Cinema, Movie and Series Streams. Watch the highest quality content for free!" />
		<meta name="keywords" content="{$CONFIG.DOMAIN}, {$ENTRY.name_de}, {$ENTRY.name_en}, Movies, Cinemas, Series, Shows, Serien, Filme, Download, Filme kostenlos, Stream, Online Stream, Kino, Filme kostenlos schauen" />
		<meta name="robots" content="index, follow, noarchive, nosnippet" />
		<meta name="revisit-after" content="3 days" />
		<base href="//{$CONFIG.DOMAIN}/" />
		<link rel="shortcut icon" type="image/x-icon" href="//{$CONFIG.STATIC_URL}/images/favicon.ico" />
		<title>{$CONFIG.TITLE} - {if $ENTRY.name_de|count_characters > 0}{$ENTRY.name_de}{else}{$ENTRY.name_en}{/if} ({$ENTRY.year})</title>
	</head>
	<body>
		<h1>{$ENTRY.name_de} ({$ENTRY.year})</h1>
		<h2>{$ENTRY.name_en} ({$ENTRY.year})</h2>
		<p>{$ENTRY.plot_de}</p>
		<p>{$ENTRY.plot_en}</p>
	</body>
</html>