<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {

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

		if ( ! is_dir ( ABS_PATH . "references/tmp/" ) ) {

			mkdir ( ABS_PATH . "references/tmp/", 0777 );

		} else {

			chmod ( ABS_PATH . "references/tmp/", 0777 );

		}

		move_uploaded_file ( $_FILES["file"]["tmp_name"], ABS_PATH . "references/tmp/tmp.txt" );

		$file = fopen ( ABS_PATH . "references/tmp/tmp.txt", "r" );

		if ( ! $file ) {

			$nbtErrorText = "Error opening file";

			include ( ABS_PATH . "error.php" );

		} else {

			$filesize = filesize ( ABS_PATH . "references/tmp/tmp.txt" );

			if ( ! $filesize ) {

				$nbtErrorText = "File is empty: " . $filesize;

				include ( ABS_PATH . "error.php" );

			} else {

				$filecontent = fread ( $file, $filesize );

				fclose ( $file );

				$counter = 0;

				$lines = array();

				foreach ( explode ( "\r", $filecontent ) as $line ) {

					$lines[$counter] = $line;

					$counter++;

				}

				$columns = explode ("\t", $lines[0]);

				unset ($lines[0]);

				?><div class="nbtGreyGradient" style="margin: 20px 40px 20px 40px;">
					<h2>New reference set</h2>
					<p>Below is a table generated from the file you uploaded. The column headings are taken from the first row of the file, and the table has been populated with the subsequent 3 rows only.</p>
					<p>Please select the appropriate data types for each of the columns. Numbat has tried to guess appropriate ones based on your column headings, but please double-check. Click "Save changes" to finish making your new reference set and insert the data from the file into the database.</p>
					<form action="<?php echo SITE_URL; ?>references/new.php" method="POST">
						<h3>Choose a name for your reference set</h3>
						<input type="text" name="nbtNewRefSetName">
						<input type="hidden" name="nbtNumberOfColumns" value="<?php echo count($columns); ?>">
						<input type="submit" value="Save changes">
						<table class="nbtTabledData" style="margin-top: 10px;">
							<tr class="nbtTableHeaders"><?php

								$colcount = 0;

								foreach ( $columns as $column ) {

									?><td><?php echo $column; ?><br>
									<input type="hidden" name="nbtNewColName<?php echo $colcount; ?>" value="<?php echo $column; ?>">
									<select name="nbtNewColType<?php echo $colcount; ?>">
										<option>Choose data type</option>
										<option value="int"<?php

											if ( ( $column == "year" ) || ( $column == "databaseid") ) {

												?> selected<?php

											}

										?>>Integer</option>
										<option value="varchar50"<?php

											if ( $column == "database" ) {

												?> selected<?php

											}

										?>>Varchar (50)</option>
										<option value="varchar500"<?php

											if ( ( $column == "title" ) || ( $column == "journal") ) {

												?> selected<?php

											}

										?>>Varchar (500)</option>
										<option value="varchar1000"<?php

											if ( $column == "authors" ) {

												?> selected<?php

											}

										?>>Varchar (1000)</option>
										<option value="varchar6000"<?php

											if ( $column == "abstract" ) {

												?> selected<?php

											}

										?>>Varchar (6000)</option>
										<option value="date"<?php

											if ( $column == "date" ) {

												?> selected<?php

											}

										?>>Date</option>
									</select></td><?php

									$colcount++;

								}

							?></tr>
						<?php

						// Then give a few example rows ...

						$counter = 0;

						foreach ( $lines as $line ) if ($counter++ < 3) {

							?><tr><?php

							$cells = explode ("\t", $line);

							foreach ( $cells as $cell ) {

								// if the cell begins and ends with a quotation mark, remove it

								$length = strlen ($cell);

								if ( ( substr ($cell, $length-1, 1) == "\"" ) && ( substr ($cell, 0, 1) == "\"" ) ) {

									$cell = substr ( $cell, 1, $length-2 );

								}

								?><td><?php echo $cell; ?></td><?php


							}

							?></tr><?php

						}

						?>
						</table>
					</form>
				</div><?php

			}

		}

	}

} else {

	$nbtErrorText = "You do not have sufficient privileges";

	include ( ABS_PATH . "header.php" );
	include ( ABS_PATH . "error.php" );

}

include ( ABS_PATH . "footer.php" );

?>
