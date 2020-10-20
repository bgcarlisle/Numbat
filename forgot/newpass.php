<div class="nbtSigninPanel nbtGreyGradient" id="nbtChangePass">
    <h2>Enter a new password</h2>
    <input type="password" id="nbtSignUpPassword1" onfocus="nbtClearText(this, 'Password');" onblur="nbtRestoreText(this, 'Password');" onkeyup="nbtPasswordCheck();" value="Password" name="nbtSignupPassword1"><br>
    <input type="password" id="nbtSignUpPassword2" onkeyup="nbtPasswordCheck();" name="nbtSignupPassword2">
    <p class="nbtFeedback nbtFinePrint" id="nbtSignupPasswordFeedback">Passwords must match</p>
    <button id="nbtChangePassButton" onclick="nbtChangePassword('<?php echo $_GET['username']; ?>', '<?php echo $_GET['code']; ?>');" disabled>Change password</button>
</div>
