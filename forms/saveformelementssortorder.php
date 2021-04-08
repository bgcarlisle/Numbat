<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    $elements = json_decode($_POST['elementorder']);

    $successes = 0;
    $sortorder = 1;
    foreach ($elements as $element) {

	if (nbt_update_element_sortorder ($element, $sortorder)) {
	    $successes ++;
	}

	$sortorder ++;
    }

    if ($successes == count ($elements)) {
	echo json_encode (TRUE);
    }
    
}

?>
