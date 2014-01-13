<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_add_citation_property ( $_POST['element'] );
	
}

$citationelementid = $_POST['element'];
				
include ('./citationproperties.php');

?>