<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	nbt_new_extraction_form ($_POST['formtype']);

}

include ('./formstable.php');

?>
