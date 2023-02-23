<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	nbt_subrefdata_change_format ( $_POST['subelement'], $_POST['newcolumnname'] );

}

?>
