<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) >= 2 ) {

	nbt_copy_multi_select_to_master ( $_POST['formid'], $_POST['refset'], $_POST['ref'], $_POST['extrid'], $_POST['element'] );

}

?>