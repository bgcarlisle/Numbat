<div class="nbtContentPanel nbtGreyGradient">
	<h2>
		<img src="<?php echo SITE_URL; ?>images/extract.png" class="nbtTitleImage">
		Your extractions
	</h2>
	<button onclick="$('.nbtCompleteAssignment').fadeToggle();">Show / hide complete</button>
	<button onclick="$('.nbtInProgressAssignment').fadeToggle();">Show / hide in progress</button>
	<button onclick="$('.nbtNotStartedAssignment').fadeToggle();">Show / hide not yet started</button>
	<p class="nbtFinePrint">Complete assignments are hidden automatically. Click the button above to show them.</p>
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
						<td>Reference set / form</td>
						<td>Status</td>
						<td>Extract</td>
					</tr>
						<?php

						foreach ( $assignments as $assignment ) {

							$assignment_status = nbt_get_status_for_assignment ( $assignment );

							?><tr<?php

							switch ( $assignment_status ) {

								case 0:

									?> class="nbtNotStartedAssignment"<?php

								break;

								case 1:

									?> class="nbtInProgressAssignment"<?php

								break;

								case 2:

									?> class="nbtCompleteAssignment"<?php

								break;

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
									<?php

									switch ( $assignment_status ) {

										case 0:

											?>Not yet started<?php

										break;

										case 1:

											?>In progress<?php

										break;

										case 2:

											?>Complete<?php

										break;

									}

									?>
								</td>
								<td>
									<a href="<?php echo SITE_URL; ?>extract/?action=extract&form=<?php echo $assignment['formid'] ?>&refset=<?php echo $assignment['refsetid']; ?>&ref=<?php echo $assignment['referenceid']; ?>" target="_blank">Extract</a>
								</td>
							</tr><?php

						}

				?></table><?php

			}

		}

		?>

	<div class="nbtHidden nbtFeedbackGood nbtFeedback nbtFinePrint" id="nbtPrivilegeFeedback">&nbsp;</div>

</div>
