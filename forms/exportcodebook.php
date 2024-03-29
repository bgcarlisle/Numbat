<?php

$form = nbt_get_form_for_id ($_GET['id']);

header('Content-Type: text/markdown');
header('Content-Disposition: attachment; filename="' . date("Y-m-d") . '-' . $form['name'] . '.md"');

$elements = nbt_get_elements_for_formid ($_GET['id']);

echo "---\n";

echo "title: \"" . $form['name'] . "\"\n";
echo "author:\n";
echo "- name: \"" . $form['author'] . "\"\n";
echo "  affiliation: \"" . $form['affiliation'] . "\"\n";
echo "date: \"" . $form['projectdate'] . "\"\n";

echo "---\n\n";

echo "# Extraction form details\n\n";

echo $form['name'] . " version " . $form['version'] . "\n\n";

echo $form['description'] . "\n\n";

echo "[" . $form['project'] . "](" . $form['protocol'] . ")\n\n";

echo "# Extraction form elements\n\n";

foreach ( $elements as $element ) {

    if ( $element['codebook'] != "" ) {

	$element['codebook'] = "\n```\n" . $element['codebook'] . "\n```";
	
    }

    switch ( $element['type'] ) {
	    
	case "section_heading":

	    echo "## " . $element['displayname'] . "\n\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }

	    break;

	case "open_text":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Open text field\n\n";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";

	    if ( $element['regex'] != "" ) {

		echo "This text field used regular expression validation.";

		echo "This field would only be saved if the text entered matched the following regex.\n\n";

		echo "Regex: `" . $element['regex'] . "`\n\n";
	    }

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }

	    break;

	case "text_area":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Text area\n\n";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }
	    
	    break;

	case "single_select":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Categorical (single selection only)\n\n";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";

	    echo "Extractors were prompted to select one of the following mutually exclusive options.\n\n";

	    $selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

	    echo "| Displayed option name | Database value |\n";
	    echo "|:----------------------|---------------:|\n";

	    foreach ( $selectoptions as $select ) {
		echo "| " . $select['displayname'] . " | " . $select['dbname'] . " |\n";
	    }
	    
	    echo "\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }
	    
	    break;

	case "multi_select":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Categorical (multiple selection allowed)\n\n";

	    echo "Extractors were prompted to select one or more of the following options.\n\n";

	    echo "The options selected by extractors would be exported with a 1 in the corresponding database column.\n\n";

	    $selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

	    echo "| Displayed option name | Database column |\n";
	    echo "|:----------------------|----------------:|\n";

	    foreach ( $selectoptions as $select ) {
		echo "| " . $select['displayname'] . " | " . $element['columnname'] . "_" . $select['dbname'] . " |\n";
	    }
	    
	    echo "\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }
	    
	    break;

	case "ltable_data":

	case "table_data":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Table data\n\n";

	    echo "Extractors were prompted to add rows to a table of open text fields with the following column headings.\n\n";

	    $tablecolumns = nbt_get_all_columns_for_table_data ( $element['id'] );

	    echo "| Displayed column name | Database column name |\n";
	    echo "|:----------------------|---------------------:|\n";

	    foreach ( $tablecolumns as $column ) {
		echo "| " . $column['displayname'] . " | " . $column['dbname'] . " |\n";
	    }
	    
	    echo "\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }

	    break;

	case "citations":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Citation selector\n\n";

	    echo "Extractors were prompted to enter citation data.\n\n";

	    $citationcolumns = nbt_get_all_columns_for_citation_selector ( $element['id'] );

	    if ( count ($citationcolumns) > 0 ) {

		echo "Extractors were prompted to code each extraction for the following properties.\n\n";

		echo "| Displayed prompt | Database column |\n";
		echo "|:-----------------|----------------:|\n";

		foreach ( $citationcolumns as $column ) {
		    echo "| " . $column['displayname'] . " | " . $column['dbname'] . " |\n";
		}
		
		echo "\n";
		
	    }

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }
	    
	    break;

	case "country_selector":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Country selector\n\n";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";

	    echo "Extractors were prompted to select a country from the following list.\n\n";

	    $countries = nbt_return_country_array ();

	    foreach ( $countries as $country ) {
		if ($country != "Choose a country" ) {
		    echo "* " . $country . "\n";
		}
	    }

	    echo "\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }
	    
	    break;

	case "date_selector":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Date\n\n";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";

	    echo "Extractors were prompted to enter a date.\n\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }

	    break;

	case "reference_data":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Extractors were prompted with the following. Names of columns in the uploaded reference set preceded by '$' would be replaced with the value of the corresponding column from the row of the reference to be extracted.\n\n";

	    echo "```\n";
	    echo $element['columnname'];
	    echo "\n```\n\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }
	    
	    break;

	case "prev_select":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Open text field\n\n";

	    echo "Extractors were prompted with values for this field that have been chosen in previous extractions in order to ensure consistency.";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }
	    
	    break;

	case "tags":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Categorical (multiple selection allowed)\n\n";

	    echo "Extractors were presented with a list of editable tags to select. Selected tags could be added to the list of tag prompts for future extractions.\n\n";

	    $tagprompts = explode(";", $element['tagprompts']);
	    $tagprompts = array_map('trim', $tagprompts);

	    echo "| Tag        |\n";
	    echo "|:-----------|\n";

	    foreach ($tagprompts as $tagprompt) {
		echo "| " . $tagprompt . " |\n";
	    }

	    echo "\n";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }
	    
	    break;

	case "assignment_editor":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Extractors were prompted to assign this reference to another extractor.\n\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }
	    
	    break;

	case "sub_extraction":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Sub-extraction: an extraction form element that contains other form elements and can be repeated by the extractor as many times as necessary within an extraction.\n\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }

	    $subelements = nbt_get_sub_extraction_elements_for_elementid ( $element['id'] );

	    foreach ($subelements as $subelement) {

		switch ( $subelement['type']) {
		    case "open_text":

			echo "#### " . $subelement['displayname'] . "\n\n";

			echo "Sub-extraction variable type: Open text field\n\n";

			echo "Database column name: `" . $subelement['dbname'] . "`\n\n";

			if ( $subelement['regex'] != "" ) {

			    echo "This text field used regular expression validation.";

			    echo "This field would only be saved if the text entered matched the following regex.\n\n";

			    echo "Regex: `" . $subelement['regex'] . "`\n\n";

			}

			if ( $subelement['codebook'] != "" ) {

			    echo "Extractor prompt: " . $subelement['codebook'] . "\n\n";
			    
			}
			
			break;

		    case "date_selector":

			echo "#### " . $subelement['displayname'] . "\n\n";

			echo "Sub-extraction variable type: Date\n\n";

			echo "Database column name: `" . $subelement['dbname'] . "`\n\n";

			if ( $subelement['codebook'] != "" ) {

			    echo "Extractor prompt: " . $subelement['codebook'] . "\n\n";
			    
			}
			
			break;

		    case "single_select":

			echo "#### " . $subelement['displayname'] . "\n\n";

			echo "Sub-extraction variable type: Categorical (single selection only)\n\n";

			echo "Database column name: `" . $subelement['dbname'] . "`\n\n";

			echo "Extractors were prompted to select one of the following mutually exclusive options.\n\n";

			$selectoptions = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );

			echo "| Displayed option name | Database value |\n";
			echo "|:----------------------|---------------:|\n";

			foreach ( $selectoptions as $select ) {
			    echo "| " . $select['displayname'] . " | " . $select['dbname'] . " |\n";
			}
			
			echo "\n";

			if ( $subelement['codebook'] != "" ) {

			    echo "Extractor prompt: " . $subelement['codebook'] . "\n\n";
			    
			}

			break;

		    case "multi_select":

			echo "#### " . $subelement['displayname'] . "\n\n";

			echo "Sub-extraction variable type: Categorical (multiple selection allowed)\n\n";

			echo "Database column prefix: `" . $subelement['dbname'] . "`\n\n";

			echo "Extractors were prompted to select one or more of the following options.\n\n";

			$selectoptions = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );

			echo "| Displayed option name | Database value |\n";
			echo "|:----------------------|---------------:|\n";

			foreach ( $selectoptions as $select ) {
			    echo "| " . $select['displayname'] . " | " . $select['dbname'] . " |\n";
			}
			
			echo "\n";

			if ( $subelement['codebook'] != "" ) {

			    echo "Extractor prompt: " . $subelement['codebook'] . "\n\n";
			    
			}

			break;

		    case "table_data":

			echo "#### " . $subelement['displayname'] . "\n\n";

			echo "Sub-extraction table data\n\n";

			echo "Extractors were prompted to add rows to a table of open text fields with the following column headings.\n\n";

			$tablecolumns = nbt_get_all_columns_for_table_data ( $element['id'], TRUE );

			echo "| Displayed column name | Database column name |\n";
			echo "|:----------------------|---------------------:|\n";

			foreach ( $tablecolumns as $column ) {
			    echo "| " . $column['displayname'] . " | " . $column['dbname'] . " |\n";
			}

			break;
		}

		
		
	    }

	    break;

	case "timer":

	    echo "## Extraction timer\n\n";

	    echo "Numbat automatically times all extractions starting from the first time a user opens the extraction, until the first time they click 'Complete'. This element displays a timer to the user when the extraction is on-going, and allows the user to re-start the timer.\n\n";

	    if ( $element['codebook'] != "" ) {

		echo "Extractor prompt: " . $element['codebook'] . "\n\n";
		
	    }

	    break;
	    
    }

    // Add conditional display here

    if ($element['startup_visible'] != 1) {

	echo "When the extraction form is loaded, this element was hidden. It would be shown if " . $element['conditional_logical_operator'] . " of the following conditions were met:\n\n";

	$events = nbt_get_conditional_events ($element['id']);

	if (count ($events) > 0) {

	    foreach ($events as $event) {
		
		foreach ($elements as $ele) {
		    if ($ele['id'] == $event['trigger_element']) {
			$trigger_element_name = $ele['displayname'];
			$trigger_elementid = $ele['id'];
		    }
		}

		switch ($event['type']) {
		    case "is":
			$trigger_options = nbt_get_all_select_options_for_element ( $trigger_elementid );
			foreach ( $trigger_options as $topt ) {
			    if ($topt['id'] == $event['trigger_option']) {
				$trigger_option_name = $topt['displayname'];
			    }
			}
			echo "* \"" . $trigger_element_name . "\" is: \"" . $trigger_option_name . "\"\n";
			break;
		    case "is-not":
			$trigger_options = nbt_get_all_select_options_for_element ( $trigger_elementid );
			foreach ( $trigger_options as $topt ) {
			    if ($topt['id'] == $event['trigger_option']) {
				$trigger_option_name = $topt['displayname'];
			    }
			}
			echo "* \"" . $trigger_element_name . "\" is not: \"" . $trigger_option_name . "\"\n";
			break;
		    case "has-response":
			echo "* \"" . $trigger_element_name . "\" has a response\n";
			break;
		    case "no-response":
			echo "* \"" . $trigger_element_name . "\" has no response\n";
			break;
		}

	    }

	    echo "\n";
	    
	} else {

	    echo "No conditions were selected.\n\n";
	    
	}


	if ($element['destructive_hiding'] == 1) {
	    echo "In the case that this element was hidden again by a conditional display event after a response was entered, the response would be cleared.\n\n";
	} else {
	    echo "In the case that this element was hidden again by a conditional display event after a response was entered, the response would be preserved.\n\n";
	}
	
    }
    
}

echo "# Acknowledgements\n\n";

echo "This codebook was automatically generated by Numbat Systematic Review Mananger.(1)\n\n";

echo "# References\n\n";

echo "1. Carlisle, B. G. Numbat Systematic Review Manager [Software]. Retrieved from https://numbat.bgcarlisle.com: *The Grey Literature*; 2020. Available from: https://numbat.bgcarlisle.com";

?>
