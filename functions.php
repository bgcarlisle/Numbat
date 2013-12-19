<?php

session_start ();

function nbt_user_is_logged_in () {
	
	if ( isset ($_SESSION['sig_valid_login']) && $_SESSION['sig_valid_login'] == 1 ) {
		
		return TRUE;
		
	} else {
		
		return FALSE;
		
	}
	
}

function nbt_creds_check_out ( $username, $password ) { // Returns TRUE if the username and password work; FALSE otherwise
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT password, salt, emailverify FROM users WHERE username = :username");
		
		$stmt->bindParam(':username', $user);
		
		$user = $username;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ($result as $row) {
			
			if (hash('sha256', $row['salt'] . $password) == $row['password']) {
				
				if ($row['emailverify'] != "0") {
					
					return FALSE;
					
				} else {
				
					return TRUE;
					
				}
				
			} else {
				
				return FALSE;
				
			}
			
		}
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_username_for_userid ($userid) { // Returns username if the userid is taken; FALSE otherwise
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT username FROM users WHERE id = :userid");
		
		$stmt->bindParam(':userid', $user);
		
		$user = $userid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ($result as $row) {
			
			return $row['username'];
			
			$founduser = 1;
			
		}
		
		if ($founduser != 1) {
		
			return FALSE;
		
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_userid_for_username ($username) { // Returns user id if the username is taken; FALSE otherwise
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT id FROM users WHERE username = :username");
		
		$stmt->bindParam(':username', $user);
		
		$user = $username;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ($result as $row) {
			
			return $row['id'];
			
			$founduser = 1;
			
		}
		
		if ($founduser != 1) {
		
			return FALSE;
		
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_log_user_in ( $username ) {
	
	$_SESSION['sig_valid_login'] = 1;
	$_SESSION['sig_userid'] = sig_get_userid_for_username ($username);
	$_SESSION['sig_username'] = sig_get_username_for_userid ( $_SESSION['sig_userid'] );
	
	// Set the "last login" to now
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE users SET lastlogin=NOW() WHERE username = :username");
		
		$stmt->bindParam(':username', $user);
		
		$user = $username;
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_username_is_taken ( $username ) { // Returns TRUE if the username is already registered; FALSE otherwise
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT id FROM users WHERE username = :username");
		
		$stmt->bindParam(':username', $user);
		
		$user = $username;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ($result as $row) {
			
			return TRUE;
			
			$founduser = 1;
			
		}
		
		if ($founduser != 1) {
		
			return FALSE;
		
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_email_is_in_use ( $email ) { // Returns TRUE if there is an account with that email address already; FALSE otherwise
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT id FROM users WHERE email = :email");
		
		$stmt->bindParam(':email', $emailaddress);
		
		$emailaddress = $email;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ($result as $row) {
			
			return TRUE;
			
			$foundemail = 1;
			
		}
		
		if ($foundemail != 1) {
		
			return FALSE;
		
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_save_new_user ($username, $email, $password) {
	// Note that this function does NOT check whether the user exists, the email has already been used, etc.
	// Returns TRUE if it works properly.
	// Also sends the verification email.

	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare('INSERT INTO users (username, password, salt, email, emailverify) VALUES (:username, :password, :salt, :email, :verification)');
		
		$stmt->bindParam(':username', $theuser);
		$stmt->bindParam(':password', $thepass);
		$stmt->bindParam(':salt', $salt);
		$stmt->bindParam(':email', $theemail);
		$stmt->bindParam(':verification', $verification);
		
		$theuser = $username;
		
		$theemail = $email;
		
		$string = md5(uniqid(rand(), true));
		$salt = substr($string, 0, 3);
		
		$string = md5(uniqid(rand(), true));
		$verification = substr($string, 0, 10);
		
		$thepass = hash('sha256', $salt . $password);
		
		$stmt->execute();
		
		$dbh = null;
		
		$message = "Greetings!";
		
		$message = $message . "\n\n" . "You are receiving this message because your email was registered for an account on the Signals extraction form with the following user name:";
		
		$message = $message . " " . $username;
		
		$message = $message . "\n\n" . "If you are receiving this email in error, just ignore it.";
		
		$message = $message . "\n\n" . "To verify your email and activate your account, click the following link.";
		
		$message = $message . "\n\n" . SITE_URL . "signup/?username=" . $username . "&code=" . $verification;
		
		$message = $message . "\n\n" . "If you have any questions or if you find any bugs, you can contact me at murph@bgcarlisle.com.";
		
		$message = $message . "\n\n" . "Enjoy! :)";
		
		$message = $message . "\n\nBenjamin Carlisle MA\nwww.bgcarlisle.com/signals/";
		
		mail ($email, "Confirm your email address for Signals extraction", $message, "From: Benjamin Carlisle MA <murph@bgcarlisle.com>");
			
		return TRUE;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
		return FALSE;
		
	}
	
}

function nbt_send_password_recovery_email ( $username ) {
	
	// First, generate a 10-character hash
	
	$string = md5(uniqid(rand(), true));
	$passwordchangecode = substr($string, 0, 10);
	
	// Insert that into the DB for the user
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE users SET `passwordchangecode` = :code WHERE username = :username");
		
		$stmt->bindParam(':code', $code);
		$stmt->bindParam(':username', $user);
		
		$user = $username;
		$code = $passwordchangecode;
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	// Get the user's email address
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT email FROM users WHERE username = :username LIMIT 1");
		
		$stmt->bindParam(':username', $un);
		
		$un = $username;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ($result as $row) {
			
			$email = $row['email'];
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	// Then, email the user with a link including the hash
	
	$message = "Greetings!";
		
	$message = $message . "\n\n" . "You are receiving this message because someone (probably you) requested that your password be reset. If you did not request your password to be reset, just ignore this email.";
	
	$message = $message . "\n\n" . "To reset your password, open the following address in your browser.";
	
	$message = $message . "\n\n" . SITE_URL . "forgot/?username=" . $username . "&code=" . $passwordchangecode;
	
	$message = $message . "\n\n" . "If you have any questions or if you find any bugs, you can contact me at murph@bgcarlisle.com.";
	
	$message = $message . "\n\n" . "Enjoy! :)";
	
	$message = $message . "\n\nBenjamin Carlisle MA\nwww.bgcarlisle.com/signals/";
	
	mail ($email, "Signals extraction password reset", $message, "From: Benjamin Carlisle MA <murph@bgcarlisle.com>");
	
}

function nbt_password_recovery_code_checks_out ( $username, $code) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT passwordchangecode FROM users WHERE username = :user LIMIT 1");
		
		$stmt->bindParam(':user', $user);
		
		$user = $username;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ($result as $row) {
			
			$dbcode = $row['passwordchangecode'];
			
		}
		
		if ( $dbcode == $code ) {
			
			return TRUE;
			
		} else {
			
			return FALSE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_change_password ( $username, $newpass ) {
	
	$string = md5(uniqid(rand(), true));
	$salt = substr($string, 0, 3);
	
	$thepass = hash('sha256', $salt . $newpass);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE users SET password = :newpass, salt = :salt, passwordchangecode = NULL WHERE username = :user LIMIT 1");
		
		$stmt->bindParam(':user', $user);
		$stmt->bindParam(':newpass', $pass);
		$stmt->bindParam(':salt', $sal);
		
		$user = $username;
		$pass = $thepass;
		$sal = $salt;
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}

}

function nbt_get_emailverify_for_userid ($userid) { // Returns user emailverify if the user id is taken; FALSE otherwise
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT emailverify FROM users WHERE id = :userid");
		
		$stmt->bindParam(':userid', $user);
		
		$user = $userid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ($result as $row) {
			
			return $row['emailverify'];
			
			$founduser = 1;
			
		}
		
		if ($founduser != 1) {
		
			return FALSE;
		
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_verify_email_address ($username, $code) { // Returns TRUE if the user's email is verified, FALSE otherwise
	
	$userid = sig_get_userid_for_username ( $username );
	
	if ( $userid ) { // if the user exists
		
		$emailverify = sig_get_emailverify_for_userid ( $userid );
		
		if ( $emailverify == "0" ) { // The account has already been verified
			
			return FALSE;
			
		} else { // The account has not yet been verified
			
			if ( $emailverify == $code ) { // If the code is correct
				
				try {
		
					$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
					$stmt = $dbh->prepare("UPDATE users SET emailverify = '0' WHERE id = :userid AND emailverify = :code");
					
					$stmt->bindParam(':userid', $user);
					$stmt->bindParam(':code', $emailcode);
					
					$user = $userid;
					$emailcode = $code;
					
					$stmt->execute();
		
					$dbh = null;
					
					return TRUE;
					
				}
				
				catch (PDOException $e) {
					
					echo $e->getMessage();
					
				}
				
			} else { // the code is not correct
				
				return FALSE;
				
			}
			
		}
		
	} else {
		
		return FALSE;
		
	}
	
}

function nbt_log_user_out () {
	
	$_SESSION = array ();
	session_destroy();
	setcookie ("sig_userid", "", time(), "/");
	setcookie ("sig_password", "", time(), "/");
	
}

function nbt_get_drugs_that_the_current_user_has_access_to () {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM permissions WHERE userid = :userid AND permission > 0;");
		
		$stmt->bindParam(':userid', $user);
		
		$user = $_SESSION['sig_userid'];
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_drugname_for_drugid ( $drugid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT drugname FROM drugs WHERE id = :drugid LIMIT 1;");
		
		$stmt->bindParam(':drugid', $did);
		
		$did = $drugid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ( $result as $row ) {
			
			return $row['drugname'];
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_drugid_for_drugname ( $drugname ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT id FROM drugs WHERE drugname = :drugname LIMIT 1;");
		
		$stmt->bindParam(':drugname', $did);
		
		$did = $drugname;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ( $result as $row ) {
			
			return $row['id'];
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_echo_reference ( $ref, $drugname ) {
	
	?><div class="sigGreyGradient">
		<h3><?php echo $ref['authors']; ?>. <?php echo $ref['title']; ?>. <span class="sigJournalName"><?php echo $ref['journal']; ?></span>: <?php echo $ref['year']; ?></h3>
		<p><a href="<?php echo SITE_URL . "drug/" . $drugname . "/" . $ref['id'] . "/"; ?>">Extract</a></p>
	</div><?php
	
}

function nbt_get_all_references_for_drug_id ( $drugid, $start = 0, $range = 25 ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
//		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " ORDER BY id ASC LIMIT :start, :range;");
		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " ORDER BY id ASC;");
		
		$stmt->bindParam(':start', $sta);
		$stmt->bindParam(':range', $ran);
		
		$dname = $drugname;
		$sta = $start;
		$ran = $range;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_all_unstarted_references_for_drug_id ( $drugid, $start = 0, $range = 25 ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
//		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " ORDER BY id ASC LIMIT :start, :range;");
		$stmt = $dbh->prepare("SELECT * FROM `signals_extractions`.`" . $drugname . "` WHERE include = 1 AND id NOT IN ( SELECT referenceid FROM `signals_extractions`.`extractions` WHERE drugid = :drugid ) ORDER BY id ASC;");
		
		$stmt->bindParam(':drugid', $did);
		
		$did = $drugid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_count_all_references_for_drug_id ( $drugid ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . ";");
		
		$dname = $drugname;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return count ( $result );
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_all_extractions_for_drug_id ( $drugid, $start, $range ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
//		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " WHERE `include` = 1 ORDER BY id ASC LIMIT :start, :range;");
		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " WHERE `include` = 1 ORDER BY id ASC;");
		
		$stmt->bindParam(':start', $sta);
		$stmt->bindParam(':range', $ran);
		
		$dname = $drugname;
		$sta = $start;
		$ran = $range;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_count_all_extractions_for_drug_id ( $drugid ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " WHERE `include` = 1;");
		
		$dname = $drugname;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return count ( $result );
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_reference_for_drugid_and_refid ( $drugid, $refid ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " WHERE id = :refid LIMIT 1;");
		
		$stmt->bindParam(':refid', $rid);
		
		$rid = $refid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ( $result as $row ) {
			
			return $row;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_return_references_for_drug_and_query ( $drugid, $refid, $query ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	if ( is_numeric ( $query ) ) {
		
		try { // Search citations for a particular number
			
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM citations WHERE drugid = :drugid AND referenceid = :refid AND cite_no = :cite LIMIT 6;");
			
			$stmt->bindParam(':drugid', $did);
			$stmt->bindParam(':refid', $rid);
			$stmt->bindParam(':cite', $cid);
			
			$cid = $query;
			$did = $drugid;
			$rid = $refid;
			
			$stmt->execute();
		
			$result = $stmt->fetchAll();
			
			foreach ( $result as $row ) {
				
				$citationid = $row['citationid'];
				
			}
			
			$dbh = null;
			
			try {
			
				$dbh2 = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt2 = $dbh2->prepare("SELECT * FROM " . $drugname . " WHERE id = :citationid LIMIT 1;");
				
				$stmt2->bindParam(':citationid', $citid);
				
				$citid = $citationid;
				
				$stmt2->execute();
			
				$result = $stmt2->fetchAll();
				
				$dbh2 = null;
				
				return $result;
				
			}
			
			catch (PDOException $e) {
				
				echo $e->getMessage();
				
			}
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
		
	} else { // Search the references
		
		try {
			
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " WHERE title LIKE :query OR authors LIKE :query LIMIT 6;");
			
			$stmt->bindParam(':query', $quer);
			
			$quer = "%" . $query . "%";
			
			$stmt->execute();
		
			$result = $stmt->fetchAll();
			
			$dbh = null;
			
			return $result;
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
	
	}
	
}

function nbt_add_citation ( $drugid, $reference, $userid, $section, $citation ) {
	
	// $section = 1 or 2
	// 1 means "intro"
	// 2 means "discussion"
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ('INSERT INTO citations (drugid, referenceid, userid, section, citationid) VALUES (:drug, :ref, :user, :sect, :cit)');
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);
		$stmt->bindParam(':sect', $sect);
		$stmt->bindParam(':cit', $cit);
		
		$did = $drugid;
		$ref = $reference;
		$user = $userid;
		$sect = $section;
		$cit = $citation;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			return TRUE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_citations ( $drugid, $reference, $section, $userid, $orderbycitation = FALSE ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ( $orderbycitation ) {
			
			$stmt = $dbh->prepare("SELECT * FROM citations WHERE drugid = :drug AND referenceid = :ref AND userid = :user AND section = :section ORDER by citationid;");
			
		} else {
			
			$stmt = $dbh->prepare("SELECT * FROM citations WHERE drugid = :drug AND referenceid = :ref AND userid = :user AND section = :section ORDER by id DESC;");
			
		}
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);
		$stmt->bindParam(':section', $sect);
		
		$did = $drugid;
		$ref = $reference;
		$user = $userid;
		$sect = $section;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_copy_citation_to_master ( $originalid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM citations WHERE id = :id LIMIT 1;");
		
		$stmt->bindParam(':id', $oid);
		
		$oid = $originalid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	foreach ( $result as $row ) {
		
		$cite = $row;
		
	}
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("INSERT INTO `master_citations` (drugid, referenceid, cite_no, section, citationid, samedrug, citedasnegative, clinical) VALUES (:drugid, :refid, :cite, :sect, :citid, :samedrug, :citneg, :clinic);");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':cite', $cit);
		$stmt->bindParam(':sect', $sec);
		$stmt->bindParam(':citid', $cid);
		$stmt->bindParam(':samedrug', $sd);
		$stmt->bindParam(':citneg', $citn);
		$stmt->bindParam(':clinic', $clin);
		
		$did = $cite['drugid'];
		$rid = $cite['referenceid'];
		$cit = $cite['cite_no'];
		$sec = $cite['section'];
		$cid = $cite['citationid'];
		$sd = $cite['samedrug'];
		$citn = $cite['citedasnegative'];
		$clin = $cite['clinical'];
		
		$stmt->execute();
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_master_citations ( $drugid, $reference, $section, $orderbycitation = FALSE ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ( $orderbycitation ) {
			
			$stmt = $dbh->prepare("SELECT * FROM `master_citations` WHERE drugid = :drug AND referenceid = :ref AND section = :section ORDER by citationid;");
			
		} else {
			
			$stmt = $dbh->prepare("SELECT * FROM `master_citations` WHERE drugid = :drug AND referenceid = :ref AND section = :section ORDER by id;");
			
		}
		
		
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':section', $sect);
		
		$did = $drugid;
		$ref = $reference;
		$sect = $section;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_remove_master_citation ( $id ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `master_citations` WHERE id = :id LIMIT 1;");
		
		$stmt->bindParam(':id', $cid);
		
		$cid = $id;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_remove_citation ( $id ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM citations WHERE id = :id LIMIT 1;");
		
		$stmt->bindParam(':id', $cid);
		
		$cid = $id;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_toggle_uncited_question ( $uncitedid, $question ) {
	
//	echo "ID: " . $uncitedid . " question: " . $question;
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM uncited WHERE id = :id LIMIT 1;");
		
//		$stmt->bindParam(':question', $ques);
		$stmt->bindParam(':id', $unc);
		
		$unc = $uncitedid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ( $result as $row ) {
			
			$oldanswer = $row[$question];
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE uncited SET " . $question . " = :value WHERE id = :id LIMIT 1;");
		
		$stmt->bindParam(':value', $val2);
		$stmt->bindParam(':id', $unc2);
		
		$unc2 = $uncitedid;
		
		if ( $oldanswer == 0 ) {
			$val2 = 1;
		} else {
			$val2 = 0;
		}
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_toggle_discussion_citation_question ( $citationid, $discussioncitationquestion ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM citations WHERE id = :citationid LIMIT 1;");
		
//		$stmt->bindParam(':question', $ques);
		$stmt->bindParam(':citationid', $cite);
		
		$cite = $citationid;
		$ques = $discussioncitationquestion;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ( $result as $row ) {
			
			$oldanswer = $row[$discussioncitationquestion];
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE citations SET " . $discussioncitationquestion . " = :value WHERE id = :citationid LIMIT 1;");
		
		$stmt->bindParam(':value', $val2);
		$stmt->bindParam(':citationid', $cite2);
		
		$cite2 = $citationid;
		
		if ( $oldanswer == 0 ) {
			$val2 = 1;
		} else {
			$val2 = 0;
		}
		
		$ques2 = $discussioncitationquestion;
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_update_extraction ( $id, $column, $value ) {
	
	$columns = array (
		"status",
		"corrauthorlocation",
		"ctgnctid",
		"phase",
		"phasestated",
		"multicentre",
		"post_unsampled_dose",
		"post_unsampled_drug",
		"post_unsampled_pop",
		"post_unsampled_outcome",
		"post_unsampled_caution",
		"post_unsampled_unclear",
		"indication",
		"subindication",
		"stage",
		"conflictofinterest",
		"conflictofinterest_disclosed",
		"effectsize",
		"alpha",
		"beta",
		"methods_minimum",
		"threshold",
		"randomisation",
		"primary_outcome_blinding",
		"allocation_blinding",
		"description_of_withdrawals",
		"study_design",
		"study_design_other",
		"primary_drug_name",
		"primary_drug_dose",
		"primary_drug_schedule",
		"primary_drug_route",
		"primary_drug_route_other",
		"primary_drug_combination",
		"primary_drug_combo_drugname",
		"primary_drug_combo_route",
		"primary_drug_combo_route_other",
		"comparator_drug_name",
		"comparator_drug_dose",
		"comparator_drug_schedule",
		"comparator_drug_route",
		"comparator_drug_route_other",
		"comparator_drug_combination",
		"comparator_drug_combo_drugname",
		"comparator_drug_combo_route",
		"comparator_drug_combo_route_other",
		"no_comp_cites",
		"duration_of_treatment",
		"duration_of_tx_imputed",
		"date_of_pt_enrolment",
		"date_of_closure",
		"concomitant_meds",
		"paediatric_subjects",
		"burdens_washout_text",
		"burdens_other_text",
		"burdens_none",
		"results_primary_endpoint",
		"results_toxicity",
		"results_assessment_risk_benefit",
		"stats_measure",
		"total_n",
		"clinicalinterest",
		"notes"
	);
	
	if ( in_array ($column, $columns) ) {
		
		try {
		
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE extractions SET " . $column . " = :value WHERE id = :id LIMIT 1;");
			
			$stmt->bindParam(':id', $rid);
			$stmt->bindParam(':value', $val);
			
			$rid = $id;
			$val = $value;
			
			if ( $stmt->execute() ) {
				
				$dbh = null;
				
				return TRUE;
				
			} else {
				
				$dbh = null;
				
				return FALSE;
				
			}
			
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
		
	}
	
}

function nbt_update_extraction_arm ( $id, $column, $value ) {
	
	$columns = array (
		"route",
		"combination"
	);
	
	if ( in_array ($column, $columns) ) {
		
		try {
		
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE arms SET " . $column . " = :value WHERE id = :id LIMIT 1;");
			
			$stmt->bindParam(':id', $rid);
			$stmt->bindParam(':value', $val);
			
			$rid = $id;
			$val = $value;
			
			if ( $stmt->execute() ) {
				
				$dbh = null;
				
				return TRUE;
				
			} else {
				
				$dbh = null;
				
				return FALSE;
				
			}
			
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
		
	}
	
}

function nbt_toggle_extraction ( $id, $column ) {
	
	$columns = array (
		"post_unsampled_dose",
		"post_unsampled_drug",
		"post_unsampled_pop",
		"post_unsampled_outcome",
		"post_unsampled_caution",
		"post_unsampled_unclear",
		"statedgoal_safety",
		"statedgoal_dosage",
		"statedgoal_efficacy",
		"statedgoal_pd",
		"statedgoal_pk",
		"statedgoal_other",
		"sponsor_govt",
		"sponsor_biotech",
		"sponsor_pharma",
		"sponsor_nonprofit",
		"sponsor_ns",
		"burdens_invasive_res_procedures",
		"burdens_wash_out",
		"burdens_other",
		"burdens_none",
		"study_design_dose_esc",
		"study_design_dose_find",
		"study_design_dose_rang",
		"study_design_hist_cont",
		"study_design_parallel",
		"study_design_futility",
		"study_design_simon2",
		"study_design_placebo",
		"study_design_crossover",
		"study_design_invasv",
		"study_design_is_other"
		
	);
	
	if ( in_array ($column, $columns) ) {
		
		try {
		
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("SELECT * FROM extractions WHERE id = :id LIMIT 1;");
			
			$stmt->bindParam(':id', $rid);
			
			$rid = $id;
			
			$stmt->execute();
			
			$result = $stmt->fetchAll();
			
			$dbh = null;
			
			foreach ( $result as $row ) {
				
				$old_answer = $row[$column];
				
			}
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
		
		try {
		
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("UPDATE extractions SET " . $column . " = :value WHERE id = :id LIMIT 1;");
			
			$stmt->bindParam(':id', $rid);
			$stmt->bindParam(':value', $val);
			
			$rid = $id;
			if ( $old_answer == 0 ) {
				$val = 1;
			} else {
				$val = 0;
			}
			
			if ( $stmt->execute() ) {
				
				$dbh = null;
				
				return TRUE;
				
			} else {
				
				$dbh = null;
				
				return FALSE;
				
			}
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
		
	}
	
}

function nbt_get_extraction ( $drugid, $refid, $userid ) {
	
	// Insert a row
	// This will fail if there is already a row
	// Hooray for unique MySQL indices
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO extractions (drugid, referenceid, userid) VALUES (:drugid, :refid, :userid);");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);
		
		$did = $drugid;
		$rid = $refid;
		$uid = $userid;
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	// See if an extraction exists
	
	$drugname = sig_get_drugname_for_drugid ( $drugid );
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM extractions WHERE drugid = :drugid AND referenceid = :refid AND userid = :userid LIMIT 1;");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);
		
		$did = $drugid;
		$rid = $refid;
		$uid = $userid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ( $result as $row ) {
			
			return $row;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_uncited ( $drugid, $refid, $userid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM uncited WHERE drugid = :drug AND referenceid = :ref AND userid = :user;");
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);
		
		$did = $drugid;
		$ref = $refid;
		$user = $userid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_add_uncited ( $drugid, $refid, $userid, $text ) {
	
//	echo "drugid: " . $drugid . " refid: " . $refid . " userid: " . $userid . " text: " . $text;
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ('INSERT INTO uncited (drugid, referenceid, userid, text) VALUES (:drug, :ref, :user, :text)');
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);
		$stmt->bindParam(':text', $tex);
		
		$did = $drugid;
		$ref = $refid;
		$user = $userid;
		$tex = $text;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			return TRUE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}

}

function nbt_remove_uncited ($uncid) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM uncited WHERE id = :id LIMIT 1;");
		
		$stmt->bindParam(':id', $uid);
		
		$uid = $uncid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
}

function nbt_echo_multi_select ($extraction, $question, $options) {
	
	// $options must be an array of the names of the column in the db
	
	foreach ( $options as $dbcolumn => $plaintext ) {
		
		?><a href="#" class="sigTextOptionSelect <?php
		
			echo "sig" . $question;
			
			if ( $extraction[$dbcolumn] == 1 ) {
				
				?> sigTextOptionChosen<?php
				
			}
			
		?>" id="sigMS<?php echo $dbcolumn; ?>" onclick="sigSaveMultiSelect(event, <?php echo $extraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'sigMS<?php echo $dbcolumn; ?>');"  conditionalid="sig<?php echo $dbcolumn ?>Cond"><?php echo $plaintext; ?></a><?php
		
	}
	
}

function nbt_echo_single_select ($extraction, $question, $answers) {
	
	// $question must be the name of the column in the db
	// $answers must be an array of the answer entered in the db and the plain text version displayed
	
	foreach ( $answers as $dbanswer => $ptanswer ) {
		
		?><a href="#" class="sigTextOptionSelect<?php
		
		echo " sig" . $question;
		
		if ( $extraction[$question] == $dbanswer ) {
			
			?> sigTextOptionChosen<?php
			
		}
		
		$buttonid = "sigQ" . $question . "A" . str_replace ( "/", "_", str_replace (" ", "_", $dbanswer) );
		
		?>" id="<?php echo $buttonid; ?>" onclick="sigSaveSingleSelect(event, <?php echo $extraction['id']; ?>, '<?php echo $question; ?>', '<?php echo $dbanswer; ?>', '<?php echo $buttonid; ?>', 'sig<?php echo $question; ?>');" conditionalid="<?php echo $buttonid ?>Cond"><?php echo $ptanswer; ?></a><?php
		
	}
	
}

function nbt_echo_text_field ($extraction, $dbcolumn, $maxlength, $allcaps = FALSE) {
	
	?><input type="text" value="<?php
	
	echo $extraction[$dbcolumn];
	
	?>" id="sigTextField<?php echo $dbcolumn; ?>" onblur="sigSaveTextField(<?php echo $extraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'sigTextField<?php echo $dbcolumn; ?>', 'sigTextField<?php echo $dbcolumn; ?>Feedback');" maxlength="<?php echo $maxlength; ?>"<?php
	
	if ( $allcaps ) {
		
		echo " style=\"text-transform: uppercase\"";
		
	}
	
	?>><span class="sigInputFeedback" id="sigTextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span><?php
}

function nbt_echo_inline_text_field ($extraction, $dbcolumn, $plaintext, $maxlength) {
	
	?><p class="sigInlineTextField">
		<span class="sigInputLabel"><?php echo $plaintext; ?></span>
		<input type="text" value="<?php
			
			echo $extraction[$dbcolumn];
			
		?>" id="sigTextField<?php echo $dbcolumn; ?>" onblur="sigSaveTextField(<?php echo $extraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'sigTextField<?php echo $dbcolumn; ?>', 'sigTextField<?php echo $dbcolumn; ?>Feedback');" maxlength="<?php echo $maxlength; ?>">
		<span class="sigInputFeedback" id="sigTextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span>
	</p><?php
	
}

function nbt_echo_date_selector ($extraction, $dbcolumn) {
	
	?><p class="sigDateSelector">
		<input type="text" value="<?php
			
			if ( substr ($extraction[$dbcolumn], 0, 7) != "0000-00" ) {
				
				echo substr ($extraction[$dbcolumn], 0, 7);
				
			}
			
		?>" id="sigDateField<?php echo $dbcolumn; ?>" onblur="sigSaveDateField(<?php echo $extraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'sigDateField<?php echo $dbcolumn; ?>', 'sigTextField<?php echo $dbcolumn; ?>Feedback');">
		<span class="sigInputFeedback" id="sigTextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span>
	</p><?php
}

function nbt_get_arms_table_rows ( $drugid, $refid, $userid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM arms WHERE drugid = :drug AND referenceid = :ref AND userid = :user ORDER BY id ASC;");
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);
		
		$did = $drugid;
		$ref = $refid;
		$user = $userid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
}

function nbt_get_outcomes_table_rows ( $drugid, $refid, $userid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM outcomes WHERE drugid = :drug AND referenceid = :ref AND userid = :user ORDER BY id ASC;");
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);
		
		$did = $drugid;
		$ref = $refid;
		$user = $userid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
}

function nbt_get_efficacy_table_rows ( $drugid, $refid, $userid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM efficacy WHERE drugid = :drug AND referenceid = :ref AND userid = :user ORDER BY id ASC;");
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);
		
		$did = $drugid;
		$ref = $refid;
		$user = $userid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
}

function nbt_get_safety_table_rows ( $drugid, $refid, $userid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM safety WHERE drugid = :drug AND referenceid = :ref AND userid = :user ORDER BY id ASC;");
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);
		
		$did = $drugid;
		$ref = $refid;
		$user = $userid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
}

function nbt_add_new_efficacy_table_row ($drugid, $refid, $userid) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO efficacy (drugid, referenceid, userid) VALUES (:drugid, :refid, :userid);");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);
		
		$did = $drugid;
		$rid = $refid;
		$uid = $userid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_remove_efficacy_table_row ( $rowid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM efficacy WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		
		$rid = $rowid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_update_efficacy_table ($rowid, $column, $newvalue) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE efficacy SET " . $column . " = :value WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		$stmt->bindParam(':value', $val);
		
		$rid = $rowid;
		$val = $newvalue;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_update_safety_table ($rowid, $column, $newvalue) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE safety SET " . $column . " = :value WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		$stmt->bindParam(':value', $val);
		
		$rid = $rowid;
		$val = $newvalue;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_add_new_safety_table_row ($drugid, $refid, $userid) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO safety (drugid, referenceid, userid) VALUES (:drugid, :refid, :userid);");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);
		
		$did = $drugid;
		$rid = $refid;
		$uid = $userid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_remove_safety_table_row ( $rowid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM safety WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		
		$rid = $rowid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_manual_refs_for_drug_id ( $drugid ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " WHERE `manual` = 1 ORDER BY id ASC;");
		
		$dname = $drugname;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_count_citations_of_manual_ref ( $refid, $drugid ) {
	
//	echo "ref: " . $refid . " drug: " . $drugid . "<br>";
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `signals_extractions`.`citations` WHERE drugid = :drugid AND citationid = :refid;");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		
		$did = $drugid;
		$rid = $refid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return count ($result);
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_echo_manual_ref ( $ref, $drugid ) {
	
	?><div class="sigManualRef" id="sigManRef<?php echo $drugid; ?>-<?php echo $ref['id']; ?>">
		<p class="sigInlineTextField">
			<span class="sigInputLabel">Title</span>
			<input type="text" value="<?php
				
				echo $ref['title'];
				
			?>" id="sigManRefTextField<?php echo $ref['id']; ?>Title" onblur="sigUpdateManualReference(<?php echo $drugid; ?>, <?php echo $ref['id']; ?>, 'title', 'sigManRefTextField<?php echo $ref['id']; ?>Title', 'sigManRefTextField<?php echo $ref['id']; ?>TitleFeedback');">
			<span class="sigInputFeedback" id="sigManRefTextField<?php echo $ref['id']; ?>TitleFeedback">&nbsp;</span>
		</p>
		<p class="sigInlineTextField">
			<span class="sigInputLabel">Authors</span>
			<input type="text" value="<?php
				
				echo $ref['authors'];
				
			?>" id="sigManRefTextField<?php echo $ref['id']; ?>Authors" onblur="sigUpdateManualReference(<?php echo $drugid; ?>, <?php echo $ref['id']; ?>, 'authors', 'sigManRefTextField<?php echo $ref['id']; ?>Authors', 'sigManRefTextField<?php echo $ref['id']; ?>AuthorsFeedback');">
			<span class="sigInputFeedback" id="sigManRefTextField<?php echo $ref['id']; ?>AuthorsFeedback">&nbsp;</span>
		</p>
		<p class="sigInlineTextField">
			<span class="sigInputLabel">Year</span>
			<input type="text" value="<?php
				
				echo $ref['year'];
				
			?>" id="sigManRefTextField<?php echo $ref['id']; ?>Year" onblur="sigUpdateManualReference(<?php echo $drugid; ?>, <?php echo $ref['id']; ?>, 'year', 'sigManRefTextField<?php echo $ref['id']; ?>Year', 'sigManRefTextField<?php echo $ref['id']; ?>YearFeedback');">
			<span class="sigInputFeedback" id="sigManRefTextField<?php echo $ref['id']; ?>YearFeedback">&nbsp;</span>
		</p>
		<p class="sigInlineTextField">
			<span class="sigInputLabel">Journal</span>
			<input type="text" value="<?php
				
				echo $ref['journal'];
				
			?>" id="sigManRefTextField<?php echo $ref['id']; ?>Journal" onblur="sigUpdateManualReference(<?php echo $drugid; ?>, <?php echo $ref['id']; ?>, 'journal', 'sigManRefTextField<?php echo $ref['id']; ?>Journal', 'sigManRefTextField<?php echo $ref['id']; ?>JournalFeedback');">
			<span class="sigInputFeedback" id="sigManRefTextField<?php echo $ref['id']; ?>JournalFeedback">&nbsp;</span>
		</p>
		<p class="sigInputLabel">Abstract</p>
		<textarea id="sigManRefTextField<?php echo $ref['id']; ?>Abstract" onblur="sigUpdateManualReference(<?php echo $drugid; ?>, <?php echo $ref['id']; ?>, 'abstract', 'sigManRefTextField<?php echo $ref['id']; ?>Abstract', 'sigManRefTextField<?php echo $ref['id']; ?>AbstractFeedback');" style="width: 90%;"><?php
			
			echo $ref['abstract'];
			
		?></textarea>
		<span class="sigInputFeedback" id="sigManRefTextField<?php echo $ref['id']; ?>AbstractFeedback">&nbsp;</span>
		<p class="sigFinePrint" id="sigRemoveManRef<?php echo $ref['id']; ?>">Cited x <?php echo sig_count_citations_of_manual_ref ( $ref['id'], $drugid ); ?> | <a href="#" onclick="sigConfirmRemoveManualReference(event, <?php echo $drugid; ?>, <?php echo $ref['id']; ?>);">Remove this reference</a></p>
	</div><?php
}

function nbt_add_manual_ref ( $drugid ) {
	
	$drugname = sig_get_drugname_for_drugid ( $drugid );
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ('INSERT INTO ' . $drugname . ' (manual) VALUES (1);');
		
		if ($stmt->execute()) {
			
			$stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");
			$stmt2->execute();
			
			$result = $stmt2->fetchAll();
		
			$dbh = null;
			
			foreach ( $result as $row ) {
				
				$newid = $row['newid']; // This is the auto_increment-generated ID for the new compare
				
			}
			
			return $newid;
			
		} else {
			
			return FALSE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_update_manual_reference ( $drugid, $column, $refid, $newvalue ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE `signals_extractions`.`" . $drugname . "` SET `" . $column . "` = :value WHERE `sunitinib`.`id` = :refid;");
//		UPDATE  `signals_extractions`.`sunitinib` SET  `title` =  'Title' WHERE  `sunitinib`.`id` =2886;
		
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':value', $val);
		
		$rid = $refid;
		$val = $newvalue;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_remove_manual_reference ( $drugid, $refid) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM " . $drugname . " WHERE id = :rid LIMIT 1;");
		
		$stmt->bindParam(':rid', $rid);
		
		$rid = $refid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_add_new_arms_table_row ($drugid, $refid, $userid) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO arms (drugid, referenceid, userid) VALUES (:drugid, :refid, :userid);");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);
		
		$did = $drugid;
		$rid = $refid;
		$uid = $userid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_add_new_outcomes_table_row ($drugid, $refid, $userid) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO outcomes (drugid, referenceid, userid) VALUES (:drugid, :refid, :userid);");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);
		
		$did = $drugid;
		$rid = $refid;
		$uid = $userid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_remove_arms_table_row ( $rowid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM arms WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		
		$rid = $rowid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_remove_outcomes_table_row ( $rowid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM outcomes WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		
		$rid = $rowid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_update_arms_table ($rowid, $column, $newvalue) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE arms SET " . $column . " = :value WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		$stmt->bindParam(':value', $val);
		
		$rid = $rowid;
		$val = $newvalue;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_update_outcomes_table ($rowid, $column, $newvalue) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE outcomes SET " . $column . " = :value WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		$stmt->bindParam(':value', $val);
		
		$rid = $rowid;
		$val = $newvalue;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_auto_insert_efficacy_table_rows ($drugid, $refid, $userid) {
	
	// First, find out what arms and outcomes have been added
	
	$arms_rows = sig_get_arms_table_rows ( $drugid, $refid, $userid );
	
	$outcomes_rows = sig_get_outcomes_table_rows ( $drugid, $refid, $userid );
	
	foreach ( $outcomes_rows as $outcomes_row ) {
		
		foreach ( $arms_rows as $arms_row ) {
			
			// Then, insert the new rows
	
			try {
				
				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("INSERT INTO efficacy (drugid, referenceid, userid, outcome, arm) VALUES (:drugid, :refid, :userid, :outcome, :arm);");
				
				$stmt->bindParam(':drugid', $did);
				$stmt->bindParam(':refid', $rid);
				$stmt->bindParam(':userid', $uid);
				$stmt->bindParam(':outcome', $out);
				$stmt->bindParam(':arm', $arm);
				
				$did = $drugid;
				$rid = $refid;
				$uid = $userid;
				$out = $outcomes_row['outcome'];
				$arm = $arms_row['arm'];
				
				if ( ! $stmt->execute() ) {
					
					$dbh = null;
					
					return FALSE;
					
				}
				
			}
			
			catch (PDOException $e) {
				
				echo $e->getMessage();
				
			}
			
		}
		
	}
	
	return TRUE;
	
}

function nbt_auto_insert_safety_table_rows ($drugid, $refid, $userid) {
	
	// First, find out what arms and outcomes have been added
	
	$arms_rows = sig_get_arms_table_rows ( $drugid, $refid, $userid );
	
	foreach ( $arms_rows as $arms_row ) {
		
		// Then, insert the new rows

		try {
			
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("INSERT INTO safety (drugid, referenceid, userid, arm) VALUES (:drugid, :refid, :userid, :arm);");
			
			$stmt->bindParam(':drugid', $did);
			$stmt->bindParam(':refid', $rid);
			$stmt->bindParam(':userid', $uid);
			$stmt->bindParam(':arm', $arm);
			
			$did = $drugid;
			$rid = $refid;
			$uid = $userid;
			$arm = $arms_row['arm'];
			
			if ( ! $stmt->execute() ) {
				
				$dbh = null;
				
				return FALSE;
				
			}
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
		
	}
	
	return TRUE;
	
}

function nbt_update_citation ( $id, $column, $value ) {
	
	$columns = array (
		"clinical"
	);
	
	if ( in_array ($column, $columns) ) {
		
		try {
		
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE citations SET " . $column . " = :value WHERE id = :id LIMIT 1;");
			
			$stmt->bindParam(':id', $rid);
			$stmt->bindParam(':value', $val);
			
			$rid = $id;
			$val = $value;
			
			if ( $stmt->execute() ) {
				
				$dbh = null;
				
				return TRUE;
				
			} else {
				
				$dbh = null;
				
				return FALSE;
				
			}
			
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
		
	}
	
}

function nbt_get_extractions_for_drug_and_ref ( $drugid, $refid ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
//		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " ORDER BY id ASC LIMIT :start, :range;");
		$stmt = $dbh->prepare("SELECT * FROM extractions WHERE drugid = :drugid AND referenceid = :refid ORDER BY id ASC;");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		
		$did = $drugid;
		$rid = $refid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_update_drug_ref_indication ( $drugid, $refid, $newvalue ) {
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
//		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " ORDER BY id ASC LIMIT :start, :range;");
		$stmt = $dbh->prepare("UPDATE " . $drugname . " SET indication = :newval WHERE id = :refid;");
		
		$stmt->bindParam(':newval', $newv);
		$stmt->bindParam(':refid', $rid);
		
		$newv = $newvalue;
		$rid = $refid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return TRUE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_update_citeno ( $citid, $newvalue ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE citations SET cite_no = :newval WHERE id = :citid;");
		
		$stmt->bindParam(':newval', $newv);
		$stmt->bindParam(':citid', $cid);
		
		$newv = $newvalue;
		$cid = $citid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return TRUE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_toggle_ref_inclusion ( $drugid, $refid ) {
	
	// First, find out whether it's included or not
	
	$drugname = sig_get_drugname_for_drugid ($drugid);
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM " . $drugname . " WHERE id = :refid LIMIT 1;");
		
		$stmt->bindParam(':refid', $rid);
		
		$rid = $refid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		foreach ( $result as $row ) {
			
			$priorinclusion = $row['include'];
			
		}
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	// Then, set the value to its opposite
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		if ( $priorinclusion == 1 ) {
			
			$stmt = $dbh->prepare("UPDATE " . $drugname . " SET include = 0 WHERE id = :refid LIMIT 1;");
			
		} else {
			
			$stmt = $dbh->prepare("UPDATE " . $drugname . " SET include = 1 WHERE id = :refid LIMIT 1;");
			
		}
		
		$stmt->bindParam(':refid', $rid);
		
		$rid = $refid;
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_delete_extraction ( $extrid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		
		$stmt = $dbh->prepare("DELETE FROM extractions WHERE id = :extrid LIMIT 1;");
		
		$stmt->bindParam(':extrid', $eid);
		
		$eid = $extrid;
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_completed_extractions ( $drugid, $refid ) {
	
	$drugname = sig_get_drugname_for_drugid ( $drugid );
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM extractions WHERE drugid = :drugid AND referenceid = :refid AND status = 2;");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		
		$did = $drugid;
		$rid = $refid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_set_master ( $drugid, $refid, $row, $value ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE `master` SET " . $row . " = :value WHERE `drugid` = :drugid AND `referenceid` = :ref LIMIT 1;");
		
		$stmt->bindParam(':value', $val);
		$stmt->bindParam(':ref', $rid);
		$stmt->bindParam(':drugid', $did);
		
		$val = $value;
		$rid = $refid;
		$did = $drugid;
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_test_extractions_for_equality ( $extractions, $master, $row, $title ) {
	
	// First, see if they're the same
	
	$counter = 0;
	
	$allequal = TRUE;
	
	foreach ( $extractions as $extr ) {
		
		if ( $counter > 0 ) {
			
			if ( $value != $extr[$row] ) {
				
				$allequal = FALSE;
				
			}
			
		}
		
		$value = $extr[$row];
		
		$counter ++;
		
	}
	
	if ( ! $allequal ) {
		
		?><div class="sigFeedbackBad sigDoubleResult"><p>&#9998; <?php echo $title; ?></p>
		<hr><?php
		
		foreach ( $extractions as $extr ) {
			
			?><p><span class="sigHidden" id="sigCheck<?php echo $extr['id']; ?>-<?php echo $row; ?>">&#10003;</span><span class="sigExtractionName"><?php echo sig_get_username_for_userid ( $extr['userid'] ); ?></span> <?php echo $extr[$row]; ?></p>
			<button onclick="sigMasterConfirmResponse('<?php echo $extr['id']; ?>-<?php echo $row; ?>');" id="sigConfirm<?php echo $extr['id']; ?>-<?php echo $row; ?>">Use this response</button>
			<button onclick="sigMasterUseResponse('<?php echo $extr['id']; ?>-<?php echo $row; ?>', <?php echo $extr['drugid']; ?>, <?php echo $extr['referenceid']; ?>, '<?php echo $row; ?>', <?php echo $extr['id']; ?>, '<?php echo $title; ?>');" id="sigUse<?php echo $extr['id']; ?>-<?php echo $row; ?>" class="sigHidden">Use on master copy</button>
			<button onclick="sigMasterCancelResponse('<?php echo $extr['id']; ?>-<?php echo $row; ?>');" id="sigCancel<?php echo $extr['id']; ?>-<?php echo $row; ?>" class="sigHidden">Cancel</button><?php
			
		}
		
		?></div>
		<p class="sigMaster<?php
		
		if ( $master[$row] == NULL ) {
			
			echo " sigHidden";
			
		}
		
		?>" id="sigMasterFeedback<?php echo $extr['drugid']; ?>-<?php echo $extr['referenceid']; ?>-<?php echo $row; ?>">Master copy: <?php echo $master[$row]; ?></p><?php
		
	} else {
		
		?><p class="sigFeedbackGood sigDoubleResult">&#10003; <?php echo $title; ?></p>
		<?php
		
		sig_set_master ( $extr['drugid'], $extr['referenceid'], $row, $extr[$row] );
		
		?>
		<p class="sigMaster">Master copy: <?php echo $extr[$row]; ?></p><?php
		
	}
	
}

function nbt_test_multi_extractions_for_equality ( $extractions, $master, $title, $options ) {
	
	$allequal = TRUE;
	
	foreach ( $options as $dbcolumn => $plaintext ) {
		
		$counter = 0;
	
		foreach ( $extractions as $extr ) {
			
			if ( $counter > 0 ) {
				
//				echo $dbcolumn . " value: " . $value . " other: " . $extr[$dbcolumn] . "<br>";
				
				if ( $value == NULL ) {
					
					$value = 0;
					
				}
				
				if ( $extr[$dbcolumn] == NULL ) {
					
					$extr[$dbcolumn] = 0;
					
				}
				
				if ( $value != $extr[$dbcolumn] ) {
					
					$allequal = FALSE;
					
				}
				
			}
			
			$value = $extr[$dbcolumn];
			
			$counter ++;
			
		}
		
	}
	
	if ( $allequal ) {
		
		?><p class="sigFeedbackGood sigDoubleResult">&#10003; <?php echo $title; ?></p><?php
		
		?>
		<p class="sigMaster">Master copy: <?php
		
		foreach ( $options as $dbcolumn => $plaintext ) {
			
			if ( $extr[$dbcolumn] == 1 ) {
				
				sig_set_master ( $extr['drugid'], $extr['referenceid'], $dbcolumn, $plaintext );
				
				?><span class="sigDoubleMultiAnswers"><?php echo $plaintext; ?></span><?php
				
			}
			
		}
		
		?></p><?php
		
	} else {
		
		?><div class="sigFeedbackBad sigDoubleResult"><p>&#9998; <?php echo $title; ?></p>
		<hr><?php
		
			$row = str_replace (" ", "", $title);
			
			$rows = implode (" ", array_keys ( $options ) );
			
			$plaintextimplode = implode ("; ", $options );
			
			foreach ( $extractions as $extr ) {
				
				?><p><span class="sigHidden" id="sigCheck<?php echo $extr['id']; ?>-<?php echo $row; ?>">&#10003;</span><span class="sigExtractionName"><?php echo sig_get_username_for_userid ( $extr['userid'] ); ?></span> <?php
				
				foreach ( $options as $dbcolumn => $plaintext ) {
					
					if ( $extr[$dbcolumn] == 1 ) {
						
						?><span class="sigDoubleMultiAnswers"><?php echo $plaintext; ?></span><?php
						
					}
					
				}
				
				?></p>
				<button onclick="sigMasterConfirmResponse('<?php echo $extr['id']; ?>-<?php echo $row; ?>');" id="sigConfirm<?php echo $extr['id']; ?>-<?php echo $row; ?>">Use this response</button>
				<button onclick="sigMasterUseMultiResponse('<?php echo $extr['id']; ?>-<?php echo $row; ?>', <?php echo $extr['drugid']; ?>, <?php echo $extr['referenceid']; ?>, '<?php echo $rows; ?>', '<?php echo $plaintextimplode; ?>', <?php echo $extr['id']; ?>, '<?php echo $title; ?>');" id="sigUse<?php echo $extr['id']; ?>-<?php echo $row; ?>" class="sigHidden">Use on master copy</button>
				<button onclick="sigMasterCancelResponse('<?php echo $extr['id']; ?>-<?php echo $row; ?>');" id="sigCancel<?php echo $extr['id']; ?>-<?php echo $row; ?>" class="sigHidden">Cancel</button><?php
				
			}
			
		?></div>
		
		<p class="sigMaster<?php
		
		$masternull = TRUE;
		
		foreach ( $options as $dbcolumn => $plaintext ) {
			
			if ( ! ( $master[$dbcolumn] === NULL ) ) {
				
				$masternull = FALSE;
				
			}
			
		}
		
		if ( $masternull == TRUE ) {
			
			echo " sigHidden";
			
		}
		
		$titlenospaces = str_replace (" ", "", $title);
		
		?>" id="sigMasterFeedback<?php echo $extr['drugid']; ?>-<?php echo $extr['referenceid']; ?>-<?php echo $titlenospaces; ?>">Master copy: <?php
		
		foreach ( $options as $dbcolumn => $plaintext ) {
			
			if ( $master[$dbcolumn] == 1 ) {
				
				?><span class="sigDoubleMultiAnswers"><?php echo $plaintext; ?></span><?php
				
			}
			
		}
		
		?></p><?php
		
	}
	
}

function nbt_get_distinct_citations_for_ref ( $drugid, $refid, $section ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT DISTINCT citationid FROM citations WHERE drugid = :drugid AND referenceid = :refid AND section = :section;");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':section', $sid);
		
		$did = $drugid;
		$rid = $refid;
		$sid = $section;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_particular_citation ( $drugid, $refid, $userid, $section, $citationid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM citations WHERE drugid = :drugid AND referenceid = :refid AND userid = :userid AND section = :section AND citationid = :citid;");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);
		$stmt->bindParam(':section', $sid);
		$stmt->bindParam(':citid', $cid);
		
		$did = $drugid;
		$rid = $refid;
		$uid = $userid;
		$sid = $section;
		$cid = $citationid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		if ( count ($result) > 0 ) {
			
			return $result;
			
			$dbh = null;
			
		} else {
			
			return FALSE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_year_author_for_drug_and_ref ( $drugid, $refid ) {
	
	$drugname = sig_get_drugname_for_drugid ( $drugid );
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM " . $drugname . " WHERE id = :refid LIMIT 1;");
		
		$stmt->bindParam(':refid', $rid);
		
		$rid = $refid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		if ( count ($result) > 0 ) {
			
			foreach ( $result as $row ) {
				
				return $row['year'] . " " . $row['authors'];
				
			}
			
		} else {
			
			return FALSE;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_check_double_citations ($extractions, $drugid, $refid, $section, $title) {
	
	?><div class="sigDoubleResult">
		<p>&#9998; <?php echo $title ?></p>
		<table class="sigTabledData">
			<tr>
				<?php
				
				foreach ( $extractions as $extr ) {
					
					?><td><span class="sigExtractionName"><?php echo sig_get_username_for_userid ( $extr['userid'] ); ?></span></td><?php
					
				}
				
				?>
			</tr>
			<?php
			
			$distinct_cites = sig_get_distinct_citations_for_ref ( $drugid, $refid, $section );
			
			foreach ( $distinct_cites as $distinct_cite ) {
				
				?><tr><?php
					
					$users = array ();
					
					foreach ( $extractions as $extr ) {
						
						array_push ( $users, $extr['userid'] );
						
					}
					
					$usersstring = implode (" ", $users);
					
					foreach ( $extractions as $extr ) {
						
						$pcite = sig_get_particular_citation ( $drugid, $refid, $extr['userid'], $section, $distinct_cite['citationid']);
						
						if ( $pcite ) {
							
							?><td><?php
								
								foreach ( $pcite as $cite ) {
									
									if ( $cite['cite_no'] != NULL ) {
										
										echo "<p><span class=\"sigHidden\" id=\"sigCitationCheck" . $cite['id'] . "\">&#10003;</span> #" . $cite['cite_no'] . "</p>";
										
									} else {
										
										echo "<p><span class=\"sigHidden\" id=\"sigCitationCheck" . $cite['id'] . "\">&#10003;</span> " . sig_get_year_author_for_drug_and_ref ( $drugid, $cite['citationid'] ) . "</p>";
										
									}
									
									switch ( $section ) {
										
										case 1: // What to do for intro cites
											
											if ( $cite['samedrug'] == 1 ) {
												
												echo "<p>Same drug</p>";
												
											} else {
												
												echo "<p>Different drug</p>";
												
											}
											
											if ( $cite['clinical'] != "" ) {
												
												echo "<p>" . $cite['clinical'] . "</p>";
												
											}
											
										break;
										
										case 2: // Comparator cites
											
											// No extra data
											
										break;
										
										case 3: // Discussion cites
											
											if ( $cite['samedrug'] == 1 ) {
												
												echo "<p>Same drug</p>";
												
											} else {
												
												echo "<p>Different drug</p>";
												
											}
											
											if ( $cite['clinical'] != "" ) {
												
												echo "<p>" . $cite['clinical'] . "</p>";
												
											}
											
											if ( $cite['citedasnegative'] == 1 ) {
												
												echo "<p>Cited as negative</p>";
												
											}
										
										break;
										
									}
									
									?><button onclick="sigCopyCitationToMaster (<?php echo $cite['id']; ?>, <?php echo $cite['drugid']; ?>, <?php echo $cite['referenceid']; ?>, <?php echo $cite['section']; ?>);">Copy to master</button>
									<!--<button onclick="sigDoubleCitationConfirmResponse('<?php echo $cite['id']; ?>');" id="sigConfirmCite<?php echo $cite['id']; ?>">Use this response</button>
									<button onclick="sigDoubleCitationConfirmRemove('<?php echo $cite['id']; ?>');" id="sigConfirmCiteRemove<?php echo $cite['id']; ?>">Remove this response</button>
									<button onclick="sigDoubleCitationUseResponse('<?php echo $cite['id']; ?>', <?php echo $cite['drugid']; ?>, <?php echo $cite['id']; ?>, <?php echo $cite['referenceid']; ?>, <?php echo $cite['section']; ?>, <?php echo $cite['citationid']; ?>, '<?php echo $usersstring; ?>');" id="sigUseCite<?php echo $cite['id']; ?>" class="sigHidden">Click to confirm use</button>
									<button onclick="sigDoubleCitationRemoveResponse('<?php echo $cite['id']; ?>', <?php echo $cite['id']; ?>);" id="sigRemoveCite<?php echo $cite['id']; ?>" class="sigHidden">Click to confirm removal</button>
									<button onclick="sigDoubleCitationCancelResponse('<?php echo $cite['id']; ?>');" id="sigCancelCite<?php echo $cite['id']; ?>" class="sigHidden">Cancel</button>--><?php
									
								}
								
							?></td><?php
							
						} else {
							
							?><td>
								<p>[Not cited]</p>
							</td><?php
							
						}
						
					}
				
				?></tr><?php
				
			}
			
			?>
		</table>
	</div><?php
	
	
}

function nbt_show_master_citations ( $master ) {
}

function sigMasterUseResponse ( $drugid, $reference, $row, $extrid ) {
	
	// Get the value for the extraction
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT " . $row . " FROM extractions WHERE id = :extrid LIMIT 1;");
		
		$stmt->bindParam(':extrid', $eid);
		
		$eid = $extrid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		if ( count ($result) > 0 ) {
			
			foreach ( $result as $val ) {
				
				$value = $val[$row];
				
			}
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	// Set the other extraction to that value
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE `master` SET " . $row . " = :value WHERE drugid = :drug AND referenceid = :ref;");
		
		$stmt->bindParam(':value', $val2);
		$stmt->bindParam(':ref', $rid);
		$stmt->bindParam(':drug', $did);
		
		$val2 = $value;
		$rid = $reference;
		$did = $drugid;
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	return $val2 . " ";
	
}

function sigUseDoubleCitation ( $id, $drugid, $reference, $section, $citation, $users ) {
	
	// First, get the values for the good citation
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM citations WHERE id = :dbid LIMIT 1;");
		
		$stmt->bindParam(':dbid', $dbid);
		
		$dbid = $id;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		if ( count ($result) > 0 ) {
			
			foreach ( $result as $row ) {
				
				$value = $row;
				
			}
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	// Then, make sure there's citations for that user
	
	$uids = explode ( " ", $users );
	
	foreach ( $uids as $uid ) {
		
		sig_add_citation ( $drugid, $reference, $uid, $section, $citation );
		
	}
	
	// Then, update all the other citations to have exactly the same values
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE citations SET cite_no = :citeno, samedrug = :samedrug, citedasnegative = :citedasnegative, clinical = :clinical WHERE referenceid = :refid AND section = :sect AND citationid = :citid;");
		
		$stmt->bindParam(':samedrug', $same);
		$stmt->bindParam(':citeno', $citn);
		$stmt->bindParam(':citedasnegative', $neg);
		$stmt->bindParam(':clinical', $clin);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':sect', $sect);
		$stmt->bindParam(':citid', $cit);
		
		$citn = $value['cite_no'];
		$same = $value['samedrug'];
		$neg = $value['citedasnegative'];
		$clin = $value['clinical'];
		$rid = $reference;
		$sect = $section;
		$cit = $citation;
		
		$stmt->execute();
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_assignments_for_user ( $userid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM assignments WHERE userid = :userid AND whenassigned < NOW() ORDER BY whenassigned DESC;");
		
		$stmt->bindParam(':userid', $uid);
		
		$uid = $userid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_toggle_assignment_hide ( $assignid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM assignments WHERE id = :assignid LIMIT 1;");
		
		$stmt->bindParam(':assignid', $aid);
		
		$aid = $assignid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ( $result as $row ) {
			
			$hidden = $row['hidden'];
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	if ( $hidden == 1 ) {
		
		$newvalue = 0;
		
	} else {
		
		$newvalue = 1;
		
	}
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE assignments SET hidden = :newvalue WHERE id = :assignid LIMIT 1;");
		
		$stmt->bindParam(':assignid', $aid);
		$stmt->bindParam(':newvalue', $nv);
		
		$aid = $assignid;
		$nv = $newvalue;
		
		$stmt->execute();
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_master ( $drugid, $refid ) {
	
	// Try to insert
	// If it's already there, it will fail
	// Hooray for MySQL indices
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `master` (drugid, referenceid) VALUES (:drugid, :refid);");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		
		$did = $drugid;
		$rid = $refid;
		
		$stmt->execute();
		
		$dbh = null;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	// Now, get the row
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `master` WHERE `drugid` = :drugid AND `referenceid` = :refid LIMIT 1;");
		
		$stmt->bindParam(':drugid', $did);
		$stmt->bindParam(':refid', $rid);
		
		$did = $drugid;
		$rid = $refid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		foreach ( $result as $row ) {
			
			return $row;
			
		}
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_copy_arms_row_to_master ( $originalid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM arms WHERE id = :id LIMIT 1;");
		
		$stmt->bindParam(':id', $oid);
		
		$oid = $originalid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	foreach ( $result as $row ) {
		try {
		
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("INSERT INTO `master_arms` (drugid, referenceid, arm, arm_n, dose, schedule, route, route_other, combination, combo_drugname) VALUES (:drugid, :refid, :arm, :armn, :dose, :schedule, :route, :routeo, :combination, :combodrugname);");
			
			$stmt->bindParam(':drugid', $did);
			$stmt->bindParam(':refid', $rid);
			$stmt->bindParam(':arm', $arm);
			$stmt->bindParam(':armn', $armn);
			$stmt->bindParam(':dose', $dos);
			$stmt->bindParam(':schedule', $sch);
			$stmt->bindParam(':route', $rou);
			$stmt->bindParam(':routeo', $roo);
			$stmt->bindParam(':combination', $comb);
			$stmt->bindParam(':combodrugname', $comdn);
			
			$did = $row['drugid'];
			$rid = $row['referenceid'];
			$arm = $row['arm'];
			$armn = $row['arm_n'];
			$dos = $row['dose'];
			$sch = $row['schedule'];
			$rou = $row['route'];
			$roo = $row['route_other'];
			$comb = $row['combination'];
			$comdn = $row['combo_drugname'];
			
			$stmt->execute();
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
	}
	
}

function nbt_get_master_arms_table_rows ( $drugid, $refid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM master_arms WHERE drugid = :drug AND referenceid = :ref ORDER BY id ASC;");
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		
		$did = $drugid;
		$ref = $refid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
}

function nbt_remove_master_arms_table_row ( $rowid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM master_arms WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		
		$rid = $rowid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_copy_outcomes_row_to_master ( $originalid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM outcomes WHERE id = :id LIMIT 1;");
		
		$stmt->bindParam(':id', $oid);
		
		$oid = $originalid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	foreach ( $result as $row ) {
		
		try {
		
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("INSERT INTO `master_outcomes` (drugid, referenceid, outcome, priority) VALUES (:drugid, :refid, :outcome, :priority);");
			
			$stmt->bindParam(':drugid', $did);
			$stmt->bindParam(':refid', $rid);
			$stmt->bindParam(':outcome', $out);
			$stmt->bindParam(':priority', $pri);
			
			$did = $row['drugid'];
			$rid = $row['referenceid'];
			$out = $row['outcome'];
			$pri = $row['priority'];
			
			$stmt->execute();
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
	}
	
}

function nbt_get_master_outcomes_table_rows ( $drugid, $refid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM master_outcomes WHERE drugid = :drug AND referenceid = :ref ORDER BY id ASC;");
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		
		$did = $drugid;
		$ref = $refid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
}

function nbt_remove_master_outcomes_table_row ( $rowid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM master_outcomes WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		
		$rid = $rowid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_get_master_efficacy_table_rows ( $drugid, $refid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM master_efficacy WHERE drugid = :drug AND referenceid = :ref ORDER BY id ASC;");
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		
		$did = $drugid;
		$ref = $refid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
}

function nbt_copy_efficacy_row_to_master ( $originalid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM efficacy WHERE id = :id LIMIT 1;");
		
		$stmt->bindParam(':id', $oid);
		
		$oid = $originalid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	foreach ( $result as $row ) {
		
		try {
		
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("INSERT INTO `master_efficacy` (drugid, referenceid, outcome, arm, treated_mean, treated_sd, aggr_value, aggr_variance, aggr_units, direction, significance) VALUES (:drugid, :refid, :outcome, :arm, :treatedmean, :treatedsd, :aggrvalue, :aggrvariance, :aggrunits, :direction, :significance);");
			
			$stmt->bindParam(':drugid', $did);
			$stmt->bindParam(':refid', $rid);
			$stmt->bindParam(':outcome', $out);
			$stmt->bindParam(':arm', $arm);
			$stmt->bindParam(':treatedmean', $tmean);
			$stmt->bindParam(':treatedsd', $tsd);
			$stmt->bindParam(':aggrvalue', $agval);
			$stmt->bindParam(':aggrvariance', $agvar);
			$stmt->bindParam(':aggrunits', $agun);
			$stmt->bindParam(':direction', $dir);
			$stmt->bindParam(':significance', $sig);
			
			$did = $row['drugid'];
			$rid = $row['referenceid'];
			$out = $row['outcome'];
			$arm = $row['arm'];
			$tmean = $row['treated_mean'];
			$tsd = $row['treated_sd'];
			$agval = $row['aggr_value'];
			$agvar = $row['aggr_variance'];
			$agun = $row['aggr_units'];
			$dir = $row['direction'];
			$sig = $row['significance'];
			
			$stmt->execute();
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
	}
	
}

function nbt_remove_master_efficacy_table_row ( $rowid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM master_efficacy WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		
		$rid = $rowid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

function nbt_copy_safety_row_to_master ( $originalid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM safety WHERE id = :id LIMIT 1;");
		
		$stmt->bindParam(':id', $oid);
		
		$oid = $originalid;
		
		$stmt->execute();
		
		$result = $stmt->fetchAll();
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
	foreach ( $result as $row ) {
		
		try {
		
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("INSERT INTO `master_safety` (drugid, referenceid, arm, sae_tr, g5, withdraw, sae_nc) VALUES (:drugid, :refid, :arm, :saetr, :g5, :withdraw, :saenc);");
			
			$stmt->bindParam(':drugid', $did);
			$stmt->bindParam(':refid', $rid);
			$stmt->bindParam(':arm', $arm);
			$stmt->bindParam(':saetr', $saetr);
			$stmt->bindParam(':g5', $g5);
			$stmt->bindParam(':withdraw', $with);
			$stmt->bindParam(':saenc', $saenc);
			
			$did = $row['drugid'];
			$rid = $row['referenceid'];
			$arm = $row['arm'];
			$saetr = $row['sae_tr'];
			$g5 = $row['g5'];
			$with = $row['withdraw'];
			$saenc = $row['sae_nc'];
			
			$stmt->execute();
			
		}
		
		catch (PDOException $e) {
			
			echo $e->getMessage();
			
		}
	}
	
}

function nbt_get_master_safety_table_rows ( $drugid, $refid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM master_safety WHERE drugid = :drug AND referenceid = :ref ORDER BY id ASC;");
		
		$stmt->bindParam(':drug', $did);
		$stmt->bindParam(':ref', $ref);
		
		$did = $drugid;
		$ref = $refid;
		
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		
		$dbh = null;
		
		return $result;
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
}

function nbt_remove_master_safety_table_row ( $rowid ) {
	
	try {
		
		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM master_safety WHERE id = :rowid;");
		
		$stmt->bindParam(':rowid', $rid);
		
		$rid = $rowid;
		
		if ($stmt->execute()) {
			
			$dbh = null;
			
			return TRUE;
			
		} else {
			
			$dbh = null;
			
			return FALSE;
			
		}
		
		
		
	}
	
	catch (PDOException $e) {
		
		echo $e->getMessage();
		
	}
	
}

?>