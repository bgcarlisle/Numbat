<?php

include_once ('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in

    if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	$file = fopen ( ABS_PATH . "extract/tmp/tmp.txt", "r" );

	if ( ! $file ) {

	    $nbtErrorText = "Error opening file";
	    include ( ABS_PATH . "header.php" );
	    include ( ABS_PATH . "error.php" );
	    
	} else {

	    $filesize = filesize ( ABS_PATH . "extract/tmp/tmp.txt" );

	    if ( ! $filesize ) {

		$nbtErrorText = "File is empty";

		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "error.php" );

	    } else {

		include ( ABS_PATH . "header.php" );

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

		    $lines[$counter] = $line;

		    $counter++;

		}

		$columns = explode ("\t", $lines[0]);

		unset ($lines[0]);

		switch ($_POST['import_type']) {

		    case "extraction":

			$elements = nbt_get_elements_for_formid ( $_POST['form'] );

			$selected_elements = [];

			foreach ($elements as $element) {

			    switch ( $element['type'] ) {

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

				    $selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

				    foreach ($selectoptions as $sopt) {

					if ( $_POST[$element['columnname'] . "_" . $sopt['dbname']] != "ns" ) {

					    $selected_elements[$element['columnname'] . "_" . $sopt['dbname']] = $_POST[$element['columnname'] . "_" . $sopt['dbname']];
					    
					}
					
				    }
				    
				    break;

				default:

				    if ( $_POST[$element['columnname']] != "ns" ) {

					$selected_elements[$element['columnname']] = $_POST[$element['columnname']];
					
				    }
				    
				    break;
			    }
			    
			}

			$countrows = 0;

			foreach ( $lines as $line ) {

			    if (nbt_insert_imported_extraction ( $_POST['form'], $_POST['refset'], $_POST['usercolumn'], $_POST['user'], $_POST['referenceid'], $selected_elements, $line, "\t", $_POST['status'] )) {
				$countrows++;
			    }
			    
			}

			$form = nbt_get_form_for_id ( $_POST['form'] );
			$refset = nbt_get_refset_for_id ( $_POST['refset'] );
			
			echo '<div class="nbtContentPanel nbtGreyGradient">';

			echo '<h2>Import complete</h2>';

			echo '<p>Reference set: ' . $refset['name'] . '</p>';

			echo '<p>Form: ' . $form['name'] . '</p>';

			echo '<hr>';

			echo "<p>Imported " . $countrows . " rows</p>";

			echo '</div>';
			
			break;

		    case "table_data":

			$tablecolumns = nbt_get_all_columns_for_table_data ( $_POST['element'] );

			$selected_tablecolumns = [];

			foreach ($tablecolumns as $tcol) {

			    if ( $_POST[$tcol['dbname']] != "ns") {

				$selected_tablecolumns[$tcol['dbname']] = $_POST[$tcol['dbname']];
				
			    }
			    
			}

			$countrows = 0;

			foreach ( $lines as $line ) {

			    if (nbt_insert_imported_table_data ( $_POST['form'], $_POST['element'], $_POST['refset'], $_POST['usercolumn'], $_POST['user'], $_POST['referenceid'], $selected_tablecolumns, $line, "\t" )) {
				$countrows++;
			    }
			    
			}

			$form = nbt_get_form_for_id ( $_POST['form'] );
			$element = nbt_get_form_element_for_elementid ( $_POST['element'] );
			$refset = nbt_get_refset_for_id ( $_POST['refset'] );
			
			echo '<div class="nbtContentPanel nbtGreyGradient">';

			echo '<h2>Import complete</h2>';

			echo '<p>Reference set: ' . $refset['name'] . '</p>';

			echo '<p>Form: ' . $form['name'] . ' / ' . $element['displayname'] . ' (table)</p>';

			echo '<hr>';

			echo "<p>Imported " . $countrows . " rows</p>";

			echo '</div>';
			
			break;

		    case "sub_extraction":

			$subelements = nbt_get_sub_extraction_elements_for_elementid ( $_POST['element'] );

			$selected_subelements = [];

			foreach ($subelements as $sele) {

			    switch ($sele['type']) {

				case "table_data":
				    // Do nothing
				    break;

				case "multi_select":

				    $selectoptions = nbt_get_all_select_options_for_sub_element ( $sele['id'] );

				    foreach ($selectoptions as $sopt) {

					if ( $_POST[$sele['dbname'] . "_" . $sopt['dbname']] != "ns" ) {

					    $selected_subelements[$sele['dbname'] . "_" . $sopt['dbname']] = $_POST[$sele['dbname'] . "_" . $sopt['dbname']];
					    
					}
					
				    }
				    
				    break;

				default:

				    if ( $_POST[$sele['dbname']] != "ns") {

					$selected_subelements[$sele['dbname']] = $_POST[$sele['dbname']];
					
				    }
				    
				    break;
			    }

			}

			$countrows = 0;

			foreach ( $lines as $line ) {

			    if (nbt_insert_imported_sub_extraction ( $_POST['element'], $_POST['refset'], $_POST['usercolumn'], $_POST['user'], $_POST['referenceid'], $selected_subelements, $line, "\t" )) {
				$countrows++;
			    }
			    
			}

			$form = nbt_get_form_for_id ( $_POST['form'] );
			$element = nbt_get_form_element_for_elementid ( $_POST['element'] );
			$refset = nbt_get_refset_for_id ( $_POST['refset'] );
			
			echo '<div class="nbtContentPanel nbtGreyGradient">';

			echo '<h2>Import complete</h2>';

			echo '<p>Reference set: ' . $refset['name'] . '</p>';

			echo '<p>Form: ' . $form['name'] . ' / ' . $element['displayname'] . ' (table)</p>';

			echo '<hr>';

			echo "<p>Imported " . $countrows . " rows</p>";

			echo '</div>';
			
			break;

		    case "sub_table":

			$tablecolumns = nbt_get_all_columns_for_sub_table_data ( $_POST['subelement'] );

			$selected_tablecolumns = [];

			foreach ($tablecolumns as $tcol) {

			    if ( $_POST[$tcol['dbname']] != "ns") {

				$selected_tablecolumns[$tcol['dbname']] = $_POST[$tcol['dbname']];
				
			    }
			    
			}

			$countrows = 0;

			foreach ( $lines as $line ) {

			    if (nbt_insert_imported_table_data ( $_POST['form'], $_POST['subelement'], $_POST['refset'], $_POST['usercolumn'], $_POST['user'], $_POST['referenceid'], $selected_tablecolumns, $line, "\t", TRUE, $_POST['subextractionid'] )) {
				$countrows++;
			    }
			    
			}

			$form = nbt_get_form_for_id ( $_POST['form'] );
			$element = nbt_get_form_element_for_elementid ( $_POST['element'] );
			$subelement = nbt_get_sub_element_for_subelementid ( $_POST['subelement'] );
			$refset = nbt_get_refset_for_id ( $_POST['refset'] );

			echo '<div class="nbtContentPanel nbtGreyGradient">';

			echo '<h2>Import complete</h2>';

			echo '<p>Reference set: ' . $refset['name'] . '</p>';

			echo '<p>Form: ' . $form['name'] . ' / ' . $element['displayname'] . ' (sub-extraction) / ' . $subelement['displayname'] . ' (sub-extraction table)</p>';

			echo '<hr>';

			echo "<p>Imported " . $countrows . " rows</p>";

			echo '</div>';

			break;
			
		}

	    }
	    
	}
	
    }
    
}

?>
