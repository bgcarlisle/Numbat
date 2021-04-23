<?php

include_once ("./config.php");

include_once ("./header.php");

echo '<div class="nbtContentPanel nbtGreyGradient">';

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    echo '<h2>Database migrations</h2>';

    // 1. Add columns for reference sets meta-data

    echo '<h3>Reference set meta-data</h3>';

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

    // 2. Make `columnname` 2500 characters long

    echo '<h3>Make columnname in form elements table longer</h3>';

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SHOW COLUMNS FROM `formelements` LIKE 'columnname';");

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	foreach ($result as $row) {

	    if ( $row[1] != "varchar(2500)") {

		try {

		    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		    $stmt = $dbh->prepare("ALTER TABLE `formelements` CHANGE COLUMN `columnname` `columnname` VARCHAR(2500) NULL DEFAULT NULL;");

		    if ($stmt->execute()) {
			echo "<p>Form elements table columnname column updated to 2500 characters in length</p>";
		    } else {
			echo "<p>Error updating formelements table</p>";
		    }

		    $dbh = null;
		    
		}

		catch (PDOException $e) {

		    echo $e->getMessage();

		}
		
	    } else {

		echo "<p>The form elements table columname column is already 2500 characters long</p>";
		
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

    // 5. Add the regex column to `formelements`

    echo '<h3>Add the regex column to the form elements table</h3>';

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SHOW COLUMNS FROM `formelements` LIKE 'regex';");

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	if ( count ($result) == 0 ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("ALTER TABLE `formelements` ADD `regex` VARCHAR(500) NULL DEFAULT NULL AFTER `toggle`;");

		if ($stmt->execute()) {
		    echo "<p>The regex column has been added to the form elements table</p>";
		} else {
		    echo "<p>Error adding the regex column to the form elements table</p>";
		}
		
	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }
	    
	} else {

	    echo "<p>The form elements table already has a regex column</p>";
	    
	}
	
    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // 5. Add the regex column to `subelements`

    echo '<h3>Add the regex column to the subextraction elements table</h3>';

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("SHOW COLUMNS FROM `subelements` LIKE 'regex';");

	$stmt->execute();

	$result = $stmt->fetchAll();

	$dbh = null;

	if ( count ($result) == 0 ) {

	    try {

		$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$stmt = $dbh->prepare("ALTER TABLE `subelements` ADD `regex` VARCHAR(500) NULL DEFAULT NULL AFTER `toggle`;");

		if ($stmt->execute()) {
		    echo "<p>The regex column has been added to the subextraction elements table</p>";
		} else {
		    echo "<p>Error adding the regex column to the subextraction elements table</p>";
		}
		
	    }

	    catch (PDOException $e) {

		echo $e->getMessage();

	    }
	    
	} else {

	    echo "<p>The subextraction elements table already has a regex column</p>";
	    
	}
	
    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    // 6. Add the `copypreviousprompt` column to the `subelements` table

    echo '<h3>"Copy from previous" prompt in sub-extractions</h3>';

    function check_for_subelements_column ($columnname) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("SHOW COLUMNS FROM `subelements` LIKE :column;");

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

    if ( ! check_for_subelements_column ("copypreviousprompt") ) {

	// The column doesn't exist yet

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `subelements` ADD COLUMN `copypreviousprompt` INT NOT NULL DEFAULT '1' AFTER `regex`;");

	    if ($stmt->execute()) {
		echo "<p>The sub-extraction elements table has been updated with \"copy from previous\" column</p>";
	    } else {
		echo "<p>Error updating the sub-extraction elements table with \"copy from previous\" column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {

	echo "<p>The sub-extraction elements table already has a \"copy from previous\" prompt column</p>";
	
    }

    // 7. Add the new columns to the forms table

    echo '<h3>Add form metadata columns for export and sharing</h3>';

    if ( ! check_for_forms_column("version") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `forms` ADD COLUMN `version` VARCHAR(50) NULL DEFAULT NULL AFTER `description`;");

	    if ($stmt->execute()) {
		echo "<p>The forms table has been updated with \"version\" column</p>";
	    } else {
		echo "<p>Error updating the forms table with \"version\" column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {

	echo "<p>The forms table already has a 'version' column.</p>";
	
    }

    if ( ! check_for_forms_column("author") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `forms` ADD COLUMN `author` VARCHAR(2500) NULL DEFAULT NULL AFTER `version`;");

	    if ($stmt->execute()) {
		echo "<p>The forms table has been updated with \"author\" column</p>";
	    } else {
		echo "<p>Error updating the forms table with \"author\" column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {

	echo "<p>The forms table already has an 'author' column.</p>";
	
    }

    if ( ! check_for_forms_column("affiliation") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `forms` ADD COLUMN `affiliation` VARCHAR(2500) NULL DEFAULT NULL AFTER `author`;");

	    if ($stmt->execute()) {
		echo "<p>The forms table has been updated with \"affiliation\" column</p>";
	    } else {
		echo "<p>Error updating the forms table with \"affiliation\" column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {

	echo "<p>The forms table already has an 'affiliation' column.</p>";
	
    }

    if ( ! check_for_forms_column("project") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `forms` ADD COLUMN `project` VARCHAR(2500) NULL DEFAULT NULL AFTER `affiliation`;");

	    if ($stmt->execute()) {
		echo "<p>The forms table has been updated with \"project\" column</p>";
	    } else {
		echo "<p>Error updating the forms table with \"project\" column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {

	echo "<p>The forms table already has a 'project' column.</p>";
	
    }

    if ( ! check_for_forms_column("protocol") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `forms` ADD COLUMN `protocol` VARCHAR(2500) NULL DEFAULT NULL AFTER `project`;");

	    if ($stmt->execute()) {
		echo "<p>The forms table has been updated with \"protocol\" column</p>";
	    } else {
		echo "<p>Error updating the forms table with \"protocol\" column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {

	echo "<p>The forms table already has a 'protocol' column.</p>";
	
    }

    if ( ! check_for_forms_column("projectdate") ) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `forms` ADD COLUMN `projectdate` VARCHAR(500) NULL DEFAULT NULL AFTER `protocol`;");

	    if ($stmt->execute()) {
		echo "<p>The forms table has been updated with \"projectdate\" column</p>";
	    } else {
		echo "<p>Error updating the forms table with \"projectdate\" column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {

	echo "<p>The forms table already has a 'projectdate' column.</p>";
	
    }

    // 8. Add the conditional display columns and table

    try {

	$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$stmt = $dbh->prepare("CREATE TABLE IF NOT EXISTS `conditional_display` (`id` int(11) NOT NULL AUTO_INCREMENT, `elementid` int(11) DEFAULT NULL, `trigger_element` int(11) DEFAULT NULL, `trigger_option` int(11) DEFAULT NULL, `type` varchar(50) DEFAULT 'is', PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

	if ($stmt->execute()) {
	    echo "<p>The conditional display table has been created if it did not exist</p>";
	} else {
	    echo "<p>Error attempting to create the conditional display table</p>";
	}

	$dbh = null;
	
    }

    catch (PDOException $e) {

	echo $e->getMessage();

    }

    function check_for_formelements_column ($columnname) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("SHOW COLUMNS FROM `formelements` LIKE :column;");

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

    if (! check_for_formelements_column ("startup_visible")) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `formelements` ADD COLUMN `startup_visible` INT(11) NULL DEFAULT 1 AFTER `toggle`;");

	    if ($stmt->execute()) {
		echo "<p>Form elements table updated with startup_visible column</p>";
	    } else {
		echo "<p>Error updating form elements table with startup_visible column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {
	echo "<p>Form elements table already has a startup_visible column</p>";
    }

    if (! check_for_formelements_column ("conditional_logical_operator")) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `formelements` ADD COLUMN `conditional_logical_operator` CHAR(3) NULL DEFAULT 'any' AFTER `startup_visible`;");

	    if ($stmt->execute()) {
		echo "<p>Form elements table updated with conditional_logical_operator column</p>";
	    } else {
		echo "<p>Error updating form elements table with conditional_logical_operator column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {
	echo "<p>Form elements table already has a conditional_logical_operator column</p>";
    }

    if (! check_for_formelements_column ("destructive_hiding")) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `formelements` ADD COLUMN `destructive_hiding` INT(11) NULL DEFAULT 1 AFTER `conditional_logical_operator`;");

	    if ($stmt->execute()) {
		echo "<p>Form elements table updated with destructive_hiding column</p>";
	    } else {
		echo "<p>Error updating form elements table with destructive_hiding column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {
	echo "<p>Form elements table already has a destructive_hiding column</p>";
    }

    // Add the conditional display columns for sub-extractions

    if (! check_for_subelements_column ("startup_visible")) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `subelements` ADD COLUMN `startup_visible` INT(11) NULL DEFAULT 1 AFTER `toggle`;");

	    if ($stmt->execute()) {
		echo "<p>Sub-extraction elements table updated with startup_visible column</p>";
	    } else {
		echo "<p>Error updating sub-extraction elements table with startup_visible column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {
	echo "<p>Sub-extraction elements table already has a startup_visible column</p>";
    }

    if (! check_for_subelements_column ("conditional_logical_operator")) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `subelements` ADD COLUMN `conditional_logical_operator` CHAR(3) NULL DEFAULT 'any' AFTER `startup_visible`;");

	    if ($stmt->execute()) {
		echo "<p>Sub-extraction elements table updated with conditional_logical_operator column</p>";
	    } else {
		echo "<p>Error updating sub-extraction elements table with conditional_logical_operator column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {
	echo "<p>Sub-extraction elements table already has a conditional_logical_operator column</p>";
    }

    if (! check_for_subelements_column ("destructive_hiding")) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `subelements` ADD COLUMN `destructive_hiding` INT(11) NULL DEFAULT '1' AFTER `conditional_logical_operator`;");

	    if ($stmt->execute()) {
		echo "<p>Sub-extraction elements table updated with destructive_hiding column</p>";
	    } else {
		echo "<p>Error updating sub-extraction elements table with destructive_hiding column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {
	echo "<p>Sub-extraction elements table already has a destructive_hiding column</p>";
    }

    // Add subelementid column to conditional display table

    function check_for_conditional_display_column ($columnname) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("SHOW COLUMNS FROM `conditional_display` LIKE :column;");

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

    if (! check_for_conditional_display_column ("subelementid")) {

	try {

	    $dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	    $stmt = $dbh->prepare("ALTER TABLE `conditional_display` ADD COLUMN `subelementid` INT(11) NULL DEFAULT NULL AFTER `elementid`;");

	    if ($stmt->execute()) {
		echo "<p>Conditional display table updated with subelementid column</p>";
	    } else {
		echo "<p>Error updating conditional display table with subelementid column</p>";
	    }

	    $dbh = null;
	    
	}

	catch (PDOException $e) {

	    echo $e->getMessage();

	}
	
    } else {
	echo "<p>Conditional display table already has a subelementid column</p>";
    }

    // End
    
} else { // Not admin

    echo "<p>You are not logged in, or you do not have sufficient privileges to perform database migration</p>";
    
}

echo '</div>';

include_once ("./footer.php");

?>
