<table class="nbtTabledData">
    <tr class="nbtTableHeaders">
	<td>Form name</td>
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
	    <td><h3><?php echo $form['name']; ?></h3>
		<p><?php echo $form['description']; ?></p></td>
	    <td><a href="<?php echo SITE_URL; ?>forms/?action=edit&id=<?php echo $form['id']; ?>">Edit</a></td>
	    <td><a href="<?php echo SITE_URL; ?>extract/?action=preview&form=<?php echo $form['id']; ?>" target="_blank">Preview</a></td>
	    <td><a href="<?php echo SITE_URL; ?>forms/?action=exportform&id=<?php echo $form['id']; ?>" target="_blank">Export form (.zip)</a></td>
	    <td><a href="<?php echo SITE_URL; ?>forms/?action=exportcodebook&id=<?php echo $form['id']; ?>" target="_blank">Export codebook (.md)</a></td>
	    <td><button onclick="$(this).fadeOut(0);$('#nbtDeleteForm<?php echo $form['id']; ?>').fadeIn();$('#nbtConfirmDeleteForm<?php echo $form['id']; ?>').fadeIn();">Delete</button>
		<p class="nbtHidden" id="nbtConfirmDeleteForm<?php echo $form['id']; ?>">Are you sure? This can't be undone.</p>
		<button class="nbtHidden" id="nbtDeleteForm<?php echo $form['id']; ?>" onclick="nbtDeleteForm(<?php echo $form['id']; ?>);">For real</button></td>
	</tr>
    <?php

    }

    ?>
    <tr>
	<td colspan=5><button onclick="nbtNewExtractionForm();">Add extraction form</button></td>
    </tr>
</table>
