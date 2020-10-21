<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    switch ($_POST['elementtype']) {

	case "single_select":

	    if ( nbt_update_final ( $_POST['fid'], $_POST['rsid'], $_POST['rid'], $_POST['column'], $_POST['newvalue']) ) {
		
		echo "Changes saved";
		
	    } else {
		
		echo "Something went wrong—changes not saved";
		
	    }

	    break;

	case "multi_select":

	    break;
	    
    }

}

?>
