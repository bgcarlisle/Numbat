<h2>Installing Numbat</h2>
<div class="nbtSubExtraction"><?php

// delete config.php

if ( file_exists ( $_POST['abs_path'] . "config.php" ) ) {

	unlink ( $_POST['abs_path'] . "config.php" );

}

// write new config.php

$configphp = array ();

array_push ( $configphp, "<?php\n" );
array_push ( $configphp, "\n" );
array_push ( $configphp, "define('DB_USER', '" . $_POST['dbusername'] . "');\n" );
array_push ( $configphp, "define('DB_PASS', '" . $_POST['dbpassword'] . "');\n" );
array_push ( $configphp, "define('DB_NAME', '" . $_POST['dbname'] . "');\n" );
array_push ( $configphp, "define('DB_HOST', '" . $_POST['dbhost'] . "');\n" );
array_push ( $configphp, "define('ABS_PATH', '" . $_POST['abs_path'] . "');\n" );
array_push ( $configphp, "define('SITE_URL', '" . $_POST['site_url'] . "');\n" );
array_push ( $configphp, "\n" );
array_push ( $configphp, "include_once (ABS_PATH . \"functions.php\");\n" );
array_push ( $configphp, "\n" );
array_push ( $configphp, "?>" );

if ( file_put_contents ( $_POST['abs_path'] . "config.php", $configphp ) ) {

	?><p>Configuration file written &#x2713;</p><?php

} else {

	?><p>Error writing configuration file</p><?php

}

// add new blank numbat database schema

try { // assignments table

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DROP TABLE IF EXISTS `assignments`; CREATE TABLE `assignments` ( `id` int(11) NOT NULL AUTO_INCREMENT, `whenassigned` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `userid` int(11) NOT NULL, `assignerid` int(11) NOT NULL, `formid` int(11) NOT NULL, `refsetid` int(11) NOT NULL, `referenceid` int(11) NOT NULL, `hidden` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`), UNIQUE KEY `assign_once` (`userid`,`formid`,`refsetid`,`referenceid`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	if ( $stmt->execute() ) {

		?><p>Assignments table created &#x2713;</p><?php

	} else {

		?><p>Error making assignments table</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

try { // citations columns table

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DROP TABLE IF EXISTS `citationscolumns`; CREATE TABLE `citationscolumns` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `elementid` int(11) DEFAULT NULL, `displayname` varchar(200) DEFAULT NULL, `dbname` varchar(50) DEFAULT NULL, `remind` int(11) DEFAULT '0', `caps` int(11) DEFAULT '0', `sortorder` int(11) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

	if ( $stmt->execute() ) {

		?><p>Citations metadata table created &#x2713;</p><?php

	} else {

		?><p>Error making citations metadata table</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

try { // form elements table

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DROP TABLE IF EXISTS `formelements`; CREATE TABLE `formelements` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `formid` int(11) DEFAULT NULL, `sortorder` int(11) DEFAULT NULL, `type` varchar(20) DEFAULT NULL, `columnname` varchar(500) DEFAULT NULL, `displayname` varchar(200) DEFAULT NULL, `codebook` varchar(2000) DEFAULT NULL, `toggle` varchar(50) DEFAULT NULL, `regex` varchar(500) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

	if ( $stmt->execute() ) {

		?><p>Form elements table created &#x2713;</p><?php

	} else {

		?><p>Error making form elements table</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

try { // forms table

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DROP TABLE IF EXISTS `forms`; CREATE TABLE `forms` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `name` varchar(500) DEFAULT NULL, `description` varchar(1000) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

	if ( $stmt->execute() ) {

		?><p>Forms table created &#x2713;</p><?php

	} else {

		?><p>Error making forms table</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

try { // reference sets table

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DROP TABLE IF EXISTS `referencesets`; CREATE TABLE `referencesets` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(200) NOT NULL DEFAULT '', `title` int(11) DEFAULT NULL, `authors` int(11) DEFAULT NULL, `year` int(11) DEFAULT NULL, `journal` int(11) DEFAULT NULL, `abstract` int(11) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	if ( $stmt->execute() ) {

		?><p>Reference sets table created &#x2713;</p><?php

	} else {

		?><p>Error making reference sets table</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

try { // selection options table

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DROP TABLE IF EXISTS `selectoptions`; CREATE TABLE `selectoptions` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `elementid` int(11) DEFAULT NULL, `subelementid` int(11) DEFAULT NULL, `displayname` varchar(200) DEFAULT NULL, `dbname` varchar(50) DEFAULT NULL, `toggle` varchar(50) DEFAULT NULL, `sortorder` int(11) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

	if ( $stmt->execute() ) {

		?><p>Selection options table created &#x2713;</p><?php

	} else {

		?><p>Error making selection options table</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

try { // settings table

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DROP TABLE IF EXISTS `settings`; CREATE TABLE `settings` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `key` varchar(100) DEFAULT NULL, `value` varchar(500) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

	if ( $stmt->execute() ) {

		?><p>Numbat settings table created &#x2713;</p><?php

	} else {

		?><p>Error making Numbat settings table</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

try { // sub elements table

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DROP TABLE IF EXISTS `subelements`; CREATE TABLE `subelements` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `elementid` int(11) DEFAULT NULL, `sortorder` int(11) DEFAULT NULL, `type` varchar(20) DEFAULT NULL, `dbname` varchar(50) DEFAULT NULL, `displayname` varchar(200) DEFAULT NULL, `codebook` varchar(2000) DEFAULT NULL, `toggle` varchar(50) DEFAULT NULL, `regex` varchar(500) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

	if ( $stmt->execute() ) {

		?><p>Sub extraction elements table created &#x2713;</p><?php

	} else {

		?><p>Error making sub extraction elements table</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

try { // tabledata columns table

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DROP TABLE IF EXISTS `tabledatacolumns`; CREATE TABLE `tabledatacolumns` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `elementid` int(11) DEFAULT NULL, `subelementid` int(11) DEFAULT NULL, `displayname` varchar(200) DEFAULT NULL, `dbname` varchar(50) DEFAULT NULL, `sortorder` int(11) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

	if ( $stmt->execute() ) {

		?><p>Tabled data columns table created &#x2713;</p><?php

	} else {

		?><p>Error making tabled data columns table</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

try { // users table

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("DROP TABLE IF EXISTS `users`; CREATE TABLE `users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(30) NOT NULL, `password` varchar(64) NOT NULL, `salt` varchar(3) NOT NULL, `email` varchar(300) NOT NULL, `privileges` int(11) DEFAULT '0', `emailverify` varchar(10) NOT NULL, `lastlogin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `passwordchangecode` varchar(10) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	if ( $stmt->execute() ) {

		?><p>Users table created &#x2713;</p><?php

	} else {

		?><p>Error making users table</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}


// insert admin user

try {

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("INSERT INTO `users` (`username`, `password`, `salt`, `email`, `emailverify`, `privileges`) VALUES (:username, :password, :salt, :email, :verification, :privileges);");

	$stmt->bindParam(':username', $theuser);
	$stmt->bindParam(':password', $thepass);
	$stmt->bindParam(':salt', $salt);
	$stmt->bindParam(':email', $theemail);
	$stmt->bindParam(':verification', $verification);
	$stmt->bindParam(':privileges', $priv);

	$theuser = $_POST['nbt_username'];

	$theemail = $_POST['nbt_email'];

	$string = md5(uniqid(rand(), true));
	$salt = substr($string, 0, 3);

	$verification = 0;

	$priv = 4;

	$thepass = hash('sha256', $salt . $_POST['nbt_password']);

	if ( $stmt->execute() ) {

		?><p>Admin user created &#x2713;</p><?php

	} else {

		?><p>Error making admin user</p><?php

	}

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

	return FALSE;

}

// add numbat settings (project name and admin email)

try {

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("INSERT INTO `settings` (`key`, `value`) VALUES (:key, :value);");

	$stmt->bindParam(':key', $key);
	$stmt->bindParam(':value', $val);

	$key = "project_name";
	$val = $_POST['nbt_projname'];

	$stmt->execute();

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

try {

	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("INSERT INTO `settings` (`key`, `value`) VALUES (:key, :value);");

	$stmt->bindParam(':key', $key);
	$stmt->bindParam(':value', $val);

	$key = "admin_email";
	$val = $_POST['nbt_email'];

	$stmt->execute();

	$dbh = null;

}

catch (PDOException $e) {

	echo $e->getMessage();

}

// delete the installer files and directory

unlink ( $_POST['abs_path'] . "install/install.php");
unlink ( $_POST['abs_path'] . "install/testmysql.php");
unlink ( $_POST['abs_path'] . "install/writeconfig.php");

if ( rmdir ( $_POST['abs_path'] . "install/") ) {

	?><p>Installation directory deleted &#x2713;</p><?php

}

// make a place to save backup mysql dumps

if ( ! file_exists ( $_POST['abs_path'] . "backup/dumpfiles/" ) ) {

	if ( mkdir ( $_POST['abs_path'] . "backup/dumpfiles/" ) ) {

		?><p>Backup directory created &#x2713;</p><?php

	} else {

		?><p>Error creating backup directory</p><?php

	}

}

?></div>
<p>Numbat has been installed! You may now <a href="<?php echo $_POST['site_url']; ?>">log in</a>.</p>
