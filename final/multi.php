<?php

$refsets = nbt_get_all_ref_sets ();

$forms = nbt_get_all_extraction_forms ();

if ( count ( $refsets ) > 0 ) {

	foreach ( $refsets as $refset ) {

		$refs = nbt_get_all_references_for_refset ( $refset['id'] );

		?><div class="nbtContentPanel nbtGreyGradient">
			<h2>Reconcile: <?php echo $refset['name']; ?></h2>
			<button onclick="$('.nbtMasterNotYetStarted<?php echo $refset['id']; ?>').fadeToggle();">Show / hide not yet started </button>
			<button onclick="$('.nbtMasterInProgress<?php echo $refset['id']; ?>').fadeToggle();">Show / hide in progress</button>
			<button onclick="$('.nbtMasterCompleted<?php echo $refset['id']; ?>').fadeToggle();">Show / hide complete</button><?php

			foreach ( $forms as $form ) {

				$extractedrefs = nbt_get_all_extracted_references_for_refset_and_form ( $refset['id'], $form['id'] );

				if ( count ( $extractedrefs ) > 0 ) {

					switch ($form['formtype']) {

						case "extraction":

							?><h3 style="margin-top: 10px;"><?php echo $form['name']; ?></h3>
							<table class="nbtTabledData">
								<tr class="nbtTableHeaders">
									<td>Reference</td>
									<td>Extractors</td>
									<td>Status</td>
									<td>Reconcile</td>
								</tr>
								<?php

								foreach ( $extractedrefs as $extractedref ) {

								    $master = nbt_get_final ( $form['id'], $refset['id'], $extractedref['id'], FALSE );
								    // All the completed extractions:
								    $extractions = nbt_get_extractions_for_refset_ref_and_form ($refset['id'], $extractedref['id'], $form['id']);


								?><tr<?php

								     if (isset($master)) {

									 switch ( $master['status'] ) {

									     case 0:
										 echo ' class="nbtMasterNotYetStarted' . $refset['id'] . '"';
										 break;

									     case 1:
										 echo ' class="nbtMasterInProgress' . $refset['id'] . '"';
										 break;

									     case 2:
										 echo ' class="nbtMasterCompleted' . $refset['id'] . ' nbtHidden"';
										 break;

									 }
									 
								     } else {
									 echo ' class="nbtMasterNotYetStarted' . $refset['id'] . '"';
								     }

								     ?>>
								    <td>
									<?php

									$refmeta = $refset['refmeta'];
									
									preg_match_all(
									'/\$([A-Za-z0-9_-]+)/',
									$refset['refmeta'],
									$cols_to_replace
									);

									
									foreach ( $cols_to_replace[0] as $col_to_replace ) {

									    $refmeta = preg_replace (
										"/\\" . $col_to_replace . "\b/",
										$extractedref[substr($col_to_replace, 1)],
										$refmeta
									    );
									}

									echo "<div>" . $refmeta . "</div>";
									
									?>
								    </td>
								    <td>
									<?php

									// $extractions = nbt_get_extractions_for_refset_ref_and_form ( $refset['id'], $extractedref['id'], $form['id'] );

									foreach ( $extractions as $extraction ) {

									    echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';


									}

									?>
								    </td>
								    <td>
									<?php

									if (isset($master)) {

									    switch ( $master['status'] ) {

										case 0:
										    echo "Not yet started";

										    break;

										case 1:
										    echo "In progress";

										    break;

										case 2:

										    echo "Completed";

										    break;

									    }
									    
									} else {
									    echo "Not yet started";
									}

									?>
								    </td>
								    <td>
									<?php

									if ( count ( $extractions ) > 1 ) {

									?>
									    <a href="<?php echo SITE_URL; ?>final/?action=reconcile&form=<?php echo $form['id']; ?>&refset=<?php echo $refset['id']; ?>&ref=<?php echo $extractedref['id']; ?>" target="_blank">Reconcile extractions</a>
									<?php

									} else {

									    echo "Only one extraction done";

									}

									?>
								    </td>
								</tr>
								<?php

								}

								echo "</table>";
								
								break;
								
								case "screening":

							// Get the number of assignments for pagination

							?><h3 style="margin-top: 10px;"><?php echo $form['name']; ?></h3>
							<a href="<?php echo SITE_URL; ?>final/?action=reconcilescreened&form=<?php echo $form['id']; ?>&refset=<?php echo $refset['id']; ?>" target="_blank">Reconcile extractions</a>
							<?php

							break;

					}

				}

			} // End of form loop

			?>
		</div><?php

	}

} else {

	?><div class="nbtContentPanel nbtGreyGradient">
		<h2>Error</h2>
		<p>You haven't got any reference sets.</p>
	</div><?php

}

?>
