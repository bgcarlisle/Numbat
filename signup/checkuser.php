<?php

include_once('../functions.php');

// Here's the actual code.

if ( sig_username_is_taken ( $_POST['username'] ) ) {
	
	echo "Username is not available :(";
	
} else {
	
	echo "Username is available :D";
	
}

?>