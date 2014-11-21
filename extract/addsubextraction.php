<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	if (nbt_add_new_sub_extraction ( $_POST['elementid'], $_POST['refset'], $_POST['ref'], $_SESSION[INSTALL_HASH . '_nbt_userid'] )) {

		$nbtSubExtractionElementID = $_POST['elementid'];
		$nbtExtractRefSet = $_POST['refset'];
		$nbtExtractRefID = $_POST['ref'];

		include ('./subextraction.php');

	} else {

		echo "Something went wrong";

	}

}

?>
