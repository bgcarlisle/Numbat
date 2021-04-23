<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    if (nbt_update_conditional_display_trigger_element ($_POST['event'], $_POST['trigger_element'])) {
	$options = nbt_get_all_select_options_for_sub_element ($_POST['trigger_element']);
	echo json_encode($options);
    } else {
	json_encode (FALSE);
    }

}



?>
