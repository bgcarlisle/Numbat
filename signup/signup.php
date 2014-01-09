<div class="sigSigninPanel sigGreyGradient">
	<h2>Sign up</h2>
	<p>User name</p>
	<form action="<?php echo SITE_URL . "signup/"; ?>" method="post">
		<input type="text" id="sigSignUpUsername" onfocus="sigClearText(this, 'User name');" onblur="sigRestoreText(this, 'User name');" onkeyup="sigSignupCheckUsername(this);" value="User name" name="sigSignupUsername">
		<p class="sigFeedback sigFinePrint" id="sigSignupUsernameFeedback" style="display: none;">&nbsp;</p>
		<p>Email</p>
		<input type="text" id="sigSignUpEmail" onfocus="sigClearText(this, 'Email');" onblur="sigRestoreText(this, 'Email');" onkeyup="sigSignupCheckEmail(this);" value="Email" name="sigSignupEmail">
		<p class="sigFeedback sigFinePrint" id="sigSignupEmailFeedback" style="display: none;">&nbsp;</p>
		<p>Password</p>
		<input type="password" id="sigSignUpPassword1" onfocus="sigClearText(this, 'Password');" onblur="sigRestoreText(this, 'Password');" onkeyup="sigPasswordCheck();" value="Password" name="sigSignupPassword1"><br>
		<input type="password" id="sigSignUpPassword2" onkeyup="sigPasswordCheck();" name="sigSignupPassword2">
		<p class="sigFeedback sigFinePrint" id="sigSignupPasswordFeedback" style="display: none;">&nbsp;</p>
		<p><button>Sign up</button></p>
	</form>
</div>