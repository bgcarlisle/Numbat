<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	nbt_add_sub_text_area_field ( $_POST['elementid'] );
	
}

$subelementid = $_POST['elementid'];

include ('./subextraction.php');

?>
