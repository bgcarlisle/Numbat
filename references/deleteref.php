<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	if ( nbt_delete_ref ( $_POST['refset'], $_POST['ref'] ) ) {
		
		echo "Deleted";
		
	} else {
		
		echo "Something went wrong—changes not saved";
		
	}
	
} else {
	
	echo "You do not have sufficient privileges";
	
}

?>
