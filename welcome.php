<div class="nbtSigninPanel nbtGreyGradient" style="background-image: url('<?php echo SITE_URL; ?>images/numbat-background.png'); background-position: top center; background-color: #eee; background-repeat: no-repeat;">
	<p class="nbtFinePrint" style="float: right;"><a href="<?php echo SITE_URL; ?>signup/">New here? Sign up.</a></p>
	<h2>Sign in</h2>
	<form action="<?php echo SITE_URL; ?>" method="post">
		<p>User name</p>
		<input type="text" id="nbtSignInUsername" onfocus="nbtClearText(this, 'User name');" onblur="nbtRestoreText(this, 'User name');" value="User name" name="nbtSigninUsername">
		<p>Password</p>
		<input type="password" id="nbtSignInPassword" onfocus="nbtClearText(this, 'Password');" onblur="nbtRestoreText(this, 'Password');" value="Password" name="nbtSigninPassword">
		<p><button>Sign in</button></p>
	</form>
</div>

