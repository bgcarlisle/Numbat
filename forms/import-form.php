<?php

include_once ('../config.php');

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

	if ( ! is_dir ( ABS_PATH . "forms/tmp/" ) ) {

	    mkdir ( ABS_PATH . "forms/tmp/", 0777 );

	} else {

	    chmod ( ABS_PATH . "forms/tmp/", 0777 );

	}

	move_uploaded_file( $_FILES["file"]["tmp_name"], ABS_PATH . "forms/tmp/tmp.txt" );

	$file = fopen ( ABS_PATH . "forms/tmp/tmp.txt", "r" );

	if ( ! $file ) {

	    $nbtErrorText = "Error opening file";

	    include ( ABS_PATH . "error.php" );
	    
	} else { // No error opening file

	    $filesize = filesize ( ABS_PATH . "forms/tmp/tmp.txt" );

	    if ( ! $filesize ) {

		$nbtErrorText = "File is empty";

		include ( ABS_PATH . "error.php" );
		
	    } else { // File is not empty

		$filecontent = fread ( $file, $filesize );

		fclose ( $file );

		$form = json_decode($filecontent, true);

		echo $form['name'];
		
	    }
	    
	}
	
    }

} else {
    
    $nbtErrorText = "You are not logged in, or you do not have sufficient privileges to perform this action.";

    include ( ABS_PATH . "header.php" );
    include ( ABS_PATH . "error.php" );

}

include ( ABS_PATH . "footer.php" );

?>
