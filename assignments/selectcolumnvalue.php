<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    $rids = nbt_get_referenceids_for_refset_column_and_value ($_POST['rsid'], $_POST['col'], $_POST['val']);

    echo json_encode($rids);
}

?>
