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

		echo '<div id="nbtSubelementContainer' . $subelement['id'] . '-' . $subextraction['id'] . '"';

		if ($subelement['startup_visible'] != 1) {
		    echo ' class="nbtHidden"';
		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );

		nbt_echo_subextraction_text_field ($nbtSubExtractionElementID, $subextraction, $subelement['dbname'], 500, FALSE, $subelement['regex']); // Needs fixin'

		if ( $previous != NULL & $subelement['copypreviousprompt'] == 1 ) {

		    echo '<button style="display: block; margin-top: 4px;" onclick="$(\'#nbtSub' . $subextraction['id'] . 'TextField' . $subelement['dbname'] . '\').val( $(\'#nbtSub' . $previous['id'] . 'TextField' . $subelement['dbname'] . '\').val() ); nbtSaveSubExtractionTextField(' . $nbtSubExtractionElementID . ', ' . $subextraction['id'] . ', \'' . $subelement['dbname'] . '\', \'nbtSub' . $subextraction['id'] . 'TextField' . $subelement['dbname'] . '\', \'nbtSub' . $subextraction['id'] . 'TextField' . $subelement['dbname'] . 'Feedback\');">Copy from previous</button>';

		}

		echo '</div>';

		break;

	    case "tags":

		echo '<div id="nbtSubelementContainer' . $subelement['id'] . '-' . $subextraction['id'] . '"';

		if ($subelement['startup_visible'] != 1) {
		    echo ' class="nbtHidden"';
		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );

		$tagprompts = explode(";", $subelement['tagprompts']);
		$tagprompts = array_map('trim', $tagprompts);

		echo '<p>Tag prompts</p>';

		echo '<input type="hidden" id="nbtElementTagsPrompts' . $element['id'] . '" value="' . $element['tagprompts'] . '">';
		
		echo '<table class="nbtTabledData">';

		echo '<tr class="nbtTableHeaders"><td colspan="2"><input class="TagSearch' . $subelement['id'] . '" type="text" onkeyup="nbtSearchTagsPrompts(' . $subelement['id'] . ');" placeholder="Search tag prompts"></td></tr>';
		echo '<tr><td colspan="2"><button onclick="$(\'.TagPromptRow\').fadeIn(0);$(\'#TagSearch' . $element['id'] . '\').val(\'\');">Show all</button> <button onclick="$(\'.TagPromptRow\').fadeOut(0);$(\'#TagSearch' . $element['id'] . '\').val(\'\');">Show none</button></td></tr>';

		foreach ($tagprompts as $tagprompt) {
		    echo '<tr class="TagPromptRow TagPrompts' . $element['id'] . '">';

		    echo '<td class="TagPromptCell">' . $tagprompt . '</td>';
		    echo '<td style="text-align: right;"><button onclick="nbtAddTagToSelected(' . $element['id'] . ', $(this).parent().parent().find(\'.TagPromptCell\').html(), ' . $extraction['id'] . ', ' . $_GET['form'] . ', \'' . $element['columnname'] . '\');">Copy tag</button></td>';

		    echo "</tr>";
		}

		echo "</table>";

		

		echo '</div>';
		
		break;

				case "date_selector":

					?><div id="nbtSubelementContainer<?php echo $subelement['id']; ?>-<?php echo $subextraction['id']; ?>"<?php if ($subelement['startup_visible'] != 1) { echo ' class="nbtHidden"'; } ?>><?php

						nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );

						nbt_echo_sub_date_selector ($nbtSubExtractionElementID, $subextraction, $subelement['dbname']);

					if ( $previous != NULL & $subelement['copypreviousprompt'] == 1) {

						?><button style="display: block; margin-top: 4px;" onclick="$('#nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $subelement['dbname']; ?>').val('<?php echo $previous[$subelement['dbname']]; ?>');nbtSaveSubExtractionDateField(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $subelement['dbname']; ?>', 'nbtSub<?php echo $subextraction['id']; ?>DateField<?php echo $subelement['dbname']; ?>', 'nbtSub<?php echo $subextraction['id']; ?>TextField<?php echo $subelement['dbname']; ?>Feedback');">Copy from previous</button><?php

					}

					?></div><?php

				break;

				case "single_select":

					?><div id="nbtSubelementContainer<?php echo $subelement['id']; ?>-<?php echo $subextraction['id']; ?>"<?php if ($subelement['startup_visible'] != 1) { echo ' class="nbtHidden"'; } ?>><?php

						nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );

						$answers = array ();
						$toggles = array ();

						$selectoptions = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );

						foreach ( $selectoptions as $option ) {

							$answers[$option['dbname']] = $option['displayname'];
							$toggles[$option['dbname']] = $option['toggle'];

						}

						nbt_echo_subextraction_single_select ( $nbtSubExtractionElementID, $subelement['id'], $subextraction, $subelement['dbname'], $answers, $toggles );

						if ( $previous != NULL & $subelement['copypreviousprompt'] == 1 ) {

							?><button style="margin-left: 20px;" onclick="nbtCopySEPreviousSingleSelect(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $previous['id']; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $subelement['dbname']; ?>');">Copy from previous</button><?php

						}

					?></div><?php

				break;

				case "multi_select":

					?><div id="nbtSubelementContainer<?php echo $subelement['id']; ?>-<?php echo $subextraction['id']; ?>"<?php if ($subelement['startup_visible'] != 1) { echo ' class="nbtHidden"'; } ?>><?php

						nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );

						$answers = array ();
						$toggles = array ();

						$selectoptions = nbt_get_all_select_options_for_sub_element ( $subelement['id'] );

						foreach ( $selectoptions as $option ) {

							$answers[$option['dbname']] = $option['displayname'];
							$toggles[$option['dbname']] = $option['toggle'];

						}

						nbt_echo_subextraction_multi_select ($nbtSubExtractionElementID, $subelement['id'], $subextraction, $subelement['dbname'], $answers, $toggles );

						if ( $previous != NULL & $subelement['copypreviousprompt'] == 1 ) {

							?><button style="margin-left: 20px;" onclick="nbtCopySEPreviousMultiSelect(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $previous['id']; ?>, <?php echo $subextraction['id']; ?>, '<?php echo $subelement['dbname']; ?>');">Copy from previous</button><?php

						}

					?></div><?php

				break;
						
				case "table_data":

					?><div id="nbtSubelementContainer<?php echo $subelement['id']; ?>-<?php echo $subextraction['id']; ?>"<?php if ($subelement['startup_visible'] != 1) { echo ' class="nbtHidden"'; } ?>><?php

						nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );

						?><div id="nbtSubTableExtraction<?php echo $subelement['id']; ?>-<?php echo $subextraction['id']; ?>"><?php

						$nbtExtractTableDataID = $subelement['id'];

						if ( ! isset ( $nbtExtractRefSet ) ) {

							$nbtExtractRefSet = $_GET['refset'];

						}

						if ( ! isset ( $nbtExtractRefID ) ) {

							$nbtExtractRefID = $_GET['ref'];

						}


						$tableformat = "table_data";
						$nbtSubTableSubextractionID = $subextraction['id'];

						include ('./subtabledata.php');

						?></div>

					</div><?php

				break;

			}

		}

	?></div><?php

	$previous = $subextraction;

}

?><button onclick="nbtNewSubExtraction ( <?php echo $nbtSubExtractionElementID; ?>, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>, <?php echo $_SESSION[INSTALL_HASH . '_nbt_userid']; ?> );">Add new sub-extraction</button>
