<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    // array_map( 'unlink', glob ( ABS_PATH . "export/*.csv" ) );

    // $filename = substr (hash('sha256', rand(0, 10000)), 0, 12);

    $filename = date("Y-m-d_His");
    
    // Get the name of the form and make it filename-friendly
    $form = nbt_get_form_for_id($_POST[formid]);
    $formname = nbt_remove_special($form['name']);

    // Get the name of the reference set and make it filename-friendly
    $refset = nbt_get_refset_for_id($_POST['refsetid']);
    $rsname = nbt_remove_special($refset['name']);

    // Get the columns from the reference set

    $rs_cols = nbt_get_columns_for_refset ( $_POST['refsetid'] );

    // Put those columns together into a string

    $rscols_to_export = array();
    foreach ($rs_cols as $rs_col) {
	if ($rs_col[0] != "id" & $rs_col[0] != "manual") {
	    if ($rs_col[0] == "type") {
		$rs_col[0] = "\`type\`";
	    }
	    $rscols_to_export[] = $rs_col[0];
	}
    }

    $rs_cols_string = "referenceset_" . $_POST['refsetid'] . "." . implode(", referenceset_" . $_POST['refsetid'] . ".", $rscols_to_export);

    switch ( $_POST['export_type'] ) {

        case "extraction":

	    // Get the columns for the extraction form

	    $elements = nbt_get_elements_for_formid ( $_POST['formid'] );

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

            if ( $_POST['final'] == 0 ) {

		// Export the extractions

		// Put those columns together into a string

		$mcols = array();
		$mcols[] = "referenceid";
		$mcols[] = "timestamp_started";
		$mcols[] = "timestamp_finished";

		$meta_cols_string = "extractions_" . $_POST['formid'] . "." . implode(", extractions_" . $_POST['formid'] . ".", $mcols);

		foreach ($fcols as $key => $value) {
		    $fcols[$key] = "REPLACE(REPLACE(REPLACE(extractions_" . $_POST['formid'] . "." . $value . ", '\\\"', '&quot;'), '\\r', '\\\\n'), '\\n', '\\\\n') as '" . $value . "'";
		}

		$form_cols_string = ", " . implode(", ", $fcols);

		if (count($fcols) == 0) {
		    $form_cols_string = "";
		}
		
		$select_cols = $meta_cols_string . ", users.username, " . $rs_cols_string . $form_cols_string;

		$filename = $filename . "_form_" . $_POST['formid'] . "_" . $formname . "_refset_" . $_POST['refsetid'] . "_" . $rsname . "_extractions";
		
		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT " . $select_cols . " FROM referenceset_" . $_POST['refsetid'] . ", extractions_" . $_POST['formid'] . ", users WHERE extractions_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND extractions_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id AND users.id = extractions_" . $_POST['formid'] . ".userid AND extractions_" . $_POST['formid'] . ".status = 2 ORDER BY extractions_" . $_POST['formid'] . ".timestamp_started ASC;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

            } else {

		// Export the final copy

		// Put those columns together into a string

		$mcols = array();
		$mcols[] = "referenceid";
		$mcols[] = "timestamp_started";
		$mcols[] = "timestamp_finished";

		$meta_cols_string = "m_extractions_" . $_POST['formid'] . "." . implode(", m_extractions_" . $_POST['formid'] . ".", $mcols);

		foreach ($fcols as $key => $value) {
		    $fcols[$key] = "REPLACE(REPLACE(REPLACE(m_extractions_" . $_POST['formid'] . "." . $value . ", '\\\"', '&quot;'), '\\r', '\\\\n'), '\\n', '\\\\n') as '" . $value . "'";
		}

		$form_cols_string = ", " . implode(", ", $fcols);

		if (count($fcols) == 0) {
		    $form_cols_string = "";
		}

		$select_cols = $meta_cols_string . ", " .  $rs_cols_string . $form_cols_string;

		$filename = $filename . "_form_" . $_POST['formid'] . "_" . $formname . "_refset_" . $_POST['refsetid'] . "_" . $rsname . "_final";
		
		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT " . $select_cols . " FROM referenceset_" . $_POST['refsetid'] . ", m_extractions_" . $_POST['formid'] . " WHERE m_extractions_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND m_extractions_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id AND m_extractions_" . $_POST['formid'] . ".status = 2 ORDER BY m_extractions_" . $_POST['formid'] . ".timestamp_started ASC;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

            }

            break;

        case "sub_extraction":

	    $se_element = nbt_get_element_for_subextraction_dbname ($_POST['formid']);
	    $form = nbt_get_form_for_id($se_element['formid']);
	    $formname = nbt_remove_special($form['name']);

	    // Get the columns for the sub-extraction form

	    $subelements = nbt_get_subextraction_elements_for_subextraction_dbname ($_POST['formid']);
	    
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
	    
	    if ( $_POST['final'] == 0 ) {

		foreach ($fcols as $key => $value) {
		    $fcols[$key] = "REPLACE(REPLACE(REPLACE(sub_" . $_POST['formid'] . "." . $value . ", '\\\"', '&quot;'), '\\r', '\\\\n'), '\\n', '\\\\n') as '" . $value . "'";
		}

		// Put those columns together into a string

		$form_cols_string = ", " . implode(", ", $fcols);

		if (count($fcols) == 0) {
		    $form_cols_string = 0;
		}

		$select_cols = "sub_" . $_POST['formid'] . ".referenceid, users.username, " . $rs_cols_string . $form_cols_string;

		// echo "sub";

		$filename = $filename . "_sub_" . $_POST['formid'] . "_form_" . $formname . "_refset_" . $_POST['refsetid'] . "_" . $rsname . "_extractions";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT " . $select_cols . " FROM referenceset_" . $_POST['refsetid'] . ", sub_" . $_POST['formid'] . ", users WHERE sub_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND sub_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id AND users.id = sub_" . $_POST['formid'] . ".userid;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    } else {

		foreach ($fcols as $key => $value) {
		    $fcols[$key] = "REPLACE(REPLACE(REPLACE(msub_" . $_POST['formid'] . "." . $value . ", '\\\"', '&quot;'), '\\r', '\\\\n'), '\\n', '\\\\n') as '" . $value . "'";
		}

		// Put those columns together into a string

		$form_cols_string = ", " . implode(", ", $fcols);

		if (count($fcols) == 0) {
		    $form_cols_string = 0;
		}

		$select_cols = "msub_" . $_POST['formid'] . ".referenceid, " . $rs_cols_string . $form_cols_string;

		// echo sub final;

		$filename = $filename . "_sub_" . $_POST['formid'] . "_form_" . $formname . "_refset_" . $_POST['refsetid'] . "_" . $rsname . "_final";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT " . $select_cols . " FROM referenceset_" . $_POST['refsetid'] . ", msub_" . $_POST['formid'] . " WHERE msub_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND msub_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    }


            break;

        case "citations":


	    if ( $_POST['final'] == 0 ) {

		// echo "cite\n\n";

		$filename = $filename . "-cite_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-cite-extraction";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", citations_" . $_POST['formid'] . " WHERE citations_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND citations_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    } else {

		// echo cite final;

		$filename = $filename . "-cite_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-cite-final";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mcite_" . $_POST['formid'] . " WHERE mcite_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mcite_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    }


            break;

        case "table_data":

	    $table_element = nbt_get_table_element_by_tablename ($_POST['formid']);
	    $form = nbt_get_form_for_id($table_element['formid']);
	    $formname = nbt_remove_special($form['name']);

	    if ( $_POST['final'] == 0 ) {

		// echo "table";

		$filename = $filename . "_table_" . $_POST['formid'] . "_form_" . $formname . "_refset_" . $_POST['refsetid'] . "_" . $rsname . "_extractions";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    } else {

		// echo "table final";

		$filename = $filename . "_table_" . $_POST['formid'] . "_form_" . $formname . "_refset_" . $_POST['refsetid'] . "_" . $rsname . "_final";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    }

            break;

        case "ltable_data":

	    $table_element = nbt_get_table_element_by_tablename ($_POST['formid']);
	    $form = nbt_get_form_for_id($table_element['formid']);
	    $formname = nbt_remove_special($form['name']);

	    if ( $_POST['final'] == 0 ) {

		// echo "ltable";

		$filename = $filename . "_table_" . $_POST['formid'] . "_form_" . $formname . "_refset_" . $_POST['refsetid'] . "_" . $rsname . "_extractions";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    } else {

		// echo ltable final;

		$filename = $filename . "_table_" . $_POST['formid'] . "_form_" . $formname . "_refset_" . $_POST['refsetid'] . "_" . $rsname . "_final";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    }

            break;

	case "sub_table":

	    $sub_element = nbt_get_sub_table_element_by_tablename ($_POST['formid']);
	    $element = nbt_get_form_element_for_elementid ($sub_element['elementid']);
	    $form = nbt_get_form_for_id($element['formid']);
	    $formname = nbt_remove_special($form['name']);

	    if ($_POST['final'] == 0) { // extracted copy

		$filename = $filename . "_table_" . $_POST['formid'] . "_form_" . $formname . "_refset_" . $_POST['refsetid'] . "_" . $rsname . "_extractions";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );
		
	    } else { // final copy

		$filename = $filename . "_table_" . $_POST['formid'] . "_form_" . $formname . "_refset_" . $_POST['refsetid'] . "_" . $rsname . "_final";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );
		
	    }
	    
	    break;

    }

}

?>
