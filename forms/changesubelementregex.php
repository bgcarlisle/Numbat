<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {
    
    nbt_change_subelement_regex ( $_POST['subelement'], $_POST['newregex'] );
    
}

?>
