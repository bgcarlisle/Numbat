<?php

include_once ('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in

	if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

		$file = fopen ( ABS_PATH . "references/tmp/tmp.txt", "r" );

		if ( ! $file ) {

			$nbtErrorText = "Error opening file";

			include ( ABS_PATH . "header.php" );
			include ( ABS_PATH . "error.php" );

		} else {

			$filesize = filesize ( ABS_PATH . "references/tmp/tmp.txt" );

			if ( ! $filesize ) {

				$nbtErrorText = "File is empty: " . $filesize;

				include ( ABS_PATH . "header.php" );
				include ( ABS_PATH . "error.php" );

			} else {

				include ( ABS_PATH . "header.php" );

				?><div class="nbtGreyGradient nbtContentPanel">
				<h2>New reference set</h2><?php

					$filecontent = fread ( $file, $filesize );

					fclose ( $file );

					$counter = 0;

					$lines = array();

					if ( count ( explode ( "\n", $filecontent ) ) > count ( explode ( "\r", $filecontent ) ) ) {

					   $line_demarcation = "\n";

					} else {

					   $line_demarcation = "\r";

					}

					foreach ( explode ( $line_demarcation, $filecontent ) as $line ) {

						$lines[$counter] = $line;

						$counter++;

					}

							  $original_columns = explode ("\t", $lines[0]);

							  $columns = [];
							  
							  foreach ( $original_columns as $column ) {
							      $column = str_replace(" ", "_", $column);
							      
							      $column = preg_replace('/[^A-Za-z0-9 ]/', "_", $column);

							      $column = preg_replace('/^(_)+/', '', $column);

							      $column = preg_replace('/(_)+$/', '', $column);

							      $column = strtolower($column);

							      array_push($columns, $column);
							  }

					unset ($lines[0]);

					// Make a new row in the referencesets table

					$refsetid = nbt_make_new_refset_row ( $_POST['nbtNewRefSetName'] );

					// Make a new refset table

					if ( nbt_make_new_refset_table ( $refsetid ) ) {

						echo "<p>New table made: " . $_POST['nbtNewRefSetName'] . " (referenceset_" . $refsetid . ")</p>";

					} else {

						echo "<p>Error making table</p>";

					}

					// Add columns as appropriate

					$counter = 0;

					?><p><?php

					while ( $counter < ( $_POST['nbtNumberOfColumns'] ) ) {

						if ( nbt_add_column_to_refset_table ( $refsetid, $_POST['nbtNewColName' . $counter], $_POST['nbtNewColType' . $counter] ) ) {

							echo "Added column: " . $_POST['nbtNewColName' . $counter] . "<br>";

						} else {

							echo "Error adding column: " . $_POST['nbtNewColName' . $counter] . "<br>";

						}

						$counter++;

					}

					?></p><?php

					// Insert rows from the file

					$countrows = 0;

					foreach ( $lines as $line ) {

						if ( nbt_insert_row_into_columns ( $refsetid, $columns, $line, "\t" ) ) {

							$countrows++;

						}

					}

					echo "<p>Added " . $countrows . " rows</p>";

				?></div><?php

			}

		}

	} else {

		$nbtErrorText = "You do not have permission to manage reference sets.";

		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "error.php" );

	}

} else {

	$nbtErrorText = "You are not logged in.";

	include ( ABS_PATH . "header.php" );
	include ( ABS_PATH . "error.php" );

}

include ( ABS_PATH . "footer.php" );

?>
