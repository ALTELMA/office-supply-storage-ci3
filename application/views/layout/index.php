<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<title><?php echo $title;?></title>
	<?php

	$styles = [
		'bootstrap'    => ['href' => base_url('assets/css/bootstrap.min.css'), 'rel' => 'stylesheet', 'type' => 'text/css'],
		'metisMenu'    => ['href' => base_url('assets/css/metisMenu.min.css'), 'rel' => 'stylesheet', 'type' => 'text/css'],
		'sb-admin-2'   => ['href' => base_url('assets/css/sb-admin-2.css'), 'rel' => 'stylesheet', 'type' => 'text/css'],
		'morris'       => ['href' => base_url('assets/css/morris.css'), 'rel' => 'stylesheet', 'type' => 'text/css'],
		'dataTables'   => ['href' => base_url('assets/css/dataTables.bootstrap.min.css'), 'rel' => 'stylesheet', 'type' => 'text/css'],
		'font-awesome' => ['href' => base_url('assets/css/font-awesome.min.css'), 'rel' => 'stylesheet', 'type' => 'text/css'],
		'dashboard'    => ['href' => base_url('assets/css/dashboard.css'), 'rel' => 'stylesheet', 'type' => 'text/css'],
		'jquery-ui'    => ['href' => base_url('assets/js/jquery-ui-1.10.2.custom/css/redmond/jquery-ui-1.10.2.custom.min.css'), 'rel' => 'stylesheet', 'type' => 'text/css'],
	];

	foreach ($styles as $style) {
		echo link_tag($style);
	}

	// Favicon
	$favicon = ['href' => base_url('assets/images/icons/favicon.ico'), 'rel' => 'shortcut icon'];
	echo link_tag($favicon);

	?>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<?php if(isset($header)) echo $header ;?>
	<?php if(isset($left)) echo $left ;?>
	<?php if(isset($middle)) echo $middle ;?>
	<?php if(isset($footer)) echo $footer ;?>
</body>
</html>