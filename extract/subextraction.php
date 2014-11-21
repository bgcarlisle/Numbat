<?php

$subelements = nbt_get_sub_extraction_elements_for_elementid ( $nbtSubExtractionElementID );

$subextractions = nbt_get_sub_extractions ( $nbtSubExtractionElementID, $nbtExtractRefSet, $nbtExtractRefID, $_SESSION[INSTALL_HASH . '_nbt_userid'] );

foreach ( $subextractions as $subextraction ) {

	?><div class="nbtSubExtraction" id="nbtSubExtractionInstance<?php echo $nbtSubExtractionElementID; ?>-<?php echo $subextraction['id']; ?>">
		<button style="float: right;" onclick="$(this).fadeOut(0);$('#nbtSubRemoveExtraction<?php echo $nbtSubExtractionElementID; ?>-<?php echo $subextraction['id']; ?>').fadeIn();">Delete</button>
		<button class="nbtHidden" id="nbtSubRemoveExtraction<?php echo $nbtSubExtractionElementID; ?>-<?php echo $subextraction['id']; ?>" style="float: right;" onclick="nbtDeleteSubExtraction(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $subextraction['id']; ?>);">For real</button>
		<button style="float: right;" onclick="nbtMoveSubExtraction(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $subextraction['id']; ?>, -1, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>, <?php echo $_SESSION[INSTALL_HASH . '_nbt_userid']; ?>);">&#8595;</button>
		<button style="float: right;" onclick="nbtMoveSubExtraction(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $subextraction['id']; ?>, 1, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>, <?php echo $_SESSION[INSTALL_HASH . '_nbt_userid']; ?>);">&#8593;</button>
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

					if ( $previous != NULL ) {

						?><button style="display: block; margin-top: 4px;" onclick="$('#nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $subelement['dbname']; ?>').val( $('#nbtSub<?php echo $previous['id']; ?>TextField<?php echo $subelement['dbname']; ?>').val() ); nbtSaveSubExtractionTextField(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $subelement['dbname']; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $subelement['dbname']; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $subelement['dbname']; ?>Feedback');">Copy from previous</button><?php

					}

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

					if ( $previous != NULL ) {

						?><button style="display: block; margin-top: 4px;" onclick="$('#nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $subelement['dbname']; ?>').val('<?php echo substr ( $previous[$subelement['dbname']], 0, 7); ?>');nbtSaveSubExtractionDateField(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $subelement['dbname']; ?>', 'nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $subelement['dbname']; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $subelement['dbname']; ?>Feedback');">Copy from previous</button><?php

					}

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

						if ( $previous != NULL ) {

							?><button style="margin-left: 20px;" onclick="nbtCopySEPreviousSingleSelect(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $previous['id']; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $subelement['dbname']; ?>');">Copy from previous</button><?php

						}

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

						if ( $previous != NULL ) {

							?><button style="margin-left: 20px;" onclick="nbtCopySEPreviousMultiSelect(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $previous['id']; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $subelement['dbname']; ?>');">Copy from previous</button><?php

						}

					?></div><?php

				break;

			}

		}

	?></div><?php

	$previous = $subextraction;

}

?><button onclick="nbtNewSubExtraction ( <?php echo $nbtSubExtractionElementID; ?>, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>, <?php echo $_SESSION[INSTALL_HASH . '_nbt_userid']; ?> );">Add new sub-extraction</button>
