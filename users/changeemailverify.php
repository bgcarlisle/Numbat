<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    if ($_POST['verify'] == 1) {
	$newval = 0;
    } else {
	$newval = 1;
    }

    nbt_manually_change_email_verification ($_POST['user'], $newval);
    
}

?>
