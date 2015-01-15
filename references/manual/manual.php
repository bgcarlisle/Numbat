<?php

include_once ('../../config.php');

$refsetname = nbt_get_name_for_refsetid ( $_GET['refset'] );

?><div class="nbtGreyGradient nbtContentPanel">

	<h2>Manual references: <?php echo $refsetname; ?></h2>
	<?php

	$refs = nbt_get_manual_refs_for_refset ( $_GET['refset'] );

	foreach ( $refs as $ref ) {

		nbt_echo_manual_ref ( $ref, $_GET['refset'] );

	}

?></div>
