<?php

include_once ("../config.php");

if ( nbt_user_is_logged_in () ) { // User is logged in

    if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	echo nbt_delete_uploaded_file ($_POST['fid']);
	
    }
}

?>
