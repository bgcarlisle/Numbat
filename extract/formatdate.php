<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	if ( $_POST['datestring'] != "" ) {

		date_default_timezone_set ('America/Montreal');

		echo date ("Y-m", strtotime ( $_POST['datestring'] ) );

	}

}

?>
