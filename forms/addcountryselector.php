<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_add_country_selector ( $_POST['formid'] );
	
}

$_GET['id'] = $_POST['formid'];

include ('./elements.php');

?>