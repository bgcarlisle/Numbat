<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_move_form_element ( $_POST['element'], $_POST['direction'] );
	
}

$_GET['id'] = $_POST['formid'];

include ('./elements.php');

?>