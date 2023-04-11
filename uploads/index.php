<?php

include_once('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in

    if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	if ($_POST['action'] == "upload") { // Trying to upload

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

		if ( ! is_dir ( ABS_PATH . "uploads/files/" ) ) {

                    mkdir ( ABS_PATH . "uploads/files/", 0777 );

		} else {

                    chmod ( ABS_PATH . "uploads/files/", 0777 );

		}

		$path = $_FILES["file"]["name"];
		$ext = pathinfo($path, PATHINFO_EXTENSION);

		$upload_id = nbt_new_file_upload($path);
		
		if (move_uploaded_file ( $_FILES["file"]["tmp_name"], ABS_PATH . "uploads/files/" . $path )) { // File moved to uploads folder successfully

		    include ( ABS_PATH . "header.php" );
		    include ( ABS_PATH . "uploads/success.php");
		    include ( ABS_PATH . "uploads/list-uploads.php");
		    

		} else { // File not moved to uploads folder successfully

		    $nbtErrorText = "Error moving file to uploads folder.";

		    include ( ABS_PATH . "header.php" );
		    include ( ABS_PATH . "error.php" );
		    
		}
		
	    }
	    
	} else { // Not trying to upload

	    include ( ABS_PATH . "header.php" );
	    include ( ABS_PATH . "uploads/list-uploads.php" );
	    
	}

    } else {

	$nbtErrorText = "You do not have permission to upload files.";

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
