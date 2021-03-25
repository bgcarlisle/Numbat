<table class="nbtTabledData">
    <tr class="nbtTableHeaders">
	<td>Form metadata</td>
	<td style="width: 100px;">Edit</td>
	<td style="width: 100px;">Preview</td>
	<td style="width: 100px;">Export form</td>
	<td style="width: 100px;">Export codebook</td>
	<td style="width: 100px;">Delete</td>
    </tr>
    <?php
    
    $allforms = nbt_get_all_extraction_forms ();

    foreach ( $allforms as $form ) {

    ?>
	<tr>
	    <td>
		<h3><?php echo $form['name']; ?> <?php echo $form['version']; ?></h3>
		<p><?php echo $form['author']; ?> <?php if ($form['affiliation'] != "") { echo "(" . $form['affiliation'] . ")"; } ?></p>
		<p><?php echo $form['description']; ?></p>
		<?php

		if ( $form['protocol'] != "" ) {
		    echo '<p><a href="' . $form['protocol'] . '" target="_blank">' . $form['project'] . '</a></p>';
		} else {
		    echo '<p>' . $form['project'] . '</p>';
		}
		
		?>
		<p><?php echo $form['projectdate']; ?></p>
	    </td>
	    <td><a href="<?php echo SITE_URL; ?>forms/?action=edit&id=<?php echo $form['id']; ?>">Edit</a></td>
	    <td><a href="<?php echo SITE_URL; ?>extract/?action=preview&form=<?php echo $form['id']; ?>" target="_blank">Preview</a></td>
	    <td><a href="<?php echo SITE_URL; ?>forms/?action=exportform&id=<?php echo $form['id']; ?>" target="_blank">Export form (.json)</a></td>
	    <td><a href="<?php echo SITE_URL; ?>forms/?action=exportcodebook&id=<?php echo $form['id']; ?>" target="_blank">Export codebook (.md)</a></td>
	    <td><button onclick="$(this).fadeOut(0);$('#nbtDeleteForm<?php echo $form['id']; ?>').fadeIn();$('#nbtConfirmDeleteForm<?php echo $form['id']; ?>').fadeIn();">Delete</button>
		<p class="nbtHidden" id="nbtConfirmDeleteForm<?php echo $form['id']; ?>">Are you sure? This can't be undone.</p>
		<button class="nbtHidden" id="nbtDeleteForm<?php echo $form['id']; ?>" onclick="nbtDeleteForm(<?php echo $form['id']; ?>);">For real</button></td>
	</tr>
    <?php

    }

    ?>
    <tr>
	<td colspan="6"><button onclick="nbtNewExtractionForm();">Add new extraction form</button></td>
    </tr>
    <tr>
	<td colspan="6">
	    <button id="nbtShowImportFormButton" onclick="$('#nbtImportForm').slideDown();$('#nbtShowImportFormButton').slideUp();">Import an extraction form</button>
	    <div class="nbtHidden" id="nbtImportForm">
		<h3>Import a Numbat form from a .json file</h3>
		<form action="<?php echo SITE_URL; ?>forms/import-form.php" method="post" enctype="multipart/form-data">
		    <input type="file" name="file" id="file">
		    <button>Import Numbat form</button>
		    <button onclick="event.preventDefault();$('#nbtShowImportFormButton').slideDown();$('#nbtImportForm').slideUp();">Cancel</button>
		</form>
	    </div>
	</td>
    </tr>
</table>
