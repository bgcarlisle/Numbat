<!DOCTYPE html>
<html lang="en">

<head>

	<title>Numbat</title>
	
	<!-- Google Analytics -->
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-8721708-1']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>
	<!-- / Google Analytics -->
	
	<!-- jQuery -->
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js">
	</script>
	<!-- / jQuery -->
	
	<!-- Google Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Fenix|Roboto' rel='stylesheet' type='text/css'>
	<!-- / Google Fonts -->
	
	<!-- Numbat JS -->
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/config.js">
	</script>
	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/numbat.js">
	</script>
	<!-- / Signals JS -->
	
	<!-- CSS -->
	<link rel="stylesheet" href="<?php echo SITE_URL; ?>css/reset.css" />
	<link rel="stylesheet" href="<?php echo SITE_URL; ?>css/numbat.css" />
	<!-- / CSS -->

</head>

<body>

<div id="nbtTopBanner">
	<a href="<?php echo SITE_URL; ?>"><h1>Numbat</h1></a>
	<?php
	
	if ( nbt_user_is_logged_in () ) {
		
		?><a href="<?php echo SITE_URL . "signout/"; ?>">
			<span id="nbtTopBannerRight">Sign out (<?php echo $_SESSION['nbt_username']; ?>)</span>
		</a><?php
		
	}
	
	?>
</div>