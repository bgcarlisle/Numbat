<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    array_map( 'unlink', glob ( ABS_PATH . "export/*.csv" ) );

    $filename = substr (hash('sha256', rand(0, 10000)), 0, 12);

    echo $filename;

    switch ( $_POST['export_type'] ) {

        case "extraction":

            if ( $_POST['final'] == 0 ) {

		// echo "extraction";

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", extractions_" . $_POST['formid'] . " WHERE extractions_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND extractions_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id AND extractions_" . $_POST['formid'] . ".status = 2 ORDER BY extractions_" . $_POST['formid'] . ".timestamp_started ASC;\" > " . ABS_PATH . "export/" . $filename . ".csv" );

            } else {

		// echo "final";

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", m_extractions_" . $_POST['formid'] . " WHERE m_extractions_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND m_extractions_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id AND m_extractions_" . $_POST['formid'] . ".status = 2 ORDER BY m_extractions_" . $_POST['formid'] . ".timestamp_started ASC;\" > " . ABS_PATH . "export/" . $filename . ".csv" );

            }

            break;

        case "sub_extraction":

	    if ( $_POST['final'] == 0 ) {

		// echo "sub";

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", sub_" . $_POST['formid'] . " WHERE sub_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND sub_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".csv" );

	    } else {

		// echo sub final;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", msub_" . $_POST['formid'] . " WHERE msub_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND msub_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".csv" );

	    }


            break;

        case "citations":


	    if ( $_POST['final'] == 0 ) {

		// echo "cite\n\n";

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", citations_" . $_POST['formid'] . " WHERE citations_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND citations_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".csv" );

	    } else {

		// echo cite final;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mcite_" . $_POST['formid'] . " WHERE mcite_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mcite_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".csv" );

	    }


            break;

        case "table_data":

	    if ( $_POST['final'] == 0 ) {

		// echo "table";

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".csv" );

	    } else {

		// echo "table final";

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".csv" );

	    }

            break;

        case "ltable_data":

	    if ( $_POST['final'] == 0 ) {

		// echo "ltable";

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".csv" );

	    } else {

		// echo ltable final;

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".csv" );

	    }

            break;

	case "sub_table":

	    if ($_POST['final'] == 0) { // extracted copy

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".csv" );
		
	    } else { // final copy

		exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".refsetid = " . $_POST['refsetid'] . " AND mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/" . $filename . ".csv" );
		
	    }
	    
	    break;

    }

}

?>
