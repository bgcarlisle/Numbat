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
					
					echo $subextraction[$subelement['dbname']];
					
				break;
				
				case "date_selector":
					
					echo substr ( $subextraction[$subelement['dbname']], 0, 7 );
					
				break;
				
				case "single_select":
					
					$options = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );
					
					foreach ( $options as $option ) {
						
						if ( $option['dbname'] == $subextraction[$subelement['dbname']] ) {
							
							echo $option['displayname'];
							
						}
						
					}
					
				break;
				
				case "multi_select":
					
					$options = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );
					
					foreach ( $options as $option ) {
						
						if ( $subextraction[$subelement['dbname'] . "_" . $option['dbname']] == 1 ) {
							
							?><span class="nbtDoubleMultiAnswers"><?php echo $option['displayname']; ?></span><?php
							
						}
						
					}
					
				break;
				
			}
			
		}
		
	?></div><?php

}