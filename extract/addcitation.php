<?php

include_once ("../config.php");

nbt_add_citation ($_POST['citationsid'], $_POST['refset'], $_POST['reference'], $_POST['userid'], $_POST['citation']);

$nbtListCitationsCitationID = $_POST['citationsid'];
$nbtListCitationsCitationDB = $_POST['citationsuffix'];
$nbtListCitationsRefSetID = $_POST['refset'];
$nbtListCitationsReference = $_POST['reference'];

include ("./listcitations.php");

?>