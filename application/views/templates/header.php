<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $title;?></title>
<?php
// RESET CSS
$resetCSS = array(
			'href' => 'assets/css/resetcss.css',
			'rel' => 'stylesheet',
			'type' => 'text/css'
			);
echo link_tag($resetCSS);

// CORE CSS
$coreCSS = array(
			'href' => 'assets/css/core.css',
			'rel' => 'stylesheet',
			'type' => 'text/css'
			);
echo link_tag($coreCSS);

// JQUERY CSS
$jqueryCSS = array(
				'href' => base_url().'assets/js/jquery-ui-1.10.2.custom/css/redmond/jquery-ui-1.10.2.custom.css',
				'rel' => 'stylesheet',
				'type' => 'text/css'
			);
echo link_tag($jqueryCSS);

// FAVICON
$favicon = array(
		'href' => 'assets/img/icons/favicon.ico',
		'rel' => 'shortcut icon'
		);
echo link_tag($favicon);
?>
</head>

<body>
<header><div id="headerArea"></div></header>