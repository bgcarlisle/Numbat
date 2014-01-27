<?php

$subelements = nbt_get_sub_extraction_elements_for_elementid ( $nbtSubExtractionElementID );

$no_of_columns = count ( $subelements );

$subextractions = nbt_get_sub_extractions ( $nbtSubExtractionElementID, $nbtExtractRefSet, $nbtExtractRefID, $nbtExtractUserID );

foreach ( $subextractions as $subextraction ) {

	?><div class="nbtSubExtraction" id="nbtSubExtractionInstance<?php echo $nbtSubExtractionElementID; ?>-<?php echo $subextraction['id']; ?>">
		<button style="float: right;" onclick="nbtCopySubExtraction(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>, <?php echo $subextraction['id']; ?>);">Copy to master</button><?php
		
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