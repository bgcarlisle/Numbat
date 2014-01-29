<div class="nbtContentPanel nbtGreyGradient">
	<h2>
		<img src="<?php echo SITE_URL; ?>images/extract.png" class="nbtTitleImage">
		Your extractions
	</h2>
		<?php
		
		$referencesets = nbt_get_all_ref_sets ();
		
		foreach ( $referencesets as $refset ) {
			
			$assignments = nbt_get_assignments_for_user_and_refset ( $_SESSION['nbt_userid'], $refset['id'] );
			
			if ( count ( $assignments ) > 0 ) {
				
				?><h3><?php echo $refset['name']; ?></h3>
				<button onclick="$('.nbtHiddenAssignment').fadeToggle();">Toggle hidden</button>
				<table class="nbtTabledData">
					<tr class="nbtTableHeaders">
						<td>When assigned</td>
						<td>Assignment</td>
						<td>Reference set / form</td>
						<td>Extract</td>
						<td>Hide</td>
					</tr>
						<?php
						
						foreach ( $assignments as $assignment ) {
							
							?><tr<?php
							
							if ( $assignment['hidden'] == 1 ) {
								
								?> class="nbtHiddenAssignment"<?php
								
							}
							
							?> id="nbtAssignment<?php echo $assignment['id']; ?>">
								<td><?php echo substr ($assignment['whenassigned'], 0, 10); ?></td>
								<td>
									<h4><?php echo $assignment['title']; ?></h4>
									<p><?php echo $assignment['authors']; ?></p>
									<?php
									
									if ( $assignment['journalname'] != "" && $assignment['year'] != "" ) {
										
										?><p><?php echo $assignment['journalname']; ?>: <?php echo $assignment['year']; ?></p><?php
										
									}
									
									?>
								</td>
								<td><?php
								
									$refsetname = nbt_get_name_for_refsetid ( $assignment['refsetid'] );
									
									$form = nbt_get_form_for_id ( $assignment['formid'] );
									
									echo $refsetname . " / " . $form['name'];
									
								?></td>
								<td>
									<button onclick="window.open('<?php echo SITE_URL; ?>extract/?action=extract&form=<?php echo $assignment['formid'] ?>&refset=<?php echo $assignment['refsetid']; ?>&ref=<?php echo $assignment['referenceid']; ?>','_self');">Extract</button>
								</td>
								<td>
									<button onclick="nbtHideAssignment( <?php echo $assignment['id']; ?> );">Hide</button>
								</td>
							</tr><?php
							
						}
						
				?></table><?php
				
			}
			
		}
		
		?>
	
	<div class="nbtHidden nbtFeedbackGood nbtFeedback nbtFinePrint" id="nbtPrivilegeFeedback">&nbsp;</div>
	
</div>