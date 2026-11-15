<?php

$refset = nbt_get_refset_for_id ( $_GET['refset'] );
$refsetcols = nbt_get_columns_for_refset ( $_GET['refset'] );
$references = nbt_get_all_references_for_refset ( $_GET['refset'] );

?><div style="padding: 20px 20px 80px 20px;">

    <h3>Reference set name</h3>
    <input id="nbtNewRefSetName" type="text" value="<?php echo $refset['name']; ?>" onblur="nbtChangeRefSetName(<?php echo $refset['id']; ?>);">
    <span class="nbtFinePrint nbtHidden nbtFeedback" id="nbtNewRefSetNameFeedback">&nbsp;</span>
    <p><?php echo count($references); ?> reference(s)</p>
    <h3>Reference metadata</h3>
    <p>In order to let extractors know what they are extracting, Numbat will display metadata for each reference drawn from the columns specified below for each reference.</p>

    <textarea id="nbtMetadata-refmeta" style="width: 100%; height: 150px;"><?php echo $refset['refmeta']; ?></textarea>
    <button onclick="nbtUpdateRefsetMetadata('refmeta', '<?php echo $refset['id']; ?>')">Save changes</button>
    <span id="nbtMetadataResponse-refmeta"></span>
    
    
    <h3>Insert new references into this reference set</h3>
    <button onclick="$('#nbtNewReferencesInstructions').slideDown();$(this).fadeOut();" id="nbtShowNewRefsButton">Add new references</button>

    <div class="nbtHidden" id="nbtNewReferencesInstructions">
	<p>To insert new references, prepare a TSV file with a row for column names and a row for every new reference to be uploaded. You will be prompted to indicate which columns in the uploaded TSV correspond to which columns in the already-uploaded reference set. Numbat will try to match columns automatically where they match exactly.</p>
	<form action="<?php echo SITE_URL; ?>references/upload-additional.php" method="post" enctype="multipart/form-data">
	    <input type="file" name="file" id="file">
	    <input type="hidden" name="refset" value="<?php echo $_GET['refset']; ?>">
	    <input type="submit" value="Upload">
	    <button onclick="event.preventDefault();$('#nbtShowNewRefsButton').slideDown();$('#nbtNewReferencesInstructions').slideUp();">Cancel</button>
	</form>
    </div>
    
    <table class="nbtTabledData" style="margin-top: 20px;">
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
