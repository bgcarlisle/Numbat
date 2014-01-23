<?php

include_once ('../config.php');

if ( nbt_update_manual_reference ( $_POST['refset'], $_POST['column'], $_POST['ref'], $_POST['newvalue'] ) ) {
	
	echo "Changes saved";
	
} else {
	
	echo "Something went wrong—changes not saved";
	
}

?>