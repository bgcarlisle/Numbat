<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	nbt_remove_sub_extraction_instance ( $_POST['elementid'], $_POST['subextractionid'] );

}

?>
