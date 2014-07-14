<?php

include ('../config.php');

if ( nbt_password_recovery_code_checks_out ( $_POST['username'], $_POST['changecode'] ) ) {
	
	nbt_change_password ($_POST['username'], $_POST['newpassword']);
	
	echo "success";
	
} else {
	
	echo "failure";
	
}

?>