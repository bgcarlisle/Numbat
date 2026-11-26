<?php

include_once ("../config.php");

nbt_save_outgoing_email_settings (
    $_POST['enabled'],
    $_POST['host'],
    $_POST['user'],
    $_POST['password']
);

echo "Saved";

?>
