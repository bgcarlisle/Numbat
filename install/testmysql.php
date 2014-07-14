<?php

try {
		
	$dbh = new PDO('mysql:dbname=' . $_POST['dbname'] . ';host=' . $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SELECT version();");
	
	if ( $stmt->execute() ) {
		
		echo "Successful connexion to MySQL database";
		
	} else {
		
		echo "Connexion failure";
		
	}
	
	$dbh = null;
	
}

catch (PDOException $e) {
	
	echo $e->getMessage();
	
}

?>