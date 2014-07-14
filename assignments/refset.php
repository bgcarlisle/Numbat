<div class="nbtContentPanel nbtGreyGradient">
	<h2>Manage extraction assignments</h2>
	
	<h3>Currently assigned</h3>
	<table class="nbtTabledData">
		<tr class="nbtTableHeaders">
			<td>When assigned</td>
			<td>User name</td>
			<td>Form</td>
			<td>Reference</td>
			<td style="width: 80px;">Delete</td>
		</tr>
		<?php
		
		$allassignments = nbt_get_all_assignments_for_refset ( $_GET['refset'] );
		
		foreach ( $allassignments as $assignment ) {
			
			?><tr id="nbtAssignmentTableRow<?php echo $assignment['id']; ?>">
				<td>
					<?php echo substr ( $assignment['whenassigned'], 0, 10 ); ?>
				</td>
				<td>
					<?php echo $assignment['username']; ?>
				</td>
				<td>
					<?php echo $assignment['formname']; ?>
				</td>
				<td>
					<h4><?php echo $assignment['title']; ?></h4>
					<p><?php echo $assignment['authors']; ?></p>
					<p><?php echo $assignment['journal']; ?>: <?php echo $assignment['year']; ?></p>
				</td>
				<td>
					<button onclick="$(this).fadeOut(0);$('#nbtDeleteAssignment<?php echo $assignment['id']; ?>').fadeIn();">Delete</button>
					<button class="nbtHidden" id="nbtDeleteAssignment<?php echo $assignment['id']; ?>" onclick="nbtDeleteAssignment(<?php echo $assignment['id']; ?>);">For real</button>
				</td>
			</tr><?php
			
		}
		
		?>
	</table>
	
	<div class="nbtHidden nbtFeedbackGood nbtFeedback nbtFinePrint" id="nbtPrivilegeFeedback">&nbsp;</div>
	
</div>