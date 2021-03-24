<?php

if ( file_exists ( "./config.php" ) ) {
	
	include_once ('./config.php');
	
	if ( nbt_user_is_logged_in () ) { // User is logged in
		
		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "home.php" );
		
	} else { // User is not logged in
		
		if ( isset ( $_POST['nbtSigninUsername'] ) ) { // User is trying to log in
			
			if ( nbt_creds_check_out ( $_POST['nbtSigninUsername'], $_POST['nbtSigninPassword'] ) ) {
				
				nbt_log_user_in ( $_POST['nbtSigninUsername'] );
				
				include ( ABS_PATH . "header.php" );
				include ( ABS_PATH . "home.php" );
				
			} else {
				
				$nbtErrorText = "Your sign-in credentials couldn't be verified. <a href=\"" . SITE_URL . "\">Try again!</a></p><p class=\"nbtFinePrint\" style=\"margin-top: 20px;\"><a href=\"" . SITE_URL . "forgot/?username=" . $_POST['nbtSigninUsername'] . "\">Forgot your password?</a>";
				
				include ( ABS_PATH . "header.php" );
				include ( ABS_PATH . "error.php" );
				
			}
			
		} else {
		
			// Display the "welcome" page
			
			include ( ABS_PATH . "header.php" );
			include ( ABS_PATH . "welcome.php" );
		
		}
		
	}
	
	include ( ABS_PATH . "footer.php" );

} else {
	
	include ( "./install/install.php" );
	
}

?>
