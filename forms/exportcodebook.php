<?php

header('Content-Type: text/markdown');

$form = nbt_get_form_for_id ($_GET['id']);

$elements = nbt_get_elements_for_formid ($_GET['id']);

echo "---\n";

echo "title: \"" . $form['name'] . "\"\n";
echo "date: \"" . date("Y-m-d") . "\"\n";

echo "---\n\n";

echo "# Extraction form elements\n\n";

foreach ( $elements as $element ) {

    switch ( $element['type'] ) {
	case "section_heading":

	    echo "## " . $element['displayname'] . "\n\n";

	    break;

	case "open_text":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Open text field\n\n";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";

	    break;

	case "text_area":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Text area\n\n";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";
	    
	    break;

	case "single_select":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Categorical (single selection only)\n\n";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";

	    echo "Extractors were prompted to select one of the following mutually exclusive options.\n\n";

	    $selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

	    echo "| Displayed option name | Database value |\n";
	    echo "|-----------------------|----------------|\n";

	    foreach ( $selectoptions as $select ) {
		echo "| " . $select['displayname'] . " | " . $select['dbname'] . " |\n";
	    }
	    
	    echo "\n";
	    
	    break;

	case "multi_select":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Categorical (multiple selection allowed)\n\n";

	    echo "Database column prefix: `" . $element['columnname'] . "`\n\n";

	    echo "Extractors were prompted to select one or more of the following options.\n\n";

	    $selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

	    echo "| Displayed option name | Database value |\n";
	    echo "|-----------------------|----------------|\n";

	    foreach ( $selectoptions as $select ) {
		echo "| " . $select['displayname'] . " | " . $select['dbname'] . " |\n";
	    }
	    
	    echo "\n";
	    
	    break;

	case "ltable_data":

	case "table_data":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Table data\n\n";

	    echo "Extractors were prompted to add rows to a table of open text fields with the following column headings.\n\n";

	    $tablecolumns = nbt_get_all_columns_for_table_data ( $element['id'] );

	    echo "| Displayed column name | Database column name |\n";
	    echo "|-----------------------|----------------------|\n";

	    foreach ( $tablecolumns as $column ) {
		echo "| " . $column['displayname'] . " | " . $column['dbname'] . " |\n";
	    }
	    
	    echo "\n";

	    break;

	case "citations":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Citation selector\n\n";

	    echo "Extractors were prompted to enter citation data.\n\n";

	    $citationcolumns = nbt_get_all_columns_for_citation_selector ( $element['id'] );

	    if ( count ($citationcolumns) > 0 ) {

		echo "Extractors were prompted to code each extraction for the following properties.\n\n";

		echo "| Displayed prompt | Database column |\n";
		echo "|------------------|-----------------|\n";

		foreach ( $citationcolumns as $column ) {
		    echo "| " . $column['displayname'] . " | " . $column['dbname'] . " |\n";
		}
		
		echo "\n";
		
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
	    
	    break;

	case "date_selector":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Date\n\n";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";

	    echo "Extractors were prompted to enter a date.\n\n";

	    break;

	case "reference_data":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Extractors were prompted with the following. Names of columns in the uploaded reference set preceded by '$' would be replaced with the value of the corresponding column from the row of the reference to be extracted.\n\n";

	    echo "```\n";
	    echo $element['columnname'];
	    echo "\n```\n\n";
	    

	    break;

	case "prev_select":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Variable type: Open text field\n\n";

	    echo "Extractors were prompted with values for this field that have been chosen in previous extractions in order to ensure consistency.";

	    echo "Database column name: `" . $element['columnname'] . "`\n\n";
	    
	    break;

	case "assignment_editor":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Extractors were prompted to assign this reference to another extractor.\n\n";
	    
	    break;

	case "sub_extraction":

	    echo "### " . $element['displayname'] . "\n\n";

	    echo "Sub-extraction: an extraction form element that contains other form elements and can be repeated by the extractor as many times as necessary within an extraction.\n\n";

	    // THIS ISN'T DONE YET

	    break;
	    
    }

    if ( $element['codebook'] != "" ) {

	echo "Extractor prompt: " . $element['codebook'] . "\n\n";
	
    }
    
}

?>
