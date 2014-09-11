<?php

session_start ();

function nbt_user_is_logged_in () {

	if ( isset ($_SESSION['nbt_valid_login']) && $_SESSION['nbt_valid_login'] == 1 ) {

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

	$_SESSION['nbt_valid_login'] = 1;
	$_SESSION['nbt_userid'] = nbt_get_userid_for_username ($username);
	$_SESSION['nbt_username'] = nbt_get_username_for_userid ( $_SESSION['nbt_userid'] );

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

		$message = $message . "\n\n" . "You are receiving this message because your email was registered for an account on an installation of Numbat with the following user name:";

		$message = $message . " " . $username;

		$message = $message . "\n\n" . "If you are receiving this email in error, just ignore it.";

		$message = $message . "\n\n" . "To verify your email and activate your account, click the following link.";

		$message = $message . "\n\n" . SITE_URL . "signup/?username=" . $username . "&code=" . $verification;

		$message = $message . "\n\n" . "Enjoy! :)";

		mail ($email, "Confirm your email address for Numbat", $message, "From: Numbat <" . nbt_get_setting ( "admin_email" ) . ">");

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

	$message = $message . "\n\n" . "Enjoy! :)";

	mail ($email, "Numbat password reset", $message, "From: Numbat <" . nbt_get_setting ( "admin_email" ) . ">");

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

	$userid = nbt_get_userid_for_username ( $username );

	if ( $userid ) { // if the user exists

		$emailverify = nbt_get_emailverify_for_userid ( $userid );

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
	setcookie ("nbt_userid", "", time(), "/");
	setcookie ("nbt_password", "", time(), "/");

}

function nbt_get_drugs_that_the_current_user_has_access_to () {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM permissions WHERE userid = :userid AND permission > 0;");

		$stmt->bindParam(':userid', $user);

		$user = $_SESSION['nbt_userid'];

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_name_for_refsetid ( $drugid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT name FROM referencesets WHERE id = :drugid LIMIT 1;");

		$stmt->bindParam(':drugid', $did);

		$did = $drugid;

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		foreach ( $result as $row ) {

			return $row['name'];

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_refsetid_for_name ( $name ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT id FROM referencesets WHERE name = :name LIMIT 1;");

		$stmt->bindParam(':name', $did);

		$did = $name;

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

function nbt_get_refset_for_id ( $refsetid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM referencesets WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $rsid);

		$rsid = $refsetid;

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

function nbt_echo_reference ( $ref, $drugname ) {

	?><div class="sigGreyGradient">
		<h3><?php echo $ref['authors']; ?>. <?php echo $ref['title']; ?>. <span class="sigJournalName"><?php echo $ref['journal']; ?></span>: <?php echo $ref['year']; ?></h3>
		<p><a href="<?php echo SITE_URL . "drug/" . $drugname . "/" . $ref['id'] . "/"; ?>">Extract</a></p>
	</div><?php

}

function nbt_get_all_references_for_refset ( $refsetid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `referenceset_" . $refsetid . "` ORDER BY id ASC;");

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_all_extracted_references_for_refset_and_form ( $refsetid, $formid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `referenceset_" . $refsetid . "` WHERE `id` IN (SELECT `referenceid` FROM `extractions_" . $formid . "` WHERE `refsetid` = :refset) ORDER BY id ASC;");

		$stmt->bindParam(':refset', $rsid);

		$rsid = $refsetid;

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_extractions_for_refset_ref_and_form ( $refsetid, $refid, $formid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT *, (SELECT `username` FROM `users` WHERE `users`.`id` = `extractions_" . $formid . "`.`userid`) as `username` FROM `extractions_" . $formid . "` WHERE `refsetid` = :refset AND `referenceid` = :ref AND `status` = 2 ORDER BY id ASC;");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $rid);

		$rsid = $refsetid;
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

function nbt_get_all_unstarted_references_for_drug_id ( $drugid, $start = 0, $range = 25 ) {

	$drugname = nbt_get_name_for_refsetid ($drugid);

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

	$drugname = nbt_get_name_for_refsetid ($drugid);

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

	$drugname = nbt_get_name_for_refsetid ($drugid);

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

	$drugname = nbt_get_name_for_refsetid ($drugid);

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

function nbt_get_reference_for_refsetid_and_refid ( $refsetid, $refid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `referenceset_" . $refsetid . "` WHERE id = :refid LIMIT 1;");

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

function nbt_return_references_for_refset_and_query ( $citationsid, $refsetid, $refid, $query ) {

	$altquery = str_replace ("- ", "%", $query);

	$element = nbt_get_form_element_for_elementid ( $citationsid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `referenceset_" . $refsetid . "` WHERE `title` LIKE :query OR `title` LIKE :altquery OR `authors` LIKE :query LIMIT 6;");

		$stmt->bindParam(':query', $quer);
		$stmt->bindParam(':altquery', $altquer);

		$quer = "%" . $query . "%";
		$altquer = "%" . $altquery . "%";

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_return_references_for_assignment_search ( $refsetid, $query ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `referenceset_" . $refsetid . "` WHERE title LIKE :query OR authors LIKE :query LIMIT 6;");

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

function nbt_add_citation ( $citationsection, $refsetid, $reference, $userid, $citation ) {

	$element = nbt_get_form_element_for_elementid ( $citationsection );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `citations_" . $element['columnname'] . "` (refsetid, referenceid, userid, citationid) VALUES (:refset, :ref, :user, :cit)");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);
		$stmt->bindParam(':cit', $cit);

		$rsid = $refsetid;
		$ref = $reference;
		$user = $userid;
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

function nbt_get_citations ( $citations, $refsetid, $reference, $userid, $orderbycitation = FALSE ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

		if ( $orderbycitation ) {

			$stmt = $dbh->prepare("SELECT * FROM `citations_" . $citations . "` WHERE refsetid = :refset AND referenceid = :ref AND userid = :user ORDER by citationid;");

		} else {

			$stmt = $dbh->prepare("SELECT * FROM `citations_" . $citations . "` WHERE refsetid = :refset AND referenceid = :ref AND userid = :user ORDER by id DESC;");

		}

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);

		$rsid = $refsetid;
		$ref = $reference;
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

function nbt_copy_citation_to_master ( $elementid, $originalid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$columns = nbt_get_all_columns_for_citation_selector ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `citations_" . $element['columnname'] . "` WHERE id = :id LIMIT 1;");

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
		$stmt = $dbh->prepare("INSERT INTO `mcite_" . $element['columnname'] . "` (refsetid, referenceid, cite_no, citationid) VALUES (:refset, :refid, :cite, :citid);");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':cite', $cit);
		$stmt->bindParam(':citid', $cid);

		$rsid = $cite['refsetid'];
		$rid = $cite['referenceid'];
		$cit = $cite['cite_no'];
		$cid = $cite['citationid'];

		if ( $stmt->execute() ) {

			$stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");
			$stmt2->execute();

			$result = $stmt2->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$newid = $row['newid']; // This is the auto_increment-generated ID for the new compare

			}

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	foreach ( $columns as $column ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `mcite_" . $element['columnname'] . "` SET `" . $column['dbname'] . "` = :newvalue WHERE `id` = :id;");

			$stmt->bindParam(':newvalue', $new);
			$stmt->bindParam(':id', $cid);

			$new = $cite[$column['dbname']];
			$cid = $newid;

			$stmt->execute();

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

}

function nbt_get_master_citations ( $elementid, $refsetid, $reference, $orderbycitation = FALSE ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

		if ( $orderbycitation ) {

			$stmt = $dbh->prepare("SELECT * FROM `mcite_" . $element['columnname'] . "` WHERE `refsetid` = :refset AND `referenceid` = :ref ORDER by `citationid`;");

		} else {

			$stmt = $dbh->prepare("SELECT * FROM `mcite_" . $element['columnname'] . "` WHERE `refsetid` = :refset AND `referenceid` = :ref ORDER by `id`;");

		}



		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $ref);

		$rsid = $refsetid;
		$ref = $reference;

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_remove_master_citation ( $section, $id ) {

	$element = nbt_get_form_element_for_elementid ( $section );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `mcite_" . $element['columnname'] . "` WHERE id = :id LIMIT 1;");

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

function nbt_remove_citation ( $section, $citation ) {

	$element = nbt_get_form_element_for_elementid ( $section );

	echo $citation . " : ";

	echo "DELETE FROM `citations_" . $element['columnname'] . "` WHERE id = :id LIMIT 1;";

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `citations_" . $element['columnname'] . "` WHERE `id` = :id LIMIT 1;");

		$stmt->bindParam(':id', $cid);

		$cid = $citation;

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

function nbt_update_extraction ( $fid, $id, $column, $value ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `extractions_" . $fid . "` SET `" . $column . "` = :value WHERE id = :id LIMIT 1;");

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

function nbt_update_sub_extraction ( $eid, $id, $column, $value ) {

	$element = nbt_get_form_element_for_elementid ( $eid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `sub_" . $element['columnname'] . "` SET `" . $column . "` = :value WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $subexid);
		$stmt->bindParam(':value', $val);

		$subexid = $id;
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

function nbt_update_msub_extraction ( $eid, $id, $column, $value ) {

	$element = nbt_get_form_element_for_elementid ( $eid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `msub_" . $element['columnname'] . "` SET `" . $column . "` = :value WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $subexid);
		$stmt->bindParam(':value', $val);

		$subexid = $id;
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

function nbt_toggle_extraction ( $formid, $id, $column ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM `extractions_" . $formid . "` WHERE id = :id LIMIT 1;");

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
		$stmt = $dbh->prepare ("UPDATE `extractions_" . $formid . "` SET `" . $column . "` = :value WHERE id = :id LIMIT 1;");

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

function nbt_toggle_sub_extraction ( $elementid, $id, $column ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM `sub_" . $element['columnname'] . "` WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $seid);

		$seid = $id;

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
		$stmt = $dbh->prepare ("UPDATE `sub_" . $element['columnname'] . "` SET `" . $column . "` = :value WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $seid);
		$stmt->bindParam(':value', $val);

		$seid = $id;
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

function nbt_toggle_msub_extraction ( $elementid, $id, $column ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM `msub_" . $element['columnname'] . "` WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $seid);

		$seid = $id;

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
		$stmt = $dbh->prepare ("UPDATE `msub_" . $element['columnname'] . "` SET `" . $column . "` = :value WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $seid);
		$stmt->bindParam(':value', $val);

		$seid = $id;
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

function nbt_get_extraction ( $formid, $refsetid, $refid, $userid ) {

	// Insert a row
	// This will fail if there is already a row
	// Hooray for unique MySQL indices

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO extractions_" . $formid . " (refsetid, referenceid, userid) VALUES (:refset, :refid, :userid);");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);

		$rsid = $refsetid;
		$rid = $refid;
		$uid = $userid;

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// See if an extraction exists

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM `extractions_" . $formid . "` WHERE `refsetid` = :refset AND `referenceid` = :refid AND `userid` = :userid LIMIT 1;");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);

		$rsid = $refsetid;
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

function nbt_echo_multi_select ($formid, $extraction, $question, $options, $toggles = NULL ) {

	// $options must be an array of the names of the column in the db

	foreach ( $options as $dbcolumn => $plaintext ) {

		?><a href="#" class="nbtTextOptionSelect <?php

			echo "sig" . $question;

			if ( $extraction[$question . "_" . $dbcolumn] == 1 ) {

				?> nbtTextOptionChosen<?php

			}

		?>" id="nbtMS<?php echo $dbcolumn; ?>" onclick="event.preventDefault();nbtSaveMultiSelect(<?php echo $formid; ?>, <?php echo $extraction['id']; ?>, '<?php echo $question . "_" . $dbcolumn; ?>', 'nbtMS<?php echo $dbcolumn; ?>');"  conditionalid="<?php echo $toggles[$dbcolumn]; ?>"><?php echo $plaintext; ?></a><?php

	}

}

function nbt_echo_subextraction_multi_select ($elementid, $subextraction, $question, $options, $toggles = NULL ) {

	// $options must be an array of the names of the column in the db

	foreach ( $options as $dbcolumn => $plaintext ) {

		?><a href="#" class="nbtTextOptionSelect <?php

			echo "nbt" . $question;

			echo " nbtSub" . $subextraction['id'] . "-" . $question;

			if ( $subextraction[$question . "_" . $dbcolumn] == 1 ) {

				?> nbtTextOptionChosen<?php

			}

		?>" id="nbtSub<?php echo $elementid ?>-<?php echo $subextraction['id']; ?>MS<?php echo $dbcolumn; ?>" onclick="event.preventDefault();nbtSaveSubExtractionMultiSelect(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $question . "_" . $dbcolumn; ?>', 'nbtSub<?php echo $elementid ?>-<?php echo $subextraction['id']; ?>MS<?php echo $dbcolumn; ?>');"  conditionalid="<?php echo $toggles[$dbcolumn]; ?>"><?php echo $plaintext; ?></a><?php

	}

}

function nbt_echo_msubextraction_multi_select ($elementid, $subextraction, $question, $options, $toggles = NULL ) {

	// $options must be an array of the names of the column in the db

	foreach ( $options as $dbcolumn => $plaintext ) {

		?><a href="#" class="nbtTextOptionSelect <?php

			echo "nbt" . $question;

			echo " nbtSub" . $subextraction['id'] . "-" . $question;

			if ( $subextraction[$question . "_" . $dbcolumn] == 1 ) {

				?> nbtTextOptionChosen<?php

			}

		?>" id="nbtSub<?php echo $elementid ?>-<?php echo $subextraction['id']; ?>MS<?php echo $dbcolumn; ?>" onclick="event.preventDefault();nbtSaveMasterSubExtractionMultiSelect(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $question . "_" . $dbcolumn; ?>', 'nbtSub<?php echo $elementid ?>-<?php echo $subextraction['id']; ?>MS<?php echo $dbcolumn; ?>');"  conditionalid="<?php echo $toggles[$dbcolumn]; ?>"><?php echo $plaintext; ?></a><?php

	}

}

function nbt_echo_single_select ($formid, $extraction, $question, $answers, $toggles = NULL) {

	// $question must be the name of the column in the db
	// $answers must be an array of the answer entered in the db and the plain text version displayed

	foreach ( $answers as $dbanswer => $ptanswer ) {

		?><a href="#" class="nbtTextOptionSelect<?php

		echo " nbt" . $question;

		if ( ! is_null ( $extraction[$question] ) ) { // This is because PHP will say that 0 and NULL are the same

			if ( $extraction[$question] . " " == $dbanswer . " " ) { // This is because PHP has a hard time testing for equality between strings and integers

				?> nbtTextOptionChosen<?php

			}

		}

		$buttonid = "nbtQ" . $question . "A" . str_replace ( "/", "_", str_replace (" ", "_", $dbanswer) );

		?>" id="<?php echo $buttonid; ?>" onclick="event.preventDefault();nbtSaveSingleSelect(<?php echo $formid; ?>, <?php echo $extraction['id']; ?>, '<?php echo $question; ?>', '<?php echo $dbanswer; ?>', '<?php echo $buttonid; ?>', 'nbt<?php echo $question; ?>');" conditionalid="<?php echo $toggles[$dbanswer]; ?>"><?php echo $ptanswer; ?></a><?php

	}

}

function nbt_echo_subextraction_single_select ($elementid, $subextraction, $question, $answers, $toggles = NULL) {

	// $question must be the name of the column in the db
	// $answers must be an array of the answer entered in the db and the plain text version displayed

	foreach ( $answers as $dbanswer => $ptanswer ) {

		?><a href="#" class="nbtTextOptionSelect<?php

		echo " nbtSub" . $subextraction['id'] . "-" . $question;

		if ( ! is_null ( $subextraction[$question] ) ) { // This is because PHP will say that 0 and NULL are the same

			if ( $subextraction[$question] . " " == $dbanswer . " " ) { // This is because PHP has a hard time testing for equality between strings and integers

				?> nbtTextOptionChosen<?php

			}

		}

		$buttonid = "nbtSub" . $elementid . "-" . $subextraction['id'] . "Q" . $question . "A" . str_replace ( "/", "_", str_replace (" ", "_", $dbanswer) );

		?>" id="<?php echo $buttonid; ?>" onclick="event.preventDefault();nbtSaveSubExtractionSingleSelect(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $question; ?>', '<?php echo $dbanswer; ?>', '<?php echo $buttonid; ?>', 'nbtSub<?php echo $subextraction['id'] . "-" . $question; ?>');" conditionalid="<?php echo $toggles[$dbanswer]; ?>"><?php echo $ptanswer; ?></a><?php

	}

}

function nbt_echo_msubextraction_single_select ($elementid, $subextraction, $question, $answers, $toggles = NULL) {

	// $question must be the name of the column in the db
	// $answers must be an array of the answer entered in the db and the plain text version displayed

	foreach ( $answers as $dbanswer => $ptanswer ) {

		?><a href="#" class="nbtTextOptionSelect<?php

		echo " nbtSub" . $subextraction['id'] . "-" . $question;

		if ( ! is_null ( $subextraction[$question] ) ) { // This is because PHP will say that 0 and NULL are the same

			if ( $subextraction[$question] . " " == $dbanswer . " " ) { // This is because PHP has a hard time testing for equality between strings and integers

				?> nbtTextOptionChosen<?php

			}

		}

		$buttonid = "nbtSub" . $elementid . "-" . $subextraction['id'] . "Q" . $question . "A" . str_replace ( "/", "_", str_replace (" ", "_", $dbanswer) );

		?>" id="<?php echo $buttonid; ?>" onclick="event.preventDefault();nbtSaveMasterSubExtractionSingleSelect(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $question; ?>', '<?php echo $dbanswer; ?>', '<?php echo $buttonid; ?>', 'nbtSub<?php echo $subextraction['id'] . "-" . $question; ?>');" conditionalid="<?php echo $toggles[$dbanswer]; ?>"><?php echo $ptanswer; ?></a><?php

	}

}

function nbt_echo_text_field ($formid, $extraction, $dbcolumn, $maxlength, $allcaps = FALSE) {

	?><input type="text" value="<?php

	echo $extraction[$dbcolumn];

	?>" id="nbtTextField<?php echo $dbcolumn; ?>" onblur="nbtSaveTextField(<?php echo $formid; ?>, <?php echo $extraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtTextField<?php echo $dbcolumn; ?>', 'nbtTextField<?php echo $dbcolumn; ?>Feedback');" maxlength="<?php echo $maxlength; ?>"<?php

	if ( $allcaps ) {

		echo " style=\"text-transform: uppercase\"";

	}

	?>><span class="nbtInputFeedback" id="nbtTextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span><?php

}

function nbt_echo_text_area_field ($formid, $extraction, $dbcolumn, $maxlength, $allcaps = FALSE) {

	?><textarea style="width: 100%; height: 150px;" id="nbtTextAreaField<?php echo $dbcolumn; ?>" onkeyup="nbtCheckTextAreaCharacters('nbtTextAreaField<?php echo $dbcolumn; ?>', 5000);" onblur="nbtSaveTextField(<?php echo $formid; ?>, <?php echo $extraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtTextAreaField<?php echo $dbcolumn; ?>', 'nbtTextAreaField<?php echo $dbcolumn; ?>Feedback');" maxlength="<?php echo $maxlength; ?>"><?php

	echo $extraction[$dbcolumn];

	?></textarea>
	<p class="nbtInputFeedback" id="nbtTextAreaField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span><?php

}

function nbt_echo_subextraction_text_field ($elementid, $subextraction, $dbcolumn, $maxlength, $allcaps = FALSE) {

	?><input type="text" value="<?php

	echo $subextraction[$dbcolumn];

	?>" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>" onblur="nbtSaveSubExtractionTextField(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback');" maxlength="<?php echo $maxlength; ?>"<?php

	if ( $allcaps ) {

		echo " style=\"text-transform: uppercase\"";

	}

	?>><span class="nbtInputFeedback" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span><?php

}

function nbt_echo_msubextraction_text_field ($elementid, $subextraction, $dbcolumn, $maxlength, $allcaps = FALSE) {

	?><input type="text" value="<?php

	echo $subextraction[$dbcolumn];

	?>" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>" onblur="nbtSaveMasterSubExtractionTextField(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback');" maxlength="<?php echo $maxlength; ?>"<?php

	if ( $allcaps ) {

		echo " style=\"text-transform: uppercase\"";

	}

	?>><span class="nbtInputFeedback" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span><?php

}

function nbt_echo_date_selector ($formid, $extraction, $dbcolumn) {

	?><p class="nbtDateSelector">
		<input type="text" value="<?php

			if ( substr ($extraction[$dbcolumn], 0, 7) != "0000-00" ) {

				echo substr ($extraction[$dbcolumn], 0, 7);

			}

		?>" id="nbtDateField<?php echo $dbcolumn; ?>" onblur="nbtSaveDateField(<?php echo $formid; ?>, <?php echo $extraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtDateField<?php echo $dbcolumn; ?>', 'nbtTextField<?php echo $dbcolumn; ?>Feedback');">
		<span class="nbtInputFeedback" id="nbtTextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span>
	</p><?php

}

function nbt_echo_sub_date_selector ($elementid, $subextraction, $dbcolumn) {

	?><input type="text" value="<?php

			if ( substr ($subextraction[$dbcolumn], 0, 7) != "0000-00" ) {

				echo substr ($subextraction[$dbcolumn], 0, 7);

			}

		?>" id="nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $dbcolumn; ?>" onblur="nbtSaveSubExtractionDateField(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback');">
		<span class="nbtInputFeedback" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span><?php

}

function nbt_echo_msub_date_selector ($elementid, $subextraction, $dbcolumn) {

	?><input type="text" value="<?php

			if ( substr ($subextraction[$dbcolumn], 0, 7) != "0000-00" ) {

				echo substr ($subextraction[$dbcolumn], 0, 7);

			}

		?>" id="nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $dbcolumn; ?>" onblur="nbtSaveMasterSubExtractionDateField(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback');">
		<span class="nbtInputFeedback" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span><?php

}

function nbt_get_table_data_rows ( $elementid, $refsetid, $refid, $userid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `tabledata_" . $element['columnname'] . "` WHERE refsetid = :refset AND referenceid = :ref AND userid = :user ORDER BY id ASC;");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);

		$rsid = $refsetid;
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

function nbt_add_new_extraction_table_data_row ($tableid, $refsetid, $refid, $userid) {

	$element = nbt_get_form_element_for_elementid ( $tableid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `tabledata_" . $element['columnname'] . "` (refsetid, referenceid, userid) VALUES (:refset, :refid, :userid);");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);

		$rsid = $refsetid;
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

function nbt_remove_master_table_row ( $elementid, $rowid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM `mtable_" . $element['columnname'] . "` WHERE id = :rowid;");

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

function nbt_get_manual_refs_for_refset ( $refsetid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `referenceset_" . $refsetid . "` WHERE `manual` = 1 ORDER BY id ASC;");

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

function nbt_echo_manual_ref ( $ref, $refsetid ) {

	?><div class="nbtManualRef" id="nbtManRef<?php echo $refsetid; ?>-<?php echo $ref['id']; ?>">
		<p class="nbtInlineTextField">
			<span class="nbtInputLabel">Title</span>
			<input type="text" value="<?php

				echo $ref['title'];

			?>" id="nbtManRefTextField<?php echo $ref['id']; ?>Title" onblur="nbtUpdateManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>, 'title', 'nbtManRefTextField<?php echo $ref['id']; ?>Title', 'nbtManRefTextField<?php echo $ref['id']; ?>TitleFeedback');">
			<span class="nbtInputFeedback" id="nbtManRefTextField<?php echo $ref['id']; ?>TitleFeedback">&nbsp;</span>
		</p>
		<p class="nbtInlineTextField">
			<span class="nbtInputLabel">Authors</span>
			<input type="text" value="<?php

				echo $ref['authors'];

			?>" id="nbtManRefTextField<?php echo $ref['id']; ?>Authors" onblur="nbtUpdateManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>, 'authors', 'nbtManRefTextField<?php echo $ref['id']; ?>Authors', 'nbtManRefTextField<?php echo $ref['id']; ?>AuthorsFeedback');">
			<span class="nbtInputFeedback" id="nbtManRefTextField<?php echo $ref['id']; ?>AuthorsFeedback">&nbsp;</span>
		</p>
		<p class="nbtInlineTextField">
			<span class="nbtInputLabel">Year</span>
			<input type="text" value="<?php

				echo $ref['year'];

			?>" id="nbtManRefTextField<?php echo $ref['id']; ?>Year" onblur="nbtUpdateManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>, 'year', 'nbtManRefTextField<?php echo $ref['id']; ?>Year', 'nbtManRefTextField<?php echo $ref['id']; ?>YearFeedback');">
			<span class="nbtInputFeedback" id="nbtManRefTextField<?php echo $ref['id']; ?>YearFeedback">&nbsp;</span>
		</p>
		<p class="nbtInlineTextField">
			<span class="nbtInputLabel">Journal</span>
			<input type="text" value="<?php

				echo $ref['journal'];

			?>" id="nbtManRefTextField<?php echo $ref['id']; ?>Journal" onblur="nbtUpdateManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>, 'journal', 'nbtManRefTextField<?php echo $ref['id']; ?>Journal', 'nbtManRefTextField<?php echo $ref['id']; ?>JournalFeedback');">
			<span class="nbtInputFeedback" id="nbtManRefTextField<?php echo $ref['id']; ?>JournalFeedback">&nbsp;</span>
		</p>
		<p class="nbtInputLabel">Abstract</p>
		<textarea id="nbtManRefTextField<?php echo $ref['id']; ?>Abstract" onblur="nbtUpdateManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>, 'abstract', 'nbtManRefTextField<?php echo $ref['id']; ?>Abstract', 'nbtManRefTextField<?php echo $ref['id']; ?>AbstractFeedback');" style="width: 90%;"><?php

			echo $ref['abstract'];

		?></textarea>
		<span class="nbtInputFeedback" id="nbtManRefTextField<?php echo $ref['id']; ?>AbstractFeedback">&nbsp;</span>
		<button onclick="$(this).fadeOut(0);$('#nbtRemoveReference<?php echo $ref['id']; ?>').fadeIn()">Remove this reference</button>
		<button id="nbtRemoveReference<?php echo $ref['id']; ?>" onclick="nbtRemoveManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>);" class="nbtHidden">For real</button>
	</div><?php
}

function nbt_add_manual_ref ( $refset ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ('INSERT INTO `referenceset_' . $refset . '` (manual) VALUES (1);');

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

function nbt_update_manual_reference ( $refsetid, $column, $refid, $newvalue ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE `referenceset_" . $refsetid . "` SET `" . $column . "` = :value WHERE `id` = :refid;");

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

function nbt_remove_manual_reference ( $refsetid, $refid) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `referenceset_" . $refsetid . "` WHERE id = :rid LIMIT 1;");

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

function nbt_auto_insert_efficacy_table_rows ($drugid, $refid, $userid) {

	// First, find out what arms and outcomes have been added

	$arms_rows = nbt_get_arms_table_rows ( $drugid, $refid, $userid );

	$outcomes_rows = nbt_get_outcomes_table_rows ( $drugid, $refid, $userid );

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

	$arms_rows = nbt_get_arms_table_rows ( $drugid, $refid, $userid );

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

	$refsetname = nbt_get_name_for_refsetid ($drugid);

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
//		$stmt = $dbh->prepare("SELECT * FROM " . $refsetname . " ORDER BY id ASC LIMIT :start, :range;");
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

	$refsetname = nbt_get_name_for_refsetid ($drugid);

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
//		$stmt = $dbh->prepare("SELECT * FROM " . $refsetname . " ORDER BY id ASC LIMIT :start, :range;");
		$stmt = $dbh->prepare("UPDATE " . $refsetname . " SET indication = :newval WHERE id = :refid;");

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

function nbt_update_citeno ( $section, $citid, $newvalue ) {

	if ( $newvalue == "" ) {

		$newvalue = NULL;

	}

	$element = nbt_get_form_element_for_elementid ( $section );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `citations_" . $element['columnname'] ."` SET cite_no = :newval WHERE id = :citid;");

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

	$refsetname = nbt_get_name_for_refsetid ($drugid);

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM " . $refsetname . " WHERE id = :refid LIMIT 1;");

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

			$stmt = $dbh->prepare("UPDATE " . $refsetname . " SET include = 0 WHERE id = :refid LIMIT 1;");

		} else {

			$stmt = $dbh->prepare("UPDATE " . $refsetname . " SET include = 1 WHERE id = :refid LIMIT 1;");

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

	$refsetname = nbt_get_name_for_refsetid ( $drugid );

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

function nbt_get_distinct_citations_for_element_refset_and_ref ( $elementid, $refsetid, $refid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT DISTINCT citationid FROM `citations_" . $element['columnname'] . "` WHERE refsetid = :refset AND referenceid = :refid;");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':refid', $rid);

		$rsid = $refsetid;
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

function nbt_get_particular_citation ( $elementid, $refsetid, $refid, $userid, $citationid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT *, (SELECT `title` FROM `referenceset_" . $refsetid . "` WHERE `id` = :citid) as `title`, (SELECT `authors` FROM `referenceset_" . $refsetid . "` WHERE `id` = :citid) as `authors`, (SELECT `journal` FROM `referenceset_" . $refsetid . "` WHERE `id` = :citid) as `journal`, (SELECT `year` FROM `referenceset_" . $refsetid . "` WHERE `id` = :citid) as `year` FROM `citations_" . $element['columnname'] . "` WHERE `refsetid` = :refset AND `referenceid` = :refid AND `userid` = :userid AND `citationid` = :citid;");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);
		$stmt->bindParam(':citid', $cid);

		$rsid = $refsetid;
		$rid = $refid;
		$uid = $userid;
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

	$refsetname = nbt_get_name_for_refsetid ( $drugid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT * FROM " . $refsetname . " WHERE id = :refid LIMIT 1;");

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

					?><td><span class="sigExtractionName"><?php echo nbt_get_username_for_userid ( $extr['userid'] ); ?></span></td><?php

				}

				?>
			</tr>
			<?php

			$distinct_cites = nbt_get_distinct_citations_for_ref ( $drugid, $refid, $section );

			foreach ( $distinct_cites as $distinct_cite ) {

				?><tr><?php

					$users = array ();

					foreach ( $extractions as $extr ) {

						array_push ( $users, $extr['userid'] );

					}

					$usersstring = implode (" ", $users);

					foreach ( $extractions as $extr ) {

						$pcite = nbt_get_particular_citation ( $drugid, $refid, $extr['userid'], $section, $distinct_cite['citationid']);

						if ( $pcite ) {

							?><td><?php

								foreach ( $pcite as $cite ) {

									if ( $cite['cite_no'] != NULL ) {

										echo "<p><span class=\"sigHidden\" id=\"sigCitationCheck" . $cite['id'] . "\">&#10003;</span> #" . $cite['cite_no'] . "</p>";

									} else {

										echo "<p><span class=\"sigHidden\" id=\"sigCitationCheck" . $cite['id'] . "\">&#10003;</span> " . nbt_get_year_author_for_drug_and_ref ( $drugid, $cite['citationid'] ) . "</p>";

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

function nbt_copy_to_master ( $formid, $refsetid, $reference, $row, $extrid ) {

	// Get the value for the extraction

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT " . $row . " FROM `extractions_" . $formid . "` WHERE id = :extrid LIMIT 1;");

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
		$stmt = $dbh->prepare ("UPDATE `m_extractions_" . $formid . "` SET " . $row . " = :value WHERE refsetid = :refset AND referenceid = :ref;");

		$stmt->bindParam(':value', $val2);
		$stmt->bindParam(':ref', $rid);
		$stmt->bindParam(':refset', $rsid);

		$val2 = $value;
		$rid = $reference;
		$rsid = $refsetid;

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	return $val2 . " ";

}

function nbt_copy_multi_select_to_master ( $formid, $refsetid, $reference, $extrid, $elementid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$selectoptions = nbt_get_all_select_options_for_element ( $elementid );

	foreach ( $selectoptions as $option ) {

		// Get the value for the extraction

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("SELECT `" . $element['columnname'] . "_" . $option['dbname'] . "` FROM `extractions_" . $formid . "` WHERE id = :extrid LIMIT 1;");

			$stmt->bindParam(':extrid', $eid);

			$eid = $extrid;

			$stmt->execute();

			$result = $stmt->fetchAll();

			$dbh = null;

			if ( count ($result) > 0 ) {

				foreach ( $result as $val ) {

					$value = $val[$element['columnname'] . "_" . $option['dbname']];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// Set the other extraction to that value

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("UPDATE `m_extractions_" . $formid . "` SET " . $element['columnname'] . "_" . $option['dbname'] . " = :value WHERE refsetid = :refset AND referenceid = :ref LIMIT 1;");

			$stmt->bindParam(':value', $val2);
			$stmt->bindParam(':ref', $rid);
			$stmt->bindParam(':refset', $rsid);

			$val2 = $value;
			$rid = $reference;
			$rsid = $refsetid;

			$stmt->execute();

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

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

		nbt_add_citation ( $drugid, $reference, $uid, $section, $citation );

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

function nbt_get_assignments_for_user_and_refset ( $userid, $refsetid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT *, (SELECT `title` FROM `referenceset_" . $refsetid . "` WHERE `id` LIKE `referenceid`) as `title`, (SELECT `authors` FROM `referenceset_" . $refsetid . "` WHERE `id` LIKE `referenceid`) as `authors`, (SELECT `journal` FROM `referenceset_" . $refsetid . "` WHERE `id` LIKE `referenceid`) as `journalname`, (SELECT `year` FROM `referenceset_" . $refsetid . "` WHERE `id` LIKE `referenceid`) as `year`, (SELECT `id` FROM `forms` WHERE `id` LIKE `formid`) as `formid`, (SELECT `name` FROM `forms` WHERE `id` LIKE `formid`) as `formname` FROM `assignments` WHERE userid = :userid AND `refsetid` = " . $refsetid . " AND whenassigned < NOW() ORDER BY `whenassigned` DESC;");

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

function nbt_get_status_for_assignment ( $assignment ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("SELECT `status` FROM `extractions_" . $assignment['formid'] . "` WHERE `referenceid` = " . $assignment['referenceid'] . " AND `refsetid` = " . $assignment['refsetid'] . " AND `userid` = " . $assignment['userid'] . " LIMIT 1;");

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			if ( count ( $result ) > 0 ) {

				foreach ( $result as $row ) {

					$dbh = null;

					// echo "Success: " . "SELECT `status` FROM `extractions_" . $assignment['formid'] . "` WHERE `referenceid` = " . $assignment['referenceid'] . " AND `userid` = " . $assignment['userid'] . " LIMIT 1;";

					return $row['status'];

				}

			}

		}

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

function nbt_get_master ( $formid, $refsetid, $refid ) {

	// Try to insert
	// If it's already there, it will fail
	// Hooray for MySQL indices

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `m_extractions_" . $formid . "` (refsetid, referenceid) VALUES (:refset, :refid);");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':refid', $rid);

		$rsid = $refsetid;
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
		$stmt = $dbh->prepare("SELECT * FROM `m_extractions_" . $formid . "` WHERE `refsetid` = :refset AND `referenceid` = :refid LIMIT 1;");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':refid', $rid);

		$rsid = $refsetid;
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

function nbt_get_master_table_rows ( $elementid, $refsetid, $refid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `mtable_" . $element['columnname'] . "` WHERE `refsetid` = :refset AND `referenceid` = :ref ORDER BY id ASC;");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $ref);

		$rsid = $refsetid;
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

function nbt_copy_table_row_to_master ( $elementid, $refsetid, $refid, $originalid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `tabledata_" . $element['columnname'] . "` WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $oid);

		$oid = $originalid;

		$stmt->execute();

		$result = $stmt->fetchAll();

		foreach ( $result as $row ) {

			$original = $row;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// Make a new row, get the id

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `mtable_" . $element['columnname'] . "` (refsetid, referenceid) VALUES (:refset, :ref);");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $rid);

		$rsid = $refsetid;
		$rid = $refid;

		$stmt->execute();

		$stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");

		$stmt2->execute();

		$result = $stmt2->fetchAll();

		$dbh = null;

		foreach ( $result as $row ) {

			$newid = $row['newid'];

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// Copy the data over

	$columns = nbt_get_all_columns_for_table_data ( $elementid );

	foreach ( $columns as $column ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `mtable_" . $element['columnname'] . "` SET `" . $column['dbname'] . "` = :value WHERE id = :id LIMIT 1;");

			$stmt->bindParam(':id', $nid);
			$stmt->bindParam(':value', $val);

			$nid = $newid;
			$val = $original[$column['dbname']];

			$stmt->execute();

			$result = $stmt->fetchAll();

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

}

function nbt_get_privileges_for_userid ( $userid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT privileges FROM users WHERE id = :userid");

		$stmt->bindParam(':userid', $user);

		$user = $userid;

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		foreach ($result as $row) {

			return $row['privileges'];

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

function nbt_get_all_users () {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM users;");

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_user_privileges ( $userid, $privileges ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE users SET privileges=:privileges WHERE id = :userid LIMIT 1;");

		$stmt->bindParam(':userid', $user);
		$stmt->bindParam(':privileges', $priv);

		$user = $userid;
		$priv = $privileges;

		if ( $stmt->execute() ) {

			echo "Changes saved";

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_all_ref_sets () {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM referencesets;");

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_all_extraction_forms () {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM forms ORDER BY `id` ASC;");

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_new_extraction_form () {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO forms (name, description) VALUES (:name, :description);");

		$stmt->bindParam(':name', $name);
		$stmt->bindParam(':description', $desc);

		$name = "New extraction form";
		$desc = "Add a useful description of your new form here.";

		$stmt->execute();

		$stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");

		$stmt2->execute();

		$result = $stmt2->fetchAll();

		$dbh = null;

		foreach ( $result as $row ) {

			$newid = $row['newid'];

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// Make a new extraction table with the name `extraction_newid`
	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("CREATE TABLE `extractions_" . $newid . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `timestamp_started` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `userid` int(11) NOT NULL, `status` int(11) NOT NULL, `notes` varchar(500) DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `refsetid` (`refsetid`,`referenceid`,`userid`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// Make the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("CREATE TABLE `m_extractions_" . $newid . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `timestamp_started` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `status` int(11) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `refsetid` (`refsetid`,`referenceid`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		if ($stmt->execute()) {

			$dbh = null;

			return TRUE;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_delete_extraction_form ( $formid ) {

	// first delete the elements within the form

	$elements = nbt_get_elements_for_formid ( $formid );

	foreach ( $elements as $element ) {

		nbt_delete_form_element ( $element['id'] );

	}

	// then remove the form from the list

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `forms` WHERE id = :id LIMIT 1; DROP TABLE `extractions_" . $formid . "`;");

		$stmt->bindParam(':id', $fid);

		$fid = $formid;

		if ($stmt->execute()) {

			$dbh = null;

			return TRUE;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_form_for_id ( $formid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `forms` WHERE `id` = :fid LIMIT 1");

		$stmt->bindParam(':fid', $fid);

		$fid = $formid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				return $row;

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_form_name ( $formid, $newname ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `forms` SET name=:nn WHERE id = :formid LIMIT 1;");

		$stmt->bindParam(':formid', $fid);
		$stmt->bindParam(':nn', $nn);

		$fid = $formid;
		$nn = $newname;

		if ( $stmt->execute() ) {

			echo "Changes saved";

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_form_description ( $formid, $description ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `forms` SET description=:desc WHERE id = :formid LIMIT 1;");

		$stmt->bindParam(':formid', $fid);
		$stmt->bindParam(':desc', $desc);

		$fid = $formid;
		$desc = $description;

		if ( $stmt->execute() ) {

			echo "Changes saved";

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_elements_for_formid ( $formid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid ORDER BY `sortorder` ASC;");

		$stmt->bindParam(':fid', $fid);

		$fid = $formid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			return $result;

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_form_element_for_elementid ( $elementid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `id` = :eid LIMIT 1;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				return $row;

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_sub_element_for_subelementid ( $subelementid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `subelements` WHERE `id` = :seid LIMIT 1;");

		$stmt->bindParam(':seid', $seid);

		$seid = $subelementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				return $row;

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_select_for_selectid ( $selectid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `id` = :sid LIMIT 1;");

		$stmt->bindParam(':sid', $sid);

		$sid = $selectid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				return $row;

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_column_for_columnid ( $columnid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `id` = :cid LIMIT 1;");

		$stmt->bindParam(':cid', $cid);

		$cid = $columnid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				return $row;

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_add_open_text_field ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `extractions_" . $formid . "` LIKE 'open_text_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "open_text_" . $counter;

				$foundgoodcolumn = TRUE;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		$counter ++;

	}

	// then insert a new element into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname) VALUES (:form, :sort, :type, :column);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "open_text";
		$col = $columnname;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $formid . "` ADD COLUMN " . $columnname . " varchar(200) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add the column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` ADD COLUMN " . $columnname . " varchar(200) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_add_text_area_field ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `extractions_" . $formid . "` LIKE 'text_area" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "text_area_" . $counter;

				$foundgoodcolumn = TRUE;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		$counter ++;

	}

	// then insert a new element into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname) VALUES (:form, :sort, :type, :column);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "text_area";
		$col = $columnname;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $formid . "` ADD COLUMN " . $columnname . " TEXT DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add the column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` ADD COLUMN " . $columnname . " TEXT DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_delete_form_element ( $elementid ) {

	// first get the form element to be removed

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$formid = $element['formid'];

	$columnname = $element['columnname'];

	switch ( $element['type'] ) {

		case "section_heading":

			// Nothing to do (element is removed from form elements table below)

		break;

		case "open_text":

			// remove the column from the extractions table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $formid . "` DROP COLUMN " . $columnname . ";");

				$stmt->execute();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// remove it from the master table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` DROP COLUMN " . $columnname . ";");

				$stmt->execute();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

		break;

		case "text_area":

			// remove the column from the extractions table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $formid . "` DROP COLUMN " . $columnname . ";");

				$stmt->execute();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// remove it from the master table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` DROP COLUMN " . $columnname . ";");

				$stmt->execute();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

		break;

		case "single_select":

			// remove the column from the extractions table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $formid . "` DROP COLUMN " . $columnname . ";");

				$stmt->execute();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// remove it from the master table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` DROP COLUMN " . $columnname . ";");

				$stmt->execute();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// the options are removed from the selectoptions table below

		break;

		case "multi_select":

			// remove all the columns from the table

			$options = nbt_get_all_select_options_for_element ( $element['id'] );

			foreach ( $options as $option ) {

				nbt_remove_multi_select_option ( $elementid, $option['id'] );

			}

		break;

		case "table_data":

			// remove all the columns from the table data columns table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare("DELETE FROM `tabledatacolumns` WHERE elementid = :id;");

				$stmt->bindParam(':id', $eid);

				$eid = $elementid;

				if ($stmt->execute()) {

					$dbh = null;

				}

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// delete the table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare("DROP TABLE `tabledata_" . $element['columnname'] . "`;");

				if ($stmt->execute()) {

					$dbh = null;

				}

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// delete the master table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare("DROP TABLE `mtable_" . $element['columnname'] . "`;");

				if ($stmt->execute()) {

					$dbh = null;

				}

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

		break;

		case "citations":

			// delete the table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare("DROP TABLE `citations_" . $element['columnname'] . "`;");

				if ($stmt->execute()) {

					$dbh = null;

				}

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// delete the master table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare("DROP TABLE `mcite_" . $element['columnname'] . "`;");

				if ($stmt->execute()) {

					$dbh = null;

				}

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// then remove all the columns from the citations columns table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare("DELETE FROM `citationscolumns` WHERE elementid = :id;");

				$stmt->bindParam(':id', $eid);

				$eid = $elementid;

				if ($stmt->execute()) {

					$dbh = null;

				}

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

		break;

		case "country_selector":

			// remove the column from the extractions table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $formid . "` DROP COLUMN " . $columnname . ";");

				$stmt->execute();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// remove it from the master table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` DROP COLUMN " . $columnname . ";");

				$stmt->execute();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

		break;

		case "date_selector":

			// remove the column from the extractions table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $formid . "` DROP COLUMN " . $columnname . ";");

				$stmt->execute();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// remove it from the master table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` DROP COLUMN " . $columnname . ";");

				$stmt->execute();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

		break;

		case "sub_extraction":

			// delete the table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare("DROP TABLE `sub_" . $element['columnname'] . "`;");

				if ($stmt->execute()) {

					$dbh = null;

				}

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// delete the master table

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare("DROP TABLE `msub_" . $element['columnname'] . "`;");

				if ($stmt->execute()) {

					$dbh = null;

				}

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

			// then remove all the sub extraction elements

			$subelements = nbt_get_sub_extraction_elements_for_elementid ( $elementid );

			foreach ( $subelements as $subelement ) {

				nbt_delete_sub_element ( $subelement['id'] );

			}

		break;

	}

	// then remove all the options from the select options table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `selectoptions` WHERE elementid = :id LIMIT 1;");

		$stmt->bindParam(':id', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then remove the element from the formelement table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `formelements` WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_column_name ( $elementid, $newcolumnname, $dbsize = 200 ) {

	// get the old column name and the form id

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// Start a counter to see if everything saved properly

	$itworked = 0;

	if ( $element['type'] == "text_area" ) {

		// then alter the column in the extraction table

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $element['formid'] . "` CHANGE " . $element['columnname'] . " " . $newcolumnname . " TEXT DEFAULT NULL;");

			if ($stmt->execute()) {

				$itworked ++;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// then alter the column in the master table

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $element['formid'] . "` CHANGE " . $element['columnname'] . " " . $newcolumnname . " TEXT DEFAULT NULL;");

			if ($stmt->execute()) {

				$itworked ++;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	} else {

		// then alter the column in the extraction table

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $element['formid'] . "` CHANGE " . $element['columnname'] . " " . $newcolumnname . " varchar(" . $dbsize . ") DEFAULT NULL;");

			if ($stmt->execute()) {

				$itworked ++;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// then alter the column in the master table

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $element['formid'] . "` CHANGE " . $element['columnname'] . " " . $newcolumnname . " varchar(" . $dbsize . ") DEFAULT NULL;");

			if ($stmt->execute()) {

				$itworked ++;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 2 ) {

		// then change the form element table to match

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `formelements` SET `columnname`=:newname WHERE `id` = :eid");

			$stmt->bindParam(':eid', $eid);
			$stmt->bindParam(':newname', $nn);

			$eid = $element['id'];
			$nn = $newcolumnname;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 3 ) {

		echo "Changes saved";

	} else {

		echo "Error saving—try a different column name";

	}

}

function nbt_change_display_name ( $elementid, $newdisplayname ) {

	$itworked = 0;

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `formelements` SET `displayname`=:newname WHERE `id` = :eid");

		$stmt->bindParam(':eid', $eid);
		$stmt->bindParam(':newname', $nn);

		$eid = $elementid;
		$nn = $newdisplayname;

		if ($stmt->execute()) {

			$itworked ++;

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 1 ) {

		echo "Changes saved";

	} else {

		echo "Error saving";

	}

}

function nbt_change_element_codebook ( $elementid, $newcodebook ) {

	$itworked = 0;

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `formelements` SET `codebook`=:codebook WHERE `id` = :eid");

		$stmt->bindParam(':eid', $eid);
		$stmt->bindParam(':codebook', $ncb);

		$eid = $elementid;
		$ncb = $newcodebook;

		if ($stmt->execute()) {

			$itworked ++;

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 1 ) {

		echo "Changes saved";

	} else {

		echo "Error saving";

	}

}

function nbt_switch_elements_sortorder ( $element1id, $element2id ) {

	// get the original values

	$element1 = nbt_get_form_element_for_elementid ( $element1id );

	$element2 = nbt_get_form_element_for_elementid ( $element2id );

	// then switch them

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `formelements` SET `sortorder` = :sort WHERE `id` = :eid");

		$stmt->bindParam(':eid', $eid);
		$stmt->bindParam(':sort', $sort);

		$eid = $element1id;
		$sort = $element2['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `formelements` SET `sortorder` = :sort WHERE `id` = :eid");

		$stmt->bindParam(':eid', $eid);
		$stmt->bindParam(':sort', $sort);

		$eid = $element2id;
		$sort = $element1['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_switch_selects_sortorder ( $select1id, $select2id ) {

	// get the original values

	$select1 = nbt_get_select_for_selectid ( $select1id );

	$select2 = nbt_get_select_for_selectid ( $select2id );

	// then switch them

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `selectoptions` SET `sortorder` = :sort WHERE `id` = :sid");

		$stmt->bindParam(':sid', $sid);
		$stmt->bindParam(':sort', $sort);

		$sid = $select1id;
		$sort = $select2['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `selectoptions` SET `sortorder` = :sort WHERE `id` = :sid");

		$stmt->bindParam(':sid', $sid);
		$stmt->bindParam(':sort', $sort);

		$sid = $select2id;
		$sort = $select1['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_switch_subextraction_sortorder ( $elementid, $sub1id, $sub2id ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get the original values

	$sub1 = nbt_get_sub_extraction_for_element_and_id ( $elementid, $sub1id );

	$sub2 = nbt_get_sub_extraction_for_element_and_id ( $elementid, $sub2id );

	// then switch them

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `sub_" . $element['columnname'] . "` SET `sortorder` = :sort WHERE `id` = :seid");

		$stmt->bindParam(':seid', $seid);
		$stmt->bindParam(':sort', $sort);

		$seid = $sub1id;
		$sort = $sub2['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `sub_" . $element['columnname'] . "` SET `sortorder` = :sort WHERE `id` = :seid");

		$stmt->bindParam(':seid', $seid);
		$stmt->bindParam(':sort', $sort);

		$seid = $sub2id;
		$sort = $sub1['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_switch_tablecolumn_sortorder ( $column1id, $column2id ) {

	// get the original values

	$column1 = nbt_get_column_for_columnid ( $column1id );

	$column2 = nbt_get_column_for_columnid ( $column2id );

	// then switch them

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `tabledatacolumns` SET `sortorder` = :sort WHERE `id` = :cid");

		$stmt->bindParam(':cid', $cid);
		$stmt->bindParam(':sort', $sort);

		$cid = $column1id;
		$sort = $column2['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE `tabledatacolumns` SET `sortorder` = :sort WHERE `id` = :cid");

		$stmt->bindParam(':cid', $cid);
		$stmt->bindParam(':sort', $sort);

		$cid = $column2id;
		$sort = $column1['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_move_form_element ( $elementid, $direction ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	if ( $direction == 1 ) { // moving "up"

		// first, see if there are any elements above it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :form AND `sortorder` < :sort ORDER BY sortorder DESC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':form', $form);

			$sort = $element['sortorder'];
			$form = $element['formid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$moveup = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$moveup = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element up, if necessary

		if ( $moveup ) {

			nbt_switch_elements_sortorder ( $elementid, $moveup );

		}

	} else { // moving "up"

		// first, see if there are any elements below it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :form AND `sortorder` > :sort ORDER BY sortorder ASC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':form', $form);

			$sort = $element['sortorder'];
			$form = $element['formid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$movedown = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$movedown = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element down, if necessary

		if ( $movedown ) {

			nbt_switch_elements_sortorder ( $elementid, $movedown );

		}

	}

}

function nbt_move_select_option ( $selectid, $direction ) {

	$select = nbt_get_select_for_selectid ( $selectid );

	if ( $direction == 1 ) { // moving "up"

		// first, see if there are any elements above it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `elementid` = :eid AND `sortorder` < :sort ORDER BY sortorder DESC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':eid', $eid);

			$sort = $select['sortorder'];
			$eid = $select['elementid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$moveup = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$moveup = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element up, if necessary

		if ( $moveup ) {

			nbt_switch_selects_sortorder ( $selectid, $moveup );

		}

	} else { // moving "up"

		// first, see if there are any elements below it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `elementid` = :eid AND `sortorder` > :sort ORDER BY sortorder ASC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':eid', $eid);

			$sort = $select['sortorder'];
			$eid = $select['elementid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$movedown = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$movedown = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element down, if necessary

		if ( $movedown ) {

			nbt_switch_selects_sortorder ( $selectid, $movedown );

		}

	}

}

function nbt_move_sub_extraction ( $elementid, $refsetid, $refid, $subextractionid, $direction, $userid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$subextraction = nbt_get_sub_extraction_for_element_and_id ( $elementid, $subextractionid );

	if ( $direction == 1 ) { // moving "up"

		// first, see if there are any elements above it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `sub_" . $element['columnname'] . "` WHERE `refsetid` = :refset AND `referenceid` = :ref AND `userid` = :user AND `sortorder` < :sort ORDER BY sortorder DESC LIMIT 1;");

			$stmt->bindParam(':refset', $rsid);
			$stmt->bindParam(':ref', $rid);
			$stmt->bindParam(':user', $uid);
			$stmt->bindParam(':sort', $sort);

			$rsid = $refsetid;
			$rid = $refid;
			$uid = $userid;
			$sort = $subextraction['sortorder'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$moveup = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$moveup = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element up, if necessary

		if ( $moveup ) {

			nbt_switch_subextraction_sortorder ( $elementid, $subextractionid, $moveup );

		}

	} else { // moving "down"

		// first, see if there are any elements below it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `sub_" . $element['columnname'] . "` WHERE `refsetid` = :refset AND `referenceid` = :ref AND `userid` = :user AND `sortorder` > :sort ORDER BY sortorder ASC LIMIT 1;");

			$stmt->bindParam(':refset', $rsid);
			$stmt->bindParam(':ref', $rid);
			$stmt->bindParam(':user', $uid);
			$stmt->bindParam(':sort', $sort);

			$rsid = $refsetid;
			$rid = $refid;
			$uid = $userid;
			$sort = $subextraction['sortorder'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$movedown = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$movedown = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element down, if necessary

		if ( $movedown ) {

			nbt_switch_subextraction_sortorder ( $elementid, $subextractionid, $movedown );

		}

	}

}

function nbt_add_single_select ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `extractions_" . $formid . "` LIKE 'single_select_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "single_select_" . $counter;

				$foundgoodcolumn = TRUE;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		$counter ++;

	}

	// then insert a new element into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname) VALUES (:form, :sort, :type, :column);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "single_select";
		$col = $columnname;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $formid . "` ADD COLUMN " . $columnname . " varchar(50) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` ADD COLUMN " . $columnname . " varchar(50) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_all_select_options_for_element ( $elementid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `elementid` = :eid ORDER BY `sortorder` ASC;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_add_single_select_option ( $elementid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `elementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add the element

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO selectoptions (elementid, sortorder) VALUES (:eid, :sort);");

		$stmt->bindParam(':eid', $eid);
		$stmt->bindParam(':sort', $sort);

		$eid = $elementid;
		$sort = $highestsortorder + 1;

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_add_multi_select_option ( $elementid ) {

	// get the highest sortorder

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `elementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `extractions_" . $element['formid'] . "` LIKE '" . $element['columnname'] . "_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = $element['columnname'] . "_" . $counter;

				$foundgoodcolumn = TRUE;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		$counter ++;

	}

	// then insert a new option into the selectoptions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `selectoptions` (elementid, sortorder, dbname) VALUES (:eid, :sort, :column);");

		$stmt->bindParam(':eid', $eid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':column', $col);

		$eid = $elementid;
		$sort = $highestsortorder + 1;
		$col = $counter - 1;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $element['formid'] . "` ADD COLUMN " . $columnname . " INT(11) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $element['formid'] . "` ADD COLUMN " . $columnname . " INT(11) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_remove_single_select_option ( $selectid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM selectoptions WHERE id = :sid;");

		$stmt->bindParam(':sid', $sid);

		$sid = $selectid;

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_remove_multi_select_option ( $elementid, $selectid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$select = nbt_get_select_for_selectid ( $selectid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $element['formid'] . "` DROP COLUMN " . $element['columnname'] . "_" . $select['dbname'] . ";");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// drop the column from the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $element['formid'] . "` DROP COLUMN " . $element['columnname'] . "_" . $select['dbname'] . ";");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// remove from options table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM selectoptions WHERE id = :sid;");

		$stmt->bindParam(':sid', $sid);

		$sid = $selectid;

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_remove_sub_multi_select_option ( $subelementid, $selectid ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $subelementid );

	$element = nbt_get_form_element_for_elementid ( $subelement['elementid'] );

	$select = nbt_get_select_for_selectid ( $selectid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` DROP COLUMN " . $subelement['dbname'] . "_" . $select['dbname'] . ";");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// remove from master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` DROP COLUMN " . $subelement['dbname'] . "_" . $select['dbname'] . ";");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// remove from options table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM selectoptions WHERE id = :sid;");

		$stmt->bindParam(':sid', $sid);

		$sid = $selectid;

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_element_toggle ( $elementid, $newtoggle ) {

	$itworked = 0;

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `formelements` SET `toggle`=:newtoggle WHERE `id` = :eid");

		$stmt->bindParam(':eid', $eid);
		$stmt->bindParam(':newtoggle', $nn);

		$eid = $elementid;
		$nn = $newtoggle;

		if ($stmt->execute()) {

			$itworked ++;

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 1 ) {

		echo "Changes saved";

	} else {

		echo "Error saving";

	}

}

function nbt_update_single_select ( $selectid, $column, $newvalue ) {

	$itworked = 0;

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `selectoptions` SET `" . $column . "`=:newvalue WHERE `id` = :sid");

		$stmt->bindParam(':sid', $sid);
		$stmt->bindParam(':newvalue', $nv);

		$sid = $selectid;
		$nv = $newvalue;

		if ($stmt->execute()) {

			$itworked ++;

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 1 ) {

		echo "Changes saved";

	} else {

		echo "Error saving";

	}

}

function nbt_add_multi_select ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname) VALUES (:form, :sort, :type, :column);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "multi_select";
		$col = "multi_select";

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_increase_element_sortorder ( $elementid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `formelements` SET `sortorder` = :sort WHERE `id` = :id LIMIT 1;");

		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':id', $eid);

		$sort = $element['sortorder'] + 1;
		$eid = $element['id'];

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_add_section_heading ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then insert a new element into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type) VALUES (:form, :sort, :type);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "section_heading";

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_update_multi_select_option_column ( $elementid, $selectid, $oldcolumn, $newcolumn ) {

	$itworked = 0;

	// update the extractions table

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$previous = $element['columnname'] . "_" . $oldcolumn;

	$newcolumnname = $element['columnname'] . "_" . $newcolumn;

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $element['formid'] . "` CHANGE " . $previous . " " . $newcolumnname . " INT(11) DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// update the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $element['formid'] . "` CHANGE " . $previous . " " . $newcolumnname . " INT(11) DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// update options table

	if ( $itworked == 2) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `selectoptions` SET `dbname`=:newcolumn WHERE `id` = :sid");

			$stmt->bindParam(':sid', $sid);
			$stmt->bindParam(':newcolumn', $nc);

			$sid = $selectid;
			$nc = $newcolumn;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	} else {

		echo "Error saving";

	}



}

function nbt_update_sub_multi_select_option_column ( $subelementid, $selectid, $oldcolumn, $newcolumn ) {

	$itworked = 0;

	// update the extractions table

	$subelement = nbt_get_sub_element_for_subelementid ( $subelementid );

	$element = nbt_get_form_element_for_elementid ( $subelement['elementid'] );

	$previous = $subelement['dbname'] . "_" . $oldcolumn;

	$newcolumnname = $subelement['dbname'] . "_" . $newcolumn;

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` CHANGE " . $previous . " " . $newcolumnname . " INT(11) DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// update the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` CHANGE " . $previous . " " . $newcolumnname . " INT(11) DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// update the options table

	if ( $itworked == 2) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `selectoptions` SET `dbname`=:newcolumn WHERE `id` = :sid");

			$stmt->bindParam(':sid', $sid);
			$stmt->bindParam(':newcolumn', $nc);

			$sid = $selectid;
			$nc = $newcolumn;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	} else {

		echo "Error saving";

	}



}

function nbt_change_multi_select_column_prefix ( $elementid, $newcolumn ) {

	// get the old column name and the form id

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// Start a counter to see if everything saved properly

	$itworked = 0;

	$selectoptions = nbt_get_all_select_options_for_element ( $elementid );

	if ( count ( $selectoptions ) == 0 ) {

		$itworked = 1;

	}

	foreach ( $selectoptions as $select ) {

		// update the extractions table

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $element['formid'] . "` CHANGE " . $element['columnname'] . "_" . $select['dbname'] . " " . $newcolumn . "_" . $select['dbname'] . " INT(11) DEFAULT NULL;");

			if ($stmt->execute()) {

				$itworked = 1;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// update the master table

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $element['formid'] . "` CHANGE " . $element['columnname'] . "_" . $select['dbname'] . " " . $newcolumn . "_" . $select['dbname'] . " INT(11) DEFAULT NULL;");

			if ($stmt->execute()) {

				$itworked = 1;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}


	if ( $itworked == 1 ) {

		// then change the form element table to match

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `formelements` SET `columnname`=:newname WHERE `id` = :eid");

			$stmt->bindParam(':eid', $eid);
			$stmt->bindParam(':newname', $nn);

			$eid = $element['id'];
			$nn = $newcolumn;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 2 ) {

		echo "Changes saved";

	} else {

		echo "Error saving—try a different column name";

	}

}

function nbt_add_table_data ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the table

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW TABLES LIKE 'tabledata_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "tabledata_" . $counter;

				$foundgoodcolumn = TRUE;

			} else {

				$counter ++;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	// then make a new table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("CREATE TABLE `tabledata_" . $counter . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `userid` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then make the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("CREATE TABLE `mtable_" . $counter . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add it into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname) VALUES (:form, :sort, :type, :column);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "table_data";
		$col = $counter;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_all_columns_for_table_data ( $elementid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `elementid` = :eid ORDER BY `sortorder` ASC;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			return $result;

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_table_suffix ( $elementid, $newsuffix ) {

	// get the old column name and the form id

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// Start a counter to see if everything saved properly

	$itworked = 0;

	// then alter the column in the extraction table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("RENAME TABLE `tabledata_" . $element['columnname'] . "` TO `tabledata_" . $newsuffix . "`;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then alter the column in the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("RENAME TABLE `mtable_" . $element['columnname'] . "` TO `mtable_" . $newsuffix . "`;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 2 ) {

		// then change the form element table to match

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `formelements` SET `columnname`=:newname WHERE `id` = :eid");

			$stmt->bindParam(':eid', $eid);
			$stmt->bindParam(':newname', $nn);

			$eid = $element['id'];
			$nn = $newsuffix;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 3 ) {

		echo "Changes saved";

	} else {

		echo "Error saving—try a different column name";

	}

}

function nbt_add_table_data_column ( $elementid ) {

	// get the highest sortorder

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `elementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `tabledata_" . $element['columnname'] . "` LIKE 'column_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "column_" . $counter;

				$foundgoodcolumn = TRUE;

			} else {

				$counter ++;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	// then insert a new option into the tabledatacolumns table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `tabledatacolumns` (elementid, sortorder, dbname) VALUES (:eid, :sort, :column);");

		$stmt->bindParam(':eid', $eid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':column', $col);

		$eid = $elementid;
		$sort = $highestsortorder + 1;
		$col = "column_" . $counter;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `tabledata_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " VARCHAR(200) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add a column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `mtable_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " VARCHAR(200) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_table_column_for_columnid ( $columnid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `id` = :cid LIMIT 1;");

		$stmt->bindParam(':cid', $cid);

		$cid = $columnid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				return $row;

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_remove_table_data_column ( $elementid, $columnid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$column = nbt_get_table_column_for_columnid ( $columnid );

	echo "ALTER TABLE `tabledata_" . $element['columnname'] . "` DROP COLUMN " . $column['dbname'] . ";";

	// tabledata

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `tabledata_" . $element['columnname'] . "` DROP COLUMN " . $column['dbname'] . ";");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `mtable_" . $element['columnname'] . "` DROP COLUMN " . $column['dbname'] . ";");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// columns table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM tabledatacolumns WHERE id = :cid;");

		$stmt->bindParam(':cid', $cid);

		$cid = $columnid;

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_update_table_data_column_display ( $columnid, $newvalue ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `tabledatacolumns` SET `displayname`=:newvalue WHERE `id` = :cid");

		$stmt->bindParam(':cid', $cid);
		$stmt->bindParam(':newvalue', $nv);

		$cid = $columnid;
		$nv = $newvalue;

		if ($stmt->execute()) {

			echo "Changes saved";

		} else {

			echo "MySQL fail";

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_move_table_data_column ( $columnid, $direction ) {

	$column = nbt_get_column_for_columnid ( $columnid );

	if ( $direction == 1 ) { // moving "up"

		// first, see if there are any elements above it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `elementid` = :eid AND `sortorder` < :sort ORDER BY sortorder DESC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':eid', $eid);

			$sort = $column['sortorder'];
			$eid = $column['elementid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$moveup = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$moveup = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element up, if necessary

		if ( $moveup ) {

			nbt_switch_tablecolumn_sortorder ( $columnid, $moveup );

		}

	} else { // moving "down"

		// first, see if there are any elements below it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `elementid` = :eid AND `sortorder` > :sort ORDER BY sortorder ASC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':eid', $eid);

			$sort = $column['sortorder'];
			$eid = $column['elementid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$movedown = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$movedown = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element down, if necessary

		if ( $movedown ) {

			nbt_switch_tablecolumn_sortorder ( $columnid, $movedown );

		}

	}

}

function nbt_update_table_data_column_db ( $columnid, $newcolumnname ) {

	// get the old column name and the form id

	$column = nbt_get_table_column_for_columnid ( $columnid );

	$element = nbt_get_form_element_for_elementid ( $column['elementid'] );

	// Start a counter to see if everything saved properly

	$itworked = 0;

	// then alter the column in the extraction table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `tabledata_" . $element['columnname'] . "` CHANGE " . $column['dbname'] . " " . $newcolumnname . " varchar(200) DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// alter the column in the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `mtable_" . $element['columnname'] . "` CHANGE " . $column['dbname'] . " " . $newcolumnname . " varchar(200) DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 2 ) {

		// then change the form element table to match

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `tabledatacolumns` SET `dbname`=:newname WHERE `id` = :cid");

			$stmt->bindParam(':cid', $cid);
			$stmt->bindParam(':newname', $nn);

			$cid = $columnid;
			$nn = $newcolumnname;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 3 ) {

		echo "Changes saved";

	} else {

		echo "Error saving—try a different column name";

	}

}

function nbt_add_country_selector ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `extractions_" . $formid . "` LIKE 'country_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "country_" . $counter;

				$foundgoodcolumn = TRUE;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		$counter ++;

	}

	// then insert a new element into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname) VALUES (:form, :sort, :type, :column);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "country_selector";
		$col = $columnname;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $formid . "` ADD COLUMN " . $columnname . " varchar(50) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add a column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` ADD COLUMN " . $columnname . " varchar(50) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_new_dump_file () {

	date_default_timezone_set ('America/Montreal');

	exec('mysqldump --user=' . DB_USER . ' --password=' . DB_PASS . ' --host=' . DB_HOST . ' ' . DB_NAME . ' > ' . ABS_PATH . 'backup/dumpfiles/' . date('Y-m-d-H-i') . '.sql' );

}

function nbt_add_date_selector ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `extractions_" . $formid . "` LIKE 'date_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "date_" . $counter;

				$foundgoodcolumn = TRUE;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		$counter ++;

	}

	// then insert a new element into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname) VALUES (:form, :sort, :type, :column);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "date_selector";
		$col = $columnname;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $formid . "` ADD COLUMN " . $columnname . " DATE DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add a column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` ADD COLUMN " . $columnname . " DATE DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_date_column_name ( $elementid, $newcolumnname ) {

	// get the old column name and the form id

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// Start a counter to see if everything saved properly

	$itworked = 0;

	// then alter the column in the extraction table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $element['formid'] . "` CHANGE " . $element['columnname'] . " " . $newcolumnname . " DATE DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// alter the column in the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $element['formid'] . "` CHANGE " . $element['columnname'] . " " . $newcolumnname . " DATE DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 2 ) {

		// then change the form element table to match

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `formelements` SET `columnname`=:newname WHERE `id` = :eid");

			$stmt->bindParam(':eid', $eid);
			$stmt->bindParam(':newname', $nn);

			$eid = $element['id'];
			$nn = $newcolumnname;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 3 ) {

		echo "Changes saved";

	} else {

		echo "Error saving—try a different column name";

	}

}

function nbt_add_citation_selector ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the table

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW TABLES LIKE 'citations_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "citations_" . $counter;

				$foundgoodcolumn = TRUE;

			} else {

				$counter ++;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	// then make a new table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("CREATE TABLE `citations_" . $counter . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `userid` int(11) NOT NULL, `citationid` int(11) NOT NULL, `cite_no` int(11) NULL, PRIMARY KEY (`id`), UNIQUE KEY `unique_cite` (`refsetid`,`referenceid`,`userid`,`citationid`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then make the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("CREATE TABLE `mcite_" . $counter . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `citationid` int(11) NOT NULL, `cite_no` int(11) NULL, PRIMARY KEY (`id`), UNIQUE KEY `unique_cite` (`refsetid`,`referenceid`,`citationid`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add it into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname) VALUES (:form, :sort, :type, :column);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "citations";
		$col = $counter;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_citation_selector_suffix ( $elementid, $newsuffix ) {

	// get the old column name and the form id

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// Start a counter to see if everything saved properly

	$itworked = 0;

	// then alter the column in the extraction table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("RENAME TABLE `citations_" . $element['columnname'] . "` TO `citations_" . $newsuffix . "`;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then alter the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("RENAME TABLE `mcite_" . $element['columnname'] . "` TO `mcite_" . $newsuffix . "`;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 2 ) {

		// then change the form element table to match

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `formelements` SET `columnname`=:newname WHERE `id` = :eid");

			$stmt->bindParam(':eid', $eid);
			$stmt->bindParam(':newname', $nn);

			$eid = $element['id'];
			$nn = $newsuffix;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 3 ) {

		echo "Changes saved";

	} else {

		echo "Error saving—try a different column name";

	}

}

function nbt_get_all_columns_for_citation_selector ( $elementid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `citationscolumns` WHERE `elementid` = :eid ORDER BY `sortorder` ASC;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			return $result;

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_add_citation_property ( $elementid ) {

	// get the highest sortorder

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `citationscolumns` WHERE `elementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `citations_" . $element['columnname'] . "` LIKE 'property_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "property_" . $counter;

				$foundgoodcolumn = TRUE;

			} else {

				$counter ++;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	// then insert a new option into the citationscolumns table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `citationscolumns` (elementid, sortorder, dbname) VALUES (:eid, :sort, :column);");

		$stmt->bindParam(':eid', $eid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':column', $col);

		$eid = $elementid;
		$sort = $highestsortorder + 1;
		$col = "property_" . $counter;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `citations_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " VARCHAR(50) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add a column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `mcite_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " VARCHAR(50) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_update_citation_property_display ( $columnid, $newvalue ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `citationscolumns` SET `displayname`=:newvalue WHERE `id` = :cid");

		$stmt->bindParam(':cid', $cid);
		$stmt->bindParam(':newvalue', $nv);

		$cid = $columnid;
		$nv = $newvalue;

		if ($stmt->execute()) {

			echo "Changes saved";

		} else {

			echo "MySQL fail";

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_update_citation_property_db ( $columnid, $newcolumnname ) {

	// get the old column name and the form id

	$column = nbt_get_citation_property_for_propertyid ( $columnid );

	$element = nbt_get_form_element_for_elementid ( $column['elementid'] );

	// Start a counter to see if everything saved properly

	$itworked = 0;

	// then alter the column in the extraction table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `citations_" . $element['columnname'] . "` CHANGE " . $column['dbname'] . " " . $newcolumnname . " varchar(200) DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then alter the column in the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `mcite_" . $element['columnname'] . "` CHANGE " . $column['dbname'] . " " . $newcolumnname . " varchar(200) DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 2 ) {

		// then change the form element table to match

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `citationscolumns` SET `dbname`=:newname WHERE `id` = :cid");

			$stmt->bindParam(':cid', $cid);
			$stmt->bindParam(':newname', $nn);

			$cid = $columnid;
			$nn = $newcolumnname;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 3 ) {

		echo "Changes saved";

	} else {

		echo "Error saving—try a different column name";

	}

}

function nbt_get_citation_property_for_propertyid ( $propertyid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `citationscolumns` WHERE `id` = :pid LIMIT 1;");

		$stmt->bindParam(':pid', $pid);

		$pid = $propertyid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				return $row;

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_move_citation_property ( $columnid, $direction ) {

	$column = nbt_get_citation_property_for_propertyid ( $columnid );

	if ( $direction == 1 ) { // moving "up"

		// first, see if there are any elements above it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `citationscolumns` WHERE `elementid` = :eid AND `sortorder` < :sort ORDER BY sortorder DESC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':eid', $eid);

			$sort = $column['sortorder'];
			$eid = $column['elementid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$moveup = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$moveup = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element up, if necessary

		if ( $moveup ) {

			nbt_switch_citation_property_sortorder ( $columnid, $moveup );

		}

	} else { // moving "up"

		// first, see if there are any elements below it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `citationscolumns` WHERE `elementid` = :eid AND `sortorder` > :sort ORDER BY sortorder ASC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':eid', $eid);

			$sort = $column['sortorder'];
			$eid = $column['elementid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$movedown = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$movedown = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element down, if necessary

		if ( $movedown ) {

			nbt_switch_citation_property_sortorder ( $columnid, $movedown );

		}

	}

}

function nbt_switch_citation_property_sortorder ( $column1id, $column2id ) {

	// get the original values

	$column1 = nbt_get_citation_property_for_propertyid ( $column1id );

	$column2 = nbt_get_citation_property_for_propertyid ( $column2id );

	// then switch them

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `citationscolumns` SET `sortorder` = :sort WHERE `id` = :cid");

		$stmt->bindParam(':cid', $cid);
		$stmt->bindParam(':sort', $sort);

		$cid = $column1id;
		$sort = $column2['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE `citationscolumns` SET `sortorder` = :sort WHERE `id` = :cid");

		$stmt->bindParam(':cid', $cid);
		$stmt->bindParam(':sort', $sort);

		$cid = $column2id;
		$sort = $column1['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_return_country_array () {

return array (
	"Choose a country",
	"Afghanistan",
	"Albania",
	"Algeria",
	"Andorra",
	"Angola",
	"Antigua & Deps",
	"Argentina",
	"Armenia",
	"Australia",
	"Austria",
	"Azerbaijan",
	"Bahamas",
	"Bahrain",
	"Bangladesh",
	"Barbados",
	"Belarus",
	"Belgium",
	"Belize",
	"Benin",
	"Bhutan",
	"Bolivia",
	"Bosnia Herzegovina",
	"Botswana",
	"Brazil",
	"Brunei",
	"Bulgaria",
	"Burkina",
	"Burundi",
	"Cambodia",
	"Cameroon",
	"Canada",
	"Cape Verde",
	"Central African Rep",
	"Chad",
	"Chile",
	"China",
	"Colombia",
	"Comoros",
	"Congo",
	"Congo {Democratic Rep}",
	"Costa Rica",
	"Croatia",
	"Cuba",
	"Cyprus",
	"Czech Republic",
	"Denmark",
	"Djibouti",
	"Dominica",
	"Dominican Republic",
	"East Timor",
	"Ecuador",
	"Egypt",
	"El Salvador",
	"Equatorial Guinea",
	"Eritrea",
	"Estonia",
	"Ethiopia",
	"Fiji",
	"Finland",
	"France",
	"Gabon",
	"Gambia",
	"Georgia",
	"Germany",
	"Ghana",
	"Greece",
	"Grenada",
	"Guatemala",
	"Guinea",
	"Guinea-Bissau",
	"Guyana",
	"Haiti",
	"Honduras",
	"Hungary",
	"Iceland",
	"India",
	"Indonesia",
	"Iran",
	"Iraq",
	"Ireland {Republic}",
	"Israel",
	"Italy",
	"Ivory Coast",
	"Jamaica",
	"Japan",
	"Jordan",
	"Kazakhstan",
	"Kenya",
	"Kiribati",
	"Korea North",
	"Korea South",
	"Kosovo",
	"Kuwait",
	"Kyrgyzstan",
	"Laos",
	"Latvia",
	"Lebanon",
	"Lesotho",
	"Liberia",
	"Libya",
	"Liechtenstein",
	"Lithuania",
	"Luxembourg",
	"Macedonia",
	"Madagascar",
	"Malawi",
	"Malaysia",
	"Maldives",
	"Mali",
	"Malta",
	"Marshall Islands",
	"Mauritania",
	"Mauritius",
	"Mexico",
	"Micronesia",
	"Moldova",
	"Monaco",
	"Mongolia",
	"Montenegro",
	"Morocco",
	"Mozambique",
	"Myanmar, {Burma}",
	"Namibia",
	"Nauru",
	"Nepal",
	"Netherlands",
	"New Zealand",
	"Nicaragua",
	"Niger",
	"Nigeria",
	"Norway",
	"Oman",
	"Pakistan",
	"Palau",
	"Panama",
	"Papua New Guinea",
	"Paraguay",
	"Peru",
	"Philippines",
	"Poland",
	"Portugal",
	"Qatar",
	"Romania",
	"Russian Federation",
	"Rwanda",
	"St Kitts & Nevis",
	"St Lucia",
	"Saint Vincent & the Grenadines",
	"Samoa",
	"San Marino",
	"Sao Tome & Principe",
	"Saudi Arabia",
	"Senegal",
	"Serbia",
	"Seychelles",
	"Sierra Leone",
	"Singapore",
	"Slovakia",
	"Slovenia",
	"Solomon Islands",
	"Somalia",
	"South Africa",
	"South Sudan",
	"Spain",
	"Sri Lanka",
	"Sudan",
	"Suriname",
	"Swaziland",
	"Sweden",
	"Switzerland",
	"Syria",
	"Taiwan",
	"Tajikistan",
	"Tanzania",
	"Thailand",
	"Togo",
	"Tonga",
	"Trinidad & Tobago",
	"Tunisia",
	"Turkey",
	"Turkmenistan",
	"Tuvalu",
	"Uganda",
	"Ukraine",
	"United Arab Emirates",
	"United Kingdom",
	"United States",
	"Uruguay",
	"Uzbekistan",
	"Vanuatu",
	"Vatican City",
	"Venezuela",
	"Vietnam",
	"Yemen",
	"Zambia",
	"Zimbabwe"
);

}

function nbt_remove_table_data_row ( $tableid, $rowid ) {

	$element = nbt_get_form_element_for_elementid ( $tableid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM `tabledata_" . $element['columnname'] . "` WHERE id = :rowid;");

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

function nbt_update_extraction_table_data ($tableid, $rowid, $column, $newvalue) {

	$element = nbt_get_form_element_for_elementid ( $tableid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE `tabledata_" . $element['columnname'] . "` SET `" . $column . "` = :value WHERE id = :rowid;");

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

function nbt_update_extraction_mtable_data ($tableid, $rowid, $column, $newvalue) {

	$element = nbt_get_form_element_for_elementid ( $tableid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE `mtable_" . $element['columnname'] . "` SET `" . $column . "` = :value WHERE id = :rowid;");

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

function nbt_update_citation_property ( $section, $cid, $column, $value ) {

	$element = nbt_get_form_element_for_elementid ( $section );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `citations_" . $element['columnname'] . "` SET `" . $column . "` = :value WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $id);
		$stmt->bindParam(':value', $val);

		$id = $cid;
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

function nbt_get_all_assignments_for_refset ( $refsetid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT *, (SELECT `username` FROM `users` WHERE `id` LIKE `userid`) as `username`, (SELECT `title` FROM `referenceset_" . $refsetid . "` WHERE `id` LIKE `referenceid`) as `title`, (SELECT `authors` FROM `referenceset_" . $refsetid . "` WHERE `id` LIKE `referenceid`) as `authors`, (SELECT `journal` FROM `referenceset_" . $refsetid . "` WHERE `id` LIKE `referenceid`) as `journal`, (SELECT `year` FROM `referenceset_" . $refsetid . "` WHERE `id` LIKE `referenceid`) as `year`, (SELECT `id` FROM `forms` WHERE `id` LIKE `formid`) as `formid`, (SELECT `name` FROM `forms` WHERE `id` LIKE `formid`) as `formname` FROM `assignments` WHERE `refsetid` = '" . $refsetid . "' ORDER BY `whenassigned` DESC;");

		$stmt->bindParam(':username', $user);

		$user = $username;

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbtAddAssignment ( $userid, $formid, $refsetid, $refid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `assignments` (userid, assignerid, formid, refsetid, referenceid) VALUES (:user, :assigner, :form, :refset, :ref)");

		$stmt->bindParam(':user', $user);
		$stmt->bindParam(':assigner', $assign);
		$stmt->bindParam(':form', $form);
		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $ref);

		$user = $userid;
		$assign = $_SESSION['nbt_userid'];
		$form = $formid;
		$rsid = $refsetid;
		$ref = $refid;

		if ($stmt->execute()) {

			$dbh = null;

			return TRUE;

		} else {

			return FALSE;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbtDeleteAssignment ( $assignmentid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `assignments` WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $aid);

		$aid = $assignmentid;

		if ($stmt->execute()) {

			$dbh = null;

			return TRUE;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_echo_display_name_and_codebook ( $displayname, $codebook ) {

	?><p><?php echo $displayname; ?><?php

	if ( $codebook != "" ) {

		$codebook = str_replace ("\n", "<br>", $codebook);

		?> <a href="#" onclick="event.preventDefault();$(this).parent().next('.nbtCodebook').slideToggle(100);">(?)</a></p>
		<div class="nbtCodebook"><?php echo $codebook; ?></div><?php

	} else {

		?></p><?php

	}

}

function nbt_add_sub_extraction ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the table

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW TABLES LIKE 'sub_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "sub_" . $counter;

				$foundgoodcolumn = TRUE;

			} else {

				$counter ++;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	// then make a new table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("CREATE TABLE `sub_" . $counter . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `userid` int(11) NOT NULL, `sortorder` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then make a master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("CREATE TABLE `msub_" . $counter . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add it into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname) VALUES (:form, :sort, :type, :column);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "sub_extraction";
		$col = $counter;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_sub_extraction_elements_for_elementid ( $elementid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `subelements` WHERE `elementid` = :eid ORDER BY `sortorder` ASC;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			return $result;

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_sub_extraction_suffix ( $elementid, $newsuffix ) {

	// get the old column name and the form id

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// Start a counter to see if everything saved properly

	$itworked = 0;

	// then alter the column in the extraction table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("RENAME TABLE `sub_" . $element['columnname'] . "` TO `sub_" . $newsuffix . "`;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then alter the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("RENAME TABLE `msub_" . $element['columnname'] . "` TO `msub_" . $newsuffix . "`;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 2 ) {

		// then change the form element table to match

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `formelements` SET `columnname`=:newname WHERE `id` = :eid");

			$stmt->bindParam(':eid', $eid);
			$stmt->bindParam(':newname', $nn);

			$eid = $element['id'];
			$nn = $newsuffix;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 3 ) {

		echo "Changes saved";

	} else {

		echo "Error saving—try a different column name";

	}

}

function nbt_add_sub_open_text_field ( $elementid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get the highest sortorder value

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `subelements` WHERE `elementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `sub_" . $element['columnname'] . "` LIKE 'open_text_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "open_text_" . $counter;

				$foundgoodcolumn = TRUE;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		$counter ++;

	}

	// then insert a new element into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `subelements` (elementid, sortorder, type, dbname) VALUES (:element, :sort, :type, :dbname);");

		$stmt->bindParam(':element', $eid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':dbname', $dbn);

		$eid = $elementid;
		$sort = $highestsortorder + 1;
		$type = "open_text";
		$dbn = $columnname;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " varchar(200) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add a column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " varchar(200) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_delete_sub_element ( $subelementid ) {

	// first get the form element to be removed

	$subelement = nbt_get_sub_element_for_subelementid ( $subelementid );

	$elementid = $subelement['elementid'];

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$dbname = $subelement['dbname'];

	// then remove the element from the formelement table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `subelements` WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $seid);

		$seid = $subelementid;

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then remove the column from the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` DROP COLUMN " . $dbname . ";");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then remove the column from the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` DROP COLUMN " . $dbname . ";");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then remove all the options from the select options table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `selectoptions` WHERE `subelementid` = :id;");

		$stmt->bindParam(':id', $seid);

		$seid = $subelementid;

		if ($stmt->execute()) {

			$dbh = null;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_sub_element_display_name ( $subelementid, $newdisplayname ) {

	$itworked = 0;

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `subelements` SET `displayname`=:newname WHERE `id` = :seid");

		$stmt->bindParam(':seid', $seid);
		$stmt->bindParam(':newname', $nn);

		$seid = $subelementid;
		$nn = $newdisplayname;

		if ($stmt->execute()) {

			$itworked ++;

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 1 ) {

		echo "Changes saved";

	} else {

		echo "Error saving";

	}

}

function nbt_change_sub_element_column_name ( $subelementid, $newcolumnname ) {

	// get the old column name and the element id

	$subelement = nbt_get_sub_element_for_subelementid ( $subelementid );

	$elementid = $subelement['elementid'];

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// Start a counter to see if everything saved properly

	$itworked = 0;

	// then alter the column in the extraction table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` CHANGE " . $subelement['dbname'] . " " . $newcolumnname . " varchar(200) DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then alter the column in the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` CHANGE " . $subelement['dbname'] . " " . $newcolumnname . " varchar(200) DEFAULT NULL;");

		if ($stmt->execute()) {

			$itworked ++;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 2 ) {

		// then change the form element table to match

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `subelements` SET `dbname`=:newname WHERE `id` = :seid");

			$stmt->bindParam(':seid', $seid);
			$stmt->bindParam(':newname', $nn);

			$seid = $subelementid;
			$nn = $newcolumnname;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 3 ) {

		echo "Changes saved";

	} else {

		echo "Error saving—try a different column name";

	}

}

function nbt_change_sub_element_codebook ( $subelementid, $newcodebook ) {

	$itworked = 0;

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `subelements` SET `codebook`=:codebook WHERE `id` = :seid");

		$stmt->bindParam(':seid', $seid);
		$stmt->bindParam(':codebook', $ncb);

		$seid = $subelementid;
		$ncb = $newcodebook;

		if ($stmt->execute()) {

			$itworked ++;

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 1 ) {

		echo "Changes saved";

	} else {

		echo "Error saving";

	}

}

function nbt_change_sub_element_toggle ( $subelementid, $newtoggle ) {

	$itworked = 0;

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `subelements` SET `toggle`=:newtoggle WHERE `id` = :seid");

		$stmt->bindParam(':seid', $seid);
		$stmt->bindParam(':newtoggle', $nn);

		$seid = $subelementid;
		$nn = $newtoggle;

		if ($stmt->execute()) {

			$itworked ++;

		}

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	if ( $itworked == 1 ) {

		echo "Changes saved";

	} else {

		echo "Error saving";

	}

}

function nbt_switch_sub_elements_sortorder ( $subelement1id, $subelement2id ) {

	// get the original values

	$subelement1 = nbt_get_sub_element_for_subelementid ( $subelement1id );

	$subelement2 = nbt_get_sub_element_for_subelementid ( $subelement2id );

	// then switch them

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `subelements` SET `sortorder` = :sort WHERE `id` = :seid");

		$stmt->bindParam(':seid', $seid);
		$stmt->bindParam(':sort', $sort);

		$seid = $subelement1id;
		$sort = $subelement2['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `subelements` SET `sortorder` = :sort WHERE `id` = :seid");

		$stmt->bindParam(':seid', $seid);
		$stmt->bindParam(':sort', $sort);

		$seid = $subelement2id;
		$sort = $subelement1['sortorder'];

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_move_sub_element ( $subelementid, $direction ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $subelementid );

	if ( $direction == 1 ) { // moving "up"

		// first, see if there are any elements above it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `subelements` WHERE `elementid` = :eid AND `sortorder` < :sort ORDER BY sortorder DESC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':eid', $eid);

			$sort = $subelement['sortorder'];
			$eid = $subelement['elementid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$moveup = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$moveup = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element up, if necessary

		if ( $moveup ) {

			nbt_switch_sub_elements_sortorder ( $subelementid, $moveup );

		}

	} else { // moving "up"

		// first, see if there are any elements below it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `subelements` WHERE `elementid` = :eid AND `sortorder` > :sort ORDER BY sortorder ASC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':eid', $eid);

			$sort = $subelement['sortorder'];
			$eid = $subelement['elementid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$movedown = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$movedown = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element down, if necessary

		if ( $movedown ) {

			nbt_switch_sub_elements_sortorder ( $subelementid, $movedown );

		}

	}

}

function nbt_add_sub_single_select ( $elementid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get the highest sortorder value

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `subelements` WHERE `elementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `sub_" . $element['columnname'] . "` LIKE 'single_select_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "single_select_" . $counter;

				$foundgoodcolumn = TRUE;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		$counter ++;

	}

	// then insert a new element into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `subelements` (elementid, sortorder, type, dbname) VALUES (:element, :sort, :type, :column);");

		$stmt->bindParam(':element', $eid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$eid = $elementid;
		$sort = $highestsortorder + 1;
		$type = "single_select";
		$col = $columnname;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " varchar(50) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add a column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " varchar(50) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_all_select_options_for_sub_element ( $subelementid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `subelementid` = :seid ORDER BY `sortorder` ASC;");

		$stmt->bindParam(':seid', $seid);

		$seid = $subelementid;

		$stmt->execute();

		$result = $stmt->fetchAll();

		return $result;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_add_sub_single_select_option ( $subelementid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `subelementid` = :seid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':seid', $seid);

		$seid = $subelementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add the element

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO selectoptions (subelementid, sortorder) VALUES (:seid, :sort);");

		$stmt->bindParam(':seid', $seid);
		$stmt->bindParam(':sort', $sort);

		$seid = $subelementid;
		$sort = $highestsortorder + 1;

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_move_sub_select_option ( $selectid, $direction ) {

	$select = nbt_get_select_for_selectid ( $selectid );

	if ( $direction == 1 ) { // moving "up"

		// first, see if there are any elements above it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `subelementid` = :seid AND `sortorder` < :sort ORDER BY sortorder DESC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':seid', $seid);

			$sort = $select['sortorder'];
			$seid = $select['subelementid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$moveup = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$moveup = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element up, if necessary

		if ( $moveup ) {

			nbt_switch_selects_sortorder ( $selectid, $moveup );

		}

	} else { // moving "up"

		// first, see if there are any elements below it

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `subelementid` = :seid AND `sortorder` > :sort ORDER BY sortorder ASC LIMIT 1;");

			$stmt->bindParam(':sort', $sort);
			$stmt->bindParam(':seid', $seid);

			$sort = $select['sortorder'];
			$seid = $select['subelementid'];

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$movedown = FALSE;

			} else { // there are elements higher than this one

				foreach ( $result as $row ) {

					$movedown = $row['id'];

				}

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		// move the element down, if necessary

		if ( $movedown ) {

			nbt_switch_selects_sortorder ( $selectid, $movedown );

		}

	}

}

function nbt_add_sub_multi_select ( $elementid ) {


	// get the highest sortorder value

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `subelements` WHERE `elementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO subelements (elementid, sortorder, type, dbname) VALUES (:element, :sort, :type, :column);");

		$stmt->bindParam(':element', $eid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$eid = $elementid;
		$sort = $highestsortorder + 1;
		$type = "multi_select";
		$col = "multi_select";

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_change_sub_multi_select_column_prefix ( $subelementid, $newcolumn ) {

	// get the old column name and the form id

	$subelement = nbt_get_sub_element_for_subelementid ( $subelementid );

	$element = nbt_get_form_element_for_elementid ( $subelement['elementid'] );

	// Start a counter to see if everything saved properly

	$itworked = 0;

	$selectoptions = nbt_get_all_select_options_for_sub_element ( $subelementid );

	if ( count ( $selectoptions ) == 0 ) {

		$itworked = 1;

	}

	foreach ( $selectoptions as $select ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` CHANGE " . $subelement['dbname'] . "_" . $select['dbname'] . " " . $newcolumn . "_" . $select['dbname'] . " INT(11) DEFAULT NULL;");

			if ($stmt->execute()) {

				$itworked = 1;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` CHANGE " . $subelement['dbname'] . "_" . $select['dbname'] . " " . $newcolumn . "_" . $select['dbname'] . " INT(11) DEFAULT NULL;");

			if ($stmt->execute()) {

				$itworked = 1;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}


	if ( $itworked == 1 ) {

		// then change the form element table to match

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("UPDATE `subelements` SET `dbname`=:newname WHERE `id` = :seid");

			$stmt->bindParam(':seid', $seid);
			$stmt->bindParam(':newname', $nn);

			$seid = $subelement['id'];
			$nn = $newcolumn;

			if ($stmt->execute()) {

				$itworked ++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	if ( $itworked == 2 ) {

		echo "Changes saved";

	} else {

		echo "Error saving—try a different column name";

	}

}

function nbt_add_sub_multi_select_option ( $subelementid ) {

	// get the highest sortorder

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE `subelementid` = :seid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':seid', $seid);

		$seid = $subelementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	$subelement = nbt_get_sub_element_for_subelementid ( $subelementid );

	$element = nbt_get_form_element_for_elementid ( $subelement['elementid'] );

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `sub_" . $element['columnname'] . "` LIKE '" . $subelement['dbname'] . "_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = $subelement['dbname'] . "_" . $counter;

				$foundgoodcolumn = TRUE;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		$counter ++;

	}

	// then insert a new option into the selectoptions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `selectoptions` (subelementid, sortorder, dbname) VALUES (:seid, :sort, :column);");

		$stmt->bindParam(':seid', $seid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':column', $col);

		$seid = $subelementid;
		$sort = $highestsortorder + 1;
		$col = $counter - 1;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " INT(11) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add a column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " INT(11) DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_add_sub_date_selector ( $elementid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get the highest sortorder value

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `subelements` WHERE `elementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':eid', $eid);

		$eid = $elementid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// find a good name for the new column

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SHOW COLUMNS FROM `sub_" . $element['columnname'] . "` LIKE 'date_" . $counter . "';");

			$stmt->execute();

			$result = $stmt->fetchAll();

			if ( count ( $result ) == 0 ) {

				$columnname = "date_" . $counter;

				$foundgoodcolumn = TRUE;

			}

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

		$counter ++;

	}

	// then insert a new element into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO subelements (elementid, sortorder, type, dbname) VALUES (:element, :sort, :type, :column);");

		$stmt->bindParam(':element', $eid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);
		$stmt->bindParam(':column', $col);

		$eid = $elementid;
		$sort = $highestsortorder + 1;
		$type = "date_selector";
		$col = $columnname;

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then, add a column to the extractions table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " DATE DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add a column to the master table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " DATE DEFAULT NULL;");

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_sub_extractions ( $elementid, $refsetid, $refid, $userid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `sub_" . $element['columnname'] . "` WHERE refsetid = :refset AND referenceid = :ref AND userid = :user ORDER BY `sortorder` ASC;");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $ref);
		$stmt->bindParam(':user', $user);

		$rsid = $refsetid;
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

function nbt_get_master_sub_extractions ( $elementid, $refsetid, $refid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `msub_" . $element['columnname'] . "` WHERE refsetid = :refset AND referenceid = :ref ORDER BY id ASC;");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $ref);

		$rsid = $refsetid;
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

function nbt_add_new_sub_extraction ($elementid, $refsetid, $refid, $userid) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// first get the highest existing sortorder

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `sub_" . $element['columnname'] . "` WHERE `refsetid` = :refset AND `referenceid` = :refid AND `userid` = :userid ORDER BY `sortorder` DESC LIMIT 1;");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);

		$rsid = $refsetid;
		$rid = $refid;
		$uid = $userid;

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				$highestsortorder = $row['sortorder'];

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then add the new row

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `sub_" . $element['columnname'] . "` (refsetid, referenceid, userid, sortorder) VALUES (:refset, :refid, :userid, :newsort);");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':refid', $rid);
		$stmt->bindParam(':userid', $uid);
		$stmt->bindParam(':newsort', $nso);

		$rsid = $refsetid;
		$rid = $refid;
		$uid = $userid;
		$nso = $highestsortorder + 1;

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

function nbt_remove_sub_extraction_instance ( $elementid, $subextractionid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM `sub_" . $element['columnname'] . "` WHERE id = :seid;");

		$stmt->bindParam(':seid', $seid);

		$seid = $subextractionid;

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

function nbt_change_refset_name ( $refsetid, $newname ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE referencesets SET name=:newname WHERE id = :id");

		$stmt->bindParam(':id', $rsid);
		$stmt->bindParam(':newname', $nn);

		$rsid = $refsetid;
		$nn = $newname;

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

function nbt_make_new_refset_row ( $newname ) { // Returns the id of the new refset

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("INSERT INTO referencesets (name) VALUES (:name)");

		$stmt->bindParam(':name', $name);

		$name = $newname;

		$stmt->execute();

		$stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");
		$stmt2->execute();

		$result = $stmt2->fetchAll();

		$dbh = null;

		foreach ( $result as $row ) {

			return $row['newid']; // This is the auto_increment-generated ID for the new row

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_make_new_refset_table ( $refsetid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("CREATE TABLE `referenceset_" . $refsetid . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `manual` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		if ($stmt->execute()) {

			$dbh = null;

			return TRUE;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_add_column_to_refset_table ( $refsetid, $columnname, $columntype ) {

	switch ( $columntype ) {

		case "int":

			$sqltype = "int(11) DEFAULT NULL";

		break;

		case "varchar50":

			$sqltype = "varchar(50) DEFAULT NULL";

		break;

		case "varchar500":

			$sqltype = "varchar(500) DEFAULT NULL";

		break;

		case "varchar1000":

			$sqltype = "varchar(1000) DEFAULT NULL";

		break;

		case "varchar6000":

			$sqltype = "varchar(6000) DEFAULT NULL";

		break;

		case "date":

			$sqltype = "date DEFAULT NULL";

		break;

	}

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("ALTER TABLE `referenceset_" . $refsetid . "` ADD COLUMN `" . $columnname . "` " . $sqltype . ";");

		if ( $stmt->execute() ) {

			return TRUE;

		} else {

			return FALSE;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_insert_row_into_columns ( $refset, $columns, $row, $separator ) {

	$sqlcols = "`" . implode ( "`, `", $columns ) . "`";

	$sqlparams = ":" . implode ( ", :", $columns );

	$values = explode ($separator, $row);

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `referenceset_" . $refset . "` (" . $sqlcols . ") VALUES (" . $sqlparams . ")");

		$counter = 0;
		$colvars = array ();

		foreach ( $columns as $column ) {

			$stmt->bindParam(':' . $column, $colvars[$counter]);

			// This removes quotes if they're at the beginning and the end of a field

			$length = strlen ($values[$counter]);

			if ( ( substr ($values[$counter], $length-1, 1) == "\"" ) && ( substr ($values[$counter], 0, 1) == "\"" ) ) {

				$values[$counter] = substr ( $values[$counter], 1, $length-2 );

			}

			$colvars[$counter] = $values[$counter];

			$counter++;

		}

		if ($stmt->execute()) {

			$dbh = null;
			return TRUE;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_delete_refset ( $refsetid ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("DELETE FROM `referencesets` WHERE id = :id LIMIT 1; DROP TABLE `referenceset_" . $refsetid . "`;");

		$stmt->bindParam(':id', $rsid);

		$rsid = $refsetid;

		if ($stmt->execute()) {

			$dbh = null;

			return TRUE;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbtQueryReferenceSet ( $refsetid, $query ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `referenceset_" . $refsetid . "` WHERE " . $query . ";");

		if ( $stmt->execute() ) {

			$result = $stmt->fetchAll();

			$dbh = null;

			return $result;

		} else {

			$dbh = null;

			return FALSE;

		}



	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbtAddAdvancedAssignment ( $userid, $formid, $refsetid, $query ) {

	$result = nbtQueryReferenceSet ( $refsetid, $query );

	$counter = 0;

	foreach ( $result as $row ) {

		try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("INSERT INTO `assignments` (userid, assignerid, formid, refsetid, referenceid) VALUES (:user, :assigner, :form, :refset, :ref)");

			$stmt->bindParam(':user', $user);
			$stmt->bindParam(':assigner', $assign);
			$stmt->bindParam(':form', $form);
			$stmt->bindParam(':refset', $rsid);
			$stmt->bindParam(':ref', $ref);

			$user = $userid;
			$assign = $_SESSION['nbt_userid'];
			$form = $formid;
			$rsid = $refsetid;
			$ref = $row['id'];

			if ($stmt->execute()) {

				$counter++;

			}

			$dbh = null;

		}

		catch (PDOException $e) {

			echo $e->getMessage();

		}

	}

	echo $counter . " assignments added";

}

function nbt_copy_sub_extraction_to_master ( $elementid, $refsetid, $refid, $originalid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `sub_" . $element['columnname'] . "` WHERE id = :id LIMIT 1;");

		$stmt->bindParam(':id', $oid);

		$oid = $originalid;

		$stmt->execute();

		$result = $stmt->fetchAll();

		foreach ( $result as $row ) {

			$original = $row;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// Make a new row, get the id

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO `msub_" . $element['columnname'] . "` (refsetid, referenceid) VALUES (:refset, :ref);");

		$stmt->bindParam(':refset', $rsid);
		$stmt->bindParam(':ref', $rid);

		$rsid = $refsetid;
		$rid = $refid;

		$stmt->execute();

		$stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");

		$stmt2->execute();

		$result = $stmt2->fetchAll();

		$dbh = null;

		foreach ( $result as $row ) {

			$newid = $row['newid'];

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// copy the data over

	$subelements = nbt_get_sub_extraction_elements_for_elementid ( $elementid );

	foreach ( $subelements as $subelement ) {

		if ( $subelement['type'] != "multi_select" ) {

			try {

				$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$stmt = $dbh->prepare("UPDATE `msub_" . $element['columnname'] . "` SET `" . $subelement['dbname'] . "` = :value WHERE id = :id LIMIT 1;");

				$stmt->bindParam(':id', $nid);
				$stmt->bindParam(':value', $val);

				$nid = $newid;
				$val = $original[$subelement['dbname']];

				$stmt->execute();

				$result = $stmt->fetchAll();

			}

			catch (PDOException $e) {

				echo $e->getMessage();

			}

		} else {

			$options = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );

			foreach ( $options as $option ) {

				try {

					$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
					$stmt = $dbh->prepare("UPDATE `msub_" . $element['columnname'] . "` SET `" . $subelement['dbname'] . "_" . $option['dbname'] . "` = :value WHERE id = :id LIMIT 1;");

					$stmt->bindParam(':id', $nid);
					$stmt->bindParam(':value', $val);

					$nid = $newid;
					$val = $original[$subelement['dbname'] . "_" . $option['dbname']];

					$stmt->execute();

					$result = $stmt->fetchAll();

				}

				catch (PDOException $e) {

					echo $e->getMessage();

				}

			}

		}

	}

}

function nbt_remove_master_sub_extraction ( $elementid, $originalid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("DELETE FROM `msub_" . $element['columnname'] . "` WHERE id = :original;");

		$stmt->bindParam(':original', $oid);

		$oid = $originalid;

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

function nbt_get_sub_extraction_for_element_and_id ( $elementid, $subextractionid ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `sub_" . $element['columnname'] . "` WHERE `id` = :id LIMIT 1;");

		$stmt->bindParam(':id', $seid);

		$seid = $subextractionid;

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		foreach ($result as $row) {

			return $row;

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_get_setting ( $key ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `settings` WHERE `key` = :key LIMIT 1;");

		$stmt->bindParam(':key', $ke);

		$ke = $key;

		$stmt->execute();

		$result = $stmt->fetchAll();

		$dbh = null;

		foreach ($result as $row) {

			return $row['value'];

		}

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_add_assignment_editor ( $formid, $elementid ) {

	// this element is the one immediately before where we want to insert a new element

	$element = nbt_get_form_element_for_elementid ( $elementid );

	// get all the elements after this one and increase their sortorder value by 1

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `formid` = :fid AND `sortorder` > :sort;");

		$stmt->bindParam(':fid', $fid);
		$stmt->bindParam(':sort', $sort);

		$fid = $formid;
		$sort = $element['sortorder'];

		if ($stmt->execute()) {

			$result = $stmt->fetchAll();

			$dbh = null;

			foreach ( $result as $row ) {

				nbt_increase_element_sortorder ( $row['id'] );

			}

		} else {

			echo "MySQL fail";

		}


	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

	// then insert a new element into the form elements table

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type) VALUES (:form, :sort, :type);");

		$stmt->bindParam(':form', $fid);
		$stmt->bindParam(':sort', $sort);
		$stmt->bindParam(':type', $type);

		$fid = $formid;
		$sort = $element['sortorder'] + 1;
		$type = "assignment_editor";

		$stmt->execute();

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

function nbt_set_master_status ( $formid, $masterid, $newstatus ) {

	try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare ("UPDATE `m_extractions_" . $formid . "` SET `status` = :value WHERE `id` = :mid LIMIT 1;");

		$stmt->bindParam(':mid', $mid);
		$stmt->bindParam(':value', $new);

		$mid = $masterid;
		$new = $newstatus;

		$stmt->execute();

		$dbh = null;

	}

	catch (PDOException $e) {

		echo $e->getMessage();

	}

}

?>
