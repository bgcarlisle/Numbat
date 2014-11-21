<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	nbt_update_multi_select_option_column ( $_POST['element'], $_POST['selectid'], $_POST['oldcolumn'], $_POST['newcolumn'] );
	
}

$tableelementid = $_POST['element'];

include ('./multiselectoptionstable.php');

?>