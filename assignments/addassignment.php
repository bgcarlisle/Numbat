<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) >= 2 ) {

	if ( nbtAddAssignment ( $_POST['userid'], $_POST['formid'], $_POST['refset'], $_POST['ref'] ) ) {
		
		?>Assignment added<?php
		
	} else {
		
		?>Assignment fail<?php
		
	}
	
}

?>