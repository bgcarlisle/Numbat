<div class="nbtContentPanel nbtGreyGradient">
	<h2>
		<img src="<?php echo SITE_URL; ?>images/managerefsets.png" class="nbtTitleImage">
		Manage reference sets
	</h2>
	<table class="nbtTabledData">
		<tr class="nbtTableHeaders">
			<td>Reference set</td>
			<td>Edit reference set</td>
			<td>Manually added references</td>
			<td>Manage multiples</td>
			<td>Delete</td>
		</tr>
		<?php

		$allrefsets = nbt_get_all_ref_sets ();

		foreach ( $allrefsets as $refset ) {

			?><tr id="nbtRefSetRow<?php echo $refset['id']; ?>">
				<td><?php echo $refset['name']; ?></td>
				<td><a href="<?php echo SITE_URL; ?>references/?action=edit&refset=<?php echo $refset['id']; ?>">Edit</a></td>
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
			<td colspan=5><button onclick="$('#nbtNewReferenceSetInstructions').slideDown();$(this).fadeOut();">Add new reference set</button></td>
		</tr>
	</table>

	<div class="nbtHidden" id="nbtNewReferenceSetInstructions">
		<p>To make a new reference set, prepare your references as a tab-delimited text file. The first row should include column headings for your reference metadata. You may include any other columns you like. You will be prompted on upload to choose which columns correspond to title, authors, year, journal, abstract metadata for your references.</p>
		<form action="<?php echo SITE_URL; ?>references/upload.php" method="post" enctype="multipart/form-data">
			<input type="file" name="file" id="file">
			<input type="submit" style="" value="Upload new reference set">
		</form>
	</div>

</div>
