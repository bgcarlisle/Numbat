<!DOCTYPE html>
<html lang="en">

<head>

	<title>Numbat</title>
	
	<!-- jQuery -->
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js">
	</script>
	<!-- / jQuery -->
	
	<!-- Google Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Fenix|Oxygen' rel='stylesheet' type='text/css'>
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
	
	<link rel="SHORTCUT ICON" href="<?php echo SITE_URL; ?>images/favicon.ico"/>

</head>

<body>

<div id="nbtTopBanner">
	<?php
	
	if ( nbt_user_is_logged_in () ) {
		
		?><a href="#" onclick="event.preventDefault();$('nav').slideToggle();"><h1>Numbat</h1></a><?php
		
	} else {
		
		?><a href="<?php echo SITE_URL; ?>"><h1>Numbat</h1></a><?php
		
	}
	
	if ( nbt_user_is_logged_in () ) {
		
		?><a href="<?php echo SITE_URL . "signout/"; ?>">
			<span id="nbtTopBannerRight">Sign out (<?php echo $_SESSION['nbt_username']; ?>)</span>
		</a><?php
		
	}
	
	?>
</div>
<?php

if ( nbt_user_is_logged_in () ) {
	
	?><nav>
		<ul>
			<li>
				<a href="<?php echo SITE_URL; ?>users/">
					<img src="<?php echo SITE_URL; ?>images/useradmin.png">
					User administration
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>references/">
					<img src="<?php echo SITE_URL; ?>images/managerefsets.png">
					Manage reference sets
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>forms/">
					<img src="<?php echo SITE_URL; ?>images/editforms.png">
					Edit extraction forms
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>assignments/">
					<img src="<?php echo SITE_URL; ?>images/assignments.png">
					Manage extraction assignments
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>extract/">
					<img src="<?php echo SITE_URL; ?>images/extract.png">
					Do extractions
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>master/">
					<img src="<?php echo SITE_URL; ?>images/reconcile.png">
					Reconcile finished extractions
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>backup/">
					<img src="<?php echo SITE_URL; ?>images/backup.png">
					Backup data
				</a>
			</li>
		</ul>
	</nav><?php
	
}
	
?>