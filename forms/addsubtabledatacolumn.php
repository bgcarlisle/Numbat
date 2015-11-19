<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	nbt_add_table_data_column ( $_POST['subelement'], "table_data", TRUE );

}

$tableelementid = $_POST['subelement'];
$tableformat = "table_data";

include ('./subtabledata.php');

?>
