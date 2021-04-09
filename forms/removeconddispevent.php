<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    if (nbt_remove_conditional_display_event ($_POST['event'])) {
	echo "Success";
    } else {
	echo "Error removing conditional display event to database";
    }

}



?>
