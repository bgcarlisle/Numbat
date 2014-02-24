<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) >= 2 ) {

	nbt_set_master_status ( $_POST['formid'], $_POST['masterid'], $_POST['masterstatus'] );

}

echo $_POST['formid'] . " " . $_POST['masterid'] . " " . $_POST['masterstatus'];

?>