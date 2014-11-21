<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	if ( nbt_update_citeno ( $_POST['sectionid'], $_POST['id'], $_POST['newvalue'] ) ) {
		
		echo "&#10003;";
		
	} else {
		
		echo ":(";
		
	}

}

?>