<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );

$ref = nbt_get_reference_for_refsetid_and_refid ( $_GET['refset'], $_GET['ref'] );

$extractions = nbt_get_extractions_for_refset_ref_and_form ( $_GET['refset'], $_GET['ref'], $_GET['form'] );

$master = nbt_get_master ( $_GET['form'], $_GET['refset'], $_GET['ref'] );

?>
<div class="nbtContentPanel">
	<h2><?php echo $ref['title']; ?></h2>
	<p><?php echo $ref['authors']; ?></p>
	<?php
	
	if (( $ref['journal'] != "") && ($ref['year'] != "")) {
		
		?><p><span class="nbtJournalName"><?php echo $ref['journal']; ?></span>: <?php echo $ref['year']; ?></p><?php
		
	}
	
	?>
</div>
<div class="nbtContentPanel">
	<h3>Abstract</h3>
	<p class="nbtFinePrint"><a href="#" onclick="event.preventDefault();$('#nbtAbstract').slideToggle(200);">Show / hide abstract</a></p>
	<p id="nbtAbstract"><?php
	
	if ( $ref['abstract'] != NULL) {
		
		echo $ref['abstract'];
		
	} else {
		
		echo "[No abstract]";
		
	}
	
	?></p>
</div>
<?php

if ( $formelements[0]['type'] != "section_heading" ) {
	
	?><div class="nbtContentPanel"><?php
	
}

	foreach ( $formelements as $element ) {
		
		switch ( $element['type'] ) {
			
			case "section_heading":
				
				if ( $element['id'] != $formelements[0]['id'] ) {
					
					?></div><?php
					
				}
				
				?><div class="nbtContentPanel">
					<h3><?php echo $element['displayname']; ?><?php
				
					if ( $element['codebook'] != "" ) {
						
						$element['codebook'] = str_replace ("\n", "<br>", $element['codebook']);
						
						?> <a href="#" onclick="event.preventDefault();$(this).parent().next('.nbtCodebook').slideToggle(100);">(?)</a></h3>
						<div class="nbtCodebook"><?php echo $element['codebook']; ?></div><?php
						
					} else {
						
						?></h3><?php
						
					}
				
			break;
			
			case "open_text":
				
				// Test for equality
				
				$values = array ();
				
				foreach ( $extractions as $extraction ) {
					
					array_push ( $values, $extraction[$element['columnname']] );
					
				}
				
				if ( count ( array_unique ( $values ) ) == 1 ) { // If all the extractions got the same result
					
					nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );
					
					?><div class="nbtFeedbackGood nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						if ( $extractions[0][$element['columnname']] == "" ) {
							
							$extractions[0][$element['columnname']] = "[Left blank]";
							
						}
						
						?><p><?php echo $extractions[0][$element['columnname']]; ?></p>
						
					</div><?php
					
				} else { // If not all the extractions are the same
					
					?><div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement<?php echo $element['id']; ?>">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						foreach ( $extractions as $extraction ) {
							
							if ( $extraction[$element['columnname']] == "" ) {
								
								$extraction[$element['columnname']] = "[Left blank]";
								
							}
							
							?><p><?php echo $extraction[$element['columnname']]; ?><span id="nbtExtractedElement<?php echo $element['id']; ?>-<?php echo $extraction['userid']; ?>" class="nbtHidden nbtFeedback nbtElement<?php echo $element['id']; ?>Check">&#x2713;</span></p>
							<span class="nbtExtractionName"><?php echo $extraction['username']; ?></span>
							<button onclick="nbtCopyToMaster(<?php echo $_GET['form']; ?>, <?php echo $_GET['refset'] ?>, <?php echo $_GET['ref']; ?>, '<?php echo $element['columnname']; ?>', <?php echo $extraction['id']; ?>, <?php echo $element['id']; ?>, <?php echo $extraction['userid']; ?>);">Copy to master</button><?php
							
						}
						
						?>
					</div><?php
					
				}
				
			break;
			
			case "date_selector":
				
				// Test for equality
				
				$values = array ();
				
				foreach ( $extractions as $extraction ) {
					
					array_push ( $values, $extraction[$element['columnname']] );
					
				}
				
				if ( count ( array_unique ( $values ) ) == 1 ) { // If all the extractors got the same result
					
					nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );
					
					?><div class="nbtFeedbackGood nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						?><p><?php echo substr ( $extractions[0][$element['columnname']], 0, 7 ); ?></p>
						
					</div><?php
					
				} else {
					
					?><div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement<?php echo $element['id']; ?>">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						foreach ( $extractions as $extraction ) {
							
							?><p><?php echo substr ( $extraction[$element['columnname']], 0, 7 ); ?><span id="nbtExtractedElement<?php echo $element['id']; ?>-<?php echo $extraction['userid']; ?>" class="nbtHidden nbtFeedback nbtElement<?php echo $element['id']; ?>Check">&#x2713;</span></p>
							<span class="nbtExtractionName"><?php echo $extraction['username']; ?></span>
							<button onclick="nbtCopyToMaster(<?php echo $_GET['form']; ?>, <?php echo $_GET['refset'] ?>, <?php echo $_GET['ref']; ?>, '<?php echo $element['columnname']; ?>', <?php echo $extraction['id']; ?>, <?php echo $element['id']; ?>, <?php echo $extraction['userid']; ?>);">Copy to master</button><?php
							
						}
						
						?>
					</div><?php
					
				}
				
			break;
			
			case "single_select":
				
				$selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );
				
				$values = array ();
				
				foreach ( $extractions as $extraction ) {
					
					array_push ( $values, $extraction[$element['columnname']] );
					
				}
				
				if ( count ( array_unique ( $values ) ) == 1 ) {
					
					nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );
					
					?><div class="nbtFeedbackGood nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						foreach ( $selectoptions as $option ) {
							
							if ( $option['dbname'] == $extractions[0][$element['columnname']] ) {
								
								?><a class="nbtTextOptionSelect nbtTextOptionChosen"><?php echo $option['displayname']; ?></a><?php
								
							} else {
								
								?><a class="nbtTextOptionSelect"><?php echo $option['displayname']; ?></a><?php
								
							}
							
						}
						
						?>
						
					</div><?php
					
				} else {
					
					?><div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement<?php echo $element['id']; ?>">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						foreach ( $extractions as $extraction ) {
							
							?><p><?php
							
							foreach ( $selectoptions as $option ) {
							
								if ( $option['dbname'] == $extraction[$element['columnname']] ) {
									
									?><a class="nbtTextOptionSelect nbtTextOptionChosen"><?php echo $option['displayname']; ?></a><?php
									
								} else {
									
									?><a class="nbtTextOptionSelect"><?php echo $option['displayname']; ?></a><?php
									
								}
								
							}
							
							?><span id="nbtExtractedElement<?php echo $element['id']; ?>-<?php echo $extraction['userid']; ?>" class="nbtHidden nbtFeedback nbtElement<?php echo $element['id']; ?>Check">&#x2713;</span></p>
							<span class="nbtExtractionName"><?php echo $extraction['username']; ?></span>
							<button onclick="nbtCopyToMaster(<?php echo $_GET['form']; ?>, <?php echo $_GET['refset'] ?>, <?php echo $_GET['ref']; ?>, '<?php echo $element['columnname']; ?>', <?php echo $extraction['id']; ?>, <?php echo $element['id']; ?>, <?php echo $extraction['userid']; ?>);">Copy to master</button><?php
							
						}
						
						?>
					</div><?php
					
				}
				
			break;
			
			case "multi_select":
				
				$selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );
				
				// Test for equality
				
				$multivalues = array();
				
				foreach ( $selectoptions as $option ) {
					
					$values = array ();
					
					foreach ( $extractions as $extraction ) {
						
						if ( is_null ( $extraction[$element['columnname'] . "_" . $option['dbname']] ) ) {
							
							$extraction[$element['columnname'] . "_" . $option['dbname']] = 0;
							
						}
						
						array_push ( $values, $extraction[$element['columnname'] . "_" . $option['dbname']] );
						
					}
					
					if ( count ( array_unique ( $values ) ) == 1 ) {
						
						array_push ( $multivalues, 1 );
						
					} else {
						
						array_push ( $multivalues, 0 );
						
					}
					
				}
				
				if ( count ( array_unique ( $multivalues ) ) == 1 ) { // If they're all the same
					
					?><div class="nbtFeedbackGood nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] ); ?>
						
						<?php
						
						foreach ( $selectoptions as $option ) {
							
							nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'] . "_" . $option['dbname'], $extractions[0]['id'] );
								
							if ( $extractions[0][$element['columnname'] . "_" . $option['dbname']] == 1 ) {
								
								?><a class="nbtTextOptionSelect nbtTextOptionChosen"><?php echo $option['displayname']; ?></a><?php
								
							} else {
								
								?><a class="nbtTextOptionSelect"><?php echo $option['displayname']; ?></a><?php
								
							}
							
						}
						
						?>
						
					</div><?php
					
				} else { // If they're not all the same
					
					?><div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement<?php echo $element['id']; ?>">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] ); ?>
						
						<?php
						
						foreach ( $extractions as $extraction ) {
							
							?><p><?php
							
							foreach ( $selectoptions as $option ) {
								
								if ( $extraction[$element['columnname'] . "_" . $option['dbname']] == 1 ) {
									
									?><a class="nbtTextOptionSelect nbtTextOptionChosen"><?php echo $option['displayname']; ?></a><?php
									
								} else {
									
									?><a class="nbtTextOptionSelect"><?php echo $option['displayname']; ?></a><?php
									
								}
								
							}
							
							?><span id="nbtExtractedElement<?php echo $element['id']; ?>-<?php echo $extraction['userid']; ?>" class="nbtHidden nbtFeedback nbtElement<?php echo $element['id']; ?>Check">&#x2713;</span></p>
							<span class="nbtExtractionName"><?php echo $extraction['username']; ?></span>
							<button onclick="nbtCopyMultiSelectToMaster(<?php echo $_GET['form']; ?>, <?php echo $_GET['refset'] ?>, <?php echo $_GET['ref']; ?>, <?php echo $extraction['id']; ?>, <?php echo $element['id']; ?>, <?php echo $extraction['userid']; ?>);">Copy to master</button><?php
							
						}
						
						?>
						
					</div><?php
					
				}
				
			break;
			
			case "country_selector":
			
				$values = array ();
				
				foreach ( $extractions as $extraction ) {
					
					array_push ( $values, $extraction[$element['columnname']] );
					
				}
				
				if ( count ( array_unique ( $values ) ) == 1 ) {
					
					nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );
					
					?><div class="nbtFeedbackGood nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						if ( $extractions[0][$element['columnname']] == "" ) {
							
							$extractions[0][$element['columnname']] = "[Left blank]";
							
						}
						
						?><p></p>
						
					</div><?php
					
				} else {
					
					?><div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement<?php echo $element['id']; ?>">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						foreach ( $extractions as $extraction ) {
							
							if ( $extraction[$element['columnname']] == "" ) {
								
								$extraction[$element['columnname']] = "[Left blank]";
								
							}
							
							?><p><?php echo $extraction[$element['columnname']]; ?><span id="nbtExtractedElement<?php echo $element['id']; ?>-<?php echo $extraction['userid']; ?>" class="nbtHidden nbtFeedback nbtElement<?php echo $element['id']; ?>Check">&#x2713;</span></p>
							<span class="nbtExtractionName"><?php echo $extraction['username']; ?></span>
							<button onclick="nbtCopyToMaster(<?php echo $_GET['form']; ?>, <?php echo $_GET['refset'] ?>, <?php echo $_GET['ref']; ?>, '<?php echo $element['columnname']; ?>', <?php echo $extraction['id']; ?>, <?php echo $element['id']; ?>, <?php echo $extraction['userid']; ?>);">Copy to master</button><?php
							
						}
						
						?>
					</div><?php
					
				}
				
			break;
			
			case "table_data":
				
				nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
				
				foreach ( $extractions as $extraction ) {
					
					?><p style="margin-bottom: 5px;"><span class="nbtExtractionName"><?php echo $extraction['username']; ?></span></p>
					
					<div id="nbtTableExtraction<?php echo $element['id']; ?>-<?php $extraction['id'] ?>"><?php
					
					$nbtExtractTableDataID = $element['id'];
					$nbtExtractRefSet = $_GET['refset'];
					$nbtExtractRefID = $_GET['ref'];
					$nbtExtractUserID = $extraction['userid'];
					
					include ('./tabledata.php');
					
					?></div><?php
					
				}
				
				?><p style="margin-bottom: 5px;"><span class="nbtExtractionName">Master table</span></p>
				<div id="nbtMasterTable<?php echo $element['id']; ?>"><?php
				
				$nbtMasterTableID = $element['id'];
				$nbtMasterRefSet = $_GET['refset'];
				$nbtMasterRefID = $_GET['ref'];
				
				include ('./mastertable.php');
				
				?></div><?php
				
			break;
			
			case "citations":
				
				nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
				
				?><div class="nbtCitationList"><?php
					
					$nbtListCitationsCitationID = $element['id'];
					$nbtListCitationsRefSetID = $_GET['refset'];
					$nbtListCitationsReference = $_GET['ref'];
					$nbtListCitationsUserID = $extraction['userid'];
					
					include ("./listcitations.php");
					
				?></div>
				
				<p style="margin-bottom: 5px;"><span class="nbtExtractionName">Master citations list</span></p>
				<div class="nbtCitationList" id="nbtMasterCitations<?php echo $element['id']; ?>"><?php
					
					$nbtListCitationsCitationID = $element['id'];
					$nbtListCitationsRefSetID = $_GET['refset'];
					$nbtListCitationsReference = $_GET['ref'];
					
					include ("./mastercitations.php");
				
				?></div><?php
				
			break;
			
			case "sub_extraction":
				
				?><div<?php
				
				if ( $element['toggle'] != "" ) {
					
					?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php
					
				}
				
				?>><?php
				
					nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
					
					?><div class="nbtSubExtraction" id="nbtSubExtraction<?php echo $element['id']; ?>"><?php
					
					$nbtSubExtractionElementID = $element['id'];
					$nbtExtractRefSet = $_GET['refset'];
					$nbtExtractRefID = $_GET['ref'];
					
					include ('./subextraction.php');
					
					?></div>
				
				</div><?php
				
			break;
			
		}
		
	}

	?></div>
</div>

<div style="height: 200px;">&nbsp;</div>