<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	nbt_add_table_data ( $_POST['formid'], $_POST['elementid'], $_POST['tableformat'] );

}

$_GET['id'] = $_POST['formid'];

include ('./elements.php');

?>
