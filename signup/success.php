<?php

include_once ('../functions.php');

sig_save_new_user ($_POST['sigSignupUsername'], $_POST['sigSignupEmail'], $_POST['sigSignupPassword1']);

?><div class="sigSigninPanel sigGreyGradient">
	<h2>Success</h2>
	<p>Please check the email you provided at sign-up. A message was sent with a link that you have to visit in order to activate your account.</p>
</div>

