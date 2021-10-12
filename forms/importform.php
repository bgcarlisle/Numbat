<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

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

	if ( ! is_dir ( ABS_PATH . "forms/tmp/" ) ) {

	    mkdir ( ABS_PATH . "forms/tmp/", 0777 );

	} else {

	    chmod ( ABS_PATH . "forms/tmp/", 0777 );

	}

	move_uploaded_file( $_FILES["file"]["tmp_name"], ABS_PATH . "forms/tmp/tmp.txt" );

	$file = fopen ( ABS_PATH . "forms/tmp/tmp.txt", "r" );

	if ( ! $file ) {

	    $nbtErrorText = "Error opening file";

	    include ( ABS_PATH . "error.php" );
	    
	} else { // No error opening file

	    $filesize = filesize ( ABS_PATH . "forms/tmp/tmp.txt" );

	    if ( ! $filesize ) {

		$nbtErrorText = "File is empty";

		include ( ABS_PATH . "error.php" );
		
	    } else { // File is not empty

		// First back up the current instance

		nbt_new_dump_file ();

		// Then proceed with the import

		$filecontent = fread ( $file, $filesize );

		fclose ( $file );

		unlink( ABS_PATH . "forms/tmp/tmp.txt" );

		$import = json_decode($filecontent, true);

		switch ($import['numbatversion']) {

		    case "2.12":

			// Make a new extraction form with the imported metadata
			$newformid = nbt_new_extraction_form (
			    $import['name'],
			    $import['description'],
			    $import['version'],
			    $import['author'],
			    $import['affiliation'],
			    $import['project'],
			    $import['protocol'],
			    $import['projectdate']
			);

			// Decode the JSON for all the element types that have
			// an internal data structure
			$elements = json_decode($import['elements'], true);
			$selectoptions = json_decode($import['selectoptions'], true);
			$tabledatacols = json_decode($import['tabledatacols'], true);
			$subelements = json_decode($import['subelements'], true);
			$citecols = json_decode($import['citationscols'], true);

			// Loop through all the imported elements and re-create
			// them in the current Numbat instance individually
			$peid = 0;
			foreach ( $elements as $element ) {

			    // Get the highest element id in the form
			    $peid = nbt_get_next_eid_in_form ( $newformid );

			    switch ( $element['type'] ) {

				case "section_heading":
				    nbt_add_section_heading ($newformid, $peid, $element['displayname'], $element['codebook'], $element['toggle']);
				    break;

				case "timer":
				    nbt_add_extraction_timer($newformid, $peid, $element['codebook'], $element['toggle']);
				    break;

				case "open_text":
				    nbt_add_open_text_field ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['regex']);
				    break;

				case "text_area":
				    nbt_add_text_area_field ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle']);
				    break;

				case "date_selector":
				    nbt_add_date_selector ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle']);
				    break;

				case "single_select":
				    $newelementid = nbt_add_single_select ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle']);

				    foreach ($selectoptions as $opt) {
					if ($element['id'] == $opt['elementid']) {
					    nbt_add_single_select_option ($newelementid, $opt['displayname'], $opt['dbname'], $opt['toggle']);
					}
				    }
				    break;

				case "multi_select":
				    $newelementid = nbt_add_multi_select ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle']);

				    foreach ($selectoptions as $opt) {
					if ($element['id'] == $opt['elementid']) {
					    nbt_add_multi_select_option ($newelementid, $opt['displayname'], $opt['dbname'], $opt['toggle']);
					}
				    }
				    break;

				case "country_selector":
				    nbt_add_country_selector ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle']);
				    break;

				case "table_data":
				case "ltable_data":
				    $newelementid = nbt_add_table_data ($newformid, $peid, $element['type'], $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle']);

				    foreach ($tabledatacols as $col) {
					if ($element['id'] == $col['elementid']) {
					    nbt_add_table_data_column ($newelementid, $element['type'], FALSE, $col['displayname'], $col['dbname']);
					}
				    }
				    break;

				case "citations":
				    $newelementid = nbt_add_citation_selector ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle']);

				    foreach ($citecols as $col) {
					if ($element['id'] == $col['elementid']) {
					    nbt_add_citation_property ($newelementid, $col['displayname'], $col['dbname'], $col['remind'], $col['caps']);
					}
				    }
				    break;

				case "sub_extraction":
				    $newelementid = nbt_add_sub_extraction ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle']);

				    // Loop through all the sub-extraction elements and add them
				    foreach ($subelements as $sel) {
					if ($element['id'] == $sel['elementid']) {
					    switch ($sel['type']) {
						case "open_text":
						    nbt_add_sub_open_text_field ($newelementid, $sel['displayname'], $sel['dbname'], $sel['regex'], $sel['copypreviousprompt'], $sel['codebook'], $sel['toggle']);
						    break;
						case "date_selector":
						    nbt_add_sub_date_selector ($newelementid, $sel['displayname'], $sel['dbname'], $sel['copypreviousprompt'], $sel['codebook'], $sel['toggle']);
						    break;
						case "single_select":
						    $newseid = nbt_add_sub_single_select ($newelementid, $sel['displayname'], $sel['dbname'], $sel['copypreviousprompt'], $sel['codebook'], $sel['toggle']);

						    foreach ($selectoptions as $opt) {
							if ($sel['id'] == $opt['subelementid']) {
							    nbt_add_sub_single_select_option ($newseid, $opt['displayname'], $opt['dbname'], $opt['toggle']);
							}
						    }
						    break;
						case "multi_select":
						    $newseid = nbt_add_sub_multi_select ($newelementid, $sel['displayname'], $sel['dbname'], $sel['copypreviousprompt'], $sel['codebook'], $sel['toggle']);

						    foreach ($selectoptions as $opt) {
							if ($sel['id'] == $opt['subelementid']) {
							    nbt_add_sub_multi_select_option ($newseid, $opt['displayname'], $opt['dbname'], $opt['toggle']);
							}
						    }
						    break;
						case "table_data":
						    $newseid = nbt_add_sub_table ($newelementid, $sel['displayname'], $sel['dbname'], $sel['codebook'], $sel['toggle']);

						    foreach ($tabledatacols as $col) {
							if ($sel['id'] == $col['subelementid']) {
							    nbt_add_table_data_column ($newseid, $sel['type'], TRUE, $col['displayname'], $col['dbname']);
							}
						    }
						    break;
						    
					    }
					}
				    }
				    break;

				case "assignment_editor":
				    nbt_add_assignment_editor ($newformid, $peid, $element['displayname'], $element['codebook'], $element['toggle']);
				    break;

				    
				case "reference_data":
				    nbt_add_reference_data ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle']);
				    break;

				case "prev_select":
				    nbt_add_prev_select ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle']);
				    break;
				    
			    }
			    
			}

			include ( ABS_PATH . "forms/forms.php");
			
			break;

		    case "2.13":
			
			// Make a new extraction form with the imported metadata
			$newformid = nbt_new_extraction_form (
			    $import['name'],
			    $import['description'],
			    $import['version'],
			    $import['author'],
			    $import['affiliation'],
			    $import['project'],
			    $import['protocol'],
			    $import['projectdate']
			);

			// Decode the JSON for all the element types that have
			// an internal data structure
			$elements = json_decode($import['elements'], true);
			$selectoptions = json_decode($import['selectoptions'], true);
			$tabledatacols = json_decode($import['tabledatacols'], true);
			$subelements = json_decode($import['subelements'], true);
			$citecols = json_decode($import['citationscols'], true);
			$conditionals = json_decode($import['conditionals'], true);

			// Make lookup arrays for the old => new
			// element id's, subelement id's and select-options
			$elementid_lup = [];
			$subelementid_lup = [];
			$selopt_lup = [];
			
			// Loop through all the imported elements and re-create
			// them in the current Numbat instance individually
			$peid = 0;
			foreach ( $elements as $element ) {

			    // Get the highest element id in the form
			    $peid = nbt_get_next_eid_in_form ( $newformid );

			    // Add the old and new element id's to the lookup array
			    $elementid_lup[$element['id']] = $peid + 1;

			    switch ( $element['type'] ) {

				case "section_heading":
				    nbt_add_section_heading ($newformid, $peid, $element['displayname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);
				    break;

				case "timer":
				    nbt_add_extraction_timer($newformid, $peid, $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);
				    break;

				case "open_text":
				    nbt_add_open_text_field ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['regex'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);
				    break;

				case "text_area":
				    nbt_add_text_area_field ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);
				    break;

				case "date_selector":
				    nbt_add_date_selector ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);
				    break;

				case "single_select":
				    $newelementid = nbt_add_single_select ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);

				    foreach ($selectoptions as $opt) {
					if ($element['id'] == $opt['elementid']) {
					    $newselectoptid = nbt_add_single_select_option ($newelementid, $opt['displayname'], $opt['dbname'], $opt['toggle']);
					    $selopt_lup[$opt['id']] = $newselectoptid;
					}
				    }
				    break;

				case "multi_select":
				    $newelementid = nbt_add_multi_select ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);

				    foreach ($selectoptions as $opt) {
					if ($element['id'] == $opt['elementid']) {
					    $newselectoptid = nbt_add_multi_select_option ($newelementid, $opt['displayname'], $opt['dbname'], $opt['toggle']);
					    $selopt_lup[$opt['id']] = $newselectoptid;
					}
				    }
				    break;

				case "country_selector":
				    nbt_add_country_selector ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);
				    break;

				case "table_data":
				case "ltable_data":
				    $newelementid = nbt_add_table_data ($newformid, $peid, $element['type'], $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);

				    foreach ($tabledatacols as $col) {
					if ($element['id'] == $col['elementid']) {
					    nbt_add_table_data_column ($newelementid, $element['type'], FALSE, $col['displayname'], $col['dbname']);
					}
				    }
				    break;

				case "citations":
				    $newelementid = nbt_add_citation_selector ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);

				    foreach ($citecols as $col) {
					if ($element['id'] == $col['elementid']) {
					    nbt_add_citation_property ($newelementid, $col['displayname'], $col['dbname'], $col['remind'], $col['caps']);
					}
				    }
				    break;

				case "sub_extraction":
				    $newelementid = nbt_add_sub_extraction ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);

				    // Loop through all the sub-extraction elements and add them
				    foreach ($subelements as $sel) {
					if ($element['id'] == $sel['elementid']) {
					    switch ($sel['type']) {
						case "open_text":
						    $newseid = nbt_add_sub_open_text_field ($newelementid, $sel['displayname'], $sel['dbname'], $sel['regex'], $sel['copypreviousprompt'], $sel['codebook'], $sel['toggle']);
						    $subelementid_lup[$sel['id']] = $newseid;
						    break;
						case "tags":
						    $newseid = nbt_add_sub_tags_element ($newelementid, $sel['displayname'], $sel['dbname'], $sel['regex'], $sel['copypreviousprompt'], $sel['codebook'], $sel['toggle']);
						    $subelementid_lup[$sel['id']] = $newseid;
						    break;
						case "date_selector":
						    $newseid = nbt_add_sub_date_selector ($newelementid, $sel['displayname'], $sel['dbname'], $sel['copypreviousprompt'], $sel['codebook'], $sel['toggle']);
						    $subelementid_lup[$sel['id']] = $newseid;
						    break;
						case "single_select":
						    $newseid = nbt_add_sub_single_select ($newelementid, $sel['displayname'], $sel['dbname'], $sel['copypreviousprompt'], $sel['codebook'], $sel['toggle']);
						    $subelementid_lup[$sel['id']] = $newseid;

						    foreach ($selectoptions as $opt) {
							if ($sel['id'] == $opt['subelementid']) {
							    $newselectoptid = nbt_add_sub_single_select_option ($newseid, $opt['displayname'], $opt['dbname'], $opt['toggle']);
							    $selopt_lup[$opt['id']] = $newselectoptid;
							}
						    }
						    break;
						case "multi_select":
						    $newseid = nbt_add_sub_multi_select ($newelementid, $sel['displayname'], $sel['dbname'], $sel['copypreviousprompt'], $sel['codebook'], $sel['toggle']);
						    $subelementid_lup[$sel['id']] = $newseid;

						    foreach ($selectoptions as $opt) {
							if ($sel['id'] == $opt['subelementid']) {
							    $newselectoptid = nbt_add_sub_multi_select_option ($newseid, $opt['displayname'], $opt['dbname'], $opt['toggle']);
							    $selopt_lup[$opt['id']] = $newselectoptid;
							}
						    }
						    break;
						case "table_data":
						    $newseid = nbt_add_sub_table ($newelementid, $sel['displayname'], $sel['dbname'], $sel['codebook'], $sel['toggle']);
						    $subelementid_lup[$sel['id']] = $newseid;

						    foreach ($tabledatacols as $col) {
							if ($sel['id'] == $col['subelementid']) {
							    nbt_add_table_data_column ($newseid, $sel['type'], TRUE, $col['displayname'], $col['dbname']);
							}
						    }
						    break;
						    
					    }
					}
				    }
				    break;

				case "assignment_editor":
				    nbt_add_assignment_editor ($newformid, $peid, $element['displayname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);
				    break;

				    
				case "reference_data":
				    nbt_add_reference_data ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);
				    break;

				case "prev_select":
				    nbt_add_prev_select ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding']);
				    break;

				case "tags":
				    nbt_add_tags_element ($newformid, $peid, $element['displayname'], $element['columnname'], $element['codebook'], $element['toggle'], $element['startup_visible'], $element['conditional_logical_operator'], $element['destructive_hiding'], $element['tagprompts']);
				    break;
				    
			    }
			    
			}

			echo count($conditionals) . " conditionals<br>";

			foreach ($conditionals as $conditional) {

			    if ( ! is_null ($conditional['elementid']) ) {
				
				if (! nbt_copy_conditional_display_event ($elementid_lup[$conditional['elementid']], NULL, $elementid_lup[$conditional['trigger_element']], $selopt_lup[$conditional['trigger_option']], $conditional['type'])) {
				    echo "Error importing element " . $conditional['elementid'] . "<br>";
				}
			    }

			    if ( ! is_null ($conditional['subelementid']) ) {
				
				if (! nbt_copy_conditional_display_event (NULL, $subelementid_lup[$conditional['subelementid']], $subelementid_lup[$conditional['trigger_element']], $selopt_lup[$conditional['trigger_option']], $conditional['type'])) {
				    echo "Error importing element " . $conditional['elementid'] . "<br>";
				}
			    }
			    
			    
			}

			include ( ABS_PATH . "forms/forms.php");

			break;

		    default:

			// Trying to import from a non-supported version of Numbat
			
			$nbtErrorText = "You are trying to import a Numbat form with version " . $import['numbatversion'] . ". This Numbat instance only supports version 2.12.";

			include ( ABS_PATH . "header.php" );
			include ( ABS_PATH . "error.php" );
			
			break;
		}

	    }
	    
	}
	
    }

} else {
    
    $nbtErrorText = "You are not logged in, or you do not have sufficient privileges to perform this action.";

    include ( ABS_PATH . "header.php" );
    include ( ABS_PATH . "error.php" );

}

include ( ABS_PATH . "footer.php" );

?>
