<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

    $link = nbt_admin_generate_password_link ( $_POST['user'] );

    if ( $link ) {
	echo '<button onclick="$(\'#nbtPasswordChangeFeedback\').slideUp();" style="float: right; margin: 0 0 10px 10px;">Close</button>';
	echo "<p>You may reset the password for this account at the following link:</p>";
	echo "<p>" . $link . "</p>";
	echo "<p>Send this link to the user in question and they will be able to change the password for their account to one of their own choosing.</p>";
    } else {
	echo "No user found";
    }
    
}

?>
