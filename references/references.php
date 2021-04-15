<div class="nbtContentPanel nbtGreyGradient">
	<h2>
		<img src="<?php echo SITE_URL; ?>images/managerefsets.png" class="nbtTitleImage">
		Manage reference sets
	</h2>
	<table class="nbtTabledData">
		<tr class="nbtTableHeaders">
		    <td>Reference set</td>
		    <td>View reference set</td>
		    <td>Export reference set</td>
		    <td>Manually added references</td>
		    <td>Manage multiples</td>
		    <td>Delete</td>
		</tr>
		<?php

		$allrefsets = nbt_get_all_ref_sets ();

		foreach ( $allrefsets as $refset ) {

		?><tr id="nbtRefSetRow<?php echo $refset['id']; ?>">
      		    <td><?php echo $refset['name']; ?></td>
		    <td><a href="<?php echo SITE_URL; ?>references/?action=view&refset=<?php echo $refset['id']; ?>">View</a></td>
 		    <td><button onclick="nbtExportRefset(<?php echo $refset['id']; ?>);">Export</button></td>
		    <td><a href="<?php echo SITE_URL; ?>references/manual/?refset=<?php echo $refset['id']; ?>">Manual refs</a></td>
		    <td><a href="<?php echo SITE_URL; ?>references/multiple/?refset=<?php echo $refset['id']; ?>">Multiples</a></td>
		    <td>
			<button onclick="$(this).fadeOut(0);$('#nbtDeleteRefSet<?php echo $refset['id']; ?>').fadeIn();">Delete</button>
			<button class="nbtHidden" id="nbtDeleteRefSet<?php echo $refset['id']; ?>" onclick="nbtDeleteRefSet(<?php echo $refset['id']; ?>);">For real</button>
		    </td>
		</tr><?php

		     }

		     ?>
		<tr>
		    <td colspan="7"><button onclick="$('#nbtNewReferenceSetInstructions').slideDown();$(this).fadeOut();" id="nbtShowNewRefsetButton">Add new reference set</button></td>
		</tr>
	</table>

	<div class="nbtHidden" id="nbtNewReferenceSetInstructions">
	    <h3>Add a new reference set</h3>
	    <p>To make a new reference set, prepare your references as a tab-delimited text file. The first row should include column headings for your reference metadata. You may include any other columns you like. You will be prompted on upload to choose which columns correspond to title, authors, year, journal, abstract metadata for your references.</p>
	    <form action="<?php echo SITE_URL; ?>references/upload.php" method="post" enctype="multipart/form-data">
		<input type="file" name="file" id="file">
		<input type="submit" style="" value="Upload new reference set">
		<button onclick="event.preventDefault();$('#nbtShowNewRefsetButton').slideDown();$('#nbtNewReferenceSetInstructions').slideUp();">Cancel</button>
	    </form>
	</div>

</div>

<div id="nbtCoverup" style="display: none; background: #ccc; opacity: 0.5; z-index: 1; width: 100%; height: 100%; position: fixed; top: 0; left: 0;" onclick="$(this).fadeOut();$('#nbtThinky').fadeOut();">&nbsp;</div>
<div id="nbtThinky" style="display: none; z-index: 2; position: fixed; top: 100px; width: 100%; text-align: center;">
    <div style="padding: 10px 20px 10px 20px; border: 2px solid #666; border-radius: 5px; background: #eee; color: #666; display: inline;"><a href="<?php echo SITE_URL ?>export/result.csv" id="nbtThinkyLinky">Download</a></div>
</div>
