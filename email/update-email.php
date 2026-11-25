<?php

include_once (__DIR__ . "../config.php");

if (nbt_save_outgoing_email_settings (
    $_POST['enabled'],
    $_POST['host'],
    $_POST['user'],
    $_POST['password']
)) {
    echo "Saved";
} else {
    echo "Not saved";
}

?>
