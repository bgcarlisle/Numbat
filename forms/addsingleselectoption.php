<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	nbt_add_single_select_option ( $_POST['element'] );
	
}

$tableelementid = $_POST['element'];

include ('./singleselectoptionstable.php');

?>