<?php

include_once ("../config.php");

$element = nbt_get_form_element_for_elementid ($_GET['elementid']);

header("Content-Type: text/tsv");
header("Content-Disposition: attachment; filename=" . $element['columnname'] . ".tsv");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {
    
    $extractions = nbt_get_all_extractions_for_refset_and_form ( $_GET['refset'], $_GET['formid'] );

    $all_extractors = [];
    foreach ($extractions as $extraction) {
	$all_extractors[] = $extraction['userid'];
    }
    $extractors = array_unique($all_extractors);
    /* $extractors is an array with the userid's for each
       extractor */
    
    $extractor_names = [];
    foreach ($extractors as $extractor) {
	$extractor_names[] = nbt_get_username_for_userid ($extractor);
    }
    /* $extractor_names is an array with the user names for each
       extractor */

    $all_refs = [];
    foreach ($extractions as $ref) {
	$all_refs[] = $ref['referenceid'];
    }
    $refs = array_unique($all_refs);
    /* $refs is an array with one reference id for each reference*/

    switch ($element['type']) {

	case "single_select":
	case "open_text":

	    // Print the extractor names as column headings
	    echo implode("\t", $extractor_names) . "\n";

	    // Loop through each extracted reference
	    foreach ($refs as $ref) {

		$ref_responses = [];

		// Loop through extractors
		foreach ($extractors as $extractor) {

		    $extraction_found = FALSE;

		    foreach ($extractions as $extraction) {

			if ($extraction['userid'] == $extractor & $extraction['referenceid'] == $ref) {
			    if ($extraction[$element['columnname']] === NULL) {
				$ref_responses[] = "NULL";
			    } else {
				$ref_responses[] = $extraction[$element['columnname']];
			    }
			    $extraction_found = TRUE;
			}
			
		    }

		    if (! $extraction_found) {
			$ref_responses[] = "NA";
		    }
		    
		}

		echo implode("\t", $ref_responses) . "\n";
		
	    }
	    
	    break;
	    
    }

    
}

?>
