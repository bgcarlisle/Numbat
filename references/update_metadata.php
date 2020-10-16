<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    if (nbt_update_refset_metadata ( $_POST['refsetid'], $_POST['column'], $_POST['newcolumn'] )) {

	echo "Changes saved";
	
    } else {

	echo "Error";
	
    }
    
}

?>
