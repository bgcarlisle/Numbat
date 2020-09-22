<!DOCTYPE html>
<html lang="en">

<head>

	<title>Numbat: <?php echo nbt_get_setting ( "project_name" ); ?></title>

	<!-- jQuery -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js">
	</script>
	<!-- / jQuery -->

	<!-- jQuery UI -->
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
	<!-- / jQuery UI -->

	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Fenix|Oxygen' rel='stylesheet' type='text/css'>
	<!-- / Google Fonts -->

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

		?><a href="#" onclick="event.preventDefault();$('nav').slideToggle();"><h1>Numbat: <?php echo nbt_get_setting ( "project_name" ); ?></h1></a><?php

	} else {

		?><a href="<?php echo SITE_URL; ?>"><h1>Numbat: <?php echo nbt_get_setting ( "project_name" ); ?></h1></a><?php

	}

	if ( nbt_user_is_logged_in () ) {

		?><a href="<?php echo SITE_URL . "signout/"; ?>">
			<span id="nbtTopBannerRight">Sign out (<?php echo $_SESSION[INSTALL_HASH . '_nbt_username']; ?>)</span>
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
					User administration
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>references/">
					Manage reference sets
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>forms/">
					Edit extraction forms
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>attach/">
					Attach files to references
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>assignments/">
					Manage extraction assignments
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>extract/">
					Do extractions
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>final/">
					Reconcile finished extractions
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>export/">
					Export data
				</a>
			</li>
			<li>
				<a href="<?php echo SITE_URL; ?>backup/">
					Backup data
				</a>
			</li>
		</ul>
	</nav><?php

}

?>
