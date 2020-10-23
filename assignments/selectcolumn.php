<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    $values = nbt_get_unique_values_for_refset_column ( $_POST['rsid'], $_POST['col']);

    echo json_encode($values);

}

?>
