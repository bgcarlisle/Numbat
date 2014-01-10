<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_update_table_data_column_display ( $_POST['column'], $_POST['newvalue'] );
	
}

?>