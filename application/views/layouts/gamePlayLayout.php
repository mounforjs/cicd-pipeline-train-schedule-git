<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<title>GAME</title>
	<link rel="canonical" href="https://winwinlabs.org" />
	<link rel="shortcut icon" href="https://dg7ltaqbp10ai.cloudfront.net/16509259056267215141269_icon.png" type="image/png">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i) {w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-KSLM9S7');</script>
	<!-- End Google Tag Manager -->
	
	<script src="https://kit.fontawesome.com/b2582bc9e2.js" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<!-- <script type="text/javascript" src="<?php echo asset_url('assets/js/jquery.timer.js');?>"></script> -->

	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script src="<?php echo asset_url('assets/js/sweet_alert_function.js'); ?>"></script>

	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/style.css'); ?>" />
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" ></script>
	<link href="<?php echo asset_url('assets/css/gameview.css'); ?>" rel="stylesheet">
	
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css"/>
	<link type="text/css" rel="stylesheet" href="<?php echo asset_url('assets/css/countstyle.css'); ?>" />

	<script src="<?php echo asset_url('assets/js/common.js'); ?>"></script>
</head>

<body>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KSLM9S7"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->

	<?php echo $template['body']; ?>

	<!-- Script files include -->

	<script  src="<?php echo asset_url('assets/js/fullscreen.js'); ?>"></script>

	<!-- Counter -->
	<?php if ($this->uri->segment(2) != 'stats_preview' && $this->uri->segment(2) != 'stats') { ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script>
		<script  src="<?php echo asset_url('assets/js/play_game.js'); ?>"></script>
		<script src="<?php echo asset_url('assets/js/confetti.js'); ?>"></script>
	<?php } ?>
	<!-- End Counter -->

</body>
</html>