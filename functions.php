<?php

session_start ();

define('INSTALL_HASH', substr (hash('sha256', SITE_URL), 0, 10));
define('NUMBAT_VERSION', '2.13');

function nbt_user_is_logged_in () {

    if ( isset ($_SESSION[INSTALL_HASH . '_nbt_valid_login']) && $_SESSION[INSTALL_HASH . '_nbt_valid_login'] == 1 ) {

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

    $_SESSION[INSTALL_HASH . '_nbt_valid_login'] = 1;
    $_SESSION[INSTALL_HASH . '_nbt_userid'] = nbt_get_userid_for_username ($username);
    $_SESSION[INSTALL_HASH . '_nbt_username'] = nbt_get_username_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] );

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

function nbt_admin_generate_password_link ( $userid ) {

    $username = nbt_get_username_for_userid ($userid);

    if ($username) {
	
	// First, generate a 10-character hash

	$string = md5(uniqid(rand(), true));
	$passwordchangecode = substr($string, 0, 10);

	// Insert that into the DB for the user

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("UPDATE users SET `passwordchangecode` = :code WHERE id = :uid");

	    $stmt->bindParam(':code', $code);
	    $stmt->bindParam(':uid', $uid);

	    $uid = $userid;
	    $code = $passwordchangecode;


	    if ($stmt->execute()) {

		return SITE_URL . "forgot/?username=" . $username . "&code=" . $passwordchangecode;
		
	    }

	    $dbh = null;

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
    } else {
	
	return FALSE;
	
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
    setcookie (INSTALL_HASH . "_nbt_userid", "", time(), "/");
    setcookie (INSTALL_HASH . "_nbt_password", "", time(), "/");

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

function nbt_get_all_extractions_for_refset_and_form ( $refsetid, $formid, $minstatus = 2 ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT *, " . $formid . " AS `formid` FROM `extractions_" . $formid . "`, `referenceset_" . $refsetid . "` WHERE `extractions_" . $formid . "`.`refsetid` = " . $refsetid . " AND `extractions_" . $formid . "`.`referenceid` = `referenceset_" . $refsetid . "`.`id` AND `extractions_" . $formid . "`.`status` >= " . $minstatus);

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	return $result;

    }

    catch (PDOException $e) {

	echo $e->getMessage();
    }

}

function nbt_get_extractions_for_refset_ref_and_form ( $refsetid, $refid, $formid, $minstatus = 2 ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT *, (SELECT `username` FROM `users` WHERE `users`.`id` = `extractions_" . $formid . "`.`userid`) as `username` FROM `extractions_" . $formid . "` WHERE `refsetid` = :refset AND `referenceid` = :ref AND `status` >= :status ORDER BY id ASC;");

	$stmt->bindParam(':refset', $rsid);
	$stmt->bindParam(':ref', $rid);
	$stmt->bindParam(':status', $stat);

	$rsid = $refsetid;
	$rid = $refid;
	$stat = $minstatus;

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

    $columns = nbt_get_columns_for_refset ( $refsetid );

    $refset = nbt_get_refset_for_id ( $refsetid );

    $titlecol = $columns[$refset['title']][0];
    $authorscol = $columns[$refset['authors']][0];

    $altquery = str_replace ("- ", "%", $query);

    $element = nbt_get_form_element_for_elementid ( $citationsid );

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `referenceset_" . $refsetid . "` WHERE `" . $titlecol . "` LIKE :query OR `" . $titlecol . "` LIKE :altquery OR `" . $authorscol . "` LIKE :query LIMIT 6;");

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

function nbt_get_columns_for_refset ( $refsetid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SHOW columns FROM referenceset_" . $refsetid);

	$stmt->execute();

	$columns = $stmt->fetchAll();

	$dbh = null;

	return $columns;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_update_refset_metadata ( $refset, $column, $newvalue ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `referencesets` SET `" . $column . "` = :newvalue WHERE `id` = :id;");

	$stmt->bindParam(':newvalue', $new);
	$stmt->bindParam(':id', $id);

	$id = $refset;
	$new = $newvalue;

	if ($stmt->execute()) {
	    return TRUE;
	} else {
	    return FALSE;
	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_return_references_for_assignment_search ( $refsetid, $query ) {

    $refset = nbt_get_refset_for_id ( $refsetid );

    $columns = nbt_get_columns_for_refset ( $refsetid );

    $titlecol = $columns[$refset['title']][0];
    $authorscol = $columns[$refset['authors']][0];

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `referenceset_" . $refsetid . "` WHERE " . $titlecol . " LIKE :query OR " . $authorscol . " LIKE :query LIMIT 6;");

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

		$newid = $row['newid']; // This is the auto_increment-generated ID

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

function nbt_update_extraction ( $fid, $id, $column, $value ) {

    if ( $column == "status" && $value == 2 ) { // Special case: they're clicking "completed"

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("UPDATE `extractions_" . $fid . "` SET `timestamp_finished` = NOW() WHERE id = :id and `timestamp_finished` IS NULL LIMIT 1;");

	    $stmt->bindParam(':id', $rid);

	    $rid = $id;

	    if ( $stmt->execute() ) {

		$dbh = null;

	    } else {

		$dbh = null;

	    }


	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    }

    // And in the special case or the regular case, do this:

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

function nbt_toggle_extraction ( $formid, $id, $column ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("UPDATE `extractions_" . $formid . "` SET `" . $column . "` = IF(`" . $column . "`=1,0,1) WHERE id = :id LIMIT 1;");

	$stmt->bindParam(':id', $rid);

	$rid = $id;

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
	$stmt = $dbh->prepare ("UPDATE `sub_" . $element['columnname'] . "` SET `" . $column . "` = IF(`" . $column . "`=1,0,1) WHERE id = :id LIMIT 1;");

	$stmt->bindParam(':id', $seid);

	$seid = $id;

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
	$stmt = $dbh->prepare ("UPDATE `msub_" . $element['columnname'] . "` SET `" . $column . "` = IF(`" . $column . "`=1,0,1) WHERE id = :id LIMIT 1;");

	$stmt->bindParam(':id', $seid);

	$seid = $id;
	
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

	echo '<a href="#" class="nbtTextOptionSelect ';

	echo "sig" . $question;

	if ( $extraction[$question . "_" . $dbcolumn] == 1 ) {

	    echo ' nbtTextOptionChosen';

	}

	echo '" id="nbtMS';

	echo $question . "_" . $dbcolumn;

	echo '" onclick="event.preventDefault();nbtSaveMultiSelect(';

	echo $formid;

	echo ', ';

	echo $extraction['id'];

	echo ", '";

	echo $question . "_" . $dbcolumn;

	echo "', 'nbtMS";

	echo $question . "_" . $dbcolumn;

	echo '\');"  conditionalid="';

	echo $toggles[$dbcolumn];

	echo '">';

	echo $plaintext;

	echo "</a>";

    }

}

function nbt_echo_subextraction_multi_select ($elementid, $subextraction, $question, $options, $toggles = NULL ) {

    // $options must be an array of the names of the column in the db

    foreach ( $options as $dbcolumn => $plaintext ) {

	echo '<a href="#" class="nbtTextOptionSelect ';

	echo "nbt" . $question;

	echo " nbtSub" . $subextraction['id'] . "-" . $question;

	if ( $subextraction[$question . "_" . $dbcolumn] == 1 ) {

	    echo ' nbtTextOptionChosen';

	}

	echo '" id="nbtSub';

	echo $elementid . '-';
	echo $subextraction['id'] . "MS";
	echo $dbcolumn;
	echo '" onclick="event.preventDefault();nbtSaveSubExtractionMultiSelect(';
	echo $elementid . ', ';
	echo $subextraction['id'] . ", '";
	echo $question . "_" . $dbcolumn;
	echo "', 'nbtSub";
	echo $elementid . "-";
	echo $subextraction['id'] . "MS";
	echo $dbcolumn . "');";
	echo '" conditionalid="';
	echo $toggles[$dbcolumn] . "_sub";
	echo $subextraction['id'];
	echo '">';
	echo $plaintext . "</a>";


    }

}

function nbt_echo_msubextraction_multi_select ($elementid, $subextraction, $question, $options, $toggles = NULL ) {

    // $options must be an array of the names of the column in the db

    foreach ( $options as $dbcolumn => $plaintext ) {

	echo '<a href="#" class="nbtTextOptionSelect ';

	echo "nbt" . $question;

	echo " nbtSub" . $subextraction['id'] . "-" . $question;

	if ( $subextraction[$question . "_" . $dbcolumn] == 1 ) {

	    echo ' nbtTextOptionChosen';

	}

	echo '" id="nbtSub';

	echo $elementid;

	echo '-';

	echo $subextraction['id'];

	echo "MS";

	echo $dbcolumn;

	echo '" onclick="event.preventDefault();nbtSaveMasterSubExtractionMultiSelect(';

	echo $elementid;

	echo ", ";

	echo $subextraction['id'];

	echo ", '";

	echo $question . "_" . $dbcolumn;

	echo "', 'nbtSub";

	echo $elementid;

	echo "-";

	echo $subextraction['id'];

	echo "MS";

	echo $dbcolumn;

	echo '\');"  conditionalid="';

	echo $toggles[$dbcolumn];

	echo '">';

	echo $plaintext;

	echo "</a>";

    }

}

function nbt_echo_single_select ($formid, $extraction, $question, $answers, $toggles = NULL) {

    // $question must be the name of the column in the db
    // $answers must be an array of the answer entered in the db and the plain text version displayed

    foreach ( $answers as $dbanswer => $ptanswer ) {

	echo '<a href="#" class="nbtTextOptionSelect';

	echo " nbt" . $question;

	if ( ! is_null ( $extraction[$question] ) ) { // This is because PHP will say that 0 and NULL are the same

	    if ( $extraction[$question] . " " == $dbanswer . " " ) { // This is because PHP has a hard time testing for equality between strings and integers

		echo ' nbtTextOptionChosen';

	    }

	}

	$buttonid = "nbtQ" . $question . "A" . str_replace ( "/", "_", str_replace (" ", "_", $dbanswer) );

	echo '" id="';

	echo $buttonid;

	echo '" onclick="event.preventDefault();nbtSaveSingleSelect(';

	echo $formid;

	echo ", ";

	echo $extraction['id'];

	echo ", '";

	echo $question;

	echo "', '";

	echo $dbanswer;

	echo "', '";

	echo $buttonid;

	echo "', 'nbt";

	echo $question;

	echo '\');" conditionalid="';

	echo $toggles[$dbanswer];

	echo '">';

	echo $ptanswer;

	echo "</a>";

    }

}

function nbt_echo_subextraction_single_select ($elementid, $subextraction, $question, $answers, $toggles = NULL) {

    // $question must be the name of the column in the db
    // $answers must be an array of the answer entered in the db and the plain text version displayed

    foreach ( $answers as $dbanswer => $ptanswer ) {

	echo '<a href="#" class="nbtTextOptionSelect';

	echo " nbtSub" . $subextraction['id'] . "-" . $question;

	if ( ! is_null ( $subextraction[$question] ) ) { // This is because PHP will say that 0 and NULL are the same

	    if ( $subextraction[$question] . " " == $dbanswer . " " ) { // This is because PHP has a hard time testing for equality between strings and integers

		echo ' nbtTextOptionChosen';

	    }

	}

	$buttonid = "nbtSub" . $elementid . "-" . $subextraction['id'] . "Q" . $question . "A" . str_replace ( "/", "_", str_replace (" ", "_", $dbanswer) );

?>" id="<?php echo $buttonid; ?>" onclick="event.preventDefault();nbtSaveSubExtractionSingleSelect(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $question; ?>', '<?php echo $dbanswer; ?>', '<?php echo $buttonid; ?>', 'nbtSub<?php echo $subextraction['id'] . "-" . $question; ?>');" conditionalid="<?php echo $toggles[$dbanswer]; ?>_sub<?php echo $subextraction['id']; ?>"><?php echo $ptanswer; ?></a>
<?php }

}

function nbt_echo_msubextraction_single_select ($elementid, $subextraction, $question, $answers, $toggles = NULL) {

    // $question must be the name of the column in the db
    // $answers must be an array of the answer entered in the db and the plain text version displayed

    foreach ( $answers as $dbanswer => $ptanswer ) {

	echo '<a href="#" class="nbtTextOptionSelect';

	echo " nbtSub" . $subextraction['id'] . "-" . $question;

	if ( ! is_null ( $subextraction[$question] ) ) { // This is because PHP will say that 0 and NULL are the same

	    if ( $subextraction[$question] . " " == $dbanswer . " " ) { // This is because PHP has a hard time testing for equality between strings and integers

		echo ' nbtTextOptionChosen';

	    }

	}

	$buttonid = "nbtSub" . $elementid . "-" . $subextraction['id'] . "Q" . $question . "A" . str_replace ( "/", "_", str_replace (" ", "_", $dbanswer) );

?>" id="<?php echo $buttonid; ?>" onclick="event.preventDefault();nbtSaveMasterSubExtractionSingleSelect(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $question; ?>', '<?php echo $dbanswer; ?>', '<?php echo $buttonid; ?>', 'nbtSub<?php echo $subextraction['id'] . "-" . $question; ?>');" conditionalid="<?php echo $toggles[$dbanswer]; ?>"><?php echo $ptanswer; ?></a>
<?php }

}

function nbt_echo_text_field ($formid, $extraction, $dbcolumn, $maxlength, $allcaps = FALSE, $regex = NULL) {

    echo '<input type="text" value="';

    echo $extraction[$dbcolumn];

    echo '" id="nbtTextField';

    echo $dbcolumn;

    echo '" onblur="nbtSaveTextField(';

    echo $formid;

    echo ', ';

    echo $extraction['id'];

    echo ", '";

    echo $dbcolumn;

    echo "', 'nbtTextField";

    echo $dbcolumn;

    echo "', 'nbtTextField";

    echo $dbcolumn;

    echo 'Feedback\', \'';

    echo $regex;

    echo '\');" maxlength="';

    echo $maxlength;

    echo '"';

    if ( $allcaps ) {

	echo " style=\"text-transform: uppercase\"";

    }

    echo ">";

    echo '<span id="nbtTextField' . $dbcolumn . 'Feedback" class="nbtInputFeedback"></span>';

}

function nbt_echo_text_area_field ($formid, $extraction, $dbcolumn, $maxlength, $allcaps = FALSE) {

?><textarea style="width: 100%; height: 150px;" id="nbtTextAreaField<?php echo $dbcolumn; ?>" onkeyup="nbtCheckTextAreaCharacters('nbtTextAreaField<?php echo $dbcolumn; ?>', 5000);" onblur="nbtSaveTextField(<?php echo $formid; ?>, <?php echo $extraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtTextAreaField<?php echo $dbcolumn; ?>', 'nbtTextAreaField<?php echo $dbcolumn; ?>Feedback');" maxlength="<?php echo $maxlength; ?>"><?php echo $extraction[$dbcolumn]; ?></textarea>
    <p class="nbtInputFeedback" id="nbtTextAreaField<?php echo $dbcolumn; ?>Feedback">&nbsp;</p>
<?php

}

function nbt_echo_subextraction_text_field ($elementid, $subextraction, $dbcolumn, $maxlength, $allcaps = FALSE, $regex = NULL) {

    echo '<input type="text" value="';

    echo $subextraction[$dbcolumn];

?>" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>" onblur="nbtSaveSubExtractionTextField(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback', '<?php echo $regex; ?>');" maxlength="<?php echo $maxlength; ?>"<?php if ( $allcaps ) { echo " style=\"text-transform: uppercase\""; } ?>><span class="nbtInputFeedback" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span>
<?php

}

function nbt_echo_msubextraction_text_field ($elementid, $subextraction, $dbcolumn, $maxlength, $allcaps = FALSE) {

    echo '<input type="text" value="';

    echo $subextraction[$dbcolumn];

?>" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>" onblur="nbtSaveMasterSubExtractionTextField(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback');" maxlength="<?php echo $maxlength; ?>"<?php if ( $allcaps ) { echo " style=\"text-transform: uppercase\""; } ?>><span class="nbtInputFeedback" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span>
<?php

}

function nbt_echo_date_selector ($formid, $extraction, $dbcolumn) {

?><p class="nbtDateSelector">
    <input type="text" value="<?php

			      if ( $extraction[$dbcolumn] != "0000-00-00" ) {

				  echo $extraction[$dbcolumn];

			      }

			      ?>" id="nbtDateField<?php echo $dbcolumn; ?>" onblur="nbtSaveDateField(<?php echo $formid; ?>, <?php echo $extraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtDateField<?php echo $dbcolumn; ?>');">
</p>
<?php

}

function nbt_echo_sub_date_selector ($elementid, $subextraction, $dbcolumn) {

?><input type="text" value="<?php

			    if ( $subextraction[$dbcolumn] != "0000-00-00" ) {

				echo $subextraction[$dbcolumn];

			    }

			    ?>" id="nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $dbcolumn; ?>" onblur="nbtSaveSubExtractionDateField(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback');">
    <span class="nbtInputFeedback" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span>
<?php

}

function nbt_echo_msub_date_selector ($elementid, $subextraction, $dbcolumn) {

?><input type="text" value="<?php

			    if ( $subextraction[$dbcolumn] != "0000-00-00" ) {

				echo $subextraction[$dbcolumn];

			    }

			    ?>" id="nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $dbcolumn; ?>" onblur="nbtSaveMasterSubExtractionDateField(<?php echo $elementid; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $dbcolumn; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback');">
    <span class="nbtInputFeedback" id="nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $dbcolumn; ?>Feedback">&nbsp;</span>
<?php

}

function nbt_get_table_data_rows ( $elementid, $refsetid, $refid, $userid, $sub_table = FALSE, $subextractionid = NULL ) {

    if ( $sub_table ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $elementid );

    } else {

	$element = nbt_get_form_element_for_elementid ( $elementid );

    }


    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare("SELECT * FROM `tabledata_" . $subelement['dbname'] . "` WHERE refsetid = :refset AND referenceid = :ref AND userid = :user AND subextractionid = :seid ORDER BY id ASC;");

	    $stmt->bindParam(':seid', $seid);

	    $seid = $subextractionid;

	} else {

	    $stmt = $dbh->prepare("SELECT * FROM `tabledata_" . $element['columnname'] . "` WHERE refsetid = :refset AND referenceid = :ref AND userid = :user ORDER BY id ASC;");

	}

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

function nbt_get_all_table_data_rows_for_refset ( $elementid, $refsetid, $sub_extraction = FALSE ) {

    if ( $sub_extraction == TRUE ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $elementid );

	$suffix = $subelement['dbname'];
	
    } else {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$suffix = $element['columnname'];
	
    }

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `tabledata_" . $suffix . "` WHERE refsetid = :refset ORDER BY id ASC;");

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

function nbt_get_all_sub_extraction_rows_for_refset ( $elementid, $refsetid ) {

    $element = nbt_get_form_element_for_elementid ( $elementid );

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `sub_" . $element['columnname'] . "` WHERE refsetid = :refset ORDER BY id ASC;");

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

function nbt_get_all_sub_extraction_table_data_rows_for_refset ( $elementid, $refsetid ) {

    $element = nbt_get_form_element_for_elementid ( $elementid );

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `tabledata_" . $element['columnname'] . "` WHERE refsetid = :refset ORDER BY id ASC;");

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

function nbt_get_all_reconciled_table_data_rows_for_refset ( $elementid, $refsetid, $sub_extraction = FALSE ) {

    if ( $sub_extraction ) {
	
	$subelement = nbt_get_sub_element_for_subelementid ( $elementid );

	$suffix = $subelement['dbname'];
	
    } else {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$suffix = $element['columnname'];
	
    }

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `mtable_" . $suffix . "` WHERE refsetid = :refset ORDER BY id ASC;");

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

function nbt_get_all_reconciled_sub_extraction_rows_for_refset ( $elementid, $refsetid ) {

    $element = nbt_get_form_element_for_elementid ( $elementid );

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `msub_" . $element['columnname'] . "` WHERE refsetid = :refset ORDER BY id ASC;");

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

function nbt_add_new_extraction_table_data_row ( $tableid, $refsetid, $refid, $userid, $sub_table = FALSE, $subextractionid = NULL ) {

    if ( $sub_table ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $tableid );

    } else {

	$element = nbt_get_form_element_for_elementid ( $tableid );

    }


    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("INSERT INTO `tabledata_" . $subelement['dbname'] . "` (refsetid, referenceid, userid, subextractionid) VALUES (:refset, :refid, :userid, :seid);");

	    $stmt->bindParam(':seid', $seid);

	    $seid = $subextractionid;

	} else {

	    $stmt = $dbh->prepare ("INSERT INTO `tabledata_" . $element['columnname'] . "` (refsetid, referenceid, userid) VALUES (:refset, :refid, :userid);");

	}


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

function nbt_remove_master_table_row ( $elementid, $rowid, $sub_table = FALSE ) {

    if ( $sub_table ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $elementid );

    } else {

	$element = nbt_get_form_element_for_elementid ( $elementid );

    }


    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("DELETE FROM `mtable_" . $subelement['dbname'] . "` WHERE id = :rowid;");

	} else {

	    $stmt = $dbh->prepare ("DELETE FROM `mtable_" . $element['columnname'] . "` WHERE id = :rowid;");

	}

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

				  ?>" id="nbtManRefTextField<?php echo $ref['id']; ?>Title" onblur="nbtUpdateManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>, 'title', 'nbtManRefTextField<?php echo $ref['id']; ?>Title');">
    </p>
    <p class="nbtInlineTextField">
	<span class="nbtInputLabel">Authors</span>
	<input type="text" value="<?php

				  echo $ref['authors'];

				  ?>" id="nbtManRefTextField<?php echo $ref['id']; ?>Authors" onblur="nbtUpdateManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>, 'authors', 'nbtManRefTextField<?php echo $ref['id']; ?>Authors');">
    </p>
    <p class="nbtInlineTextField">
	<span class="nbtInputLabel">Year</span>
	<input type="text" value="<?php

				  echo $ref['year'];

				  ?>" id="nbtManRefTextField<?php echo $ref['id']; ?>Year" onblur="nbtUpdateManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>, 'year', 'nbtManRefTextField<?php echo $ref['id']; ?>Year');">
    </p>
    <p class="nbtInlineTextField">
	<span class="nbtInputLabel">Journal</span>
	<input type="text" value="<?php

				  echo $ref['journal'];

				  ?>" id="nbtManRefTextField<?php echo $ref['id']; ?>Journal" onblur="nbtUpdateManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>, 'journal', 'nbtManRefTextField<?php echo $ref['id']; ?>Journal');">
    </p>
    <p class="nbtInputLabel">Abstract</p>
    <textarea id="nbtManRefTextField<?php echo $ref['id']; ?>Abstract" onblur="nbtUpdateManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>, 'abstract', 'nbtManRefTextField<?php echo $ref['id']; ?>Abstract');" style="width: 90%;"><?php

																														      echo $ref['abstract'];

																														      ?></textarea>
    <button onclick="$(this).fadeOut(0);$('#nbtRemoveReference<?php echo $ref['id']; ?>').fadeIn()">Remove this reference</button>
    <button id="nbtRemoveReference<?php echo $ref['id']; ?>" onclick="nbtRemoveManualReference(<?php echo $refsetid; ?>, <?php echo $ref['id']; ?>);" class="nbtHidden">For real</button>
</div>
<?php
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

		$newid = $row['newid']; // This is the auto_increment-generated ID

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

function nbt_remove_manual_reference ( $refsetid, $refid ) {

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

    // First, get the refset name

    $refsetname = nbt_get_name_for_refsetid ($drugid);

    // Then, set the value to its opposite

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE " . $refsetname . " SET include = IF(`include`=1,0,1) WHERE id = :refid LIMIT 1;");

	$stmt->bindParam(':refid', $rid);

	$rid = $refid;

	$stmt->execute();

	$dbh = null;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_delete_extraction ( $formid, $extrid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	$stmt = $dbh->prepare("DELETE FROM `extractions_" . $formid . "` WHERE `id` = :extrid LIMIT 1;");

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

		    ?><button onclick="sigCopyCitationToMaster (<?php echo $cite['id']; ?>, <?php echo $cite['drugid']; ?>, <?php echo $cite['referenceid']; ?>, <?php echo $cite['section']; ?>);">Copy to final</button>
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
</div>
<?php

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
	$stmt = $dbh->prepare ("SELECT *, (SELECT `id` FROM `forms` WHERE `id` LIKE `formid`) as `formid`, (SELECT `name` FROM `forms` WHERE `id` LIKE `formid`) as `formname` FROM `assignments`, `referenceset_" . $refsetid . "` WHERE `assignments`.`referenceid` = `referenceset_" . $refsetid . "`.`id` AND userid = :userid AND `refsetid` = " . $refsetid . " AND whenassigned < NOW() ORDER BY `whenassigned` DESC;");

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

function nbt_get_final ( $formid, $refsetid, $refid, $insert = TRUE ) {

    // By default, try to insert
    // If it's already there, it will fail
    // Hooray for MySQL indices

    if ( $insert ) {	

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

function nbt_get_master_table_rows ( $elementid, $refsetid, $refid, $sub_table = FALSE, $subextractionid = NULL ) {

    if ( $sub_table ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $elementid );

    } else {

	$element = nbt_get_form_element_for_elementid ( $elementid );

    }


    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare("SELECT * FROM `mtable_" . $subelement['dbname'] . "` WHERE `refsetid` = :refset AND `referenceid` = :ref AND `subextractionid` = :seid ORDER BY id ASC;");

	    $stmt->bindParam(':seid', $seid);

	    $seid = $subextractionid;

	} else {

	    $stmt = $dbh->prepare("SELECT * FROM `mtable_" . $element['columnname'] . "` WHERE `refsetid` = :refset AND `referenceid` = :ref ORDER BY id ASC;");

	}


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

function nbt_add_empty_table_row_to_master ( $elementid, $refsetid, $refid, $sub_table = FALSE, $subextractionid = NULL ) {

    if ( $sub_table ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $elementid );

    } else {

	$element = nbt_get_form_element_for_elementid ( $elementid );

    }

    // Make a new row

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("INSERT INTO `mtable_" . $subelement['dbname'] . "` (refsetid, referenceid, subextractionid) VALUES (:refset, :ref, :seid);");

	    $stmt->bindParam(':seid', $seid);

	    $seid = $subextractionid;

	} else {

	    $stmt = $dbh->prepare ("INSERT INTO `mtable_" . $element['columnname'] . "` (refsetid, referenceid) VALUES (:refset, :ref);");

	}


	$stmt->bindParam(':refset', $rsid);
	$stmt->bindParam(':ref', $rid);

	$rsid = $refsetid;
	$rid = $refid;

	$stmt->execute();

	$dbh = null;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

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

function nbt_manually_change_email_verification ( $userid, $newvalue ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE users SET emailverify=:newval WHERE id = :userid LIMIT 1;");

	$stmt->bindParam(':userid', $user);
	$stmt->bindParam(':newval', $nv);

	$user = $userid;
	$nv = $newvalue;

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

function nbt_new_extraction_form ( $formname = "New extraction form", $description = "Add a useful description of your new form here.", $version = "1.0", $author = NULL, $affiliation = NULL, $project = NULL, $protocol = NULL, $projectdate = NULL) {

    if ( is_null ($projectdate) ) {
	$projectdate=date("Y-m-d");
    }

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO forms (name, description, version, author, affiliation, project, protocol, projectdate) VALUES (:name, :description, :version, :author, :affiliation, :project, :protocol, :projectdate);");

	$stmt->bindParam(':name', $name);
	$stmt->bindParam(':description', $desc);
	$stmt->bindParam(':version', $vers);
	$stmt->bindParam(':author', $auth);
	$stmt->bindParam(':affiliation', $affi);
	$stmt->bindParam(':project', $proj);
	$stmt->bindParam(':protocol', $prot);
	$stmt->bindParam(':projectdate', $prda);

	$name = $formname;
	$desc = $description;
	$vers = $version;
	$auth = $author;
	$affi = $affiliation;
	$proj = $project;
	$prot = $protocol;
	$prda = $projectdate;

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
	$stmt = $dbh->prepare("CREATE TABLE `extractions_" . $newid . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `timestamp_started` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `timestamp_finished` timestamp NULL DEFAULT NULL, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `userid` int(11) NOT NULL, `status` int(11) NOT NULL, `notes` varchar(500) DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `refsetid` (`refsetid`,`referenceid`,`userid`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	if ($stmt->execute()) {

	    $dbh = null;

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // Make the final table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("CREATE TABLE `m_extractions_" . $newid . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `timestamp_started` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `status` int(11) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `refsetid` (`refsetid`,`referenceid`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	if ($stmt->execute()) {

	    $dbh = null;

	    return $newid;

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

    // then delete the assignments for that form

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DELETE FROM `assignments` WHERE formid = :id;");

	$stmt->bindParam(':id', $fid);

	$fid = $formid;

	if ($stmt->execute()) {

	    $dbh = null;

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then remove the form from the list

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DELETE FROM `forms` WHERE id = :id LIMIT 1; DROP TABLE `extractions_" . $formid . "`; DROP TABLE `m_extractions_" . $formid . "`;");

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

function check_for_forms_column ($columnname) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SHOW COLUMNS FROM `forms` LIKE :column;");

	$stmt->bindParam(':column', $col);

	$col = $columnname;

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	if ( count ($result) == 1 ) {
	    return TRUE;
	} else {
	    return FALSE;
	}
	
    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }
    
}

function check_for_referencesets_column ($columnname) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SHOW COLUMNS FROM `referencesets` LIKE :column;");

	$stmt->bindParam(':column', $col);

	$col = $columnname;

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	if ( count ($result) == 1 ) {
	    return TRUE;
	} else {
	    return FALSE;
	}
	
    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }
    
}

function nbt_change_form_metadata ( $formid, $column, $newval ) {

    if ( check_for_forms_column ($column) ) {
	
	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("UPDATE `forms` SET " . $column . "=:nv WHERE id = :formid LIMIT 1;");

	    $stmt->bindParam(':formid', $fid);
	    $stmt->bindParam(':nv', $nv);

	    $fid = $formid;
	    $nv = $newval;

	    if ( $stmt->execute() ) {

		echo "Changes saved";

	    }

	    $dbh = null;

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
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

function nbt_get_highest_eid_in_form ( $formid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT `id` FROM `formelements` WHERE `formid` = :fid ORDER BY `id` DESC LIMIT 1;");

	$stmt->bindParam(':fid', $fid);

	$fid = $formid;

	if ($stmt->execute()) {

	    $result = $stmt->fetchAll();

	    $dbh = null;

	    return $result[0]['id'];

	} else {

	    echo "MySQL fail";

	}


    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }
    
}

function nbt_get_toggles_for_formid ( $formid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT DISTINCT(`toggle`) FROM `formelements` WHERE `toggle` != '' AND `formid` = :fid;");

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

function nbt_get_all_subelements_for_formid ( $formid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `subelements` WHERE `elementid` IN (SELECT `id` FROM `formelements` WHERE `formid` = :fid);");

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

function nbt_get_all_select_options_for_formid ( $formid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `selectoptions` WHERE (`elementid` IN (SELECT `id` FROM `formelements` WHERE `formid` = :fid)) OR (`subelementid` IN (SELECT `id` FROM `subelements` WHERE `elementid` IN (SELECT `id` FROM `formelements` WHERE `formid` = :fid))) ORDER BY elementid, subelementid, sortorder ASC;");

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

function nbt_add_open_text_field ( $formid, $elementid, $displayname = NULL, $columnname = NULL, $codebook = NULL, $toggle = NULL, $regex = NULL ) {

    $formid = intval($formid);
    $elementid = intval($elementid);
    $columnname = nbt_remove_special($columnname);

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

    if ( is_null ( $columnname ) ) {

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
	
    }

    // then insert a new element into the form elements table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname, displayname, codebook, toggle, regex) VALUES (:form, :sort, :type, :column, :displayname, :codebook, :toggle, :regex);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);
	$stmt->bindParam(':regex', $rx);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "open_text";
	$col = $columnname;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;
	$rx = $regex;

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

function nbt_add_prev_select ( $formid, $elementid, $displayname = NULL, $columnname = NULL, $codebook = NULL, $toggle = NULL ) {

    $formid = intval($formid);
    $elementid = intval($elementid);
    $columnname = nbt_remove_special($columnname);

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

    if ( is_null ($columnname) ) {

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SHOW COLUMNS FROM `extractions_" . $formid . "` LIKE 'prev_select_" . $counter . "';");

		$stmt->execute();

		$result = $stmt->fetchAll();

		if ( count ( $result ) == 0 ) {

		    $columnname = "prev_select_" . $counter;

		    $foundgoodcolumn = TRUE;

		}

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	    $counter ++;

	}

    }

    // then insert a new element into the form elements table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname, displayname, codebook, toggle) VALUES (:form, :sort, :type, :column, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "prev_select";
	$col = $columnname;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

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

function nbt_add_text_area_field ( $formid, $elementid, $displayname = NULL, $columnname = NULL, $codebook = NULL, $toggle = NULL ) {

    $formid = intval($formid);
    $elementid = intval($elementid);
    $columnname = nbt_remove_special($columnname);

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

    if ( is_null ( $columnname ) ) {

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
	
    }

    // then insert a new element into the form elements table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname, displayname, codebook, toggle) VALUES (:form, :sort, :type, :column, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "text_area";
	$col = $columnname;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

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

	case "ltable_data":

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

function nbt_change_column_name ( $elementid, $newcolumnname ) {

    // get the old column name and db type

    $element = nbt_get_form_element_for_elementid ( $elementid );

    switch ( $element['type'] ) {
	case "open_text":
	    $dbtype = "varchar(200) DEFAULT NULL";
	    break;
	case "single_select":
	    $dbtype = "varchar(200) DEFAULT NULL";
	    break;
	case "text_area":
	    $dbtype = "TEXT DEFAULT NULL";
	    break;
	case "date_selector":
	    $dbtype = "DATE DEFAULT NULL";
	    break;
	case "country_selector":
	    $dbtype = "varchar(50) DEFAULT NULL";
	    break;
	case "prev_select":
	    $dbtype = "varchar(200) DEFAULT NULL";
	    break;
	    
    }
    
    // Start a counter to see if everything saved properly

    $itworked = 0;

    // then alter the column in the extraction table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `extractions_" . $element['formid'] . "` CHANGE " . $element['columnname'] . " " . $newcolumnname . " " . $dbtype . ";");

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
	$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $element['formid'] . "` CHANGE " . $element['columnname'] . " " . $newcolumnname . " " . $dbtype . ";");

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

	if ( $itworked == 3 ) {

	    echo "Changes saved";

	} else {

	    echo "Error saving—try a different column name";

	}

    }

}

function nbt_change_regex ( $elementid, $newregex ) {

    if ( $newregex == "" ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("UPDATE `formelements` SET `regex`= NULL WHERE `id` = :eid");

	    $stmt->bindParam(':eid', $eid);

	    $eid = $elementid;

	    if ($stmt->execute()) {

		echo "Regex saved";

	    }

	    $dbh = null;

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("UPDATE `formelements` SET `regex`=:newregex WHERE `id` = :eid");

	    $stmt->bindParam(':eid', $eid);
	    $stmt->bindParam(':newregex', $nr);

	    $eid = $elementid;
	    $nr = $newregex;

	    if ($stmt->execute()) {

		echo "Regex saved";

	    }

	    $dbh = null;

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    }
    
}

function nbt_change_subelement_regex ( $subelementid, $newregex ) {

    if ( $newregex == "" ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("UPDATE `subelements` SET `regex`= NULL WHERE `id` = :seid");

	    $stmt->bindParam(':seid', $seid);

	    $seid = $subelementid;

	    if ($stmt->execute()) {

		echo "Regex saved";

	    }

	    $dbh = null;

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("UPDATE `subelements` SET `regex`=:newregex WHERE `id` = :seid");

	    $stmt->bindParam(':seid', $seid);
	    $stmt->bindParam(':newregex', $nr);

	    $seid = $subelementid;
	    $nr = $newregex;

	    if ($stmt->execute()) {

		echo "Regex saved";

	    }

	    $dbh = null;

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    }
    
}

function nbt_refdata_change_column_name ( $elementid, $newcolumnname ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `formelements` SET `columnname`=:newname WHERE `id` = :eid");

	$stmt->bindParam(':eid', $eid);
	$stmt->bindParam(':newname', $nn);

	$eid = $elementid;
	$nn = $newcolumnname;

	if ($stmt->execute()) {

	    echo "Column name saved";

	}

	$dbh = null;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

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

function nbt_add_single_select ( $formid, $elementid, $displayname = NULL, $columnname = NULL, $codebook = NULL, $toggle = NULL ) {

    $formid = intval($formid);
    $elementid = intval($elementid);
    $columnname = nbt_remove_special($columnname);

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

    if ( is_null ( $columnname ) ) {

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
	
    }

    // then insert a new element into the form elements table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname, displayname, codebook, toggle) VALUES (:form, :sort, :type, :column, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "single_select";
	$col = $columnname;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

	if ($stmt->execute()) {
	    $stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");

	    $stmt2->execute();

	    $result = $stmt2->fetchAll();

	    foreach ( $result as $row ) {
		$newid = $row['newid'];
	    }
	}
	
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

    // then, add a column to the final table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `m_extractions_" . $formid . "` ADD COLUMN " . $columnname . " varchar(200) DEFAULT NULL;");

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // return the id

    return $newid;

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

function nbt_add_single_select_option ( $elementid, $displayname = NULL, $dbname = NULL, $toggle = NULL ) {

    // Already Bobby Tables proof

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
	$stmt = $dbh->prepare ("INSERT INTO selectoptions (elementid, sortorder, displayname, dbname, toggle) VALUES (:eid, :sort, :displayname, :dbname, :toggle);");

	$stmt->bindParam(':eid', $eid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':dbname', $db);
	$stmt->bindParam(':toggle', $tg);

	$eid = $elementid;
	$sort = $highestsortorder + 1;
	$dn = $displayname;
	$db = $dbname;
	$tg = $toggle;

	$stmt->execute();

	$dbh = null;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_add_multi_select_option ( $elementid, $displayname = NULL, $dbname = NULL, $toggle = NULL ) {

    $dbname = nbt_remove_special($dbname);

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

    if ( is_null ( $dbname ) ) {

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

	$columnname = $element['columnname'] . "_" . intval($counter - 1);
	$dbname = $counter - 1;
	
    } else {

	$columnname = $element['columnname'] . "_" . $dbname;

    }

    // then insert a new option into the selectoptions table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO `selectoptions` (elementid, sortorder, dbname, displayname, toggle) VALUES (:eid, :sort, :column, :displayname, :toggle);");

	$stmt->bindParam(':eid', $eid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':toggle', $tg);

	$eid = $elementid;
	$sort = $highestsortorder + 1;
	$col = $dbname;
	$dn = $displayname;
	$tg = $toggle;

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

function nbt_add_multi_select ( $formid, $elementid, $displayname = NULL, $columnname = NULL, $codebook = NULL, $toggle = NULL ) {

    $formid = intval($formid);
    $elementid = intval($elementid);
    $columnname = nbt_remove_special($columnname);

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

    if ( is_null ($columnname) ) {
	$columnname = "multi_select";
    }
    
    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname, displayname, codebook, toggle) VALUES (:form, :sort, :type, :column, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "multi_select";
	$col = $columnname;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

	if ($stmt->execute()) {
	    $stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");

	    $stmt2->execute();

	    $result = $stmt2->fetchAll();

	    foreach ( $result as $row ) {
		$newid = $row['newid'];
	    }
	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    return $newid;

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

function nbt_add_section_heading ( $formid, $elementid, $displayname = NULL, $codebook = NULL, $toggle = NULL ) {

    // Already Bobby Tables proof

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
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, displayname, codebook, toggle) VALUES (:form, :sort, :type, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "section_heading";
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

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

function nbt_add_table_data ( $formid, $elementid, $tableformat = "table_data", $displayname = NULL, $suffix = NULL, $codebook = NULL, $toggle = NULL ) {

    $suffix = nbt_remove_special ($suffix);

    // $tableformat can take two values:
    // "table_data" - normal table where columns are VARCHAR200
    // "ltable_data" - large format table where columns are TEXT

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

    if ( ! is_null ($suffix) ) {
	// See if this can be made directly
	if ( ! nbt_table_exists ("tabledata_" . $suffix) ) {
	    $foundgoodcolumn = TRUE;
	} else {
	    $foundgoodcolumn = FALSE;
	    $counter = 1;
	    while($foundgoodcolumn == FALSE) {
		if ( ! nbt_table_exists ("tabledata_" . $suffix . "_"  . $counter) ) {
		    $columnname = "tabledata_" . $suffix . "_" . $counter;
		    $suffix = $suffix . "_" . $counter;
		    $foundgoodcolumn = TRUE;
		} else {
		    $counter++;
		}
	    }
	}
    } else {
	$foundgoodcolumn = FALSE;
	$counter = 1;
	while ( $foundgoodcolumn == FALSE ) {
	    if ( ! nbt_table_exists ("tabledata_" . $counter) ) {
		$columnname = "tabledata_" . $counter;
		$foundgoodcolumn = TRUE;
	    } else {
		$counter++;
	    }
	}

	$suffix = $counter;
    }

    // then make a new table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("CREATE TABLE `tabledata_" . $suffix . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `userid` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

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
	$stmt = $dbh->prepare("CREATE TABLE `mtable_" . $suffix . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

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
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname, displayname, codebook, toggle) VALUES (:form, :sort, :type, :column, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = $tableformat;
	$col = $suffix;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

	if ($stmt->execute()) {

	    $stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");
	    $stmt2->execute();

	    $result = $stmt2->fetchAll();

	    $dbh = null;

	    foreach ( $result as $row ) {

		$newid = $row['newid']; // This is the auto_increment-generated ID

	    }
	    
	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    return $newid;

}

function nbt_get_all_columns_for_sub_table_data ( $subelementid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `subelementid` = :seid ORDER BY `sortorder` ASC;");

	$stmt->bindParam(':seid', $seid);

	$seid = $subelementid;

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

function nbt_get_all_columns_for_table_data ( $elementid, $sub_table = FALSE ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `subelementid` = :eid ORDER BY `sortorder` ASC;");

	} else {

	    $stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `elementid` = :eid ORDER BY `sortorder` ASC;");

	}


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

    if ( $newsuffix == $element['columnname'] ) {
	$itworked = 3;
    } else {

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

    }

    if ( $itworked == 3 ) {

	echo "Changes saved";

    } else {

	echo "Error saving—try a different column name";

    }

}

function nbt_change_sub_table_suffix ( $subelementid, $newsuffix ) {

    // get the old column name and the form id

    $subelement = nbt_get_sub_element_for_subelementid ( $subelementid );

    // Start a counter to see if everything saved properly

    $itworked = 0;

    // then alter the column in the extraction table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("RENAME TABLE `tabledata_" . $subelement['dbname'] . "` TO `tabledata_" . $newsuffix . "`;");

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
	$stmt = $dbh->prepare ("RENAME TABLE `mtable_" . $subelement['dbname'] . "` TO `mtable_" . $newsuffix . "`;");

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

	    $seid = $subelement['id'];
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

function nbt_add_table_data_column ( $elementid, $tableformat = "table_data", $sub_table = FALSE, $displayname = NULL, $dbname = NULL ) {

    $dbname = nbt_remove_special ($dbname);

    // get the highest sortorder

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `subelementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

	} else {

	    $stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `elementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

	}


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

    if ( $sub_table ) {

	$element = nbt_get_sub_element_for_subelementid ( $elementid );

    } else {

	$element = nbt_get_form_element_for_elementid ( $elementid );

    }

    // find a good name for the new column

    if ( is_null ( $dbname ) ) {

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

		if ( $sub_table ) {

		    $stmt = $dbh->prepare("SHOW COLUMNS FROM `tabledata_" . $element['dbname'] . "` LIKE 'column_" . $counter . "';");

		} else {

		    $stmt = $dbh->prepare("SHOW COLUMNS FROM `tabledata_" . $element['columnname'] . "` LIKE 'column_" . $counter . "';");

		}


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

    } else {

	$columnname = $dbname;
	
    }

    // then insert a new option into the tabledatacolumns table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("INSERT INTO `tabledatacolumns` (subelementid, sortorder, dbname, displayname) VALUES (:eid, :sort, :column, :displayname);");

	} else {

	    $stmt = $dbh->prepare ("INSERT INTO `tabledatacolumns` (elementid, sortorder, dbname, displayname) VALUES (:eid, :sort, :column, :displayname);");

	}


	$stmt->bindParam(':eid', $eid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);

	$eid = $elementid;
	$sort = $highestsortorder + 1;
	$col = $columnname;
	$dn = $displayname;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    if ( $tableformat == "table_data" || $sub_table ) { // Standard table data

	// then, add a column to the table

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	    if ( $sub_table ) {

		$stmt = $dbh->prepare ("ALTER TABLE `tabledata_" . $element['dbname'] . "` ADD COLUMN " . $columnname . " VARCHAR(200) DEFAULT NULL;");

	    } else {

		$stmt = $dbh->prepare ("ALTER TABLE `tabledata_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " VARCHAR(200) DEFAULT NULL;");

	    }

	    $stmt->execute();

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

	// then add a column to the master table

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	    if ( $sub_table ) {

		$stmt = $dbh->prepare ("ALTER TABLE `mtable_" . $element['dbname'] . "` ADD COLUMN " . $columnname . " VARCHAR(200) DEFAULT NULL;");

	    } else {

		$stmt = $dbh->prepare ("ALTER TABLE `mtable_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " VARCHAR(200) DEFAULT NULL;");

	    }


	    $stmt->execute();

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

    } else { // Large table data

	// then, add a column to the table

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare ("ALTER TABLE `tabledata_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " TEXT DEFAULT NULL;");

	    $stmt->execute();

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

	// then add a column to the master table

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare ("ALTER TABLE `mtable_" . $element['columnname'] . "` ADD COLUMN " . $columnname . " TEXT DEFAULT NULL;");

	    $stmt->execute();

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

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

function nbt_get_all_table_data_cols_for_formid ( $formid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE (`elementid` IN (SELECT `id` FROM `formelements` WHERE `formid` = :fid)) OR (`subelementid` IN (SELECT `id` FROM `subelements` WHERE `elementid` IN (SELECT `id` FROM `formelements` WHERE `formid` = :fid))) ORDER BY elementid, subelementid, sortorder ASC;");

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

function nbt_remove_table_data_column ( $elementid, $columnid, $sub_table = FALSE ) {

    if ( $sub_table ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $elementid );

    } else {

	$element = nbt_get_form_element_for_elementid ( $elementid );

    }


    $column = nbt_get_table_column_for_columnid ( $columnid );

    // tabledata

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("ALTER TABLE `tabledata_" . $subelement['dbname'] . "` DROP COLUMN " . $column['dbname'] . ";");

	} else {

	    $stmt = $dbh->prepare ("ALTER TABLE `tabledata_" . $element['columnname'] . "` DROP COLUMN " . $column['dbname'] . ";");

	}


	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // master table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("ALTER TABLE `mtable_" . $subelement['dbname'] . "` DROP COLUMN " . $column['dbname'] . ";");

	} else {

	    $stmt = $dbh->prepare ("ALTER TABLE `mtable_" . $element['columnname'] . "` DROP COLUMN " . $column['dbname'] . ";");

	}


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

function nbt_move_table_data_column ( $columnid, $direction, $sub_table = FALSE ) {

    $column = nbt_get_column_for_columnid ( $columnid );

    if ( $direction == 1 ) { // moving "up"

	// first, see if there are any elements above it

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	    if ( $sub_table ) {

		$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `subelementid` = :eid AND `sortorder` < :sort ORDER BY sortorder DESC LIMIT 1;");

	    } else {

		$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `elementid` = :eid AND `sortorder` < :sort ORDER BY sortorder DESC LIMIT 1;");

	    }

	    $stmt->bindParam(':sort', $sort);
	    $stmt->bindParam(':eid', $eid);

	    $sort = $column['sortorder'];

	    if ( $sub_table ) {

		$eid = $column['subelementid'];

	    } else {

		$eid = $column['elementid'];

	    }

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

	    if ( $sub_table ) {

		$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `subelementid` = :eid AND `sortorder` > :sort ORDER BY sortorder ASC LIMIT 1;");

	    } else {

		$stmt = $dbh->prepare("SELECT * FROM `tabledatacolumns` WHERE `elementid` = :eid AND `sortorder` > :sort ORDER BY sortorder ASC LIMIT 1;");

	    }


	    $stmt->bindParam(':sort', $sort);
	    $stmt->bindParam(':eid', $eid);

	    $sort = $column['sortorder'];

	    if ( $sub_table ) {

		$eid = $column['subelementid'];

	    } else {

		$eid = $column['elementid'];

	    }


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

function nbt_update_table_data_column_db ( $columnid, $tableformat, $newcolumnname, $sub_table = FALSE ) {

    // get the old column name and the form id

    $column = nbt_get_table_column_for_columnid ( $columnid );

    if ( $sub_table ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $column['subelementid'] );

    } else {

	$element = nbt_get_form_element_for_elementid ( $column['elementid'] );

    }

    // Column types depending on table format:

    if ( $tableformat == "table_data" ) {

	$columnformat = "VARCHAR(200)";

    } else {

	$columnformat = "TEXT";

    }

    // Start a counter to see if everything saved properly

    $itworked = 0;

    // then alter the column in the extraction table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("ALTER TABLE `tabledata_" . $subelement['dbname'] . "` CHANGE " . $column['dbname'] . " " . $newcolumnname . " " . $columnformat . " DEFAULT NULL;");

	} else {

	    $stmt = $dbh->prepare ("ALTER TABLE `tabledata_" . $element['columnname'] . "` CHANGE " . $column['dbname'] . " " . $newcolumnname . " " . $columnformat . " DEFAULT NULL;");

	}


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

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("ALTER TABLE `mtable_" . $subelement['dbname'] . "` CHANGE " . $column['dbname'] . " " . $newcolumnname . " " . $columnformat . " DEFAULT NULL;");

	} else {

	    $stmt = $dbh->prepare ("ALTER TABLE `mtable_" . $element['columnname'] . "` CHANGE " . $column['dbname'] . " " . $newcolumnname . " " . $columnformat . " DEFAULT NULL;");

	}


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

	echo "Error saving—try a different column name " . $itworked;

    }

}

function nbt_add_country_selector ( $formid, $elementid, $displayname = NULL, $columnname = NULL, $codebook = NULL, $toggle = NULL ) {

    $formid = intval($formid);
    $elementid = intval($elementid);
    $columnname = nbt_remove_special($columnname);

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

    if ( is_null ( $columnname ) ) {

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

    }

    // then insert a new element into the form elements table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname, displayname, codebook, toggle) VALUES (:form, :sort, :type, :column, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "country_selector";
	$col = $columnname;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

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

function nbt_add_extraction_timer ( $formid, $elementid, $codebook = NULL, $toggle = NULL ) {

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
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, codebook, toggle) VALUES (:form, :sort, :type, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "timer";
	$cb = $codebook;
	$tg = $toggle;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_add_date_selector ( $formid, $elementid, $displayname = NULL, $columnname = NULL, $codebook = NULL, $toggle = NULL ) {

    $formid = intval($formid);
    $elementid = intval($elementid);
    $columnname = nbt_remove_special($columnname);

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

    if ( is_null ( $columnname ) ) {

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
    }
    
    // then insert a new element into the form elements table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname, displayname, codebook, toggle) VALUES (:form, :sort, :type, :column, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "date_selector";
	$col = $columnname;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

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

function nbt_add_citation_selector ( $formid, $elementid, $displayname = NULL, $suffix = NULL, $codebook = NULL, $toggle = NULL) {

    $formid = intval($formid);
    $elementid = intval($elementid);
    $suffix = nbt_remove_special($suffix);

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

    if ( ! is_null ($suffix) ) {
	// See if this can be made directly
	if (! nbt_table_exists ("citations_" . $suffix)) {
	    $foundgoodcolumn = FALSE;
	} else {
	    $foundgoodcolumn = FALSE;
	    $counter = 1;
	    while ($foundgoodcolumn == FALSE) {
		if ( ! nbt_table_exists ("citations_" . $suffix . "_" . $counter)) {
		    $columnname = "citations_" . $suffix . "_" . $counter;
		    $suffix = $suffix . "_" . $counter;
		    $foundgoodcolumn = TRUE;
		} else {
		    $counter++;
		}
	    }
	}
    } else {
	$foundgoodcolumn = FALSE;
	$counter = 1;
	while ( $foundgoodcolumn == FALSE ) {
	    if ( ! nbt_table_exists ("citations_" . $counter)) {
		$columnname = "citations_" . $counter;
		$foundgoodcolumn = TRUE;
	    } else {
		$counter++;
	    }
	}

	$suffix = $counter;
    }
    
    // then make a new table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("CREATE TABLE `citations_" . $suffix . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `userid` int(11) NOT NULL, `citationid` int(11) NOT NULL, `cite_no` int(11) NULL, PRIMARY KEY (`id`), UNIQUE KEY `unique_cite` (`refsetid`,`referenceid`,`userid`,`citationid`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

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
	$stmt = $dbh->prepare("CREATE TABLE `mcite_" . $suffix . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `citationid` int(11) NOT NULL, `cite_no` int(11) NULL, PRIMARY KEY (`id`), UNIQUE KEY `unique_cite` (`refsetid`,`referenceid`,`citationid`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

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
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname, displayname, codebook, toggle) VALUES (:form, :sort, :type, :column, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "citations";
	$col = $suffix;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

	if ($stmt->execute()) {

	    $stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");
	    $stmt2->execute();

	    $result = $stmt2->fetchAll();

	    $dbh = null;

	    foreach ( $result as $row ) {

		$newid = $row['newid']; // This is the auto_increment-generated ID for the new row

	    }

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    return $newid;

}

function nbt_change_citation_selector_suffix ( $elementid, $newsuffix ) {

    // get the old column name and the form id

    $element = nbt_get_form_element_for_elementid ( $elementid );

    if ( $newsuffix == $element['columnname'] ) {
	$itworked = 3;
    } else {

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

function nbt_add_citation_property ( $elementid, $displayname = NULL, $dbname = NULL, $remind = NULL, $caps = NULL ) {

    $elementid = intval($elementid);
    $dbname = nbt_remove_special($dbname);

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

    if ( is_null ($dbname) ) {

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SHOW COLUMNS FROM `citations_" . $element['columnname'] . "` LIKE 'property_" . $counter . "';");

		$stmt->execute();

		$result = $stmt->fetchAll();

		if ( count ( $result ) == 0 ) {

		    $dbname = "property_" . $counter;

		    $foundgoodcolumn = TRUE;

		} else {

		    $counter ++;

		}

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	}
	
    }

    // then insert a new option into the citationscolumns table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO `citationscolumns` (elementid, sortorder, dbname, displayname, remind, caps) VALUES (:eid, :sort, :column, :displayname, :remind, :caps);");

	$stmt->bindParam(':eid', $eid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':remind', $rm);
	$stmt->bindParam(':caps', $ca);

	$eid = $elementid;
	$sort = $highestsortorder + 1;
	$col = $dbname;
	$dn = $displayname;
	$rm = $remind;
	$ca = $caps;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then, add a column to the table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `citations_" . $element['columnname'] . "` ADD COLUMN " . $dbname . " VARCHAR(50) DEFAULT NULL;");

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then add a column to the master table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `mcite_" . $element['columnname'] . "` ADD COLUMN " . $dbname . " VARCHAR(50) DEFAULT NULL;");

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

function nbt_toggle_citation_property_remind ( $columnid ) {

    // get the old column name and the form id

    $column = nbt_get_citation_property_for_propertyid ( $columnid );

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `citationscolumns` SET `remind`=:remind WHERE `id` = :cid");

	$stmt->bindParam(':cid', $cid);
	$stmt->bindParam(':remind', $rem);

	$cid = $columnid;

	if ( $column['remind'] == 0 ) {

	    $rem = 1;

	} else {

	    $rem = 0;

	}

	if ($stmt->execute()) {

	    return $rem;

	}

	$dbh = null;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_toggle_citation_property_forcecaps ( $columnid ) {

    // get the old column name and the form id

    $column = nbt_get_citation_property_for_propertyid ( $columnid );

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `citationscolumns` SET `caps`=:caps WHERE `id` = :cid");

	$stmt->bindParam(':cid', $cid);
	$stmt->bindParam(':caps', $cap);

	$cid = $columnid;

	if ( $column['caps'] == 0 ) {

	    $cap = 1;

	} else {

	    $cap = 0;

	}

	if ($stmt->execute()) {

	    return $cap;

	}

	$dbh = null;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_toggle_subelement_copy_from_prev ( $subelementid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `subelements` SET `copypreviousprompt`=IF(`copypreviousprompt`=1, 0, 1) WHERE `id` = :seid");

	$stmt->bindParam(':seid', $seid);

	$seid = $subelementid;

	if ($stmt->execute()) {

	    return "Changes saved";

	}

	$dbh = null;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

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

function nbt_get_all_citations_cols_for_formid ( $formid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `citationscolumns` WHERE `elementid` IN (SELECT `id` FROM `formelements` WHERE `formid` = :fid);");

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

function nbt_remove_table_data_row ( $tableid, $rowid, $sub_table = FALSE ) {

    if ( $sub_table ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $tableid );

    } else {

	$element = nbt_get_form_element_for_elementid ( $tableid );

    }


    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("DELETE FROM `tabledata_" . $subelement['dbname'] . "` WHERE id = :rowid;");

	} else {

	    $stmt = $dbh->prepare ("DELETE FROM `tabledata_" . $element['columnname'] . "` WHERE id = :rowid;");

	}


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

function nbt_update_extraction_table_data ($tableid, $rowid, $column, $newvalue, $sub_table = FALSE) {

    if ( $sub_table ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $tableid );

    } else {

	$element = nbt_get_form_element_for_elementid ( $tableid );

    }


    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("UPDATE `tabledata_" . $subelement['dbname'] . "` SET `" . $column . "` = :value WHERE id = :rowid;");

	} else {

	    $stmt = $dbh->prepare ("UPDATE `tabledata_" . $element['columnname'] . "` SET `" . $column . "` = :value WHERE id = :rowid;");

	}


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

function nbt_update_final ( $formid, $refsetid, $refid, $column, $newvalue ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `m_extractions_" . $formid . "` SET `" . $column . "` = :nv WHERE `refsetid` = :rsid AND `referenceid` = :rid LIMIT 1;");

	$stmt->bindParam(':rsid', $rsid);
	$stmt->bindParam(':rid', $rid);
	$stmt->bindParam(':nv', $nv);

	$rsid = $refsetid;
	$rid = $refid;
	$nv = $newvalue;

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

function nbt_update_extraction_mtable_data ($tableid, $rowid, $column, $newvalue, $sub_table = FALSE) {

    if ( $sub_table ) {

	$subelement = nbt_get_sub_element_for_subelementid ( $tableid );

    } else {

	$element = nbt_get_form_element_for_elementid ( $tableid );

    }


    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	if ( $sub_table ) {

	    $stmt = $dbh->prepare ("UPDATE `mtable_" . $subelement['dbname'] . "` SET `" . $column . "` = :value WHERE id = :rowid;");

	} else {

	    $stmt = $dbh->prepare ("UPDATE `mtable_" . $element['columnname'] . "` SET `" . $column . "` = :value WHERE id = :rowid;");

	}

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

function nbt_update_master_citation_property ( $section, $cid, $column, $value ) {

    $element = nbt_get_form_element_for_elementid ( $section );

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `mcite_" . $element['columnname'] . "` SET `" . $column . "` = :value WHERE id = :id LIMIT 1;");

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
	$stmt = $dbh->prepare("SELECT *, `assignments`.`id` AS `aid` FROM `referenceset_" . $refsetid . "`, `assignments`, `users`, `forms` WHERE `referenceset_". $refsetid ."`.`id` = `assignments`.`referenceid` AND `assignments`.`refsetid` = :rsid AND `assignments`.`userid` = `users`.`id` AND `assignments`.`formid` = `forms`.`id`");

	$stmt->bindParam(':rsid', $rsid);

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
	$assign = $_SESSION[INSTALL_HASH . '_nbt_userid'];
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

function nbtAssign ( $user, $form, $refset, $refids ) {

    $rids = explode(",", $refids);

    $query = "";

    if ( $form == "all" && $user == "all" ) {
	$forms = nbt_get_all_extraction_forms ();
	$users = nbt_get_all_users ();

	foreach ($forms as $fo) {
	    foreach ($users as $us) {
		foreach ($rids as $rid) {
		    $query .= "INSERT IGNORE INTO `assignments` (userid, assignerid, formid, refsetid, referenceid) VALUES (" . $us['id'] . ", :assigner, " . $fo['id'] . ", :refset, " . $rid . "); ";
		}
	    }
	}

    } else {

	if ( $form == "all" ) {

	    $forms = nbt_get_all_extraction_forms ();

	    foreach ($forms as $fo) {
		foreach ($rids as $rid) {
		    $query .= "INSERT IGNORE INTO `assignments` (userid, assignerid, formid, refsetid, referenceid) VALUES (:user, :assigner, " . $fo['id'] . ", :refset, " . $rid . "); ";
		}
	    }

	} else {
	    if ( $user == "all" ) {

		$users = nbt_get_all_users ();

		foreach ($users as $us) {
		    foreach ($rids as $rid) {
			$query .= "INSERT IGNORE INTO `assignments` (userid, assignerid, formid, refsetid, referenceid) VALUES (" . $us['id'] . ", :assigner, :form, :refset, " . $rid . "); ";
		    }
		}

	    } else {

		foreach ( $rids as $rid ) {
		    $query .= "INSERT IGNORE INTO `assignments` (userid, assignerid, formid, refsetid, referenceid) VALUES (:user, :assigner, :form, :refset, " . $rid . "); ";
		}

	    }
	}
    }

    try {
	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare($query);

	$stmt->bindParam(':assigner', $aid);
	$stmt->bindParam(':refset', $rsid);

	if ( $user != "all" ) {
	    $stmt->bindParam(':user', $uid);
	}

	if ( $form != "all" ) {
	    $stmt->bindParam(':form', $fid);
	}

	$uid = $user;
	$aid = $_SESSION[INSTALL_HASH . '_nbt_userid'];
	$fid = $form;
	$rsid = $refset;

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

function nbtUnAssign ( $user, $form, $refset, $refids ) {

    $rids = explode(",", $refids);

    $query = "";

    if ( $form == "all" && $user == "all" ) {
	$forms = nbt_get_all_extraction_forms ();
	$users = nbt_get_all_users ();

	foreach ($forms as $fo) {
	    foreach ($users as $us) {
		foreach ($rids as $rid) {
		    $query .= "DELETE FROM `assignments` WHERE userid = " . $us['id'] . " AND formid = " . $fo['id'] . " AND refsetid = :refset AND referenceid = " . $rid . " LIMIT 1; ";
		}
	    }
	}
    } else {

	if ($form == "all") {

	    $forms = nbt_get_all_extraction_forms ();

	    foreach ($forms as $fo) {
		foreach ($rids as $rid) {
		    $query .= "DELETE FROM `assignments` WHERE userid = :user AND formid = " . $fo['id'] . " AND refsetid = :refset AND referenceid = " . $rid . " LIMIT 1; ";
		}
	    }
	} else {
	    if ($user == "all") {

		$users = nbt_get_all_users ();

		foreach ($users as $us) {
		    foreach ($rids as $rid) {
			$query .= "DELETE FROM `assignments` WHERE userid = " . $us['id'] . " AND formid = :form AND refsetid = :refset AND referenceid = " . $rid . " LIMIT 1; ";
		    }
		}
	    } else {

		foreach ($rids as $rid) {
		    $query .= "DELETE FROM `assignments` WHERE userid = :user AND formid = :form AND refsetid = :refset AND referenceid = " . $rid . " LIMIT 1; ";
		}
	    }
	}

    }

    try {
	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare($query);

	$stmt->bindParam(':refset', $rsid);

	if ($user != "all") {
	    $stmt->bindParam(':user', $uid);
	}

	if ($form != "all") {
	    $stmt->bindParam(':form', $fid);
	}

	$uid = $user;
	$aid = $_SESSION[INSTALL_HASH . '_nbt_userid'];
	$fid = $form;
	$rsid = $refset;

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

function nbt_echo_display_name_and_codebook ( $displayname, $codebook ) {

    echo '<p style="font-weight: 800;">';

    echo $displayname;

    if ( $codebook != "" ) {
	
	$codebook = str_replace ("\n", "<br>", $codebook);

	echo ' <a href="#" onclick="event.preventDefault();$(this).parent().next(\'.nbtCodebook\').slideToggle(100);">(?)</a></p>';

	echo '<div class="nbtCodebook">';

	echo $codebook;

	echo "</div>";

    } else {

	echo "</p>";

    }

}

function nbt_add_sub_extraction ( $formid, $elementid, $displayname = NULL, $suffix = NULL, $codebook = NULL, $toggle = NULL ) {

    $formid = intval($formid);
    $elementid = intval($elementid);
    $suffix = nbt_remove_special($suffix);

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

    if ( ! is_null ($suffix) ) {
	// See if this can be made directly
	if ( ! nbt_table_exists ("sub_" . $suffix) ) {
	    $foundgoodcolumn = TRUE;
	} else {
	    $foundgoodcolumn = FALSE;
	    $counter = 1;
	    while($foundgoodcolumn == FALSE) {
		if ( ! nbt_table_exists ("sub_" . $suffix . "_"  . $counter) ) {
		    $columnname = "sub_" . $suffix . "_" . $counter;
		    $suffix = $suffix . "_" . $counter;
		    $foundgoodcolumn = TRUE;
		} else {
		    $counter++;
		}
	    }
	}
    } else {
	$foundgoodcolumn = FALSE;
	$counter = 1;
	while ( $foundgoodcolumn == FALSE ) {
	    if ( ! nbt_table_exists ("sub_" . $counter) ) {
		$columnname = "sub_" . $counter;
		$foundgoodcolumn = TRUE;
	    } else {
		$counter++;
	    }
	}

	$suffix = $counter;
    }

    // then make a new table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("CREATE TABLE `sub_" . $suffix . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `userid` int(11) NOT NULL, `sortorder` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

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
	$stmt = $dbh->prepare("CREATE TABLE `msub_" . $suffix . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

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
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, columnname, displayname, codebook, toggle) VALUES (:form, :sort, :type, :column, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "sub_extraction";
	$col = $suffix;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

	if ($stmt->execute()) {

	    $stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");
	    $stmt2->execute();

	    $result = $stmt2->fetchAll();

	    $dbh = null;

	    foreach ( $result as $row ) {

		$newid = $row['newid']; // This is the auto_increment-generated ID for the new row

	    }

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    return $newid;
    
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

function nbt_add_sub_open_text_field ( $elementid, $displayname = NULL, $dbname = NULL, $regex = NULL, $copypreviousprompt = 1, $codebook = NULL, $toggle = NULL ) {

    $elementid = intval($elementid);
    $dbname = nbt_remove_special($dbname);

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

    if ( is_null ($dbname) ) {
	
	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SHOW COLUMNS FROM `sub_" . $element['columnname'] . "` LIKE 'open_text_" . $counter . "';");

		$stmt->execute();

		$result = $stmt->fetchAll();

		if ( count ( $result ) == 0 ) {

		    $dbname = "open_text_" . $counter;

		    $foundgoodcolumn = TRUE;

		}

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	    $counter ++;

	}
	
    }


    // then insert a new element into the form elements table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO `subelements` (elementid, sortorder, type, dbname, displayname, regex, copypreviousprompt, codebook, toggle) VALUES (:element, :sort, :type, :dbname, :displayname, :regex, :copypreviousprompt, :codebook, :toggle);");

	$stmt->bindParam(':element', $eid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':dbname', $dbn);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':regex', $rx);
	$stmt->bindParam(':copypreviousprompt', $cpp);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$eid = $elementid;
	$sort = $highestsortorder + 1;
	$type = "open_text";
	$dbn = $dbname;
	$dn = $displayname;
	$rx = $regex;
	$cpp = $copypreviousprompt;
	$cb = $codebook;
	$tg = $toggle;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then, add a column to the extractions table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` ADD COLUMN " . $dbname . " varchar(200) DEFAULT NULL;");

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then add a column to the master table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` ADD COLUMN " . $dbname . " varchar(200) DEFAULT NULL;");

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

    // if it's a table_data, remove those tables

    if ( $subelement['type'] == "table_data" ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("DROP TABLE `tabledata_" . $dbname . "`;");

	    $stmt->execute();

	    $dbh = null;

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("DROP TABLE `mtable_" . $dbname . "`;");

	    $stmt->execute();

	    $dbh = null;

	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

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

    switch ( $subelement['type'] ) {
	case "open_text":
	    $dbtype = "varchar(200) DEFAULT NULL";
	    break;
	case "single_select":
	    $dbtype = "varchar(200) DEFAULT NULL";
	    break;
	case "date_selector":
	    $dbtype = "DATE DEFAULT NULL";
	    break;
	    
    }

    // Start a counter to see if everything saved properly

    $itworked = 0;

    // then alter the column in the extraction table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` CHANGE " . $subelement['dbname'] . " " . $newcolumnname . " " . $dbtype . ";");

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
	$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` CHANGE " . $subelement['dbname'] . " " . $newcolumnname . " " . $dbtype . ";");

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

function nbt_add_sub_single_select ( $elementid, $displayname = NULL, $dbname = NULL, $copypreviousprompt = 1, $codebook = NULL, $toggle = NULL ) {

    $elementid = intval($elementid);
    $dbname = nbt_remove_special($dbname);

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

    if ( is_null ( $dbname ) ) {

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SHOW COLUMNS FROM `sub_" . $element['columnname'] . "` LIKE 'single_select_" . $counter . "';");

		$stmt->execute();

		$result = $stmt->fetchAll();

		if ( count ( $result ) == 0 ) {

		    $dbname = "single_select_" . $counter;

		    $foundgoodcolumn = TRUE;

		}

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	    $counter ++;

	}

    }

    // then insert a new element into the form elements table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO `subelements` (elementid, sortorder, type, dbname, displayname, copypreviousprompt, codebook, toggle) VALUES (:element, :sort, :type, :column, :displayname, :copypreviousprompt, :codebook, :toggle);");

	$stmt->bindParam(':element', $eid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':copypreviousprompt', $cpp);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$eid = $elementid;
	$sort = $highestsortorder + 1;
	$type = "single_select";
	$col = $dbname;
	$dn = $displayname;
	$cpp = $copypreviousprompt;
	$cb = $codebook;
	$tg = $toggle;

	if ($stmt->execute()) {

	    $stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");
	    $stmt2->execute();

	    $result = $stmt2->fetchAll();

	    $dbh = null;

	    foreach ( $result as $row ) {

		$newid = $row['newid']; // This is the auto_increment-generated ID

	    }
	    
	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then, add a column to the extractions table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` ADD COLUMN " . $dbname . " varchar(50) DEFAULT NULL;");

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then add a column to the master table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` ADD COLUMN " . $dbname . " varchar(50) DEFAULT NULL;");

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    return $newid;

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

function nbt_add_sub_single_select_option ( $subelementid, $displayname = NULL, $dbname = NULL, $toggle = NULL ) {

    $subelementid = intval($subelementid);
    $dbname = nbt_remove_special($dbname);

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
	$stmt = $dbh->prepare ("INSERT INTO selectoptions (subelementid, sortorder, displayname, dbname, toggle) VALUES (:seid, :sort, :displayname, :dbname, :toggle);");

	$stmt->bindParam(':seid', $seid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':dbname', $dbn);
	$stmt->bindParam(':toggle', $tg);

	$seid = $subelementid;
	$sort = $highestsortorder + 1;
	$dn = $displayname;
	$dbn = $dbname;
	$tg = $toggle;

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

function nbt_add_sub_multi_select ( $elementid, $displayname = NULL, $dbname = NULL, $copypreviousprompt = 1, $codebook = NULL, $toggle = NULL ) {

    $elementid = intval($elementid);
    $dbname = nbt_remove_special($dbname);

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
	$stmt = $dbh->prepare ("INSERT INTO subelements (elementid, sortorder, type, dbname, displayname, copypreviousprompt, codebook, toggle) VALUES (:element, :sort, :type, :column, :displayname, :copypreviousprompt, :codebook, :toggle);");

	$stmt->bindParam(':element', $eid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':copypreviousprompt', $cpp);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$eid = $elementid;
	$sort = $highestsortorder + 1;
	$type = "multi_select";
	$col = "multi_select";
	$dn = $displayname;
	$cpp = $copypreviousprompt;
	$cb = $codebook;
	$tg = $toggle;

	if ($stmt->execute()) {
	    $stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");

	    $stmt2->execute();

	    $result = $stmt2->fetchAll();

	    foreach ( $result as $row ) {
		$newid = $row['newid'];
	    }
	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    return $newid;

}

function nbt_add_sub_table ( $elementid, $displayname = NULL, $suffix = NULL, $codebook = NULL, $toggle = NULL ) {

    $elementid = intval($elementid);
    $suffix = nbt_remove_special($suffix);

    $element = nbt_get_form_element_for_elementid ( $elementid );

    // get the highest sortorder value

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `subelements` WHERE `elementid` = :eid ORDER BY `sortorder` DESC LIMIT 1;");

	$stmt->bindParam(':eid', $eid);

	$eid = $element['id'];

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

    // find a good suffix for the table

    if ( ! is_null ($suffix) ) {
	// See if this can be made directly
	if ( ! nbt_table_exists ("tabledata_" . $suffix) ) {
	    $foundgoodcolumn = TRUE;
	} else {
	    $foundgoodcolumn = FALSE;
	    $counter = 1;
	    while($foundgoodcolumn == FALSE) {
		if ( ! nbt_table_exists ("tabledata_" . $suffix . "_"  . $counter) ) {
		    $columnname = "tabledata_" . $suffix . "_" . $counter;
		    $suffix = $suffix . "_" . $counter;
		    $foundgoodcolumn = TRUE;
		} else {
		    $counter++;
		}
	    }
	}
    } else {
	$foundgoodcolumn = FALSE;
	$counter = 1;
	while ( $foundgoodcolumn == FALSE ) {
	    if ( ! nbt_table_exists ("tabledata_" . $counter) ) {
		$columnname = "tabledata_" . $counter;
		$foundgoodcolumn = TRUE;
	    } else {
		$counter++;
	    }
	}

	$suffix = $counter;
    }

    // then make a new table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("CREATE TABLE `tabledata_" . $suffix . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `subextractionid` int(11) NOT NULL, `userid` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	if ($stmt->execute()) {

	    $dbh = null;

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then make the final table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("CREATE TABLE `mtable_" . $suffix . "` ( `id` int(11) NOT NULL AUTO_INCREMENT, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `subextractionid` int(11) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	if ($stmt->execute()) {

	    $dbh = null;

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then, add it into the subelements table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO subelements (elementid, sortorder, type, dbname, displayname, codebook, toggle) VALUES (:element, :sort, :type, :column, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':element', $eid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$eid = $element['id'];
	$sort = $highestsortorder + 1;
	$type = "table_data";
	$col = $suffix;
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

	if ($stmt->execute()) {

	    $stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");
	    $stmt2->execute();

	    $result = $stmt2->fetchAll();

	    $dbh = null;

	    foreach ( $result as $row ) {

		$newid = $row['newid']; // This is the auto_increment-generated ID

	    }
	    
	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    return $newid;

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

function nbt_add_sub_multi_select_option ( $subelementid, $displayname = NULL, $dbname = NULL, $toggle = NULL ) {

    $subelementid = intval($subelementid);
    $dbname = nbt_remove_special($dbname);

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

    if ( is_null ($dbname) ) {

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

		    $dbname = $counter;
		    
		    $foundgoodcolumn = TRUE;

		}

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	    $counter ++;

	}

    } else {

	$columnname = $subelement['dbname'] . "_" . $dbname;
    }

    // then insert a new option into the selectoptions table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO `selectoptions` (subelementid, sortorder, dbname, displayname, toggle) VALUES (:seid, :sort, :column, :displayname, :toggle);");

	$stmt->bindParam(':seid', $seid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':toggle', $tg);

	$seid = $subelementid;
	$sort = $highestsortorder + 1;
	$col = $dbname;
	$dn = $displayname;
	$tg = $toggle;

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

function nbt_add_sub_date_selector ( $elementid, $displayname = NULL, $dbname = NULL, $copypreviousprompt = 1, $codebook = NULL, $toggle = NULL ) {

    $elementid = intval($elementid);
    $dbname = nbt_remove_special($dbname);

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

    if ( is_null ($dbname) ) {

	$foundgoodcolumn = FALSE;

	$counter = 1;

	while ( $foundgoodcolumn == FALSE ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("SHOW COLUMNS FROM `sub_" . $element['columnname'] . "` LIKE 'date_" . $counter . "';");

		$stmt->execute();

		$result = $stmt->fetchAll();

		if ( count ( $result ) == 0 ) {

		    $dbname = "date_" . $counter;

		    $foundgoodcolumn = TRUE;

		}

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	    $counter ++;

	}

    }

    // then insert a new element into the form elements table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("INSERT INTO subelements (elementid, sortorder, type, dbname, displayname, copypreviousprompt, codebook, toggle) VALUES (:element, :sort, :type, :column, :displayname, :copypreviousprompt, :codebook, :toggle);");

	$stmt->bindParam(':element', $eid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':column', $col);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':copypreviousprompt', $cpp);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$eid = $elementid;
	$sort = $highestsortorder + 1;
	$type = "date_selector";
	$col = $dbname;
	$dn = $displayname;
	$cpp = $copypreviousprompt;
	$cb = $codebook;
	$tg = $toggle;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then, add a column to the extractions table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `sub_" . $element['columnname'] . "` ADD COLUMN " . $dbname . " DATE DEFAULT NULL;");

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // then add a column to the master table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("ALTER TABLE `msub_" . $element['columnname'] . "` ADD COLUMN " . $dbname . " DATE DEFAULT NULL;");

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

function nbt_make_new_refset_row ( $newname, $title, $authors, $year, $journal, $abstract ) { // Returns the id of the new refset

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("INSERT INTO referencesets (name, title, authors, year, journal, abstract) VALUES (:name, :title, :authors, :year, :journal, :abstract)");

	$stmt->bindParam(':name', $name);

	$stmt->bindParam(':title', $ti);
	$stmt->bindParam(':authors', $au);
	$stmt->bindParam(':year', $ye);
	$stmt->bindParam(':journal', $jo);
	$stmt->bindParam(':abstract', $ab);
	$name = $newname;
	$ti = $title + 2; // Have to add 2 to each one, because two columns are added to each table by Numbat
	$au = $authors + 2;
	$ye = $year + 2;
	$jo = $journal + 2;
	$ab = $abstract + 2;


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


	case "varchar20":

	    $sqltype = "varchar(20) DEFAULT NULL";

	    break;
	case "varchar50":

	    $sqltype = "varchar(50) DEFAULT NULL";

	    break;
	case "varchar100":
	    $sqltype = "varchar(100) DEFAULT NULL";
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

function nbt_insert_imported_extraction ( $formid, $refsetid, $usercolumn, $user, $referenceid_column, $selected_elements, $row, $separator, $status = 2 ) {

    // $status = 2 means that they're all imported as completed

    if ( ! ctype_space($row) && $row != '' ) {

	$dbcols = [];

	foreach ($selected_elements as $sele => $colno) {

	    $dbcols[] = $sele;
	    
	}

	$sqlcols = "`" . implode ( "`, `", $dbcols ) . "`";

	$sqlparams = ":" . implode ( ", :", $dbcols );

	$values = explode($separator, $row);

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("INSERT INTO `extractions_" . $formid . "` (" . $sqlcols . ", `refsetid`, `referenceid`, `userid`, `status`) VALUES (" . $sqlparams . ", :rsid, :rid, :uid, :sta)");

	    $counter = 0;
	    $colvars = [];

	    foreach ( $selected_elements as $sele => $colno) {
		
		$stmt->bindParam(':' . $sele, $colvars[$counter]);

		// This removes quotes if they're at the beginning and the end of a field

		$length = strlen ($values[$colno]);

		if ( ( substr ($values[$colno], $length-1, 1) == "\"" ) && ( substr ($values[$colno], 0, 1) == "\"" ) ) {

		    $values[$colno] = substr ( $values[$colno], 1, $length-2 );

		}

		$colvars[$counter] = $values[$colno];

		$counter++;
		
	    }

	    $stmt->bindParam(':rsid', $rsid);
	    $stmt->bindParam(':rid', $rid);
	    $stmt->bindParam(':uid', $uid);
	    $stmt->bindParam(':sta', $sta);

	    $rsid = $refsetid;
	    $rid = $values[$referenceid_column];

	    if ($usercolumn == "ns") {
		$uid = $user;
	    } else {
		$uid = nbt_get_userid_for_username ($values[$usercolumn]);

		if ( ! $uid ) {
		    $uid = $_SESSION[INSTALL_HASH . '_nbt_userid'];
		}
	    }

	    $sta = $status;

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
    
}

function nbt_insert_imported_sub_extraction ( $elementid, $refsetid, $usercolumn, $user, $referenceid_column, $selected_elements, $row, $separator ) {

    if ( ! ctype_space($row) && $row != '' ) {

	$element = nbt_get_form_element_for_elementid ($elementid);

	$dbcols = [];

	foreach ($selected_elements as $sele => $colno) {

	    $dbcols[] = $sele;
	    
	}

	$sqlcols = "`" . implode ( "`, `", $dbcols ) . "`";

	$sqlparams = ":" . implode ( ", :", $dbcols );

	$values = explode($separator, $row);

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("INSERT INTO `sub_" . $element['columnname'] . "` (" . $sqlcols . ", `refsetid`, `referenceid`, `userid`) VALUES (" . $sqlparams . ", :rsid, :rid, :uid)");

	    $counter = 0;
	    $colvars = [];

	    foreach ( $selected_elements as $sele => $colno) {
		
		$stmt->bindParam(':' . $sele, $colvars[$counter]);

		// This removes quotes if they're at the beginning and the end of a field

		$length = strlen ($values[$colno]);

		if ( ( substr ($values[$colno], $length-1, 1) == "\"" ) && ( substr ($values[$colno], 0, 1) == "\"" ) ) {

		    $values[$colno] = substr ( $values[$colno], 1, $length-2 );

		}

		$colvars[$counter] = $values[$colno];

		$counter++;
		
	    }

	    $stmt->bindParam(':rsid', $rsid);
	    $stmt->bindParam(':rid', $rid);
	    $stmt->bindParam(':uid', $uid);

	    $rsid = $refsetid;
	    $rid = $values[$referenceid_column];

	    if ($usercolumn == "ns") {
		$uid = $user;
	    } else {
		$uid = nbt_get_userid_for_username ($values[$usercolumn]);

		if ( ! $uid ) {
		    $uid = $_SESSION[INSTALL_HASH . '_nbt_userid'];
		}
	    }

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
    
}

function nbt_insert_imported_table_data ( $formid, $elementid, $refsetid, $usercolumn, $user, $referenceid_column, $selected_columns, $row, $separator, $sub_extraction = FALSE, $subextractionid_column = NULL ) {

    if ( ! ctype_space($row) && $row != '' ) {

	$dbcols = [];

	foreach ($selected_columns as $scol => $colno) {

	    $dbcols[] = $scol;
	    
	}

	$sqlcols = "`" . implode ( "`, `", $dbcols ) . "`";

	$sqlparams = ":" . implode ( ", :", $dbcols );

	$values = explode($separator, $row);

	if ( $sub_extraction ) {
	    
	    $subelement = nbt_get_sub_element_for_subelementid ( $elementid );

	    $suffix = $subelement['dbname'];
	    
	} else {

	    $element = nbt_get_form_element_for_elementid ($elementid);

	    $suffix = $element['columnname'];
	    
	}

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	    if ( $sub_extraction ) {
		$stmt = $dbh->prepare("INSERT INTO `tabledata_" . $suffix . "` (" . $sqlcols . ", `refsetid`, `referenceid`, `userid`, `subextractionid`) VALUES (" . $sqlparams . ", :rsid, :rid, :uid, :seid)");
	    } else {
		$stmt = $dbh->prepare("INSERT INTO `tabledata_" . $suffix . "` (" . $sqlcols . ", `refsetid`, `referenceid`, `userid`) VALUES (" . $sqlparams . ", :rsid, :rid, :uid)");
	    }
	    
	    $counter = 0;
	    $colvars = [];

	    foreach ( $selected_columns as $scol => $colno) {
		
		$stmt->bindParam(':' . $scol, $colvars[$counter]);

		// This removes quotes if they're at the beginning and the end of a field

		$length = strlen ($values[$colno]);

		if ( ( substr ($values[$colno], $length-1, 1) == "\"" ) && ( substr ($values[$colno], 0, 1) == "\"" ) ) {

		    $values[$colno] = substr ( $values[$colno], 1, $length-2 );

		}

		$colvars[$counter] = $values[$colno];

		$counter++;
		
	    }

	    $stmt->bindParam(':rsid', $rsid);
	    $stmt->bindParam(':rid', $rid);
	    $stmt->bindParam(':uid', $uid);

	    $rsid = $refsetid;
	    $rid = $values[$referenceid_column];

	    if ($usercolumn == "ns") {
		$uid = $user;
	    } else {
		$uid = nbt_get_userid_for_username ($values[$usercolumn]);

		if ( ! $uid ) {
		    $uid = $_SESSION[INSTALL_HASH . '_nbt_userid'];
		}
	    }

	    if ( $sub_extraction ) {
		$stmt->bindParam(':seid', $seid);
		$seid = $values[$subextractionid_column];
	    }

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
    
}

function nbt_insert_row_into_columns ( $refset, $columns, $row, $separator ) {

    if ( ! ctype_space($row) && $row != '' ) {

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

	switch ( $subelement['type'] ) {

	    case "multi_select":

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

		break;

	    case "table_data":

		// get the rows

		try {

		    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		    $stmt = $dbh->prepare("SELECT * FROM `tabledata_" . $subelement['dbname'] . "` WHERE `subextractionid` = :seid;");

		    $stmt->bindParam(':seid', $seid);

		    $seid = $originalid;

		    $stmt->execute();

		    $rows = $stmt->fetchAll();

		}

		catch (PDOException $e) {

		    echo $e->getMessage();

		}

		// get the columns

		$columns = nbt_get_all_columns_for_table_data ( $subelement['id'], TRUE );

		foreach ( $rows as $row ) {

		    // make a new row in the master table

		    try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare ("INSERT INTO `mtable_" . $subelement['dbname'] . "` (refsetid, referenceid, subextractionid) VALUES (:refset, :ref, :seid);");

			$stmt->bindParam(':refset', $rsid);
			$stmt->bindParam(':ref', $rid);
			$stmt->bindParam(':seid', $seid);

			$rsid = $refsetid;
			$rid = $refid;
			$seid = $newid;

			$stmt->execute();

			$stmt2 = $dbh->prepare("SELECT LAST_INSERT_ID() AS newid;");

			$stmt2->execute();

			$results = $stmt2->fetchAll();

			$dbh = null;

			foreach ( $results as $result ) {

			    $newid2 = $result['newid'];

			}

		    }

		    catch (PDOException $e) {

			echo $e->getMessage();

		    }

		    foreach ( $columns as $column ) {

			// copy the data from the extractions over

			try {

			    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			    $stmt = $dbh->prepare("UPDATE `mtable_" . $subelement['dbname'] . "` SET `" . $column['dbname'] . "` = :value WHERE id = :id LIMIT 1;");

			    $stmt->bindParam(':id', $nid);
			    $stmt->bindParam(':value', $val);

			    $nid = $newid2;
			    $val = $row[$column['dbname']];

			    $stmt->execute();

			}

			catch (PDOException $e) {

			    echo $e->getMessage();

			}


		    }

		}

		break;

	    case "open_text": // intentionally blank

	    case "date_selector": // intentionally blank

	    case "single_select":

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

		break;

	}

	if ( $subelement['type'] != "multi_select" ) {



	} else {



	}

    }

}

function nbt_remove_master_sub_extraction ( $elementid, $originalid ) {

    $itworked = 0;

    $element = nbt_get_form_element_for_elementid ( $elementid );

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("DELETE FROM `msub_" . $element['columnname'] . "` WHERE id = :original;");

	$stmt->bindParam(':original', $oid);

	$oid = $originalid;

	if ($stmt->execute()) {

	    $dbh = null;

	    $itworked++;

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // See if there's any tables in the subextraction

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare ("SELECT * FROM `subelements` WHERE `elementid` = :eid AND `type` = 'table_data';");

	$stmt->bindParam(':eid', $eid);

	$eid = $elementid;

	if ($stmt->execute()) {

	    $itworked++;

	}

	$subelements = $stmt->fetchAll();

	foreach ( $subelements as $subelement ) {

	    try {

		$dbh2 = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt2 = $dbh2->prepare ("DELETE FROM `mtable_" . $subelement['dbname'] . "` WHERE `subextractionid` = :oid;");

		$stmt2->bindParam(':oid', $oid);

		$oid = $originalid;

		$stmt2->execute();

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    if ( $itworked == 2 ) {
	return TRUE;
    } else {
	return FALSE;
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

function nbt_add_assignment_editor ( $formid, $elementid, $displayname = NULL, $codebook = NULL, $toggle = NULL ) {

    // Already Bobby Tables proof
    
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
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, displayname, codebook, toggle) VALUES (:form, :sort, :type, :displayname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "assignment_editor";
	$dn = $displayname;
	$cb = $codebook;
	$tg = $toggle;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_add_reference_data ( $formid, $elementid, $displayname = NULL, $refdata = NULL, $codebook = NULL, $toggle = NULL ) {

    $formid = intval($formid);
    $elementid = intval($elementid);

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
	$stmt = $dbh->prepare ("INSERT INTO formelements (formid, sortorder, type, displayname, columnname, codebook, toggle) VALUES (:form, :sort, :type, :displayname, :columnname, :codebook, :toggle);");

	$stmt->bindParam(':form', $fid);
	$stmt->bindParam(':sort', $sort);
	$stmt->bindParam(':type', $type);
	$stmt->bindParam(':displayname', $dn);
	$stmt->bindParam(':columnname', $cn);
	$stmt->bindParam(':codebook', $cb);
	$stmt->bindParam(':toggle', $tg);

	$fid = $formid;
	$sort = $element['sortorder'] + 1;
	$type = "reference_data";
	$dn = $displayname;
	$cn = $refdata;
	$cb = $codebook;
	$tg = $toggle;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_set_master_status ( $formid, $masterid, $newstatus ) {

    if ( $newstatus == 2 ) { // Special case: they're clicking "completed"

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("UPDATE `m_extractions_" . $formid . "` SET `timestamp_finished` = NOW() WHERE id = :fid and `timestamp_finished` IS NULL LIMIT 1;");

	    $stmt->bindParam(':fid', $fid);

	    $fid = $masterid;

	    if ( $stmt->execute() ) {

		$dbh = null;

	    } else {

		$dbh = null;

	    }


	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    }

    // And do this in any case

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

function nbt_search_multiples ( $refsetid, $query ) {

    $altquery = str_replace ("- ", "%", $query);

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

function nbt_get_all_assignments_for_refset_and_ref ( $refset, $ref ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `assignments` WHERE `refsetid` = :refset AND `referenceid` = :ref;");

	$stmt->bindParam(':refset', $rsid);
	$stmt->bindParam(':ref', $rid);

	$rsid = $refset;
	$rid = $ref;

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	return $result;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_get_master_extractions_for_refset_ref_and_form ( $refsetid, $refid, $formid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `m_extractions_" . $formid . "` WHERE `refsetid` = :refset AND `referenceid` = :ref ORDER BY id ASC;");

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

function nbt_get_all_citation_form_elements () {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `formelements` WHERE `type` = 'citations';");

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	return $result;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_get_all_citations_for_element_and_citationid ( $dbname, $refset, $citationid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT *, (SELECT `username` FROM `users` WHERE `users`.`id` = `citations_" . $dbname . "`.`userid`) as `username` FROM `citations_" . $dbname . "` WHERE `citationid` = :cid AND `refsetid` = :rsid;");

	$stmt->bindParam(':rsid', $rsid);
	$stmt->bindParam(':cid', $cid);

	$rsid = $refset;
	$cid = $citationid;

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	return $result;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_move_assignments_for_refset_fromref_toref ( $refset, $from_rid, $to_rid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `assignments` SET `referenceid` = :torid WHERE `refsetid` = :refset AND `referenceid` = :fromrid;");

	$stmt->bindParam(':torid', $trid);
	$stmt->bindParam(':fromrid', $frid);
	$stmt->bindParam(':refset', $rsid);

	$trid = $to_rid;
	$frid = $from_rid;
	$rsid = $refset;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_move_citations_for_element_db_fromref_toref ( $dbname, $refset, $from_rid, $to_rid ) {

    // First do the extractions

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `citations_" . $dbname . "` SET `citationid` = :torid WHERE `refsetid` = :refset AND `citationid` = :fromrid;");

	$stmt->bindParam(':torid', $trid);
	$stmt->bindParam(':fromrid', $frid);
	$stmt->bindParam(':refset', $rsid);

	$trid = $to_rid;
	$frid = $from_rid;
	$rsid = $refset;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // Then do the master copy

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `mcite_" . $dbname . "` SET `citationid` = :torid WHERE `refsetid` = :refset AND `citationid` = :fromrid;");

	$stmt->bindParam(':torid', $trid);
	$stmt->bindParam(':fromrid', $frid);
	$stmt->bindParam(':refset', $rsid);

	$trid = $to_rid;
	$frid = $from_rid;
	$rsid = $refset;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

}

function nbt_move_extractions_for_form_db_refset_fromref_toref ( $formid, $refset, $from_rid, $to_rid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `extractions_" . $formid . "` SET `referenceid` = :torid WHERE `refsetid` = :refset AND `referenceid` = :fromrid;");

	$stmt->bindParam(':torid', $trid);
	$stmt->bindParam(':fromrid', $frid);
	$stmt->bindParam(':refset', $rsid);

	$trid = $to_rid;
	$frid = $from_rid;
	$rsid = $refset;

	if ( $stmt->execute() ) {
	    echo "UPDATE `extractions_" . $formid . "` SET `referenceid` = " . $to_rid . " WHERE `refsetid` = " . $refset . " AND `referenceid` = " . $from_rid . ";";
	}


    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // Then get the elements not kept in the main
    // extraction table

    $elements = nbt_get_elements_for_formid ( $formid );

    foreach ( $elements as $element ) {

	// Move the citations

	if ( $element['type'] == "citation" ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `citations_" . $element['columnname'] . "` SET `referenceid` = :torid WHERE `refsetid` = :refset AND `referenceid` = :fromrid;");

		$stmt->bindParam(':torid', $trid);
		$stmt->bindParam(':fromrid', $frid);
		$stmt->bindParam(':refset', $rsid);

		$trid = $to_rid;
		$frid = $from_rid;
		$rsid = $refset;

		if ($stmt->execute()) {
		    echo "Moved citations";
		}

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	}

	// Move the tables

	if ( $element['type'] == "table_data" ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `tabledata_" . $element['columnname'] . "` SET `referenceid` = :torid WHERE `refsetid` = :refset AND `referenceid` = :fromrid;");

		$stmt->bindParam(':torid', $trid);
		$stmt->bindParam(':fromrid', $frid);
		$stmt->bindParam(':refset', $rsid);

		$trid = $to_rid;
		$frid = $from_rid;
		$rsid = $refset;

		if ($stmt->execute()) {
		    echo "Moved table";
		}

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	}

	// Move the sub-extractions

	if ( $element['type'] == "sub_extraction" ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `sub_" . $element['columnname'] . "` SET `referenceid` = :torid WHERE `refsetid` = :refset AND `referenceid` = :fromrid;");

		$stmt->bindParam(':torid', $trid);
		$stmt->bindParam(':fromrid', $frid);
		$stmt->bindParam(':refset', $rsid);

		$trid = $to_rid;
		$frid = $from_rid;
		$rsid = $refset;

		if ($stmt->execute()) {
		    echo "Moved sub extraction";
		}

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	}

    }

}

function nbt_move_master_for_form_db_refset_fromref_toref ( $formid, $refset, $from_rid, $to_rid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `m_extractions_" . $formid . "` SET `referenceid` = :torid WHERE `refsetid` = :refset AND `referenceid` = :fromrid;");

	$stmt->bindParam(':torid', $trid);
	$stmt->bindParam(':fromrid', $frid);
	$stmt->bindParam(':refset', $rsid);

	$trid = $to_rid;
	$frid = $from_rid;
	$rsid = $refset;

	$stmt->execute();

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // Then get the elements not kept in the main
    // extraction table

    $elements = nbt_get_elements_for_formid ( $formid );

    foreach ( $elements as $element ) {

	// Move the citations

	if ( $element['type'] == "citation" ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `mcite_" . $element['columnname'] . "` SET `referenceid` = :torid WHERE `refsetid` = :refset AND `referenceid` = :fromrid;");

		$stmt->bindParam(':torid', $trid);
		$stmt->bindParam(':fromrid', $frid);
		$stmt->bindParam(':refset', $rsid);

		$trid = $to_rid;
		$frid = $from_rid;
		$rsid = $refset;

		$stmt->execute();

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	}

	// Move the tables

	if ( $element['type'] == "table_data" ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `mtable_" . $element['columnname'] . "` SET `referenceid` = :torid WHERE `refsetid` = :refset AND `referenceid` = :fromrid;");

		$stmt->bindParam(':torid', $trid);
		$stmt->bindParam(':fromrid', $frid);
		$stmt->bindParam(':refset', $rsid);

		$trid = $to_rid;
		$frid = $from_rid;
		$rsid = $refset;

		$stmt->execute();

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	}

	// Move the sub-extractions

	if ( $element['type'] == "sub_extraction" ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("UPDATE `msub_" . $element['columnname'] . "` SET `referenceid` = :torid WHERE `refsetid` = :refset AND `referenceid` = :fromrid;");

		$stmt->bindParam(':torid', $trid);
		$stmt->bindParam(':fromrid', $frid);
		$stmt->bindParam(':refset', $rsid);

		$trid = $to_rid;
		$frid = $from_rid;
		$rsid = $refset;

		$stmt->execute();

	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }

	}

    }

}

function nbtGetCitationPropertyReminders ( $citations, $refset, $referenceid, $citationid, $columnname ) {

    $userid = $_SESSION[INSTALL_HASH . '_nbt_userid'];

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `citations_" . $citations . "` WHERE `refsetid` = :rsid AND `citationid` = :cid AND `userid` = :uid AND `" . $columnname . "` != '' AND `referenceid` != :refid GROUP BY `" . $columnname . "`");

	$stmt->bindParam(':rsid', $rsid);
	$stmt->bindParam(':refid', $rid);
	$stmt->bindParam(':cid', $cid);
	$stmt->bindParam(':uid', $uid);

	$rsid = $refset;
	$rid = $referenceid;
	$cid = $citationid;
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

function nbt_get_all_reconciled_references_for_refset_and_form ( $refsetid, $formid ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `referenceset_" . $refsetid . "` WHERE `id` IN (SELECT `referenceid` FROM `m_extractions_" . $formid . "` WHERE `refsetid` = :refset AND `status` = 2) ORDER BY id ASC;");

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

function nbt_get_all_citations_for_refset ( $elementid, $refsetid ) {

    $element = nbt_get_form_element_for_elementid ( $elementid );

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `citations_" . $element['columnname'] . "` WHERE refsetid = :refset ORDER BY id ASC;");

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

function nbt_get_all_reconciled_citations_for_refset ( $elementid, $refsetid ) {

    $element = nbt_get_form_element_for_elementid ( $elementid );

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM `mcite_" . $element['columnname'] . "` WHERE refsetid = :refset ORDER BY id ASC;");

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

function nbt_get_unique_entries_for_prev_select ( $elementid, $refsetid, $extractionid ) {

    if ( is_numeric ( $elementid ) && is_numeric ( $refsetid ) ) {

	$element = nbt_get_form_element_for_elementid ( $elementid );

	$formid = $element['formid'];

	$columnname = $element['columnname'];

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("SELECT `" . $columnname . "` FROM `extractions_" . $formid . "` WHERE `refsetid` = :refset AND `id` != :exid AND `" . $columnname . "` IS NOT NULL AND `" . $columnname . "` != '' GROUP BY `" . $columnname . "` ORDER BY `" . $columnname . "` ASC;");

	    $stmt->bindParam(':refset', $rsid);
	    $stmt->bindParam(':exid', $exid);

	    $rsid = $refsetid;
	    $exid = $extractionid;

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

function nbt_get_unique_values_for_refset_column ( $refsetid, $column ) {

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT DISTINCT(" . $column . ") FROM `referenceset_" . $refsetid ."` ORDER BY `" . $column . "`;");

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	return $result;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }
    
}

function nbt_get_referenceids_for_refset_column_and_value ( $refsetid, $column, $value ) {
    
    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT `id` FROM `referenceset_" . $refsetid ."` WHERE `" . $column . "` = :value;");

	$stmt->bindParam(':value', $val);

	$val = $value;

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	return $result;

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }
    
}

function nbt_get_k_random_referenceids_for_refset ( $refsetid, $k, $n, $crit, $comp, $form ) {

    if ( $form != "ns") {
	
	$refsetid = intval($refsetid);
	$k = intval($k);
	$n = intval($n);
	$form = intval($form);

	switch ( $comp ) {

	    case "exactly":
		$comp = "=";
		break;

	    case "fewerthan":
		$comp = "<";
		break;

	    case "morethan":
		$comp = ">";
		break;

	    default:
		$comp = "";
		break;
	}

	switch ( $crit ) {

	    case "":
		
		try {

		    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		    $stmt = $dbh->prepare("SELECT `id` FROM `referenceset_" . $refsetid ."` ORDER BY RAND() LIMIT " . $k . ";");

		    $stmt->execute();

		    $result = $stmt->fetchAll();

		    $dbh = null;

		    return $result;

		}

		catch (PDOException $e) {

		    echo $e->getMessage();

		}
		
		break;

	    case "Assigned":

		try {

		    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		    $stmt = $dbh->prepare("SELECT id FROM `referenceset_" . $refsetid . "` WHERE (SELECT COUNT(DISTINCT(`userid`)) FROM assignments WHERE refsetid = " . $refsetid . " AND formid = " . $form . " AND assignments.referenceid = referenceset_" . $refsetid . ".id) " . $comp . " " . $n . " ORDER BY RAND() LIMIT " . $k . ";");

		    $stmt->execute();

		    $result = $stmt->fetchAll();

		    $dbh = null;

		    return $result;

		}

		catch (PDOException $e) {

		    echo $e->getMessage();

		}
		
		break;

	    case "Extracted":

		try {

		    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		    $stmt = $dbh->prepare("SELECT id FROM `referenceset_" . $refsetid . "` WHERE (SELECT COUNT(*) FROM extractions_" . $form . " WHERE status = 2 AND refsetid = " . $refsetid . " AND extractions_" . $form . ".referenceid = referenceset_" . $refsetid . ".id) " . $comp . " " . $n . " ORDER BY RAND() LIMIT " . $k . ";");

		    $stmt->execute();

		    $result = $stmt->fetchAll();

		    $dbh = null;

		    return $result;

		}

		catch (PDOException $e) {

		    echo $e->getMessage();

		}
		
		break;

	    case "Final":

		try {

		    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		    $stmt = $dbh->prepare("SELECT id FROM `referenceset_" . $refsetid . "` WHERE (SELECT COUNT(*) FROM m_extractions_" . $form . " WHERE status = 2 AND refsetid = " . $refsetid . " AND m_extractions_" . $form . ".referenceid = referenceset_" . $refsetid . ".id) " . $comp . " " . $n . " ORDER BY RAND() LIMIT " . $k . ";");

		    $stmt->execute();

		    $result = $stmt->fetchAll();

		    $dbh = null;

		    return $result;

		}

		catch (PDOException $e) {

		    echo $e->getMessage();

		}
		
		break;
		
	}
	
    }

}

function nbt_get_k_random_referenceids_for_refset_by_user ( $refsetid, $k, $form, $yn, $user ) {

    if ( $form != "ns") {
	
	$refsetid = intval($refsetid);
	$k = intval($k);
	$form = intval($form);
	$user = intval($user);

	switch ( $yn ) {

	    case "alreadyassigned":
		$yn = "IS NOT NULL";
		break;

	    case "notalreadyassigned":
		$yn = "IS NULL";
		break;

	    default:
		$yn = "";
		break;
	}

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("SELECT id FROM `referenceset_" . $refsetid . "` WHERE (SELECT id FROM assignments WHERE refsetid = " . $refsetid . " AND formid = " . $form . " AND userid = " . $user . " AND assignments.referenceid = referenceset_" . $refsetid . ".id) " . $yn . " ORDER BY RAND() LIMIT " . $k . ";");

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

function nbt_get_k_random_referenceids_for_refset_by_user_and_users ( $refsetid, $k, $form, $yn, $user, $comp, $n ) {

    switch ( $comp ) {

	case "exactly":
	    $comp = "=";
	    break;

	case "fewerthan":
	    $comp = "<";
	    break;

	case "morethan":
	    $comp = ">";
	    break;

	default:
	    $comp = "";
	    break;
	    
    }

    if ( $form != "ns") {
	
	$refsetid = intval($refsetid);
	$k = intval($k);
	$form = intval($form);
	$user = intval($user);
	$n = intval($n);

	switch ( $yn ) {

	    case "alreadyassigned":
		$yn = "IS NOT NULL";
		break;

	    case "notalreadyassigned":
		$yn = "IS NULL";
		break;

	    default:
		$yn = "";
		break;
	}

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("SELECT id FROM `referenceset_" . $refsetid . "` WHERE (SELECT id FROM assignments WHERE refsetid = " . $refsetid . " AND formid = " . $form . " AND userid = " . $user . " AND assignments.referenceid = referenceset_" . $refsetid . ".id) " . $yn . " AND (SELECT COUNT(*) FROM assignments WHERE refsetid = " . $refsetid . " AND formid = " . $form . " AND userid != " . $user . " AND assignments.referenceid = `referenceset_" . $refsetid . "`.`id`) " . $comp . " " . $n . "  ORDER BY RAND() LIMIT " . $k . ";");

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

function get_incomplete_assignments_for_form_and_refset ( $formid, $refsetid ) {

    $formid = intval($formid);

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT * FROM assignments WHERE refsetid = :rsid AND formid = :fid AND referenceid NOT IN (SELECT `referenceid` FROM `extractions_" . $formid . "` WHERE extractions_" . $formid . ".refsetid = assignments.refsetid AND `status` = 2 AND extractions_" . $formid . ".userid = assignments.userid)");

	$stmt->bindParam(':fid', $fid);
	$stmt->bindParam(':rsid', $rsid);

	$fid = $formid;
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

function nbt_get_times_for_extraction ( $formid, $refsetid, $refid, $userid ) {

    $formid = intval($formid);

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`timestamp_started`) as `time_started`, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`timestamp_finished`) as `time_finished` FROM `extractions_" . $formid . "` WHERE `refsetid` = :rsid AND `referenceid` = :rid AND `userid` = :uid");

	$stmt->bindParam(':rsid', $rsid);
	$stmt->bindParam(':rid', $rid);
	$stmt->bindParam(':uid', $uid);

	$rsid = $refsetid;
	$rid = $refid;
	$uid = $userid;

	if ($stmt->execute()) {

	    $result = $stmt->fetchAll();

	    return $result[0];

	    $dbh = null;

	} else {

	    return FALSE;

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }
    
}

function nbt_restart_extraction_timer ( $formid, $refsetid, $refid, $userid ) {

    $formid = intval($formid);

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `extractions_" . $formid . "` SET `timestamp_started` = NOW(), `timestamp_finished` = NULL WHERE `refsetid` = :rsid AND `referenceid` = :rid AND `userid` = :uid");

	$stmt->bindParam(':rsid', $rsid);
	$stmt->bindParam(':rid', $rid);
	$stmt->bindParam(':uid', $uid);

	$rsid = $refsetid;
	$rid = $refid;
	$uid = $userid;

	if ($stmt->execute()) {

	    return TRUE;

	} else {

	    return FALSE;

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }
    
}

function nbt_clear_finished_extraction_timer ( $formid, $refsetid, $refid, $userid ) {

    $formid = intval($formid);

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("UPDATE `extractions_" . $formid . "` SET `timestamp_finished` = NULL WHERE `refsetid` = :rsid AND `referenceid` = :rid AND `userid` = :uid");

	$stmt->bindParam(':rsid', $rsid);
	$stmt->bindParam(':rid', $rid);
	$stmt->bindParam(':uid', $uid);

	$rsid = $refsetid;
	$rid = $refid;
	$uid = $userid;

	if ($stmt->execute()) {

	    return TRUE;

	} else {

	    return FALSE;

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }
    
}

function nbt_table_exists ($tablename) {

    $tablename = nbt_remove_special ($tablename);

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SHOW TABLES LIKE '" . $tablename . "';");

	$stmt->execute();

	$result = $stmt->fetchAll();

	if ( count ( $result ) == 0 ) {

	    return FALSE;

	} else {

	    return TRUE;

	}

    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }
    
}

function nbt_remove_special ($original) {

    if ( ! is_null ( $original ) ) {

	$new = preg_replace("/[^A-Za-z0-9_]+/", "_", $original);
	$new = preg_replace("/__/", "_", $new);
	$new = preg_replace("/^_/", "", $new);
	$new = preg_replace("/_$/", "", $new);

	return $new;

    } else {
	return NULL;
    }

}

?>
