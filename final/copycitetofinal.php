<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	nbt_copy_citation_to_master ( $_POST['element'], $_POST['citationid'] );

}

$nbtListCitationsCitationID = $_POST['element'];
$nbtListCitationsRefSetID = $_POST['refset'];
$nbtListCitationsReference = $_POST['refid'];

include ('./finalcitations.php');

?>
