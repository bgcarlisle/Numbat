<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_add_sub_single_select_option ( $_POST['subelement'] );
	
}

$tablesubelementid = $_POST['subelement'];

include ('./subsingleselectoptionstable.php');

?>