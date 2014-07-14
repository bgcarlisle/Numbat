<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_change_form_description ( $_POST['formid'], $_POST['newname'] );
	
}

?>