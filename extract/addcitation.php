<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	nbt_add_citation ($_POST['citationsid'], $_POST['refset'], $_POST['reference'], $_POST['userid'], $_POST['citation']);

	$nbtListCitationsCitationID = $_POST['citationsid'];
	$nbtListCitationsCitationDB = $_POST['citationsuffix'];
	$nbtListCitationsRefSetID = $_POST['refset'];
	$nbtListCitationsReference = $_POST['reference'];

	include ("./listcitations.php");

}

?>
