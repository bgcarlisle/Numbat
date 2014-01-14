<?php

include_once ('../config.php');

if (nbt_add_new_extraction_table_data_row ( $_POST['tid'], $_POST['refset'], $_POST['ref'], $_SESSION['nbt_userid'] )) {

	$nbtExtractTableDataID = $_POST['tid'];
	$nbtExtractRefSet = $_POST['refset'];
	$nbtExtractRefID = $_POST['ref'];
	
	include ('./tabledata.php');
	
} else {
	
	echo "Something went wrong";
	
}

?>