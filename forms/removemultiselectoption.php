<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	nbt_remove_multi_select_option ( $_POST['element'], $_POST['selectid'] );
	
}

$tableelementid = $_POST['element'];

include ('./multiselectoptionstable.php');

?>