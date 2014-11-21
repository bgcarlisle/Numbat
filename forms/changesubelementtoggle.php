<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	nbt_change_sub_element_toggle ( $_POST['subelement'], $_POST['newtoggle'] );
	
}

?>