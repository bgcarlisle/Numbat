<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_add_sub_multi_select ( $_POST['elementid'] );
	
}

$subelementid = $_POST['elementid'];

include ('./subextraction.php');

?>