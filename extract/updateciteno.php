<?php

include_once ('../config.php');

if ( nbt_update_citeno ( $_POST['sectionid'], $_POST['id'], $_POST['newvalue'] ) ) {
	
	echo "&#10003;";
	
} else {
	
	echo ":(";
	
}

?>