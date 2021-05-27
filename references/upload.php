<?php

include_once ("../config.php");

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

		$filecontent = fread ( $file, $filesize );

		$encoding = mb_detect_encoding ($filecontent, ["UTF-8"]);

		if ($encoding != "UTF-8") {
		    $filecontent = mb_convert_encoding ($filecontent, "UTF-8", "UTF-16");
		}

		fclose ( $file );

		$counter = 0;

		$lines = array();

		if ( count ( explode ( "\n", $filecontent ) ) > count ( explode ( "\r\n", $filecontent ) ) ) {

		    $line_demarcation = "\n";

		} else {

		    $line_demarcation = "\r\n";

		}

		foreach ( explode ( $line_demarcation, $filecontent ) as $line ) {

		    if ( strlen (trim($line)) > 0 ) {

			$lines[$counter] = $line;
			
		    }

		    $counter++;

		}

		$columns = explode ("\t", $lines[0]);

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

	foreach ( $columns as $column ) {

	    // Make a guess about the column type

	    // loop through each line and determine the appropriate column type
	    $allblank = TRUE;
	    $allint = TRUE;
	    $maxstrcount = 0;
	    $alldates = TRUE;
	    foreach ($lines as $line) {

		// echo explode("\t", $line)[$colcount];

		if ( explode("\t", $line)[$colcount] != "" ) {
		    $allblank = FALSE;
		}

		if (!ctype_digit(explode("\t", $line)[$colcount])) {

		    if (explode("\t", $line)[$colcount] != "NULL" && explode("\t", $line)[$colcount] != "") {
			$allint = FALSE;
		    }
		}

		if ( strlen (explode("\t", $line)[$colcount]) > $maxstrcount ) {
		    $maxstrcount = strlen(explode("\t", $line)[$colcount]);
		}

		if ( preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', explode("\t", $line)[$colcount]) != 1 ) {
		    if (explode("\t", $line)[$colcount] != "NULL" && explode("\t", $line)[$colcount] != "") {
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

	    // echo $column . " " . $coltype;
	?>

	
	<input type="hidden" name="nbtNewColName<?php echo $colcount; ?>" value="<?php echo $column; ?>">
	<input type="hidden" name="nbtNewColType<?php echo $colcount; ?>" value="<?php echo $coltype; ?>">
	<?php

	$colcount++;

	}
	
	?>
	<h3>Choose columns for reference metadata</h3>
	<div style="margin-bottom: 20px;">
	    <p>Title</p>
	    <select name="nbtTitleColumn">
		<option>Choose a column</option>
		<?php

		$colcount = 0;

		foreach ($columns as $column) {
		    // rename the column headings

		    $column = str_replace(" ", "_", $column);
		    
		    $column = preg_replace('/[^A-Za-z0-9 ]/', "_", $column);

		    $column = preg_replace('/^(_)+/', '', $column);

		    $column = preg_replace('/(_)+$/', '', $column);

		    $column = strtolower($column);

		    if ( $column == "title" ) {
			echo '<option value="' . $colcount . '" selected>' . $column . '</option>';
		    } else {
			echo '<option value="' . $colcount . '">' . $column . '</option>';
		    }

		    $colcount++;
		}

		?>
	    </select>
	    
	    <p>Authors</p>
	    <select name="nbtAuthorsColumn">
		<option>Choose a column</option>
		<?php

		$colcount = 0;
		$selected = 0;

		foreach ($columns as $column) {
		    // rename the column headings

		    $column = str_replace(" ", "_", $column);
		    
		    $column = preg_replace('/[^A-Za-z0-9 ]/', "_", $column);

		    $column = preg_replace('/^(_)+/', '', $column);

		    $column = preg_replace('/(_)+$/', '', $column);

		    $column = strtolower($column);

		    if ( ($column == "authors" || $column == "author" || $column == "locations") && $selected == 0 ) {
			echo '<option value="' . $colcount . '" selected>' . $column . '</option>';
			$selected++;
		    } else {
			echo '<option value="' . $colcount . '">' . $column . '</option>';
		    }

		    $colcount++;
		}

		?>
	    </select>
	    
	    <p>Year</p>
	    <select name="nbtYearColumn">
		<option>Choose a column</option>
		<?php

		$colcount = 0;
		$selected = 0;

		foreach ($columns as $column) {
		    // rename the column headings

		    $column = str_replace(" ", "_", $column);
		    
		    $column = preg_replace('/[^A-Za-z0-9 ]/', "_", $column);

		    $column = preg_replace('/^(_)+/', '', $column);

		    $column = preg_replace('/(_)+$/', '', $column);

		    $column = strtolower($column);

		    if ( ($column == "year" || $column == "publication_year" || $column == "start_date") && $selected == 0 ) {
			echo '<option value="' . $colcount . '" selected>' . $column . '</option>';
		    } else {
			echo '<option value="' . $colcount . '">' . $column . '</option>';
		    }

		    $colcount++;
		}

		?>
	    </select>
	    
	    <p>Journal</p>
	    <select name="nbtJournalColumn">
		<option>Choose a column</option>
		<?php

		$colcount = 0;
		$selected = 0;

		foreach ($columns as $column) {
		    // rename the column headings

		    $column = str_replace(" ", "_", $column);
		    
		    $column = preg_replace('/[^A-Za-z0-9 ]/', "_", $column);

		    $column = preg_replace('/^(_)+/', '', $column);

		    $column = preg_replace('/(_)+$/', '', $column);

		    $column = strtolower($column);

		    if ( ($column == "journal" || $column == "publication_title" || $column == "status") && $selected == 0 ) {
			echo '<option value="' . $colcount . '" selected>' . $column . '</option>';
			$selected++;
		    } else {
			echo '<option value="' . $colcount . '">' . $column . '</option>';
		    }

		    $colcount++;
		}

		?>
	    </select>
	    
	    <p>Abstract</p>
	    <select name="nbtAbstractColumn">
		<option>Choose a column</option>
		<?php

		$colcount = 0;
		$selected = 0;

		foreach ($columns as $column) {
		    // rename the column headings

		    $column = str_replace(" ", "_", $column);
		    
		    $column = preg_replace('/[^A-Za-z0-9 ]/', "_", $column);

		    $column = preg_replace('/^(_)+/', '', $column);

		    $column = preg_replace('/(_)+$/', '', $column);

		    $column = strtolower($column);

		    if ( ($column == "abstract" || $column == "abstract_note" || $column == "acronym") && $selected == 0) {
			echo '<option value="' . $colcount . '" selected>' . $column . '</option>';
			$selected++;
		    } else {
			echo '<option value="' . $colcount . '">' . $column . '</option>';
		    }

		    $colcount++;
		}

		?>
	    </select>
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
