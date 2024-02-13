<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

  switch ($_POST['action']) {
    case "include":
      echo nbt_screening_include($_POST['fid'], $_POST['refset'], $_POST['rid']);
      break;
    case "exclude":
      echo nbt_screening_exclude($_POST['fid'], $_POST['refset'], $_POST['rid'], $_POST['reason']);
      break;
    case "notes":
      echo nbt_screening_notes($_POST['fid'], $_POST['refset'], $_POST['rid'], $_POST['notes']);
      break;

  }

}

?>
