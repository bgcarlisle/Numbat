<?php

include_once ('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in
	
	if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
		
		if ( isset ( $_GET['action'] ) ) {
			
			switch ( $_GET['action'] ) {
				
				case "edit":
					
					include ( ABS_PATH . "header.php" );
					include ( ABS_PATH . "references/edit.php" );
					
				break;
				
			}
			
		} else {
		
			include ( ABS_PATH . "header.php" );
			include ( ABS_PATH . "references/references.php" );
		
		}
		
	} else {
		
		$nbtErrorText = "You do not have permission to manage reference sets.";
		
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