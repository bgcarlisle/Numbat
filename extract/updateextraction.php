<?php

include_once ('../config.php');

if ( $_POST['answer'] == "NULL" ) {
	
	$_POST['answer'] = NULL;
	
}

if ( nbt_update_extraction ( $_POST['fid'], $_POST['id'], $_POST['question'], $_POST['answer'] ) ) {
	
	echo "Changes saved";
	
} else {
	
	echo "Something went wrong—changes not saved";
	
}

?>