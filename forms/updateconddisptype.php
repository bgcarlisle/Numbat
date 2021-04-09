<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    if (nbt_update_conditional_display_type ($_POST['event'], $_POST['cd_type'])) {
	echo "Success";
    } else {
	echo "Error updating conditional display event type";
    }

}



?>
