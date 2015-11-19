<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	nbt_move_table_data_column ( $_POST['column'], $_POST['direction'], TRUE );

}

$tableelementid = $_POST['subelement'];

include ('./subtabledata.php');

?>
