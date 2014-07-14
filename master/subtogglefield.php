<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) >= 2 ) {

	if (nbt_toggle_msub_extraction ( $_POST['eid'], $_POST['id'], $_POST['question'] )) {
		
		echo "Changes saved";
		
	} else {
		
		echo "Something went wrong—changes not saved";
		
	}

}

?>