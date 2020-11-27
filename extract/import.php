<?php

$refsets = nbt_get_all_ref_sets ();

$forms = nbt_get_all_extraction_forms ();

$users = nbt_get_all_users ();

?>
<div class="nbtContentPanel nbtGreyGradient">
    
    <h2>Import extractions</h2>

    <p>Use this page if you have data that was collected or prepared outside of Numbat that you would like to import to reconcile within Numbat.</p>

    <hr>
    
    <?php

    if ( count ($refsets) > 0 && count ($forms) > 0 ) { // There is at least one form and refset

	echo "<p>Prepare a TSV file that includes one row per extraction. Or in the case of importing table data, one row per row of table data to be imported. The first row should contain column names.</p>";

	echo '<form action="' . SITE_URL . 'extract/import-upload.php" method="post" enctype="multipart/form-data">';

	// Choose a reference set

	echo "<p>Use the following reference set:</p>";

	echo '<select name="refset" onchange="$(\'#nbtThinky\').fadeOut(0);;nbtExportRefset($(this).val());">';

	if ( count ($refsets) > 1 ) {

	    echo '<option value="ns">Choose a reference set</option>';
	    
	}

	foreach ($refsets as $refset) {

	    echo '<option value="' . $refset['id'] . '">' . $refset['name'] . '</option>';
	    
	}

	echo "</select>";

	echo '<p>Numbat assigns each reference a unique number, listed in the "id" column in the reference set. There must be a column in the extractions to be uploaded that tells Numbat the number of the reference that corresponds to each uploaded extraction.</p>';

	echo '<p class="nbtHidden" id="nbtThinky"><a href="#" id="nbtThinkyLinky">Download the reference set</a></p>';

	// Choose a form

	echo "<p>Use the following form:</p>";

	echo '<select name="form">';

	echo '<option value="ns">Choose a form</option>';

	foreach ($forms as $form) {

	    echo '<option value="' . $form['id'] . '">' . $form['name'] . '</option>';

	    $elements = nbt_get_elements_for_formid ( $form['id'] );

	    foreach ($elements as $element) {

		if ($element['type'] == "table_data" | $element['type'] == "ltable_data") {
		    echo '<option value="' . $form['id'] . '-' . $element['id'] . '">-- ' . $element['displayname'] . ' (table)</option>';
		}

		if ($element['type'] == "sub_extraction") {
		    echo '<option value="' . $form['id'] . '-' . $element['id'] . '">-- ' . $element['displayname'] . ' (sub-extraction)</option>';

		    $subelements = nbt_get_sub_extraction_elements_for_elementid ( $element['id'] );

		    foreach ( $subelements as $subelement ) {
			if ( $subelement['type'] == "table_data") {
			    echo '<option value="' . $form['id'] . '-' . $element['id'] . '-' . $subelement['id'] . '">---- ' . $element['displayname'] . ' (sub-extraction table)</option>';
			}
		    }
		    
		}

	    }
	    
	}

	echo "</select>";

	// Select a user

	echo "<p>Attribute all of the imported extractions to the following user:</p>";

	echo '<select name="user">';

	echo '<option value="ns">Attribute imported extractions to user by column value</option>';

	foreach ($users as $user) {

	    echo '<option value="' . $user['id'] . '">' . $user['username'] . '</option>';
	    
	}

	echo "</select>";

	echo "<p>If 'Attribute imported extractions to user by column value' is chosen, you will be prompted to choose a column containing user names that match those in the user list for this Numbat installation. If no matching user is found, the extraction will be attributed to the currently signed-in user.</p>";

	echo '<input type="file" name="file" id="file">';
	echo '<input type="submit" style="" value="Upload extractions">';
	echo '</form>';
	
    } else { // Either no forms or no reference sets have been defined

	echo "To import extractions, you must have at least one extraction form and at least one reference set defined.";
	
    }

    ?>
    
</div>
