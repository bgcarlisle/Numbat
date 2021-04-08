<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    $subelements = json_decode($_POST['subelementorder']);

    $successes = 0;
    $sortorder = 1;
    foreach ($subelements as $subelement) {

	if (nbt_update_subelement_sortorder ($subelement, $sortorder)) {
	    $successes ++;
	}

	$sortorder ++;
    }

    if ($successes == count ($subelements)) {
	echo json_encode (TRUE);
    }
    
}

?>
