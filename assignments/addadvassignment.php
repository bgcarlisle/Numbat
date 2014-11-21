<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	nbtAddAdvancedAssignment ( $_POST['userid'], $_POST['formid'], $_POST['refset'], $_POST['query'] );

}

?>
