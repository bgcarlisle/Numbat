<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {

	nbt_add_table_data_column ( $_POST['element'], $_POST['tableformat'] );

}

$tableelementid = $_POST['element'];
$tableformat = $_POST['tableformat'];

include ('./tabledata.php');

?>
