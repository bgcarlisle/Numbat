<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) >= 2 ) {

	if ( sig_update_extraction_table_data ( $_POST['tid'], $_POST['row'], $_POST['column'], $_POST['newvalue']) ) {
		
		echo "Changes saved";
		
	} else {
		
		echo "Something went wrong—changes not saved";
		
	}

}

?>