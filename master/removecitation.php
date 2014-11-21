<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	nbt_remove_master_citation ($_POST['section'], $_POST['citation']);

}

?>