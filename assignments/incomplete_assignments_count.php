<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    echo count (get_incomplete_assignments_for_form_and_refset($_POST['fid'], $_POST['rsid']));

}

?>
