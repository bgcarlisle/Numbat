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

		$filecontent = fread ( $file, $filesize );

		fclose ( $file );

		unlink( ABS_PATH . "forms/tmp/tmp.txt" );

		$import = json_decode($filecontent, true);

		if ($import['numbatversion'] == "2.12") { // This is the only supported version

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
			$peid = nbt_get_highest_eid_in_form ( $newformid );

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
				break;

			    case "ltable_data":
				break;

			    case "citations":
				break;

			    case "sub_extraction":
				break;

			    case "assignment_editor":
				break;

			    case "reference_data":
				break;

			    case "prev_select":
				break;
				
			}
			
		    }

		    include ( ABS_PATH . "forms/forms.php");
		    
		} else {
		    // Trying to import from a non-supported version of Numbat
		    
		    $nbtErrorText = "You are trying to import a Numbat form with version " . $import['numbatversion'] . ". This Numbat instance only supports version 2.12.";

		    include ( ABS_PATH . "header.php" );
		    include ( ABS_PATH . "error.php" );
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
