<div class="nbtSigninPanel nbtGreyGradient" style="background-image: url('<?php echo SITE_URL; ?>images/numbat-background.png'); background-position: top center; background-color: #eee; background-repeat: no-repeat;">
	<h2>Sign up</h2>
	<p>User name</p>
	<form action="<?php echo SITE_URL . "signup/"; ?>" method="post">
		<input type="text" id="nbtSignUpUsername" onfocus="nbtClearText(this, 'User name');" onblur="nbtRestoreText(this, 'User name');" onkeyup="nbtSignupCheckUsername(this);" value="User name" name="nbtSignupUsername">
		<p class="nbtFeedback nbtFinePrint" id="nbtSignupUsernameFeedback" style="display: none;">&nbsp;</p>
		<p>Email</p>
		<input type="text" id="nbtSignUpEmail" onfocus="nbtClearText(this, 'Email');" onblur="nbtRestoreText(this, 'Email');" onkeyup="nbtSignupCheckEmail(this);" value="Email" name="nbtSignupEmail">
		<p class="nbtFeedback nbtFinePrint" id="nbtSignupEmailFeedback" style="display: none;">&nbsp;</p>
		<p>Password</p>
		<input type="password" id="nbtSignUpPassword1" onfocus="nbtClearText(this, 'Password');" onblur="nbtRestoreText(this, 'Password');" onkeyup="nbtPasswordCheck();" value="Password" name="nbtSignupPassword1"><br>
		<input type="password" id="nbtSignUpPassword2" onkeyup="nbtPasswordCheck();" name="nbtSignupPassword2">
		<p class="nbtFeedback nbtFinePrint" id="nbtSignupPasswordFeedback" style="display: none;">&nbsp;</p>
		<p><button>Sign up</button></p>
	</form>
</div>