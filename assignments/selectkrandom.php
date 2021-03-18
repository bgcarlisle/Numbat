<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    $rids = nbt_get_k_random_referenceids_for_refset ($_POST['rsid'], $_POST['k'], $_POST['n'], $_POST['crit'], $_POST['comp'], $_POST['form']);

    echo json_encode($rids);
}

?>
