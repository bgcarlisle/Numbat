<?php

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	|| (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
	|| (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

$protocol = $isHttps ? "https://" : "http://";
$numbaturl = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

?><!DOCTYPE html>
<html lang="en">

    <head>

	<title>Numbat</title>

	<!-- jQuery -->
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js">
	</script>
	<!-- / jQuery -->

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

	 function nbtTestOutgoingEmail () {

	     $.ajax ({
		 url: '<?php echo $numbaturl; ?>install/testsmtp.php',
		 type: 'post',
		 data: {
		     smtp_email: $('#nbtEmail').val(),
		     smtp_host: $('#nbtSMTP_host').val(),
		     smtp_user: $('#nbtSMTP_user').val(),
		     smtp_password: $('#nbtSMTP_password').val()
		 },
		 dataType: 'html'
	     }).done ( function (html) {
		 $('#nbtSMTPTestFeedback').html(html);

		 if ( html == 'Mail sent' ) {
		     $('#nbtSMTPTestFeedback').removeClass('nbtFeedbackBad').addClass('nbtFeedbackGood');
		 } else {
		     $('#nbtSMTPTestFeedback').removeClass('nbtFeedbackGood').addClass('nbtFeedbackBad');
		 }

		 $('#nbtSMTPTestFeedback').slideDown( 500, function () {
		     setTimeout ( function () {
			 $('#nbtSMTPTestFeedback').slideUp(500);
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
		     nbt_email: $('#nbtEmail').val(),
		     smtp_host: $('#nbtSMTP_host').val(),
		     smtp_user: $('#nbtSMTP_user').val(),
		     smtp_password: $('#nbtSMTP_password').val()
		 },
		 dataType: 'html'
	     }).done ( function (html) {

		 $('#nbtInstallPane').html(html);

	     });

	 }

	</script>
	<!-- / Numbat JS -->

	<!-- CSS -->
	<link rel="stylesheet" href="<?php echo $numbaturl; ?>css/reset.css" />
	<link rel="stylesheet" href="<?php echo $numbaturl; ?>css/numbat.css" />
	<style>
	 .nbtSubExtraction p {
	     margin-top: 8px;
	 }
	 .nbtSubExtraction p.nbtFinePrint {
	     margin-top: 1px;
	 }
	</style>
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
		<p class="nbtFinePrint">Include leading and trailing slashes; e.g. "/home/webspace/numbat/"</p>
		<input id="nbtAbsPath" type="text" value="<?php

							  echo substr (__DIR__, 0, strlen ( __DIR__ ) - 7);

							  ?>">
		<p>Site URL</p>
		<p class="nbtFinePrint">Include https:// at beginning and trailing slash; e.g. "https://www.website.com/numbat/"</p>
		<input id="nbtSiteURL" type="text" value="<?php echo $numbaturl; ?>">
	    </div>
	    <div class="nbtSubExtraction">
		<h3>Outgoing email settings</h3>
		<p>Enable outgoing email</p>
		<p class="nbtFinePrint">If you enable outgoing email here, Numbat can send a link to reset your password in case you forget it. If you choose to live dangerously, you can use Numbat without outgoing email capacity, and an admin user can manually send links to reset their emails. But if all the admins forget and outgoing email has not been enabled, you gotta muck around in the database.</p>
		<select id="nbtEnableOutgoingEmail">
		    <option value="1">Enable outgoing email</option>
		    <option value="0">Disable outgoing email</option>
		</select>
		<p>Admin email</p>
		<p class="nbtFinePrint">This is the email that Numbat will use when sending confirmation emails.</p>
		<input type="text" id="nbtEmail">
		<p>SMTP host</p>
		<p class="nbtFinePrint">E.g. "smtp.emailprovider.com"</p>
		<input type="text" id="nbtSMTP_host">
		<p>SMTP user name</p>
		<p class="nbtFinePrint">There's a good chance that this and the "Admin email" field above are the same thing.</p>
		<input type="text" id="nbtSMTP_user">
		<p>SMTP password</p>
		<input type="text" id="nbtSMTP_password">
		<button style="display: block; margin-top: 25px;" onclick="nbtTestOutgoingEmail();">Test email settings</button>
		<p id="nbtSMTPTestFeedback" class="nbtFeedback nbtHidden nbtFinePrint">&nbsp;</p>
	    </div>
	    <div class="nbtSubExtraction">
		<h3>Numbat details</h3>
		<p>Numbat project name</p>
		<p class="nbtFinePrint">E.g. "Systematic Review for Signals, Safety and Success grant"</p>
		<input type="text" id="nbtProjectName">
		<p>Admin username</p>
		<p class="nbtFinePrint">Admin users can be added, changed or removed at any time, but there must be at least one user who is the admin.</p>
		<input type="text" id="nbtUsername">
		<p>Admin password</p>
		<input type="password" id="nbtPassword">
	    </div>
	    <button onclick="nbtWriteConfig();">Install Numbat</button>
	</div>
    </body>
</html>
