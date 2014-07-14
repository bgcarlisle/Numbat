<?php

include_once('../config.php');

// Here's the actual code.

if ( nbt_email_is_in_use ( $_POST['email'] ) ) {
	
	echo "Email is already in use :(";
		
} else {
	
	echo "No one has registered with that email :D";
	
}

?>