<?php

include_once ("../config.php");

if ( ! isset ( $_GET['code'] ) ) {
	
	nbt_send_password_recovery_email ( $_GET['username'] );

	include ( ABS_PATH . "header.php" );
	include ( "welcome.php" );
	
} else {
	
	if ( nbt_password_recovery_code_checks_out ( $_GET['username'], $_GET['code'] ) ) {
		
		include ( ABS_PATH . "header.php" );
		include ( "newpass.php" );
		
	} else {
		
		$nbtErrorText = "Your password recovery code doesn't check out.";
		
		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "error.php" );
		
	}
	
}

include ( ABS_PATH . "footer.php" );

?>