<?php

$subelements = nbt_get_sub_extraction_elements_for_elementid ( $nbtMasterSubExtrID );

$no_of_columns = count ( $subelements );

$subextractions = nbt_get_master_sub_extractions ( $nbtMasterSubExtrID, $nbtMasterSubExtrRefSet, $nbtMasterSubExtrRefID );

foreach ( $subextractions as $subextraction ) {

	?><div class="nbtSubExtraction" id="nbtMasterSubExtractionInstance<?php echo $nbtMasterSubExtrID; ?>-<?php echo $subextraction['id']; ?>">
		<button style="float: right;" onclick="$(this).fadeOut(0);$('#nbtRemoveSE<?php echo $nbtMasterSubExtrID; ?>-<?php echo $subextraction['id']; ?>').fadeIn();">Delete</button>
		<button id="nbtRemoveSE<?php echo $nbtMasterSubExtrID; ?>-<?php echo $subextraction['id']; ?>" class="nbtHidden" style="float: right;" onclick="nbtDeleteMasterSubExtraction(<?php echo $nbtMasterSubExtrID; ?>, <?php echo $subextraction['id']; ?>);">For real</button><?php
		
		foreach ( $subelements as $subelement ) {
			
			nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );
			
			switch ( $subelement['type'] ) {
						
				case "open_text":
				
					nbt_echo_msubextraction_text_field ($nbtMasterSubExtrID, $subextraction, $subelement['dbname'], 500, FALSE);
					
				break;
				
				case "date_selector":
					
					nbt_echo_msub_date_selector ($nbtMasterSubExtrID, $subextraction, $subelement['dbname']);
					
				break;
				
				case "single_select":
					
					$answers = array ();
					$toggles = array ();
					
					$selectoptions = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );
					
					foreach ( $selectoptions as $option ) {
						
						$answers[$option['dbname']] = $option['displayname'];
						$toggles[$option['dbname']] = $option['toggle'];
						
					}
					
					nbt_echo_msubextraction_single_select ( $nbtMasterSubExtrID, $subextraction, $subelement['dbname'], $answers, $toggles );
					
				break;
				
				case "multi_select":
					
					$answers = array ();
					$toggles = array ();
					
					$selectoptions = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );
					
					foreach ( $selectoptions as $option ) {
						
						$answers[$option['dbname']] = $option['displayname'];
						$toggles[$option['dbname']] = $option['toggle'];
						
					}
					
					nbt_echo_msubextraction_multi_select ($nbtSubExtractionElementID, $subextraction, $subelement['dbname'], $answers, $toggles );
					
				break;
				
			}
			
		}
		
	?></div><?php

}