<?php

include_once ("../config.php");

$allgood = TRUE;

if (nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) < 2) {
    $allgood = FALSE;
}

if (! is_numeric($_GET['formid'])) {
    $allgood = FALSE;
}

if (! is_numeric($_GET['refsetid'])) {
    $allgood = FALSE;
}

if (! isset($_GET['export_type']) || ! in_array($_GET['export_type'], ["base", "sub_extraction", "table_data", "ltable_data", "sub_table"], true)) {
    $allgood = false;
}

if (! ($_GET['step'] == "extraction" || $_GET['step'] == "final")) {
    $allgood = FALSE;
}

if ($allgood) {

    $filename = date("Y-m-d-His");
    
    // Get the name of the form and make it filename-friendly
    $form = nbt_get_form_for_id($_GET['formid']);
    $formname = nbt_remove_special($form['name']);

    // Get the name of the reference set and make it filename-friendly
    $refset = nbt_get_refset_for_id($_GET['refsetid']);
    $rsname = nbt_remove_special($refset['name']);
    
    // Get the columns from the reference set
    $rs_cols = nbt_get_columns_for_refset ( $_GET['refsetid'] );
    
    $rscols_to_export = array();
    foreach ($rs_cols as $rs_col) {
	if ($rs_col[0] != "id") {
	    if ($rs_col[0] == "type") {
		$rs_col[0] = "\`type\`";
	    }
	    $rscols_to_export[] = $rs_col[0];
	}
    }
    
    $rs_cols_string = "referenceset_" . $_GET['refsetid'] . "." . implode(", referenceset_" . $_GET['refsetid'] . ".", $rscols_to_export);
    
    $filename .= "_form_" . $formname;
    $filename .= "_refset_" . $rsname;

    switch ($_GET['export_type']) {
	case "base":
	    // The top-level of an extraction form
	    switch ($_GET['step']) {
		case "extraction":
		    // Pre-reconciled extractions
		    $filename .= "_extractions";

		    // Columns of metadata for each row
		    $mcols = array();
		    $mcols[] = "referenceid";
		    $mcols[] = "timestamp_started";
		    $mcols[] = "timestamp_finished";

		    $meta_cols_string = "extractions_" . $_GET['formid'] . "." . implode(", extractions_" . $_GET['formid'] . ".", $mcols);
		    
		    // Get the elements for the extraction form
		    $elements = nbt_get_elements_for_formid ( $_GET['formid'] );

		    // Assemble these into an array of columns
		    $fcols = array();
		    foreach ($elements as $ele) {
			switch ($ele['type']) {
			    case "open_text":
			    case "text_area":
			    case "date_selector":
			    case "single_select":
			    case "country_selector":
			    case "prev_select":
			    case "tags":
				$fcols[] = $ele['columnname'];
				break;
			    case "multi_select":
				$selectoptions = nbt_get_all_select_options_for_element ( $ele['id'] );
				foreach ($selectoptions as $opt) {
				    $fcols[] = $ele['columnname'] . "_" . $opt['dbname'];
				}
				break;
			}
		    }

		    // Get rid of the bad characters
		    foreach ($fcols as $key => $value) {
			$fcols[$key] = "REPLACE(REPLACE(REPLACE(extractions_" . $_GET['formid'] . "." . $value . ", '\\\"', '&quot;'), '\\r', '\\\\n'), '\\n', '\\\\n') as '" . $value . "'";
		    }

		    $form_cols_string = ", " . implode(", ", $fcols);
		    
		    if (count($fcols) == 0) {
			$form_cols_string = "";
		    }

		    $select_cols = $meta_cols_string . ", users.username, " . $rs_cols_string . $form_cols_string;

		    try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT " . $select_cols . " FROM referenceset_" . $_GET['refsetid'] . ", extractions_" . $_GET['formid'] . ", users WHERE extractions_" . $_GET['formid'] . ".refsetid = " . $_GET['refsetid'] . " AND extractions_" . $_GET['formid'] . ".referenceid = referenceset_" . $_GET['refsetid'] . ".id AND users.id = extractions_" . $_GET['formid'] . ".userid AND extractions_" . $_GET['formid'] . ".status = 2 ORDER BY extractions_" . $_GET['formid'] . ".timestamp_started ASC;");

			if ( $stmt->execute() ) {
			    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
			    echo "MySQL Error";
			}

		    }

		    catch (PDOException $e) {
			echo $e->getMessage();
		    }
		    
		    break;
		case "final":
		    // Post-reconciliation final copy
		    $filename .= "_final";

		    // Columns of metadata for each row
		    $mcols = array();
		    $mcols[] = "referenceid";
		    $mcols[] = "timestamp_started";
		    $mcols[] = "timestamp_finished";

		    $meta_cols_string = "m_extractions_" . $_GET['formid'] . "." . implode(", m_extractions_" . $_GET['formid'] . ".", $mcols);
		    
		    // Get the elements for the extraction form
		    $elements = nbt_get_elements_for_formid ( $_GET['formid'] );

		    // Assemble these into an array of columns
		    $fcols = array();
		    foreach ($elements as $ele) {
			switch ($ele['type']) {
			    case "open_text":
			    case "text_area":
			    case "date_selector":
			    case "single_select":
			    case "country_selector":
			    case "prev_select":
			    case "tags":
				$fcols[] = $ele['columnname'];
				break;
			    case "multi_select":
				$selectoptions = nbt_get_all_select_options_for_element ( $ele['id'] );
				foreach ($selectoptions as $opt) {
				    $fcols[] = $ele['columnname'] . "_" . $opt['dbname'];
				}
				break;
			}
		    }

		    // Get rid of the bad characters
		    foreach ($fcols as $key => $value) {
			$fcols[$key] = "REPLACE(REPLACE(REPLACE(m_extractions_" . $_GET['formid'] . "." . $value . ", '\\\"', '&quot;'), '\\r', '\\\\n'), '\\n', '\\\\n') as '" . $value . "'";
		    }

		    $form_cols_string = ", " . implode(", ", $fcols);
		    
		    if (count($fcols) == 0) {
			$form_cols_string = "";
		    }

		    $select_cols = $meta_cols_string . ", " . $rs_cols_string . $form_cols_string;

		    try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT " . $select_cols . " FROM referenceset_" . $_GET['refsetid'] . ", m_extractions_" . $_GET['formid'] . " WHERE m_extractions_" . $_GET['formid'] . ".refsetid = " . $_GET['refsetid'] . " AND m_extractions_" . $_GET['formid'] . ".referenceid = referenceset_" . $_GET['refsetid'] . ".id AND m_extractions_" . $_GET['formid'] . ".status = 2 ORDER BY m_extractions_" . $_GET['formid'] . ".timestamp_started ASC;");

			if ( $stmt->execute() ) {
			    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
			    echo "MySQL Error";
			}

		    }

		    catch (PDOException $e) {
			echo $e->getMessage();
		    }

		    
		    break;
	    }
	    
	    break;
	case "sub_extraction":
	    // A sub-extraction

	    
	    $se_element = nbt_get_element_for_subextraction_dbname ($_GET['formid']);
	    $form = nbt_get_form_for_id($se_element['formid']);
	    $formname = nbt_remove_special($form['name']);

	    
	    // Get the columns for the sub-extraction form

	    $subelements = nbt_get_subextraction_elements_for_subextraction_dbname ($_GET['formid']);
	    
	    $fcols = array();
	    foreach ($subelements as $se) {
		switch ($se['type']) {
		    case "open_text":
		    case "text_area":
		    case "reference_data":
		    case "date_selector":
		    case "tags":
		    case "single_select":
			$fcols[] = $se['dbname'];
			break;
		    case "multi_select":
			$selectoptions = nbt_get_all_select_options_for_sub_element ( $se['id'] );
			foreach ($selectoptions as $opt) {
			    $fcols[] = $se['dbname'] . "_" . $opt['dbname'];
			}
			break;
		}
	    }
	    
	    switch ($_GET['step']) {
		case "extraction":

		    $filename .= "_sub" . $_GET['formid'];
		    
		    foreach ($fcols as $key => $value) {
			$fcols[$key] = "REPLACE(REPLACE(REPLACE(sub_" . $_POST['formid'] . "." . $value . ", '\\\"', '&quot;'), '\\r', '\\\\n'), '\\n', '\\\\n') as '" . $value . "'";
		    }

		    $form_cols_string = ", " . implode(", ", $fcols);

		    if (count($fcols) == 0) {
			$form_cols_string = 0;
		    }
		    
		    $select_cols = "sub_" . $_POST['formid'] . ".referenceid, users.username, " . $rs_cols_string . $form_cols_string;

		    try {
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT " . $select_cols . " FROM referenceset_" . $_POST['refsetid'] . ", sub_" . $_POST['formid'] . ", users WHERE sub_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND sub_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id AND users.id = sub_" . $_POST['formid'] . ".userid;");

			if ( $stmt->execute() ) {
			    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
			    echo "MySQL Error";
			}

		    }

		    catch (PDOException $e) {
			echo $e->getMessage();
		    }
		    
		    break;
		case "final":

		    $filename .= "_finalsub" . $_GET['formid'];

		    foreach ($fcols as $key => $value) {
			$fcols[$key] = "REPLACE(REPLACE(REPLACE(msub_" . $_POST['formid'] . "." . $value . ", '\\\"', '&quot;'), '\\r', '\\\\n'), '\\n', '\\\\n') as '" . $value . "'";
		    }
		    
		    $form_cols_string = ", " . implode(", ", $fcols);

		    if (count($fcols) == 0) {
			$form_cols_string = 0;
		    }
		    
		    $select_cols = "msub_" . $_POST['formid'] . ".referenceid, " . $rs_cols_string . $form_cols_string;

		    try {

			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT " . $select_cols . " FROM referenceset_" . $_POST['refsetid'] . ", msub_" . $_POST['formid'] . " WHERE msub_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND msub_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;");

			if ( $stmt->execute() ) {
			    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
			    echo "MySQL Error";
			}

		    }

		    catch (PDOException $e) {
			echo $e->getMessage();
		    }

		    
		    break;
	    }
	    // End of sub-extraction
	    break;
	case "table_data":
	case "ltable_data":
	    // Table data (incl. ltable_data)

	    
	    $table_element = nbt_get_table_element_by_tablename ($_GET['formid']);
	    $form = nbt_get_form_for_id($table_element['formid']);
	    $formname = nbt_remove_special($form['name']);
	    
	    switch ($_GET['step']) {
		case "extraction":
		    $filename .= "_table" . $_GET['formid'] . $formname;
		    
		    try {
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;");

			if ( $stmt->execute() ) {
			    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
			    echo "MySQL Error";
			}

		    }

		    catch (PDOException $e) {
			echo $e->getMessage();
		    }
		    
		    break;
		case "final":
		    $filename .= "_finaltable" . $_GET['formid'] . $formname;
		    
		    try {
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;");

			if ( $stmt->execute() ) {
			    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
			    echo "MySQL Error";
			}

		    }

		    catch (PDOException $e) {
			echo $e->getMessage();
		    }
		    break;
	    }
	    
	    // End of table data
	    break;
	case "sub_table":
	    // Table data from within a sub-extraction

	    $sub_element = nbt_get_sub_table_element_by_tablename ($_POST['formid']);
	    $element = nbt_get_form_element_for_elementid ($sub_element['elementid']);
	    $form = nbt_get_form_for_id($element['formid']);
	    $formname = nbt_remove_special($form['name']);

	    switch ($_GET['step']) {
		case "extraction":
		    $filename .= "_table" . $_GET['formid'] . $formname;

		    try {
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;");

			if ( $stmt->execute() ) {
			    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
			    echo "MySQL Error";
			}

		    }

		    catch (PDOException $e) {
			echo $e->getMessage();
		    }
		    
		    break;
		case "final":
		    $filename .= "_finaltable" . $_GET['formid'] . $formname;
		    
		    try {
			$dbh = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, array(Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$stmt = $dbh->prepare("SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;");

			if ( $stmt->execute() ) {
			    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} else {
			    echo "MySQL Error";
			}

		    }

		    catch (PDOException $e) {
			echo $e->getMessage();
		    }
		    break;
		    
	    }
	    
	    // End of table data from within a sub-extraction
	    break;
    }

    if (isset($result) && count($result) > 0) {
	// There is a result with more than one row, prepare a CSV
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=" . $filename . ".csv");

	$colnames = [];
	foreach ($result[0] as $col => $row) {
	    $colnames[] = $col;
	}

	echo "\"" . implode("\",\"", $colnames) . "\"\n";
	
	foreach ($result as $row) {
	    echo "\"" . implode ("\",\"", $row) . "\"\n";
	}
	
    } else {
	// No result for query
	$nbtErrorText = "No rows returned for this query";
	include (ABS_PATH . "header.php");
	include ( ABS_PATH . "error.php" );
	include (ABS_PATH . "footer.php");
    }
    
} else {
    // Something didn't check out
    $nbtErrorText = "Invalid export parameters or user credentials";
    include (ABS_PATH . "header.php");
    include ( ABS_PATH . "error.php" );
    include (ABS_PATH . "footer.php");
}

?>
