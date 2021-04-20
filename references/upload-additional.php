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

		$uploaded_columns = explode ("\t", $lines[0]);
		unset ($lines[0]);
		
		$refset_columns = nbt_get_columns_for_refset ( $_POST['refset'] );
		$refset = nbt_get_refset_for_id ( $_POST['refset'] );

		/* Now we have the column names for the TSV that was
		   uploaded and the columns for the refset */

?>
<div class="nbtContentPanel nbtGreyGradient">
    <form action="<?php echo SITE_URL; ?>references/save-additional.php" method="post">
	<h2>Insert more references into a reference set</h2>
	<p>Reference set: <?php echo $refset['name']; ?></p>
	<input type="hidden" name="refset" value="<?php echo $refset['id']; ?>">
	<p>The reference set has the following columns. Please indicate which column from the uploaded TSV to draw new values from. If you do not select a column, it will be left blank.</p>
	<?php $rcol_count = 0; ?>
	<?php foreach ($refset_columns as $rcol) { ?>
	    <?php if ($rcol[0] != "id" & $rcol[0] != "manual") { ?>		
	    <div class="nbtImportElement">
		<p><?php echo $rcol[0]; ?></p>
		<select name="col<?php echo $rcol_count; ?>">
		    <option value="ns">Choose a column</option>
		    <?php $ucol_count = 0; ?>
		    <?php foreach ($uploaded_columns as $ucol) { ?>
			    
			<?php if ($rcol[0] == $ucol) { ?>
			    <option value="<?php echo $ucol_count; ?>" selected><?php echo $ucol; ?></option>
			<?php } else {?>
			    <option value="<?php echo $ucol_count; ?>"><?php echo $ucol; ?></option>
			<?php } ?>
			<?php $ucol_count++; ?>
		    <?php } ?>
		</select>
	    </div>
	    <?php } ?>
	    <?php $rcol_count++; ?>
	<?php } ?>
	<button>Save new rows</button>
    </form>
</div>
<?php
		
	    }
	}
    }

}

?>
