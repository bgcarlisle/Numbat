<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

	if ( nbtDeleteAssignment ( $_POST['assignment'] ) ) {

		?>deleted<?php

	} else {

		?>fail<?php

	}

}

?>
