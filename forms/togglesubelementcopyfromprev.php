<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	echo nbt_toggle_subelement_copy_from_prev ( $_POST['subelement'] );

}

?>
