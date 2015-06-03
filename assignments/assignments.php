<div class="nbtContentPanel nbtGreyGradient">
	<h2>
		<img src="<?php echo SITE_URL; ?>images/assignments.png" class="nbtTitleImage">
		Manage extraction assignments
	</h2>
	<table class="nbtTabledData">
		<tr class="nbtTableHeaders">
			<td>Reference set</td>
			<td>View all assignments</td>
		</tr>
		<?php

		$allrefsets = nbt_get_all_ref_sets ();

		foreach ( $allrefsets as $refset ) {

			?><tr>
				<td><?php echo $refset['name']; ?></td>
				<td><a href="<?php echo SITE_URL; ?>assignments/?action=viewrefset&refset=<?php echo $refset['id']; ?>">View</a></td>
			</tr><?php

		}

		?>
		<tr>
			<td colspan="2"><button onclick="window.open('<?php echo SITE_URL; ?>assignments/?action=new','_self');">Add new assignments</button></td>
		</tr>
	</table>

	<div class="nbtHidden nbtFeedbackGood nbtFeedback nbtFinePrint" id="nbtPrivilegeFeedback">&nbsp;</div>

</div>
