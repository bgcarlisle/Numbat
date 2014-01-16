<div class="nbtContentPanel nbtGreyGradient">
	<h2>Your assignments</h2>
		<?php
		
		$referencesets = nbt_get_all_ref_sets ();
		
		foreach ( $referencesets as $refset ) {
			
			$assignments = nbt_get_assignments_for_user_and_refset ( $_SESSION['nbt_userid'], $refset['id'] );
			
			if ( count ( $assignments ) > 0 ) {
				
				?><h3><?php echo $refset['name']; ?></h3>
				<table class="nbtTabledData">
					<tr class="nbtTableHeaders">
						<td>When assigned</td>
						<td>Assignment</td>
						<td>Extract</td>
					</tr>
						<?php
						
						foreach ( $assignments as $assignment ) {
							
							?><tr>
								<td><?php echo substr ($assignment['whenassigned'], 0, 10); ?></td>
								<td>
									<h4><?php echo $assignment['title']; ?></h4>
									<p><?php echo $assignment['authors']; ?></p>
									<p><?php echo $assignment['journal']; ?>: <?php echo $assignment['year']; ?></p>
								</td>
								<td>
									<button onclick="window.open('<?php echo SITE_URL; ?>extract/?action=extract&form=<?php echo $assignment['formid'] ?>&refset=<?php echo $assignment['refsetid']; ?>&ref=<?php echo $assignment['referenceid']; ?>','_self');">Extract</button>
								</td>
							</tr><?php
							
						}
						
				?></table><?php
				
			}
			
		}
		
		?>
	
	<div class="nbtHidden nbtFeedbackGood nbtFeedback nbtFinePrint" id="nbtPrivilegeFeedback">&nbsp;</div>
	
</div>