<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
	
	nbt_move_citation_property ( $_POST['column'], $_POST['direction'] );
	
}

$citationelementid = $_POST['element'];

include ('./citationproperties.php');

?>