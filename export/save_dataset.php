<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    // array_map( 'unlink', glob ( ABS_PATH . "export/*.csv" ) );

    // $filename = substr (hash('sha256', rand(0, 10000)), 0, 12);

    $filename = date("Y-m-d_His");

    switch ( $_POST['export_type'] ) {

        case "extraction":

            if ( $_POST['final'] == 0 ) {

		// echo "extraction";

		$filename = $filename . "-form_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-extractions";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", extractions_" . $_POST['formid'] . " WHERE extractions_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND extractions_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id AND extractions_" . $_POST['formid'] . ".status = 2 ORDER BY extractions_" . $_POST['formid'] . ".timestamp_started ASC;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

            } else {

		// echo "final";

		$filename = $filename . "-form_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-final";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", m_extractions_" . $_POST['formid'] . " WHERE m_extractions_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND m_extractions_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id AND m_extractions_" . $_POST['formid'] . ".status = 2 ORDER BY m_extractions_" . $_POST['formid'] . ".timestamp_started ASC;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

            }

            break;

        case "sub_extraction":

	    if ( $_POST['final'] == 0 ) {

		// echo "sub";

		$filename = $filename . "-sub_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-sub-extraction";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", sub_" . $_POST['formid'] . " WHERE sub_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND sub_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    } else {

		// echo sub final;

		$filename = $filename . "-sub_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-sub-final";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", msub_" . $_POST['formid'] . " WHERE msub_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND msub_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

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

	    if ( $_POST['final'] == 0 ) {

		// echo "table";

		$filename = $filename . "-table_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-table-extraction";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    } else {

		// echo "table final";

		$filename = $filename . "-table_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-table-final";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    }

            break;

        case "ltable_data":

	    if ( $_POST['final'] == 0 ) {

		// echo "ltable";

		$filename = $filename . "-table_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-table-extraction";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    } else {

		// echo ltable final;

		$filename = $filename . "-table_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-table-final";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );

	    }

            break;

	case "sub_table":

	    if ($_POST['final'] == 0) { // extracted copy

		$filename = $filename . "-table_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-table-extraction";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );
		
	    } else { // final copy

		$filename = $filename . "-table_" . $_POST['formid'] . "-refset_" . $_POST['refsetid'] . "-table-final";

		echo $filename;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".tsv" );
		
	    }
	    
	    break;

    }

}

?>
