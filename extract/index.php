<?php

include_once ('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in

    if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    	if ( isset ( $_GET['action'] ) ) {

    	    switch ( $_GET['action'] ) {

        		case "preview":

        		    $refset['title'] = 0;
        		    $refset['authors'] = 1;
        		    $refset['journal'] = 2;
        		    $refset['year'] = 3;
        		    $refset['abstract'] = 4;

        		    $ref[0] = "Example title for a very special paper";
        		    $ref[1] = "Carlisle, BG et al.";
        		    $ref[2] = "Fancypants Journal";
        		    $ref[3] = "2014";
        		    $ref[4] = "Abstract text!!";

        		    $extraction['id'] = 0;
        		    $form_preview = TRUE;

        		    include ( ABS_PATH . "header.php" );
        		    include ( ABS_PATH . "extract/extract.php" );

        		    break;

        		case "extract":

        		    $refset = nbt_get_refset_for_id ( $_GET['refset'] );
        		    $ref = nbt_get_reference_for_refsetid_and_refid ( $_GET['refset'], $_GET['ref'] );

        		    $extraction = nbt_get_extraction ( $_GET['form'], $_GET['refset'], $_GET['ref'], $_SESSION[INSTALL_HASH . '_nbt_userid'] );

        		    include ( ABS_PATH . "header.php" );
        		    include ( ABS_PATH . "extract/extract.php" );

        		    break;

            case "screen":

        		    $refset = nbt_get_refset_for_id ( $_GET['refset'] );
                $assignments = nbt_get_assignments_for_user_and_refset ( $_SESSION[INSTALL_HASH . '_nbt_userid'], $refset['id'], $_GET['sort'], $_GET['sortdirection'], "screening" );

                include ( ABS_PATH . "header.php" );
                include (ABS_PATH . "extract/screen.php");

                break;

        		case "import":

        		    if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

        			include (ABS_PATH . "header.php");
        			include (ABS_PATH . "extract/import.php");

        		    } else {

        			$nbtErrorText = "You do not have permission to administer users.";

        			include ( ABS_PATH . "header.php" );
        			include ( ABS_PATH . "error.php" );

        			}

        		    break;

    	    }

    	} else {

    	    include ( ABS_PATH . "header.php" );
    	    include ( ABS_PATH . "extract/assignments.php" );

    	}

    } else {

    	$nbtErrorText = "<p>You do not have permission to do extractions.</p><p>A user with admin privileges must go to the user administration page and change your level of privileges to User or Admin for you to access this page.</p>";

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
