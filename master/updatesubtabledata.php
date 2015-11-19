<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	if ( nbt_update_extraction_mtable_data ( $_POST['tid'], $_POST['row'], $_POST['column'], $_POST['newvalue'], TRUE) ) {

		echo "Changes saved";

	} else {

		echo "Something went wrong—changes not saved";

	}

}

?>
