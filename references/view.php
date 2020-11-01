<?php

$refset = nbt_get_refset_for_id ( $_GET['refset'] );
$refsetcols = nbt_get_columns_for_refset ( $_GET['refset'] );
$references = nbt_get_all_references_for_refset ( $_GET['refset'] );

?><div style="padding: 20px 20px 80px 20px;">
    <h2>Reference set: <?php echo $refset['name']; ?></h2>
    <p><?php echo count($references); ?> reference(s)</p>
    <table class="nbtTabledData">
	<tr class="nbtTableHeaders">
	    <?php

	    foreach ($refsetcols as $col) {

		if ( $col[0] != "id" && $col[0] != "manual" ) {

		    echo "<td>" . $col[0] . "</td>";

		}
		    
	    }

	    ?>
	</tr>
	<?php

	foreach ( $references as $ref ) {

	    echo "<tr>";

	    foreach ($refsetcols as $col) {

		if ( $col[0] != "id" && $col[0] != "manual" ) {

		    echo "<td>" . $ref[$col[0]] . "</td>";
		    
		}
		
	    }

	    echo "</tr>";
	    
	}

	?>
    </table>
</div>
