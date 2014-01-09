<?php

include_once ('../functions.php');

if ( isset ( $_POST['sigSignupUsername'] ) ) {
	
	include ("../header.php");
	include ("./submit.php");
	
} else {
	
	if ( isset ($_GET['code']) ) {
		
		if ( sig_verify_email_address ($_GET['username'], $_GET['code']) ) {
			
			include ("../header.php");
			include ("./verify.php");
			
		} else {
			
			$sigErrorText = "Your verification code didn't check out.";
			
			include ("../header.php");
			include ("../error.php");
			
		}
		
	} else {
	
		include ("../header.php");
		include ("./signup.php");
	
	}
	
}

include ("../footer.php");

?>