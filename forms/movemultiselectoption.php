<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_move_select_option ( $_POST['selectid'], $_POST['direction'] );
	
}

$tableelementid = $_POST['element'];

include ('./multiselectoptionstable.php');

?>