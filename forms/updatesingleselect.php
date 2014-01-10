<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_update_single_select ( $_POST['selectid'], $_POST['column'], $_POST['newvalue'] );
	
}

?>