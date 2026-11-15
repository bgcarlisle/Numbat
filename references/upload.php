<?php

include_once ("../config.php");

$illegal_columns = [
    "id",
    "referenceid",
    "rid"
];

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

		$lines = array();

		while (($udata = fgetcsv($file, 100000, "\t", "\"", "\\")) !== FALSE) {
		    $lines[] = $udata;
		}

		fclose ( $file );

		$counter = 0;

		$columns = $lines[0];

		unset ($lines[0]);

?><div class="nbtGreyGradient" style="margin: 20px 40px 20px 40px;">
    <h2>New reference set</h2>
    <p><?php echo count ($columns); ?> column(s); <?php echo count($lines); ?> row(s)</p>
    <form action="<?php echo SITE_URL; ?>references/new.php" method="POST">
	<h3>Choose a name for your reference set</h3>
	<input type="text" name="nbtNewRefSetName">
	<input type="hidden" name="nbtNumberOfColumns" value="<?php echo count($columns); ?>">

	<?php

	$colcount = 0;
	$newcols = [];

	foreach ( $columns as $column ) {

	    // Make a guess about the column type

	    // loop through each line and determine the appropriate column type
	    $allblank = TRUE;
	    $allint = TRUE;
	    $maxstrcount = 0;
	    $alldates = TRUE;
	    foreach ($lines as $line) {

		if ( $line[$colcount] != "" ) {
		    $allblank = FALSE;
		}

		if (!ctype_digit($line[$colcount])) {

		    if ($line[$colcount] != "NULL" && $line[$colcount] != "") {
			$allint = FALSE;
		    }
		}

		if ( strlen ($line[$colcount]) > $maxstrcount ) {
		    $maxstrcount = strlen($line[$colcount]);
		}

		if ( preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $line[$colcount]) != 1 ) {
		    if ($line[$colcount] != "NULL" && $line[$colcount] != "") {
			$alldates = FALSE;
		    }
		}
	    }

	    if ($allblank) {
		//empty
		$coltype = "text";
	    } else {
		if ($alldates) {
		    // it's a date
		    $coltype = "date";
		} else {
		    if ($allint) {
			// it's an int
			$coltype = "int";
		    } else {
			// treat it as text
			$coltype = "text";
		    }
		}
	    }

	    // rename the column headings

	    $column = str_replace(" ", "_", $column);

	    $column = preg_replace('/[^A-Za-z0-9 ]/', "_", $column);

	    $column = preg_replace('/^(_)+/', '', $column);

	    $column = preg_replace('/(_)+$/', '', $column);

	    $column = strtolower($column);

	    // Remove illegal column names
	    if (in_array($column, $illegal_columns)) {
		$column = "imported_" . $column;
	    }

	    $newcols[$colcount] = $column;

	    // echo $column . " " . $coltype;
	?>


	<input type="hidden" name="nbtNewColName<?php echo $colcount; ?>" value="<?php echo $column; ?>">
	<input type="hidden" name="nbtNewColType<?php echo $colcount; ?>" value="<?php echo $coltype; ?>">
	<?php

	$colcount++;

	}

	?>
	<h3>Choose reference metadata</h3>
	<?php

	$refmeta = "";

	if (in_array("nct_number", $newcols)) {

	    $refmeta .= '<h4>$nct_number</h4>
'; // Line break is intentional
	    
	    if (in_array("study_title", $newcols)) {

		$refmeta .= '<p>$study_title';

		if (in_array("start_date", $newcols)) {
		    $refmeta .= ' | $start_date';

		    if (in_array("primary_completion_date", $newcols)) {
			$refmeta .= ' to $primary_completion_date';
		    }
		}
		
		$refmeta .= '</p>';
		
	    }
	   
	}

	$refmetamore = "";

	if (in_array("abstract", $newcols)) {

	    $refmetamore = '<p>$abstract</p>';
	    
	} else {

	    if (in_array("study_design", $newcols)) {

	    $refmetamore = '<p>$study_design</p>';
		
	    }
	}

	?>
	<div style="margin-bottom: 20px;">
	    <p>Basic reference metadata: You must indicate which metadata are displayed to users who work with this reference set by choosing which columns are displayed. To display the contents of the `title` column, for example, enter $title into the following text field (HTML formatting allowed).</p>
	    <div style="padding: 5px 5px 0 5px; margin-bottom: 10px">
		<p>Columns in this reference set:</p>
		<?php

		foreach ($newcols as $col) {
		    echo '<span style="border: 1px solid #333; padding: 1px 4px 0 4px; border-radius: 3px; margin: 2px 4px 4px 4px; white-space: nowrap;" onclick="$(\'#nbtMetadata-refmeta\').append(\' $' . $col . '\');">$' . $col  . '</span>
'; // Line break is intentional
		}
		
		?>
	    </div>
	    <textarea id="nbtMetadata-refmeta" style="width: 100%; height: 150px;" name="nbtRefmeta"><?php echo $refmeta; ?></textarea>
	    <p>Extended reference metadata: this data is only shown to extractors when they click the "more" button. You might include a reference abstract here, for example.</p>
	    <div style="padding: 5px 5px 0 5px; margin-bottom: 10px">
		<p>Columns in this reference set:</p>
		<?php

		foreach ($newcols as $col) {
		    echo '<span style="border: 1px solid #333; padding: 1px 4px 0 4px; border-radius: 3px; margin: 2px 4px 4px 4px; white-space: nowrap;" onclick="$(\'#nbtMetadata-refmetamore\').append(\' $' . $col . '\');">$' . $col  . '</span>
';
		}
		
		?>
	    </div>
	    <textarea id="nbtMetadata-refmetamore" style="width: 100%; height: 150px;" name="nbtRefmetamore"><?php echo $refmetamore; ?></textarea>
	</div>
	<input type="submit" value="Save changes">
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
