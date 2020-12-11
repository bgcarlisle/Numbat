<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    $rows = nbt_get_table_data_rows ( $_POST['elementid'], $_POST['refset'], $_POST['ref'], $_POST['user']);

    foreach ( $rows as $row ) {

	nbt_copy_table_row_to_master ( $_POST['elementid'], $_POST['refset'], $_POST['ref'], $row['id'] );
	
    }

}

$nbtMasterTableID = $_POST['elementid'];
$nbtMasterRefSet = $_POST['refset'];
$nbtMasterRefID = $_POST['ref'];

$tableformat = $_POST['tableformat'];

include ('./finaltable.php');

?>
