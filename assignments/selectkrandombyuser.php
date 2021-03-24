<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    $rids = nbt_get_k_random_referenceids_for_refset_by_user ($_POST['rsid'], $_POST['k'], $_POST['form'], $_POST['yn'], $_POST['user']);

    echo json_encode($rids);
}

?>
