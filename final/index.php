<?php

include_once ('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in
	
	if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {
		
		if ( isset ( $_GET['action'] ) ) {
			
			switch ( $_GET['action'] ) {
				
			    case "reconcile":
				
				include ( ABS_PATH . "header.php" );
				include ( ABS_PATH . "final/final.php" );
				
				break;


			    case "reconcilescreened":
					
				include ( ABS_PATH . "header.php" );
				include ( ABS_PATH . "final/screen.php" );
				
				break;
				
			}
			
		} else {
		
			include ( ABS_PATH . "header.php" );
			include ( ABS_PATH . "final/multi.php" );
		
		}
		
	} else {
		
		$nbtErrorText = "You do not have permission to reconcile extractions.";
		
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
