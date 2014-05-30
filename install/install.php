<?php

if ($_SERVER["HTTPS"] == "on") {

	$numbaturl = "https://" . $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

} else {

	$numbaturl = "http://" . $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

}

?><!DOCTYPE html>
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
	<script type="text/javascript">

		function nbtTestMySQLConnexion () {

			$.ajax ({
				url: '<?php echo $numbaturl; ?>install/testmysql.php',
				type: 'post',
				data: {
					dbusername: $('#nbtDBusername').val(),
					dbpassword: $('#nbtDBpassword').val(),
					dbname: $('#nbtDBname').val(),
					dbhost: $('#nbtDBhost').val()
				},
				dataType: 'html'
			}).done ( function (html) {

				$('#nbtDBTestFeedback').html(html);

				if ( html == 'Successful connexion to MySQL database' ) {

					$('#nbtDBTestFeedback').removeClass('nbtFeedbackBad').addClass('nbtFeedbackGood');

				} else {

					$('#nbtDBTestFeedback').removeClass('nbtFeedbackGood').addClass('nbtFeedbackBad');

				}

				$('#nbtDBTestFeedback').slideDown( 500, function () {

					setTimeout ( function () {

						$('#nbtDBTestFeedback').slideUp(500);

					}, 3000 );

				});

			});

		}

		function nbtWriteConfig () {

			$.ajax ({
				url: '<?php echo $numbaturl; ?>install/writeconfig.php',
				type: 'post',
				data: {
					dbusername: $('#nbtDBusername').val(),
					dbpassword: $('#nbtDBpassword').val(),
					dbname: $('#nbtDBname').val(),
					dbhost: $('#nbtDBhost').val(),
					abs_path: $('#nbtAbsPath').val(),
					site_url: $('#nbtSiteURL').val(),
					nbt_projname: $('#nbtProjectName').val(),
					nbt_username: $('#nbtUsername').val(),
					nbt_password: $('#nbtPassword').val(),
					nbt_email: $('#nbtEmail').val()
				},
				dataType: 'html'
			}).done ( function (html) {

				$('#nbtInstallPane').html(html);

			});

		}

	</script>
	<!-- / Signals JS -->

	<!-- CSS -->
	<link rel="stylesheet" href="<?php echo $numbaturl; ?>css/reset.css" />
	<link rel="stylesheet" href="<?php echo $numbaturl; ?>css/numbat.css" />
	<!-- / CSS -->

	<link rel="SHORTCUT ICON" href="<?php echo $numbaturl; ?>images/favicon.ico"/>

</head>

<body>

<div id="nbtTopBanner">
	<h1>Numbat</h1>
</div>
<div id="nbtInstallPane" class="nbtSigninPanel nbtGreyGradient" style="margin-bottom: 40px;">
	<h2>Install Numbat</h2>
	<div class="nbtSubExtraction">
		<h3>MySQL details</h3>
		<p class="nbtFinePrint">Caution: This installer will write over any previous installation.</p>
		<p>Database username</p>
		<input type="text" id="nbtDBusername">
		<p>Database password</p>
		<input type="text" id="nbtDBpassword">
		<p>Database name</p>
		<input type="text" id="nbtDBname">
		<p>Database host</p>
		<p class="nbtFinePrint">E.g. "localhost"</p>
		<input type="text" id="nbtDBhost">
		<button style="display: block; margin-top: 25px;" onclick="nbtTestMySQLConnexion();">Test database connexion</button>
		<p id="nbtDBTestFeedback" class="nbtFeedback nbtHidden nbtFinePrint">&nbsp;</p>
	</div>
	<div class="nbtSubExtraction">
		<h3>Site details</h3>
		<p class="nbtFinePrint">Should auto-detect correctly; change only if you know what you're doing.</p>
		<p>Absolute path to installation</p>
		<p class="nbtFinePrint">Include trailing slash; e.g. "/home/webspace/numbat/"</p>
		<input id="nbtAbsPath" type="text" value="<?php

		echo substr (__DIR__, 0, strlen ( __DIR__ ) - 7);

		?>">
		<p>Site URL</p>
		<p class="nbtFinePrint">Include http:// at beginning and trailing slash; e.g. "http://www.website.com/numbat/"</p>
		<input id="nbtSiteURL" type="text" value="<?php echo $numbaturl; ?>">
	</div>
	<div class="nbtSubExtraction">
	<h3>Numbat details</h3>
		<p>Numbat project name</p>
		<p class="nbtFinePrint">E.g. "Meta-analyses for Signals, Safety and Success grant"</p>
		<input type="text" id="nbtProjectName">
		<p>Admin username</p>
		<p class="nbtFinePrint">Admin users can be added, changed or removed at any time, but there must be at least one user who is the admin.</p>
		<input type="text" id="nbtUsername">
		<p>Admin password</p>
		<input type="password" id="nbtPassword">
		<p>Admin email</p>
		<p class="nbtFinePrint">This is the email that Numbat will use when sending confirmation emails.</p>
		<input type="text" id="nbtEmail">
	</div>
	<button onclick="nbtWriteConfig();">Install Numbat</button>
</div>
</body>
</html>
