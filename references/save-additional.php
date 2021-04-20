<?php

include_once ("../config.php");


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

		$filecontent = fread( $file, $filesize);

		fclose ($file);

		$lines = array();

		if ( count ( explode ( "\n", $filecontent ) ) > count ( explode ( "\r\n", $filecontent ) ) ) {
		    
		    $line_demarcation = "\n";

		} else {

		    $line_demarcation = "\r\n";

		}

		// echo $_POST['refset'] . "<br><br>";

		$refsetcolumns = nbt_get_columns_for_refset ( $_POST['refset'] );

		$colcount = 0;
		$columns = array();
		foreach ($refsetcolumns as $rcol) {
		    if ($_POST["col" . $colcount] != "ns" & $_POST["col" . $colcount] != "") {
			$columns[] = $rcol[0];
		    }
		    $colcount++;
		}

		// echo "COLUMNS (" . implode(", ", $columns) . ")";
		$linecount = 0;
		foreach (explode ($line_demarcation, $filecontent) as $line) {
		    if ($linecount > 0) {
			$colcount = 0;
			$row_to_insert = array();
			foreach ($refsetcolumns as $rcol) {
			    if ($_POST["col" . $colcount] != "ns" & $_POST["col" . $colcount] != "") {
				$valcount = 0;
				foreach (explode ("\t", $line) as $val) {
				    if ($_POST["col" . $colcount] == $valcount) {
					$row_to_insert[] = $val;
				    }
				    $valcount++;
				}
			    }
			    $colcount++;
			}
			nbt_insert_row_into_columns ($_POST['refset'], $columns, implode("\t", $row_to_insert), "\t");
			// echo "VALUES (" . implode(",", $row_to_insert) . ")";
		    }
		    $linecount++;
		    // echo "<br><br>";
		}
		
		include ( ABS_PATH . "header.php" );

		$refset = nbt_get_refset_for_id ($_POST['refset']);

		
?>

    <div class="nbtGreyGradient nbtContentPanel">
	<h2>Rows added</h2>
	<p>Your new rows have been added to <a href="<?php echo SITE_URL; ?>references/?action=view&refset=<?php echo $refset['id']; ?>"><?php echo $refset['name']; ?>.</p>
    </div>
    <?php
    
    }
    }
    }
    }
    
    include ( ABS_PATH . "footer.php" );

    
    ?>
