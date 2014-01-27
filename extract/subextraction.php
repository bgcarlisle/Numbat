<?php

$subelements = nbt_get_sub_extraction_elements_for_elementid ( $nbtSubExtractionElementID );

$subextractions = nbt_get_sub_extractions ( $nbtSubExtractionElementID, $nbtExtractRefSet, $nbtExtractRefID, $_SESSION['nbt_userid'] );

foreach ( $subextractions as $subextraction ) {
	
	?><div class="nbtSubExtraction" id="nbtSubExtractionInstance<?php echo $nbtSubExtractionElementID; ?>-<?php echo $subextraction['id']; ?>">
		<button style="float: right;" onclick="$(this).fadeOut(0);$('#nbtSubRemoveExtraction<?php echo $nbtSubExtractionElementID; ?>-<?php echo $subextraction['id']; ?>').fadeIn();">Delete</button>
		<button class="nbtHidden" id="nbtSubRemoveExtraction<?php echo $nbtSubExtractionElementID; ?>-<?php echo $subextraction['id']; ?>" style="float: right;" onclick="nbtDeleteSubExtraction(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $subextraction['id']; ?>);">For real</button>
		<?php
	
		foreach ( $subelements as $subelement ) {
			
			switch ( $subelement['type'] ) {
				
				case "open_text":
					
					?><div<?php
					
					if ( $subelement['toggle'] != "" ) {
						
						?> class="nbtHidden <?php echo $subelement['toggle']; ?>"<?php
						
					}
					
					?>><?php
					
						nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );
						
						nbt_echo_subextraction_text_field ($nbtSubExtractionElementID, $subextraction, $subelement['dbname'], 500, FALSE); // Needs fixin'
					
					?></div><?php
					
				break;
				
				case "date_selector":
					
					?><div<?php
					
					if ( $subelement['toggle'] != "" ) {
						
						?> class="nbtHidden <?php echo $subelement['toggle']; ?>"<?php
						
					}
					
					?>><?php
					
						nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );
						
						nbt_echo_sub_date_selector ($nbtSubExtractionElementID, $subextraction, $subelement['dbname']);
					
					?></div><?php
					
				break;
				
				case "single_select":
					
					?><div<?php
					
					if ( $subelement['toggle'] != "" ) {
						
						?> class="nbtHidden <?php echo $subelement['toggle']; ?>"<?php
						
					}
					
					?>><?php
						
						nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );
						
						$answers = array ();
						$toggles = array ();
						
						$selectoptions = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );
						
						foreach ( $selectoptions as $option ) {
							
							$answers[$option['dbname']] = $option['displayname'];
							$toggles[$option['dbname']] = $option['toggle'];
							
						}
						
						nbt_echo_subextraction_single_select ( $nbtSubExtractionElementID, $subextraction, $subelement['dbname'], $answers, $toggles );
					
					?></div><?php
					
				break;
				
				case "multi_select":
					
					?><div<?php
					
					if ( $subelement['toggle'] != "" ) {
						
						?> class="nbtHidden <?php echo $subelement['toggle']; ?>"<?php
						
					}
					
					?>><?php
					
						nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );
						
						$answers = array ();
						$toggles = array ();
						
						$selectoptions = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );
						
						foreach ( $selectoptions as $option ) {
							
							$answers[$option['dbname']] = $option['displayname'];
							$toggles[$option['dbname']] = $option['toggle'];
							
						}
						
						nbt_echo_subextraction_multi_select ($nbtSubExtractionElementID, $subextraction, $subelement['dbname'], $answers, $toggles );
					
					?></div><?php
					
				break;
				
			}
			
		}
	
	?></div><?php
	
}

?><button onclick="nbtNewSubExtraction ( <?php echo $nbtSubExtractionElementID; ?>, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?> );">Add new sub-extraction</button>