<?php

$refsets = nbt_get_all_ref_sets ();

$forms = nbt_get_all_extraction_forms ();

if ( count ( $refsets ) > 0 ) {
	
	foreach ( $refsets as $refset ) {
		
		$refs = nbt_get_all_references_for_refset ( $refset['id'] );
		
		?><div class="nbtContentPanel nbtGreyGradient">
			<h2><?php echo $refset['name']; ?></h2><?php
			
			foreach ( $forms as $form ) {
				
				$extractedrefs = nbt_get_all_extracted_references_for_refset_and_form ( $refset['id'], $form['id'] );
				
				if ( count ( $extractedrefs ) > 0 ) {
					
					?><h3 style="margin-top: 10px;"><?php echo $form['name']; ?></h3>
					<table class="nbtTabledData">
						<tr class="nbtTableHeaders">
							<td>Reference</td>
							<td>Extractors</td>
							<td>Reconcile</td>
						</tr>
						<?php
						
						foreach ( $extractedrefs as $extractedref ) {
							
							?><tr>
								<td>
									<h4><?php echo $extractedref['title']; ?></h4>
									<p><?php echo $extractedref['authors']; ?>, <em><?php echo $extractedref['journal']; ?></em>: <?php echo $extractedref['year']; ?></p></td>
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
									
									if ( count ( $extractions ) > 1 ) {
										
										?><a href="<?php echo SITE_URL; ?>master/?action=reconcile&form=<?php echo $form['id']; ?>&refset=<?php echo $refset['id']; ?>&ref=<?php echo $extractedref['id']; ?>">Reconcile extractions</a><?php
										
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