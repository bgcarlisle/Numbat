<?php

include_once ('../../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in
	
	if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {
		
		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "references/manual/manual.php" );
		
	} else {
		
		$nbtErrorText = "You do not have permission to edit manually added references.";
		
		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "error.php" );
		
	}
	
} else {
	
	$nbtErrorText = "You are not logged in.";
			
	include ( ABS_PATH . "header.php" );
	include ( ABS_PATH . "error.php" );
	
}

include ( ABS_PATH . "footer.php" );

?>