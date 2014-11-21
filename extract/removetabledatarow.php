<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	nbt_remove_table_data_row ( $_POST['tid'], $_POST['row'] );

}

?>
