<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	if ( nbt_change_refset_name ( $_POST['refset'], $_POST['newname'] ) ) {
		
		echo "Changes saved";
		
	} else {
		
		echo "Something went wrong—changes not saved";
		
	}
	
	
	
} else {
	
	echo "You do not have sufficient privileges";
	
}

?>