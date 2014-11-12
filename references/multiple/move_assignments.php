<?php

include_once ("../../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {

      nbt_move_assignments_for_refset_fromref_toref ( $_POST['refset'], $_POST['from_rid'], $_POST['to_rid'] );

}

?>
