<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_delete_sub_element ( $_POST['element'] );
	
}

?>