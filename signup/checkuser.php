<?php

include_once('../config.php');

// Here's the actual code.

if ( nbt_username_is_taken ( $_POST['username'] ) ) {
	
	echo "Username is not available :(";
	
} else {
	
	echo "Username is available :D";
	
}

?>