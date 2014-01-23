<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );

$ref = nbt_get_reference_for_refsetid_and_refid ( $_GET['refset'], $_GET['ref'] );

$extractions = nbt_get_extractions_for_refset_ref_and_form ( $_GET['refset'], $_GET['ref'], $_GET['form'] );

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
				
				$values = array ();
				
				foreach ( $extractions as $extraction ) {
					
					array_push ( $values, $extraction[$element['columnname']] );
					
				}
				
				if ( count ( array_unique ( $values ) ) == 1 ) {
					
					?><div class="nbtFeedbackGood nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						if ( $extractions[0][$element['columnname']] == "" ) {
							
							$extractions[0][$element['columnname']] = "[Left blank]";
							
						}
						
						?><p><?php echo $extractions[0][$element['columnname']]; ?></p>
						
					</div><?php
					
				} else {
					
					?><div class="nbtFeedbackBad nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						foreach ( $extractions as $extraction ) {
							
							if ( $extraction[$element['columnname']] == "" ) {
								
								$extraction[$element['columnname']] = "[Left blank]";
								
							}
							
							?><p><?php echo $extraction[$element['columnname']]; ?></p>
							<span class="nbtExtractionName"><?php echo $extraction['username']; ?></span>
							<button>Copy to master</button><?php
							
						}
						
						?>
					</div><?php
					
				}
				
			break;
			
			case "date_selector":
				
				$values = array ();
				
				foreach ( $extractions as $extraction ) {
					
					array_push ( $values, $extraction[$element['columnname']] );
					
				}
				
				if ( count ( array_unique ( $values ) ) == 1 ) {
					
					?><div class="nbtFeedbackGood nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						?><p><?php echo substr ( $extractions[0][$element['columnname']], 0, 7 ); ?></p>
						
					</div><?php
					
				} else {
					
					?><div class="nbtFeedbackBad nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						foreach ( $extractions as $extraction ) {
							
							?><p><?php echo substr ( $extraction[$element['columnname']], 0, 7 ); ?></p>
							<span class="nbtExtractionName"><?php echo $extraction['username']; ?></span>
							<button>Copy to master</button><?php
							
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
					
					?><div class="nbtFeedbackBad nbtDoubleResult">
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
							
							?></p>
							<span class="nbtExtractionName"><?php echo $extraction['username']; ?></span>
							<button>Copy to master</button><?php
							
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
								
							if ( $extractions[0][$element['columnname'] . "_" . $option['dbname']] == 1 ) {
								
								?><a class="nbtTextOptionSelect nbtTextOptionChosen"><?php echo $option['displayname']; ?></a><?php
								
							} else {
								
								?><a class="nbtTextOptionSelect"><?php echo $option['displayname']; ?></a><?php
								
							}
							
						}
						
						?>
						
					</div><?php
					
				} else { // If they're not all the same
					
					?><div class="nbtFeedbackBad nbtDoubleResult">
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
							
							?></p>
							<span class="nbtExtractionName"><?php echo $extraction['username']; ?></span>
							<button>Copy to master</button><?php
							
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
					
					?><div class="nbtFeedbackGood nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						if ( $extractions[0][$element['columnname']] == "" ) {
							
							$extractions[0][$element['columnname']] = "[Left blank]";
							
						}
						
						?><p><?php echo $extractions[0][$element['columnname']]; ?></p>
						
					</div><?php
					
				} else {
					
					?><div class="nbtFeedbackBad nbtDoubleResult">
						<?php nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
						
						foreach ( $extractions as $extraction ) {
							
							if ( $extraction[$element['columnname']] == "" ) {
								
								$extraction[$element['columnname']] = "[Left blank]";
								
							}
							
							?><p><?php echo $extraction[$element['columnname']]; ?></p>
							<span class="nbtExtractionName"><?php echo $extraction['username']; ?></span>
							<button>Copy to master</button><?php
							
						}
						
						?>
					</div><?php
					
				}
				
			break;
			
			case "table_data":
				
				?><div<?php
				
				if ( $element['toggle'] != "" ) {
					
					?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php
					
				}
				
				?>><?php
				
					nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
					
					?><div id="nbtTableExtraction<?php echo $element['id']; ?>"><?php
					
					$nbtExtractTableDataID = $element['id'];
					$nbtExtractRefSet = $_GET['refset'];
					$nbtExtractRefID = $_GET['ref'];
					
					include ('./tabledata.php');
					
					?></div>
				
				</div><?php
				
			break;
			
			case "citations":
				
				?><div<?php
				
				if ( $element['toggle'] != "" ) {
					
					?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php
					
				}
				
				?>><?php
				
					nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );
					
					?><p>
						Add a new citation:
						<a href="#" onclick="event.preventDefault();$('#nbtCitationSuggestions<?php echo $element['id']; ?>').html('&nbsp;');$('#nbtCitationFinder<?php echo $element['id']; ?>').val('');">(Clear field)</a>
						<span class="nbtDoubleCitationFeedback nbtHidden" id="nbtDoubleCitationFeedback<?php echo $element['id']; ?>">You have already cited this reference here!</span>
					</p>
					<input type="text" class="nbtCitationFinder" id="nbtCitationFinder<?php echo $element['id']; ?>" onkeyup="nbtFindCitation(<?php echo $element['id']; ?>, '<?php echo $element['columnname']; ?>', 'nbtCitationSuggestions<?php echo $element['id']; ?>', <?php echo $element['id']; ?>, <?php echo $_GET['refset']; ?>, <?php echo $_GET['ref']; ?>);">
					<div class="nbtCitationSuggestions" id="nbtCitationSuggestions<?php echo $element['id']; ?>">&nbsp;</div>
					<div class="nbtCitationList" id="nbtCitationList<?php echo $element['id']; ?>"><?php
						
						$nbtListCitationsCitationID = $element['id'];
						$nbtListCitationsCitationDB = $element['columnname'];
						$nbtListCitationsRefSetID = $_GET['refset'];
						$nbtListCitationsReference = $_GET['ref'];
						
						include ("./listcitations.php");
						
					?></div>
				
				</div><?php
				
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