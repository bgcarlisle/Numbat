<?php

include_once ('../config.php');

if ( $_POST['answer'] == "NULL" ) {
	
	$_POST['answer'] = NULL;
	
}

if ( nbt_update_citation_property ( $_POST['section'], $_POST['cid'], $_POST['question'], $_POST['answer'] ) ) {
	
	echo "Changes saved";
	
} else {
	
	echo "Something went wrong—changes not saved";
	
}

?>