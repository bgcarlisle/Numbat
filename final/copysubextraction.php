<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	nbt_copy_sub_extraction_to_master ( $_POST['elementid'], $_POST['refset'], $_POST['ref'], $_POST['original'] );

}

$nbtMasterSubExtrID = $_POST['elementid'];
$nbtMasterRefSet = $_POST['refset'];
$nbtMasterRefID = $_POST['ref'];

include ('./finalsubextraction.php');

?>
