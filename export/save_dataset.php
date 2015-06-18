<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	unlink ( ABS_PATH . "export/result.csv" );

	switch ( $_POST['export_type'] ) {

            case "extraction":

                  if ( $_POST['master'] == 0 ) {

				exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", extractions_" . $_POST['formid'] . " WHERE extractions_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id AND extractions_" . $_POST['formid'] . ".status = 2 ORDER BY extractions_" . $_POST['formid'] . ".timestamp_started ASC;\" > " . ABS_PATH . "export/result.csv" );

                  } else {

				exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", m_extractions_" . $_POST['formid'] . " WHERE m_extractions_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id AND m_extractions_" . $_POST['formid'] . ".status = 2 ORDER BY m_extractions_" . $_POST['formid'] . ".timestamp_started ASC;\" > " . ABS_PATH . "export/result.csv" );

                  }

            break;

            case "sub_extraction":

			if ( $_POST['master'] == 0 ) {

				exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", sub_" . $_POST['formid'] . " WHERE sub_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/result.csv" );

			} else {

				exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", msub_" . $_POST['formid'] . " WHERE msub_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/result.csv" );

			}


            break;

            case "citations":

			if ( $_POST['master'] == 0 ) {

				exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", citations_" . $_POST['formid'] . " WHERE citations_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/result.csv" );

			} else {

				exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mcite_" . $_POST['formid'] . " WHERE mcite_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/result.csv" );

			}


            break;

            case "table_data":

			if ( $_POST['master'] == 0 ) {

				exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/result.csv" );

			} else {

				exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/result.csv" );

			}

            break;

            case "ltable_data":

			if ( $_POST['master'] == 0 ) {

				exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", tabledata_" . $_POST['formid'] . " WHERE tabledata_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/result.csv" );

			} else {

				exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ", mtable_" . $_POST['formid'] . " WHERE mtable_" . $_POST['formid'] . ".referenceid = referenceset_" . $_POST['refsetid'] . ".id;\" > " . ABS_PATH . "export/result.csv" );

			}

            break;

      }

}

?>
