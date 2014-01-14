<?php

include_once ('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in
	
	if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) >= 2 ) {
		
		if ( isset ( $_GET['action'] ) ) {
			
			switch ( $_GET['action'] ) {
				
				case "preview":
				
					$ref['title'] = "Example title for a very special paper";
					$ref['authors'] = "Carlisle, BG et al.";
					$ref['journal'] = "The Journal of Fancypants";
					$ref['year'] = "2014";
					$ref['abstract'] = "BACKGROUND: Mostly white. METHODS: Many? RESULTS: Few. DISCUSSION: No thank you.";
					
					include ( ABS_PATH . "header.php" );
					include ( ABS_PATH . "extract/extract.php" );
				
				break;
				
				case "extract":
				
					$ref = nbt_get_reference_for_refsetid_and_refid ( $_GET['refset'], $_GET['ref'] );
				
					include ( ABS_PATH . "header.php" );
					include ( ABS_PATH . "extract/extract.php" );
				
				break;
				
			}
			
		}
		
		
		
	} else {
		
		$nbtErrorText = "You do not have permission to do extractions.";
		
		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "error.php" );
		
	}
	
} else {
	
	$nbtErrorText = "You are not logged in.";
			
	include ( ABS_PATH . "header.php" );
	include ( ABS_PATH . "error.php" );
	
}

?>