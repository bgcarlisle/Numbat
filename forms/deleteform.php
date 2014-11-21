<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	nbt_delete_extraction_form ( $_POST['formid'] );
	
}

include ('./formstable.php');

?>