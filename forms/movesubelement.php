<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_move_sub_element ( $_POST['subelement'], $_POST['direction'] );
	
}

$subelementid = $_POST['element'];

include ('./subextraction.php');

?>