<?php

$refset = nbt_get_refset_for_id ( $_GET['refset'] );
$refsetcols = nbt_get_columns_for_refset ( $_GET['refset'] );
$references = nbt_get_all_references_for_refset ( $_GET['refset'] );

?><div style="padding: 20px 20px 80px 20px;">
    <h2>Reference set: <?php echo $refset['name']; ?></h2>
    <p><?php echo count($references); ?> reference(s)</p>
    <button onclick="nbtAddNewReferenceToRefSet(<?php echo $refset['id']; ?>)" style="margin: 5px 0 10px 0;">Add a new reference</button>
    <table class="nbtTabledData">
	<tr class="nbtTableHeaders">
	    <td></td>
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

	    echo '<tr id="nbtRefRow' . $ref['id'] . '">';

	    echo '<td><button id="nbtReftableRowDeletePrompt' . $ref['id'] . '" onclick="$(\'#nbtReftableRow' . $ref['id'] . '\').slideDown();$(\'#nbtReftableRowDeletePrompt' . $ref['id'] . '\').slideUp();">Delete</button><div class="nbtHidden" id="nbtReftableRow' . $ref['id'] . '"><button onclick="nbtDeleteRef(' . $refset['id'] . ', ' . $ref['id'] . ');">For real</button><button onclick="$(\'#nbtReftableRow' . $ref['id'] . '\').slideUp();$(\'#nbtReftableRowDeletePrompt' . $ref['id'] . '\').slideDown();">Cancel</button></div></td>';

	    foreach ($refsetcols as $col) {

		if ( $col[0] != "id" && $col[0] != "manual" ) {

		    if ( strlen ($ref[$col[0]]) > 50 ) {
			echo "<td>" . substr($ref[$col[0]], 0, 50) . "...</td>";
		    } else {
			echo "<td>" . $ref[$col[0]] . "</td>";
		    }
		    
		}
		
	    }

	    echo "</tr>";
	    
	}

	?>
    </table>
</div>
<div class="nbtCoverup" id="nbtManualRefsCoverup">&nbsp;</div>
<div id="nbtManualRefs" class="nbtInlineManualNewRef">&nbsp;</div>
