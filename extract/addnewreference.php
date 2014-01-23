<?php

include_once ('../config.php');

$ref = nbt_get_reference_for_refsetid_and_refid ( $_POST['refset'], nbt_add_manual_ref ( $_POST['refset'] ));

nbt_echo_manual_ref ( $ref, $_POST['refset'] );
	
?><button onclick="$('#nbtManualRefsCoverup').fadeOut(500); $('#nbtManualRefs').fadeOut(500);">Close and save</button>