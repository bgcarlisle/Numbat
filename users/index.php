<?php

include_once ('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in
	
	if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
		
		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "users/users.php" );
		
	} else {
		
		$nbtErrorText = "You do not have permission to administer users.";
		
		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "error.php" );
		
	}
	
} else {
	
	$nbtErrorText = "You are not logged in.";
			
	include ( ABS_PATH . "header.php" );
	include ( ABS_PATH . "error.php" );
	
}

?>