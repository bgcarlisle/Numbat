<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	nbt_add_sub_multi_select_option ( $_POST['subelement'] );
	
}

$tablesubelementid = $_POST['subelement'];

include ('./submultiselectoptionstable.php');

?>