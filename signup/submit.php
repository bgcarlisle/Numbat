<?php

include_once ('../functions.php');

if ( ! preg_match("/^[0-9A-Za-z.\-_]+$/", $_POST['sigSignupUsername']) ) { // If there's nonstandard characters in the user name
	
	echo "<p class=\"sigSignupError sigFeedbackBad sigFinePrint\">Please include only the following in your chosen user name: 0-9, A-Z, periods, hyphens and underscores.</p>";
	
	include ('./signup.php');
	
} else { // User name has no nonstandard characters
	
	if ( sig_username_is_taken ($_POST['sigSignupUsername']) ) { // Check that the username isn't taken
		
		echo "<p class=\"sigSignupError sigFeedbackBad sigFinePrint\">The user name you chose was already taken.</p>";
		
		include ('./signup.php');
		
	} else { // Username is not taken
		
		if ( ! preg_match("/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/", $_POST['sigSignupEmail']) ) { // Check that it's a well-formed email
			
			echo "<p class=\"sigSignupError sigFeedbackBad sigFinePrint\">The email address you provided was not well-formed.</p>";
		
			include ('./signup.php');
			
		} else { // Email is well-formed
			
			if ( sig_email_is_in_use ( $_POST['sigSignupEmail'] ) ) { // Check that email is not in use
				
				echo "<p class=\"sigSignupError sigFeedbackBad sigFinePrint\">The email address you provided is already in use.</p>";
		
				include ('./signup.php');
				
			} else { // Email is not in use
				
				if ( $_POST['sigSignUpPassword1'] != $_POST['sigSignUpPassword2'] ) { // Two passwords are not the same
					
					echo "<p class=\"sigSignupError sigFeedbackBad sigFinePrint\">You must type the same password twice</p>";
		
					include ('./signup.php');
					
				} else { // Two passwords are the same
					
					include ('./success.php');
					
				}
				
			}
			
		}
		
	}
	
}

?>