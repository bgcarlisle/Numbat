<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    if (nbt_update_conditional_display_trigger_option ($_POST['event'], $_POST['trigger_option'])) {
	echo "Success";
    } else {
	echo "Error updating conditional display event";
    }

}



?>
