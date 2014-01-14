<?php

include_once ('../functions.php');

sig_save_new_user ($_POST['nbtSignupUsername'], $_POST['nbtSignupEmail'], $_POST['nbtSignupPassword1']);

?><div class="nbtSigninPanel nbtGreyGradient">
	<h2>Success</h2>
	<p>Please check the email you provided at sign-up. A message was sent with a link that you have to visit in order to activate your account.</p>
</div>

