<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    if (nbt_add_conditional_display_event ($_POST['element'])) {
	$elementid = $_POST['element'];
	include (ABS_PATH . 'forms/conditionals.php');
    } else {
	echo "Error adding conditional display event to database";
    }

}



?>
