<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	nbt_refdata_change_column_name ( $_POST['element'], $_POST['newcolumnname'] );

}

?>
