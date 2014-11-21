<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	nbt_move_sub_extraction ( $_POST['element'], $_POST['refset'], $_POST['ref'], $_POST['subextraction'], $_POST['direction'], $_POST['userid'] );
	
}

$nbtSubExtractionElementID = $_POST['element'];
$nbtExtractRefSet = $_POST['refset'];
$nbtExtractRefID = $_POST['ref'];
$nbtExtractUserID = $_POST['userid'];

include ( ABS_PATH . 'master/subextraction.php');

?>