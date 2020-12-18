<?php

include_once ("./config.php");

include_once ("./header.php");

echo '<div class="nbtContentPanel nbtGreyGradient">';

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    echo '<h2>Database migrations</h2>';

    // 1. Add columns for reference sets meta-data

    echo '<h3>Reference set meta-data</h3>';

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

    if ( ! check_for_referencesets_column ("title") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `referencesets` ADD COLUMN `title` INT(11) NULL DEFAULT NULL AFTER `name`;");

	    if ($stmt->execute()) {
		echo "<p>Reference sets table updated with title column</p>";
	    } else {
		echo "<p>Error updating reference sets table with title column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

    } else {
	
	echo "<p>The reference sets table already has a title column</p>";
	
    }

    if ( ! check_for_referencesets_column ("authors") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `referencesets` ADD COLUMN `authors` INT(11) NULL DEFAULT NULL AFTER `title`;");

	    if ($stmt->execute()) {
		echo "<p>Reference sets table updated with authors column</p>";
	    } else {
		echo "<p>Error updating reference sets table with authors column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

    } else {
	
	echo "<p>The reference sets table already has an authors column</p>";
	
    }

    if ( ! check_for_referencesets_column ("year") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `referencesets` ADD COLUMN `year` INT(11) NULL DEFAULT NULL AFTER `authors`;");

	    if ($stmt->execute()) {
		echo "<p>Reference sets table updated with year column</p>";
	    } else {
		echo "<p>Error updating reference sets table with year column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

    } else {
	
	echo "<p>The reference sets table already has a year column</p>";
	
    }

    if ( ! check_for_referencesets_column ("journal") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `referencesets` ADD COLUMN `journal` INT(11) NULL DEFAULT NULL AFTER `year`;");

	    if ($stmt->execute()) {
		echo "<p>Reference sets table updated with journal column</p>";
	    } else {
		echo "<p>Error updating reference sets table with journal column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

    } else {
	
	echo "<p>The reference sets table already has a journal column</p>";
	
    }

    if ( ! check_for_referencesets_column ("abstract") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `referencesets` ADD COLUMN `abstract` INT(11) NULL DEFAULT NULL AFTER `journal`;");

	    if ($stmt->execute()) {
		echo "<p>Reference sets table updated with abstract column</p>";
	    } else {
		echo "<p>Error updating reference sets table with abstract column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

    } else {
	
	echo "<p>The reference sets table already has an abstract column</p>";
	
    }

    // 2. Make `columnname` 500 characters long

    echo '<h3>Make columnname in form elements table longer</h3>';

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SHOW COLUMNS FROM `formelements` LIKE 'columnname';");

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	foreach ($result as $row) {

	    if ( $row[1] != "varchar(500)") {

		try {

		    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		    $stmt = $dbh->prepare("ALTER TABLE `formelements` CHANGE COLUMN `columnname` `columnname` VARCHAR(500) NULL DEFAULT NULL;");

		    if ($stmt->execute()) {
			echo "<p>Form elements table columnname column updated to 500 characters in length</p>";
		    } else {
			echo "<p>Error updating formelements table</p>";
		    }

		    $dbh = null;
		    
		}

		catch (PDOException $e) {

		    echo $e->getMessage();

		}
		
	    } else {

		echo "<p>The form elements table columname column is already 500 characters long</p>";
		
	    }
	    
	}
	
    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // 3. Add a `timestamp_finished` column to every extraction table

    echo '<h3>Add timestamp_finished to every extraction table</h3>';

    $forms = nbt_get_all_extraction_forms ();

    foreach ($forms as $form) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("SHOW COLUMNS FROM `extractions_" . $form['id'] . "` LIKE 'timestamp_finished';");

	    $stmt->execute();

	    $result = $stmt->fetchAll();

	    $dbh = null;

	    if ( count ($result) == 0 ) {

		try {

		    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		    $stmt = $dbh->prepare("ALTER TABLE `extractions_" . $form['id'] . "` ADD COLUMN `timestamp_finished` TIMESTAMP NULL DEFAULT NULL AFTER `timestamp_started`;");

		    if ($stmt->execute()) {
			echo "<p>The form called '" . $form['name'] . "' has been updated with a timestamp_finished column</p>";
		    } else {
			echo "<p>Error updating table extractions_" . $form['id'] . " with a timestamp_finished column</p>";
		    }

		    $dbh = null;
		    
		}

		catch (PDOException $e) {

		    echo $e->getMessage();

		}
		
	    } else {

		echo "<p>The form called '" . $form['name'] . "' already has a timestamp_finished column</p>";
		
	    }
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

    }

    // Add a `timestamp_finished` column to every final table


    foreach ($forms as $form) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("SHOW COLUMNS FROM `m_extractions_" . $form['id'] . "` LIKE 'timestamp_finished';");

	    $stmt->execute();

	    $result = $stmt->fetchAll();

	    $dbh = null;

	    if ( count ($result) == 0 ) {

		try {

		    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		    $stmt = $dbh->prepare("ALTER TABLE `m_extractions_" . $form['id'] . "` ADD COLUMN `timestamp_finished` TIMESTAMP NULL DEFAULT NULL AFTER `timestamp_started`;");

		    if ($stmt->execute()) {
			echo "<p>The form called '" . $form['name'] . "' (final version) has been updated with a timestamp_finished column</p>";
		    } else {
			echo "<p>Error updating table m_extractions_" . $form['id'] . " with a timestamp_finished column</p>";
		    }

		    $dbh = null;
		    
		}

		catch (PDOException $e) {

		    echo $e->getMessage();

		}
		
	    } else {

		echo "<p>The form called '" . $form['name'] . "' (final version) already has a timestamp_finished column</p>";
		
	    }
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}

    }

    // 4. Make the sub-extraction `codebook` column 2000 characters long

    echo '<h3>Make codebook in form sub-extraction elements table longer</h3>';

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SHOW COLUMNS FROM `subelements` LIKE 'codebook';");

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	foreach ($result as $row) {

	    if ( $row[1] != "varchar(2000)") {

		try {

		    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		    $stmt = $dbh->prepare("ALTER TABLE `subelements` CHANGE COLUMN `codebook` `codebook` VARCHAR(2000) NULL DEFAULT NULL;");

		    if ($stmt->execute()) {
			echo "<p>Form sub-extraction elements table codebook column updated to 2000 characters in length</p>";
		    } else {
			echo "<p>Error updating subelements table</p>";
		    }

		    $dbh = null;
		    
		}

		catch (PDOException $e) {

		    echo $e->getMessage();

		}
		
	    } else {

		echo "<p>The form sub-extraction elements table columname column is already 2000 characters long</p>";
		
	    }
	    
	}
	
    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }
    
} else {

    echo "<p>You are not logged in, or you do not have sufficient privileges to perform database migration</p>";
    
}

echo '</div>';

include_once ("./footer.php");

?>
