<?php

$refsets = nbt_get_all_ref_sets ();

$forms = nbt_get_all_extraction_forms ();

if ( count ( $refsets ) > 0 ) {

	foreach ( $refsets as $refset ) {

		$refs = nbt_get_all_references_for_refset ( $refset['id'] );

		?><div class="nbtContentPanel nbtGreyGradient">
			<h2><?php echo $refset['name']; ?></h2>
			<button onclick="$('.nbtMasterNotYetStarted<?php echo $refset['id']; ?>').fadeToggle();">Show / hide not yet started </button>
			<button onclick="$('.nbtMasterInProgress<?php echo $refset['id']; ?>').fadeToggle();">Show / hide in progress</button>
			<button onclick="$('.nbtMasterCompleted<?php echo $refset['id']; ?>').fadeToggle();">Show / hide complete</button><?php

			foreach ( $forms as $form ) {

				$extractedrefs = nbt_get_all_extracted_references_for_refset_and_form ( $refset['id'], $form['id'] );
 
				if ( count ( $extractedrefs ) > 0 ) {

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

							$master = nbt_get_master ( $form['id'], $refset['id'], $extractedref['id'], FALSE );

							?><tr<?php

								switch ( $master['status'] ) {

									case 0:

										?> class="nbtMasterNotYetStarted<?php echo $refset['id']; ?>"<?php

									break;

									case 1:

										?> class="nbtMasterInProgress<?php echo $refset['id']; ?>"<?php

									break;

									case 2:

										?> class="nbtMasterCompleted<?php echo $refset['id']; ?> nbtHidden"<?php

									break;

								}

								?>>
								<td>
									<h4><?php echo $extractedref[$refset['title']]; ?></h4>
									<p><?php echo $extractedref[$refset['authors']]; ?>, <em><?php echo $extractedref[$refset['journal']]; ?></em>: <?php echo $extractedref[$refset['year']]; ?></p></td>
								<td>
									<?php

									$extractions = nbt_get_extractions_for_refset_ref_and_form ( $refset['id'], $extractedref['id'], $form['id'] );

									foreach ( $extractions as $extraction ) {

										?><span class="nbtExtractionName"><?php echo $extraction['username']; ?></span><?php

									}

									?>
								</td>
								<td>
									<?php

									switch ( $master['status'] ) {

										case 0:

											?>Not yet started<?php

										break;

										case 1:

											?>In progress<?php

										break;

										case 2:

											?>Completed<?php

										break;

									}

									?>
								</td>
								<td>
									<?php

									if ( count ( $extractions ) > 1 ) {

										?><a href="<?php echo SITE_URL; ?>final/?action=reconcile&form=<?php echo $form['id']; ?>&refset=<?php echo $refset['id']; ?>&ref=<?php echo $extractedref['id']; ?>" target="_blank">Reconcile extractions</a><?php

									} else {

										?>Only one extraction done<?php

									}

									?>
								</td>
							</tr><?php

						}

					?></table><?php

				}

			}

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
