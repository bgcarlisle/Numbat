<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {
    
    nbt_change_tags_prompts ( $_POST['element'], $_POST['newtagsprompts'] );
    
}

?>
