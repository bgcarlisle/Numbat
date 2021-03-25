<?php

include_once ('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in

    if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	if ( ! isset ( $_GET['action'] ) ) {

	    include ( ABS_PATH . "header.php" );
	    include ( ABS_PATH . "forms/forms.php" );

	} else {

	    switch ( $_GET['action'] ) {

		case "edit":

		    include ( ABS_PATH . "header.php" );
		    include ( ABS_PATH . "forms/editform.php" );

		    break;

		case "exportform":

		    include ( ABS_PATH . "forms/exportform.php" );

		    break;

		case "exportcodebook":

		    include ( ABS_PATH . "forms/exportcodebook.php" );

		    break;
	    }

	}


    } else {

	$nbtErrorText = "You do not have permission to edit extraction forms.";

	include ( ABS_PATH . "header.php" );
	include ( ABS_PATH . "error.php" );

    }

} else {

    $nbtErrorText = "You are not logged in.";

    include ( ABS_PATH . "header.php" );
    include ( ABS_PATH . "error.php" );

}

if ( $_GET['action'] != "export" ) {
    include ( ABS_PATH . "footer.php" );
}


?>
