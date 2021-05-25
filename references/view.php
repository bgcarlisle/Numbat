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
    <p>In order to let extractors know what they are extracting, Numbat will draw four values for each reference and format them as a journal reference and display a fifth as an abstract. Choose the columns that represent these metadata from the reference set columns below.</p>
    <p>Title</p>
    <select id="nbtMetadata-title" onchange="nbtUpdateRefsetMetadata('title', <?php echo $refset['id']; ?>);">
	<option value="NULL"<?php if (is_null($refset['title'])) { echo " selected"; } ?>>None selected</option>
	<?php

	$colcount = 0;

	foreach ( $refsetcols as $col ) {

	    if ( ! is_null($refset['title']) & $colcount == $refset['title'] ) {

		echo "<option value=\"" . $colcount . "\" selected>" . $col[0] . "</option>";
		
	    } else {

		echo "<option value=\"" . $colcount . "\">" . $col[0] . "</option>";
	    }

	    $colcount++;
	    
	}

	?>
    </select>
    <span id="nbtMetadataResponse-title">&nbsp;</span>
    
    <p>Authors</p>
    <select id="nbtMetadata-authors" onchange="nbtUpdateRefsetMetadata('authors', <?php echo $refset['id']; ?>);">
	<option value="NULL"<?php if (is_null($refset['authors'])) { echo " selected"; } ?>>None selected</option>
	<?php

	$colcount = 0;

	foreach ( $refsetcols as $col ) {

	    if ( ! is_null($refset['authors']) & $colcount == $refset['authors'] ) {

		echo "<option value=\"" . $colcount . "\" selected>" . $col[0] . "</option>";
	    } else {

		echo "<option value=\"" . $colcount . "\">" . $col[0] . "</option>";
	    }

	    $colcount++;
	    
	}

	?>
    </select>
    <span id="nbtMetadataResponse-authors">&nbsp;</span>
    
    
    <p>Year</p>
    <select id="nbtMetadata-year" onchange="nbtUpdateRefsetMetadata('year', <?php echo $refset['id']; ?>);">
	<option value="NULL"<?php if (is_null($refset['year'])) { echo " selected"; } ?>>None selected</option>
	<?php

	$colcount = 0;

	foreach ( $refsetcols as $col ) {

	    if ( ! is_null($refset['year']) & $colcount == $refset['year'] ) {

		echo "<option value=\"" . $colcount . "\" selected>" . $col[0] . "</option>";
	    } else {

		echo "<option value=\"" . $colcount . "\">" . $col[0] . "</option>";
	    }

	    $colcount++;
	    
	}

	?>
    </select>
    <span id="nbtMetadataResponse-year">&nbsp;</span>
    
    
    <p>Journal</p>
    <select id="nbtMetadata-journal" onchange="nbtUpdateRefsetMetadata('journal', <?php echo $refset['id']; ?>);">
	<option value="NULL"<?php if (is_null($refset['journal'])) { echo " selected"; } ?>>None selected</option>
	<?php

	$colcount = 0;

	foreach ( $refsetcols as $col ) {

	    if ( ! is_null($refset['journal']) & $colcount == $refset['journal'] ) {

		echo "<option value=\"" . $colcount . "\" selected>" . $col[0] . "</option>";
	    } else {

		echo "<option value=\"" . $colcount . "\">" . $col[0] . "</option>";
	    }

	    $colcount++;
	    
	}

	?>
    </select>
    <span id="nbtMetadataResponse-journal">&nbsp;</span>
    
    <p>Abstract</p>
    <select id="nbtMetadata-abstract" onchange="nbtUpdateRefsetMetadata('abstract', <?php echo $refset['id']; ?>);">
	<option value="NULL"<?php if (is_null($refset['abstract'])) { echo " selected"; } ?>>None selected</option>
	<?php

	$colcount = 0;

	foreach ( $refsetcols as $col ) {

	    if ( ! is_null($refset['abstract']) & $colcount == $refset['abstract'] ) {

		echo "<option value=\"" . $colcount . "\" selected>" . $col[0] . "</option>";
	    } else {

		echo "<option value=\"" . $colcount . "\">" . $col[0] . "</option>";
	    }

	    $colcount++;
	    
	}

	?>
    </select>
    <span id="nbtMetadataResponse-abstract">&nbsp;</span>

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
