<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_change_element_toggle ( $_POST['element'], $_POST['newtoggle'] );
	
}

?>