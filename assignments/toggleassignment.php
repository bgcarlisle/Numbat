<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    $allassignments = nbt_get_all_assignments_for_refset ( $_POST['refset'] );

    $assignmentfound = FALSE;

    foreach ( $allassignments as $assign ) {

	if (
	    $assign['referenceid'] == $_POST['ref'] &
	    $assign['formid'] == $_POST['formid'] &
	    $assign['userid'] == $_POST['userid']
	) {
	    $assignmentfound = TRUE;
	    $aid = $assign['aid'];
	}
	
    }

    if ( $assignmentfound ) {

	nbtDeleteAssignment($aid);
	
    } else {
	
	nbtAddAssignment ( $_POST['userid'], $_POST['formid'], $_POST['refset'], $_POST['ref'] );
	
    }

}

?>
