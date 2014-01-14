<?php

include_once ('../config.php');

if ( sig_update_extraction_table_data ( $_POST['tid'], $_POST['row'], $_POST['column'], $_POST['newvalue']) ) {
	
	echo "Changes saved";
	
} else {
	
	echo "Something went wrong—changes not saved";
	
}

?>