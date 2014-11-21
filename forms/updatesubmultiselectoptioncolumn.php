<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	nbt_update_sub_multi_select_option_column ( $_POST['subelement'], $_POST['selectid'], $_POST['oldcolumn'], $_POST['newcolumn'] );
	
}

$tablesubelementid = $_POST['subelement'];
				
include ('./submultiselectoptionstable.php');

?>