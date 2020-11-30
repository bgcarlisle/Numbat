<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    if ( nbt_restart_extraction_timer ( $_POST['fid'], $_POST['rsid'], $_POST['rid'], $_SESSION[INSTALL_HASH . '_nbt_userid'] ) ) {

	echo 'Timer reset';
	
    } else {

	echo 'DB error';
	
    }

}

?>
