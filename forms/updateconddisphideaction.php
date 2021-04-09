<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    if (nbt_update_conditional_display_destructive_hiding ($_POST['element'], $_POST['action'])) {
	echo "Success";
    } else {
	echo "Error updating conditional display event logical operator";
    }

}



?>
