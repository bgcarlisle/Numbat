<?php

include_once ('../functions.php');

if ( ! preg_match("/^[0-9A-Za-z.\-_]+$/", $_POST['nbtSignupUsername']) ) { // If there's nonstandard characters in the user name
	
	echo "<p class=\"nbtSignupError nbtFeedbackBad nbtFinePrint\">Please include only the following in your chosen user name: 0-9, A-Z, periods, hyphens and underscores.</p>";
	
	include ('./signup.php');
	
} else { // User name has no nonstandard characters
	
	if ( nbt_username_is_taken ($_POST['nbtSignupUsername']) ) { // Check that the username isn't taken
		
		echo "<p class=\"nbtSignupError nbtFeedbackBad nbtFinePrint\">The user name you chose was already taken.</p>";
		
		include ('./signup.php');
		
	} else { // Username is not taken
		
		if ( ! preg_match("/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/", $_POST['nbtSignupEmail']) ) { // Check that it's a well-formed email
			
			echo "<p class=\"nbtSignupError nbtFeedbackBad nbtFinePrint\">The email address you provided was not well-formed.</p>";
		
			include ('./signup.php');
			
		} else { // Email is well-formed
			
			if ( nbt_email_is_in_use ( $_POST['nbtSignupEmail'] ) ) { // Check that email is not in use
				
				echo "<p class=\"nbtSignupError nbtFeedbackBad nbtFinePrint\">The email address you provided is already in use.</p>";
		
				include ('./signup.php');
				
			} else { // Email is not in use
				
				if ( $_POST['nbtSignUpPassword1'] != $_POST['nbtSignUpPassword2'] ) { // Two passwords are not the same
					
					echo "<p class=\"nbtSignupError nbtFeedbackBad nbtFinePrint\">You must type the same password twice</p>";
		
					include ('./signup.php');
					
				} else { // Two passwords are the same
					
					include ('./success.php');
					
				}
				
			}
			
		}
		
	}
	
}

?>