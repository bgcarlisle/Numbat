<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	nbt_remove_table_data_column ( $_POST['subelement'], $_POST['column'], TRUE );

}

$tableelementid = $_POST['subelement'];

include ('./subtabledata.php');

?>
