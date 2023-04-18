<?php

include_once('../config.php');

if ( nbt_user_is_logged_in () ) { // User is logged in

    if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	if ($_POST['action'] == "upload") { // Trying to upload

	    if ($_FILES["file"]["error"][0] > 0) { // There's an upload error
		
		if ($_FILES["file"]["error"][0] == 4) { // No file selected

                    $nbtErrorText = "No file selected";

                    include ( ABS_PATH . "header.php" );
                    include ( ABS_PATH . "error.php" );

		} else {

                    $nbtErrorText = "Upload error: " . $_FILES["file"]["error"][0];

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

		$files_n = count($_FILES['file']['name']);
		$file_counter = 0;
		
		for ($i = 0; $i < $files_n; $i++) {

		    $path = $_FILES["file"]["name"][$i];
		    $ext = pathinfo($path, PATHINFO_EXTENSION);

		    // Record upload in db
		    $upload_id = nbt_new_file_upload($path, $_SESSION[INSTALL_HASH . '_nbt_userid']);

		    if ($upload_id) { // Successfully recorded in db
			
			if (move_uploaded_file ( $_FILES["file"]["tmp_name"][$i], ABS_PATH . "uploads/files/" . $path )) { // File moved to uploads folder successfully
			    

			} else { // File not moved to uploads folder successfully

			    $nbtErrorText = "Error moving file to uploads folder.";

			    include ( ABS_PATH . "header.php" );
			    include ( ABS_PATH . "error.php" );
			    
			}
			
		    }
		    
		}

		include ( ABS_PATH . "header.php" );
		include ( ABS_PATH . "uploads/success.php");
		include ( ABS_PATH . "uploads/list-uploads.php");
		
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
