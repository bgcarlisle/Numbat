<div class="nbtContentPanel nbtGreyGradient">
	<h2>Manage reference sets</h2>
	<table class="nbtTabledData">
		<tr class="nbtTableHeaders">
			<td>Reference set</td>
			<td>View all references</td>
			<td>Delete</td>
		</tr>
		<?php
		
		$allrefsets = nbt_get_all_ref_sets ();
		
		foreach ( $allrefsets as $refset ) {
			
			?><tr>
				<td><?php echo $refset['name']; ?></td>
				<td>View</td>
				<td><button>Delete</button></td>
			</tr><?php
			
		}
		
		?>
		<tr>
			<td colspan=3><button>Add new reference set</button></td>
		</tr>
	</table>
	
	<div class="nbtHidden nbtFeedbackGood nbtFeedback nbtFinePrint" id="nbtPrivilegeFeedback">&nbsp;</div>
	
</div>