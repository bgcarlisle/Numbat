<?php

include_once ("../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
	
	nbt_remove_sub_multi_select_option ( $_POST['subelement'], $_POST['selectid'] );
	
}

$tablesubelementid = $_POST['subelement'];

include ('./submultiselectoptionstable.php');

?>