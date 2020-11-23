<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    if ( $_POST['form'] != "ns" && $_POST['refset'] != "ns" ) {

	if ($_FILES["file"]["error"] > 0) { // There's an upload error
	    if ($_FILES["file"]["error"] == 4) { // No file selected

		$nbtErrorText = "No file selected";

		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "error.php" );

	    } else {

		$nbtErrorText = "Upload error: " . $_FILES["file"]["error"];

		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "error.php" );

	    }
	} else { // No error on upload

	    include ( ABS_PATH . "header.php" );
	    
	    if ( ! is_dir ( ABS_PATH . "extract/tmp/" ) ) {

		mkdir ( ABS_PATH . "extract/tmp/", 0777 );

	    } else {

		chmod ( ABS_PATH . "extract/tmp/", 0777 );

	    }

	    move_uploaded_file ( $_FILES["file"]["tmp_name"], ABS_PATH . "extract/tmp/tmp.txt" );

	    $file = fopen ( ABS_PATH . "extract/tmp/tmp.txt", "r" );

	    if ( ! $file ) {

		$nbtErrorText = "Error opening file";

		include ( ABS_PATH . "error.php" );

	    } else { // No error opening file

		$filesize = filesize ( ABS_PATH . "extract/tmp/tmp.txt" );
		
		if ( ! $filesize ) {

		    $nbtErrorText = "File is empty";

		    include ( ABS_PATH . "error.php" );

		} else { // File is not empty

		    $filecontent = fread ( $file, $filesize );

		    fclose ( $file );

		    $counter = 0;

		    $lines = array();

		    if ( count ( explode ( "\n", $filecontent ) ) > count ( explode ( "\r\n", $filecontent ) ) ) {

			$line_demarcation = "\n";

		    } else {

			$line_demarcation = "\r\n";

		    }

		    foreach ( explode ( $line_demarcation, $filecontent ) as $line ) {

			if ( strlen (trim($line)) > 0 ) {

			    $lines[$counter] = $line;
			    
			}

			$counter++;

		    }

		    $columns = explode ("\t", $lines[0]);

		    unset ($lines[0]);

		    echo '<div class="nbtContentPanel nbtGreyGradient">';

		    echo '<form action="' . SITE_URL . 'extract/import-save.php" method="post">';

		    echo "<h2>Import extractions</h2>";

		    $refset = nbt_get_refset_for_id ( $_POST['refset'] );

		    echo "<p>Reference set: " . $refset['name'] . "</p>";

		    echo '<input type="hidden" name="refset" value="' . $refset['id'] . '">';

		    if ( ! strpos ($_POST['form'], "-") ) { // If it is not a table or sub-extraction

			echo '<input type="hidden" name="import_type" value="extraction">';

			$form = nbt_get_form_for_id ( $_POST['form'] );
			$elements = nbt_get_elements_for_formid ( $form['id'] );

			echo "<p>Form: " . $form['name'] . "</p>";

			echo '<input type="hidden" name="form" value="' . $form['id'] . '">';

			foreach ( $elements as $element ) {

			    switch ( $element['type'] ) {

				    // These types don't really make sense to include here
				case "section_heading":
				case "table_data":
				case "ltable_data":
				case "sub_extraction":
				case "citations":
				case "assignment_editor":
				case "reference_data":
				    // Do nothing
				    break;

				case "multi_select":

				    echo '<div class="nbtImportElement">';

				    echo '<h3>' . $element['displayname'] . '</h3>';

				    echo '<p>Numbat expects each of these columns to contain 0 or 1 to indicate non-selected and selected options, respectively.</p>';

				    $selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

				    foreach ($selectoptions as $sopt) {

					echo '<p>' . $sopt['displayname'] . '</p>';

					echo '<select name="' . $element['columnname'] . '_' . $sopt['dbname'] . '">';

					echo '<option value="ns">Leave blank</option>';
					
					foreach ($columns as $index => $col) {

					    if ($col == $element['columnname'] . "_" . $sopt['dbname']) {
						echo '<option value="' . $index . '" selected>' . $col . '</option>';
					    } else {
						echo '<option value="' . $index . '">' . $col . '</option>';
					    }
					    
					}

					echo '</select>';
					
				    }
				    
				    echo '</div>';
				    
				    break;

				case "single_select":

				    echo '<div class="nbtImportElement">';

				    $selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

				    echo '<h3>' . $element['displayname'] . '</h3>';

				    echo '<p>Numbat expects the selected column to contain only the following values:</p>';
				    
				    echo '<ul>';

				    foreach ($selectoptions as $sopt) {
					echo '<li>' . $sopt['dbname'] . '</li>';
				    }

				    echo '</ul>';

				    echo '<select name="' . $element['columnname'] . '">';

				    echo '<option value="ns">Leave blank</option>';

				    foreach ($columns as $index => $col) {

					if ($col == $element['columnname']) {
					    echo '<option value="' . $index . '" selected>' . $col . '</option>';
					} else {
					    echo '<option value="' . $index . '">' . $col . '</option>';
					}
					
				    }

				    echo '</select>';
				    
				    echo '</div>';

				    break;

				case "date_selector":

				    echo '<div class="nbtImportElement">';

				    echo '<h3>' . $element['displayname'] . '</h3>';

				    echo '<p>Numbat expects the selected column to contain only numeric dates in ISO-8601 format (YYYY-MM-DD).</p>';

				    echo '<select name="' . $element['columnname'] . '">';

				    echo '<option value="ns">Leave blank</option>';

				    foreach ($columns as $index => $col) {

					if ($col == $element['columnname']) {
					    echo '<option value="' . $index . '" selected>' . $col . '</option>';
					} else {
					    echo '<option value="' . $index . '">' . $col . '</option>';
					}
					
				    }

				    echo '</select>';
				    
				    echo '</div>';
				    
				    break;

				default:
				    // All the other cases

				    echo '<div class="nbtImportElement">';

				    echo '<h3>' . $element['displayname'] . '</h3>';

				    echo '<select name="' . $element['columnname'] . '">';

				    echo '<option value="ns">Leave blank</option>';

				    foreach ($columns as $index => $col) {

					if ($col == $element['columnname']) {
					    echo '<option value="' . $index . '" selected>' . $col . '</option>';
					} else {
					    echo '<option value="' . $index . '">' . $col . '</option>';
					}
					
				    }

				    echo '</select>';
				    
				    echo '</div>';
				    
				    break;
			    }
			    
			}
			
		    } else { // It's a table or sub-extraction
			
			$elementid = substr($_POST['form'], strpos ($_POST['form'], "-")+1);

			$formid = substr($_POST['form'], 0, strpos ($_POST['form'], "-"));

			$form = nbt_get_form_for_id ( $_POST['form'] );

			$element = nbt_get_form_element_for_elementid ($elementid);

			switch ($element['type']) {

			    case "table_data":
			    case "ltable_data":

				echo "<p>Form: " . $form['name'] . " / " . $element['displayname'] . " (table)</p>";

				echo '<input type="hidden" name="import_type" value="table_data">';
				echo '<input type="hidden" name="form" value="' . $form['id'] . '">';
				echo '<input type="hidden" name="element" value="' . $element['id'] . '">';

				$tablecolumns = nbt_get_all_columns_for_table_data ( $element['id'] );

				foreach ($tablecolumns as $tcol) {

				    echo '<div class="nbtImportElement">';

				    echo '<h3>' . $tcol['displayname'] . '</h3>';

				    echo '<select name="' . $tcol['dbname'] . '">';

				    echo '<option value="ns">Leave blank</option>';

				    foreach ($columns as $index => $col) {

					if ($col == $element['dbname']) {
					    echo '<option value="' . $index . '" selected>' . $col . '</option>';
					} else {
					    echo '<option value="' . $index . '">' . $col . '</option>';
					}
					
				    }

				    echo '</select>';

				    echo '</div>';
				    
				}

				break;

			    case "sub_extraction":

				echo "<p>Form: " . $form['name'] . " / " . $element['displayname'] . " (sub-extraction)</p>";

				echo '<input type="hidden" name="import_type" value="sub_extraction">';

				$subelements = nbt_get_sub_extraction_elements_for_elementid ( $element['id'] );

				foreach ($subelements as $subel) {

				    switch ( $subel['type'] ) {

					case "open_text":
					case "date_selector":
					case "single_select":
					case "multi_select":
					case "table_data":
					    break;
				    }

				    echo '<div class="nbtImportElement">';

				    echo '<h3>' . $subel['displayname'] . '</h3>';

				    echo '<select name="' . $subel['dbname'] . '">';

				    echo '<option value="ns">Leave blank</option>';

				    foreach ($columns as $index => $col) {

					if ($col == $element['dbname']) {
					    echo '<option value="' . $index . '" selected>' . $col . '</option>';
					} else {
					    echo '<option value="' . $index . '">' . $col . '</option>';
					}
					
				    }

				    echo '</select>';

				    echo '</div>';
				    
				}
				
				break;
			}
			
		    }

		    echo "<h3>User</h3>";

		    if ( $_POST['user'] == "ns" ) {

			echo '<select name="usercolumn">';

			foreach ($columns as $index => $col) {

			    if (
				$col == "user_id" |
				$col == "userid" |
				$col == "user" |
				$col == "uid" |
				$col == "extractor_id" |
				$col == "extractorid" |
				$col == "extractor" |
				$col == "coder_id" |
				$col == "coderid" |
				$col == "coder"
			    ) {
				echo '<option value="' . $index . '" selected>' . $col . '</option>';
			    } else {
				echo '<option value="' . $index . '">' . $col . '</option>';
			    }
			    
			}

			echo '</select>';

			echo '<p>Choose a column from the uploaded file that contains usernames that represent the extractor responsible for the row in question. If no username matches, the uploaded extractions will be attributed to the currently signed-in user.</p>';

			echo '<input type="hidden" name="user" value="ns">';
			
		    } else {

			$username = nbt_get_username_for_userid ( $_POST['user'] );

			echo "<p>User: " . $username . "</p>";

			echo '<input type="hidden" name="usercolumn" value="ns">';

			echo '<input type="hidden" name="user" value="' . $_POST['user'] . '">';
			
		    }

		    echo "<hr>";

		    echo "<h3>Reference ID</h3>";

		    echo '<select name="referenceid">';

		    foreach ($columns as $index => $col) {

			if (
			    $col == "reference_id" |
			    $col == "referenceid" |
			    $col == "reference" |
			    $col == "refid" |
			    $col == "ref" |
			    $col == "rid" |
			    $col == "id"
			) {
			    echo '<option value="' . $index . '" selected>' . $col . '</option>';
			} else {
			    echo '<option value="' . $index . '">' . $col . '</option>';
			}
			
		    }

		    echo '</select>';

		    echo '<p>Choose a column from the uploaded file that contains the reference id that corresponds to the row in question.</p>';

		    echo '<hr><button>Import extractions</button>';

		    echo '</form></div>';
		    
		}

		
	    }
	    
	}
	
    } else {
	$nbtErrorText = "Please be sure that you have selected a form and a reference set.";
	include ( ABS_PATH . "header.php" );
	include ( ABS_PATH . "error.php" );
    }
    
}

?>
