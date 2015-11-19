<?php

include_once ('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in

	if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

		if ( isset ( $_GET['action'] ) ) {

			switch ( $_GET['action'] ) {

				case "preview":

					$ref['title'] = "Example title for a very special paper";
					$ref['authors'] = "Carlisle, BG et al.";
					$ref['journal'] = "Fancypants Journal";
					$ref['year'] = "2014";
					$ref['abstract'] = "BACKGROUND: Mostly white. METHODS: Many? RESULTS: Few. DISCUSSION: No thank you.";

					$extraction['id'] = 0;

					include ( ABS_PATH . "header.php" );
					include ( ABS_PATH . "extract/extract.php" );

				break;

				case "extract":

					$ref = nbt_get_reference_for_refsetid_and_refid ( $_GET['refset'], $_GET['ref'] );

					$extraction = nbt_get_extraction ( $_GET['form'], $_GET['refset'], $_GET['ref'], $_SESSION[INSTALL_HASH . '_nbt_userid'] );

					include ( ABS_PATH . "header.php" );
					include ( ABS_PATH . "extract/extract.php" );

				break;

			}

		} else {

			include ( ABS_PATH . "header.php" );
			include ( ABS_PATH . "extract/assignments.php" );

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

include ( ABS_PATH . "footer.php" );

?>
