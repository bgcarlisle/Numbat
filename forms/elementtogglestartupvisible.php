<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	echo nbt_toggle_element_startup_visible ($_POST['element']);

}

?>
