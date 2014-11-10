<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {

      if ( unlink ( ABS_PATH . "attach/files/" . $_POST['refsetid'] . "/" . $_POST['refid'] . "." . $_POST['filetype'] ) ) {

            echo "Deleted";

      }

}

?>
