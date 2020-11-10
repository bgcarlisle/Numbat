<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    array_map( 'unlink', glob ( ABS_PATH . "references/*.csv" ) );

    $filename = $_POST['refsetid'];

    exec ( "mysql -u " . DB_USER . " -p" . DB_PASS . " -h " . DB_HOST . " " . DB_NAME . " -B -e \"SELECT * FROM referenceset_" . $_POST['refsetid'] . ";\" > " . ABS_PATH . "references/referenceset_" . $filename . ".csv" );

    echo $filename;

}

?>
