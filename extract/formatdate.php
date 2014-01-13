<?php

if ( $_POST['datestring'] != "" ) {
	
	date_default_timezone_set ('America/Montreal');
	
	echo date ("Y-m", strtotime ( $_POST['datestring'] ) );
	
}

?>