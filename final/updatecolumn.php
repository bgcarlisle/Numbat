<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    switch ($_POST['elementtype']) {

	case "date_selector":

	    $converted_time =  strtotime($_POST['newvalue']);

	    if ( $converted_time ) {
		$_POST['newvalue'] = date('Y-m-d', $converted_time);

		if ( nbt_update_final ( $_POST['fid'], $_POST['rsid'], $_POST['rid'], $_POST['column'], $_POST['newvalue']) ) {
		    
		    echo "Changes saved";
		    
		} else {
		    
		    echo "Something went wrong—changes not saved";
		    
		}
	    } else {

		if ($_POST['newvalue'] == "") {

		    if ( nbt_update_final ( $_POST['fid'], $_POST['rsid'], $_POST['rid'], $_POST['column'], NULL) ) {
			
			echo "Changes saved";
			
		    }
		    
		} else {

		    echo "Something went wrong—changes not saved";

		}
		
	    }

	    break;

	default:

	    if ( nbt_update_final ( $_POST['fid'], $_POST['rsid'], $_POST['rid'], $_POST['column'], $_POST['newvalue']) ) {
		
		echo "Changes saved";
		
	    } else {
		
		echo "Something went wrong—changes not saved";
		
	    }
	    
    }

}

?>
