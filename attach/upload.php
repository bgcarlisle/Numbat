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

            if ( ! is_dir ( ABS_PATH . "attach/files/" ) ) {

                  mkdir ( ABS_PATH . "attach/files/", 0777 );

            } else {

                  chmod ( ABS_PATH . "attach/files/", 0777 );

            }

            if ( ! is_dir ( ABS_PATH . "attach/files/" . $_POST['refsetid'] . "/" ) ) {

                  mkdir ( ABS_PATH . "attach/files/" . $_POST['refsetid'] . "/", 0777 );

            } else {

                  chmod ( ABS_PATH . "attach/files/" . $_POST['refsetid'] . "/", 0777 );

            }

            $path = $_FILES["file"]["name"];
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            if (move_uploaded_file ( $_FILES["file"]["tmp_name"], ABS_PATH . "attach/files/" . $_POST['refsetid'] . "/" . $_POST['refid'] . "." . $ext )) {

                  ?><div class="nbtContentPanel nbtGreyGradient">
                        <h2>Manage attachments</h2>

                        <h3>Add a new attachment</h3>

                        <p>File has been successfully attached. <a href="<?php echo SITE_URL ?>attach/?action=viewrefset&refset=<?php echo $_POST['refsetid']; ?>">View uploaded files</a> or <a href="<?php echo SITE_URL ?>attach/?action=new">attach more files</a>.</p>
                  </div><?php

            } else {

                  ?><div class="nbtContentPanel nbtGreyGradient">
                        <h2>Manage attachments</h2>

                        <h3>Add a new attachment</h3>

                        <p>Something went wrong.</p>
                  </div><?php

            }

      }

} else {

      $nbtErrorText = "You do not have sufficient privileges";

      include ( ABS_PATH . "header.php" );
      include ( ABS_PATH . "error.php" );

}

include ( ABS_PATH . "footer.php" );

?>
