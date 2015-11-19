<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	if (nbt_add_new_extraction_table_data_row ( $_POST['tid'], $_POST['refset'], $_POST['ref'], $_SESSION[INSTALL_HASH . '_nbt_userid'], TRUE, $_POST['subextraction'] ) ) {

		$nbtExtractTableDataID = $_POST['tid'];
		$nbtExtractRefSet = $_POST['refset'];
		$nbtExtractRefID = $_POST['ref'];
		$nbtSubTableSubextractionID = $_POST['subextraction'];

		include ('./subtabledata.php');

	} else {

		echo "Something went wrong";

	}

}

?>
