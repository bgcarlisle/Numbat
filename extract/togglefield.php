<?php

include_once ('../config.php');

if (nbt_toggle_extraction ( $_POST['fid'], $_POST['id'], $_POST['question'] )) {
	
	echo "Changes saved";
	
} else {
	
	echo "Something went wrong—changes not saved";
	
}

?>