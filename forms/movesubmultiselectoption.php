<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_move_sub_select_option ( $_POST['selectid'], $_POST['direction'] );
	
}

$tablesubelementid = $_POST['subelement'];

include ('./submultiselectoptionstable.php');

?>