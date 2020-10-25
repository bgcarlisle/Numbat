<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );

$ref = nbt_get_reference_for_refsetid_and_refid ( $_GET['refset'], $_GET['ref'] );

$refset = nbt_get_refset_for_id ( $_GET['refset'] );

$extractions = nbt_get_extractions_for_refset_ref_and_form ( $_GET['refset'], $_GET['ref'], $_GET['form'] );

if ( count ( $extractions ) >= 2 ) {

    $master = nbt_get_master ( $_GET['form'], $_GET['refset'], $_GET['ref'] );

    echo '<div class="nbtNonsidebar">';

    echo '<div class="nbtContentPanel">';

    echo '<h2>' . $ref[$refset['title']] . '</h2>';

    echo '<p>' . $ref[$refset['authors']] . '</p>';

    if (( $ref[$refset['journal']] != "") && ($ref[$refset['year']] != "")) {

	echo '<p><span class="nbtJournalName">';

	echo $ref[$refset['journal']];

	echo '</span>: ' . $ref[$refset['year']] . '</p>';

    }

    echo '</div>';

    echo '<div class="nbtContentPanel">';

    echo '<h3>Abstract</h3>';

    echo '<p class="nbtFinePrint"><a href="#" onclick="event.preventDefault();$(\'#nbtAbstract\').slideToggle(200);">Show / hide abstract</a></p>';

    echo '<p id="nbtAbstract">';

    if ( $ref[$refset['abstract']] != NULL) {

	echo $ref[$refset['abstract']];

    } else {

	echo "[No abstract]";

    }

    echo '</p>';

    echo '</div>';

    echo '<div class="nbtContentPanel">';

    echo '<h3>Status of reconciliation</h3>';

    $answers = array (
	0 => "Not yet started",
	1 => "In progress",
	2 => "Completed"
    );

    foreach ( $answers as $dbanswer => $ptanswer ) {

	echo '<a href="#" class="nbtTextOptionSelect nbtstatus';

	if ( ! is_null ( $master['status'] ) ) { // This is because PHP will say that 0 and NULL are the same

	    if ( $master['status'] . " " == $dbanswer . " " ) { // This is because PHP has a hard time testing for equality between strings and integers

		echo ' nbtTextOptionChosen';

	    }

	}

	$buttonid = "nbtQstatusA" . str_replace ( "/", "_", str_replace (" ", "_", $dbanswer) );

	echo '" id="' . $buttonid . '" onclick="event.preventDefault();nbtSetMasterStatus(' . $_GET['form'] . ', ' . $master['id'] . ', ' . $dbanswer . ", '" . $buttonid . "', 'nbtstatus');\">";

	echo $ptanswer;

	echo "</a>";


    }

    echo "</div>";

    if ( $formelements[0]['type'] != "section_heading" ) {

	echo '<div class="nbtContentPanel">';

    }

    foreach ( $formelements as $element ) {

	switch ( $element['type'] ) {

	    case "section_heading":

		if ( $element['id'] != $formelements[0]['id'] ) {

		    echo '</div>';

		}

		echo '<div class="nbtContentPanel">';

		echo '<h3>';

		echo $element['displayname'];

		if ( $element['codebook'] != "" ) {

		    $element['codebook'] = str_replace ("\n", "<br>", $element['codebook']);

		    echo ' <a href="#" onclick="event.preventDefault();$(this).parent().next(\'.nbtCodebook\').slideToggle(100);">(?)</a></h3>';

		    echo '<div class="nbtCodebook">';

		    echo $element['codebook'];

		    echo '</div>';

		} else {

		    echo '</h3>';

		}

		break;

	    case "open_text":

	    case "prev_select":

		if ( ! is_null ($master[$element['columnname']]) ) { // If the final copy is non-NULL

		    // Test for equality

		    $values = array ();

		    foreach ( $extractions as $extraction ) {

			array_push ( $values, $extraction[$element['columnname']] );

		    }

		    if ( count ( array_unique ( $values ) ) == 1 ) { // If all the extractions got the same result

			// nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			if ( $extractions[0][$element['columnname']] == "" ) {

			    $extractions[0][$element['columnname']] = "[Left blank]";

			}

			echo '<p>' . $extractions[0][$element['columnname']] . '</p>';

			echo '<span class="nbtExtractionName">All extractors</span>';

			echo '<input id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" value="' . $master[$element['columnname']] . '" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo '</div>';

		    } else { // If not all the extractions are the same

			echo '<div class="nbtFeedbackGood nbtDoubleResult" id="nbtExtractedElement';

			echo $element['id'];

			echo '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    if ( $extraction[$element['columnname']] == "" ) {

				$extraction[$element['columnname']] = "[Left blank]";

			    }

			    if ( $extraction[$element['columnname']] == $master[$element['columnname']]) {

				echo '<p>';

				echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . $extraction[$element['columnname']] . '</span>';

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

				echo '<span class="nbtExtractionName">';

				echo $extraction['username'];

				echo '</span>';

				echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			    } else {

				echo '<p>';

				echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . $extraction[$element['columnname']] . '</span>';

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

				echo '<span class="nbtExtractionName">';

				echo $extraction['username'];

				echo '</span>';

				echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ", " . $element['id'] . ", " . $extraction['userid'] . ');">Copy to final</button>';

			    }

			}



			echo '<input id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" value="' . $master[$element['columnname']] . '" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo "</div>";


		    }

		} else { // The final copy is NULL

		    // Test for equality

		    $values = array ();

		    foreach ( $extractions as $extraction ) {

			array_push ( $values, $extraction[$element['columnname']] );

		    }

		    if ( count ( array_unique ( $values ) ) == 1 ) { // If all the extractions got the same result

			nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			if ( $extractions[0][$element['columnname']] == "" ) {

			    $extractions[0][$element['columnname']] = "[Left blank]";

			}

			echo "<p>" . $extractions[0][$element['columnname']] . "</p>";

			echo '<span class="nbtExtractionName">All extractors</span>';

			echo '<input id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" value="' . $extractions[0][$element['columnname']] . '" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo "</div>";

		    } else { // If not all the extractions are the same

			echo '<div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement';

			echo $element['id'];

			echo '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    if ( $extraction[$element['columnname']] == "" ) {

				$extraction[$element['columnname']] = "[Left blank]";

			    }

			    echo '<p>';

			    echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . $extraction[$element['columnname']] . '</span>';

			    echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

			    echo '<span class="nbtExtractionName">';

			    echo $extraction['username'];

			    echo '</span>';

			    echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ", " . $element['id'] . ", " . $extraction['userid'] . ');">Copy to final</button>';

			}

			echo '<input id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" value="" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo "</div>";

		    }

		}

		break;

	    case "text_area":

		// See if there is a value in the final copy

		if ( ! is_null ($master[$element['columnname']]) ) {

		    // Test for equality

		    $values = array ();

		    foreach ( $extractions as $extraction ) {

			array_push ( $values, $extraction[$element['columnname']] );

		    }

		    if ( count ( array_unique ( $values ) ) == 1 ) { // If all the extractions got the same result

			// nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			if ( $extractions[0][$element['columnname']] == "" ) {

			    $extractions[0][$element['columnname']] = "[Left blank]";

			}

			echo "<p>";

			echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . $extractions[0][$element['columnname']] . '</span>';

			echo "</p>";

			echo '<span class="nbtExtractionName">All extractors</span>';

			echo '<textarea id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">' . $master[$element['columnname']] . '</textarea>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo "</div>";

		    } else { // If not all the extractions are the same

			echo '<div class="nbtFeedbackGood nbtDoubleResult" id="nbtExtractedElement' . $element['id'] . '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    if ( $extraction[$element['columnname']] == "" ) {

				$extraction[$element['columnname']] = "[Left blank]";

			    }

			    if ( $extraction[$element['columnname']] == $master[$element['columnname']]) {

				echo '<p>';

				echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . $extraction[$element['columnname']] . '</span>';

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

				echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

				echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ", " . $element['id'] . ", " . $extraction['userid'] . ');">Copy to final</button>';

			    } else {

				echo '<p>';

				echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . $extraction[$element['columnname']] . '</span>';

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

				echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

				echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			    }

			}

			echo '<textarea id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">' . $master[$element['columnname']] . '</textarea>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo "</div>";

		    }

		} else {

		    // Test for equality

		    $values = array ();

		    foreach ( $extractions as $extraction ) {

			array_push ( $values, $extraction[$element['columnname']] );

		    }

		    if ( count ( array_unique ( $values ) ) == 1 ) { // If all the extractions got the same result

			nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			if ( $extractions[0][$element['columnname']] == "" ) {

			    $extractions[0][$element['columnname']] = "[Left blank]";

			}

			echo '<p>';

			echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . $extractions[0][$element['columnname']] . '</span>';

			echo '</p>';

			echo '<span class="nbtExtractionName">All extractors</span>';

			echo '<textarea id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">' . $extractions[0][$element['columnname']] . '</textarea>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo '</div>';

		    } else { // If not all the extractions are the same

			echo '<div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement' . $element['id'] . '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    if ( $extraction[$element['columnname']] == "" ) {

				$extraction[$element['columnname']] = "[Left blank]";

			    }

			    echo '<p>';

			    echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . $extraction[$element['columnname']] . '</span>';

			    echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>' . '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

			    echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ", " . $element['id'] . ", " . $extraction['userid'] . ');">Copy to final</button>';

			}

			echo '<textarea id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');"></textarea>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo '</div>';

		    }

		}

		break;

	    case "date_selector":

		// See if there is a value in the final copy

		if ( ! is_null ($master[$element['columnname']]) ) {

		    // Test for equality

		    $values = array ();

		    foreach ( $extractions as $extraction ) {

			array_push ( $values, $extraction[$element['columnname']] );

		    }

		    if ( count ( array_unique ( $values ) ) == 1 ) { // If all the extractors got the same result

			// nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			echo '<p>';

			echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . substr ( $extractions[0][$element['columnname']], 0, 7 ) . '</span>';

			echo '</p>';

			echo '<span class="nbtExtractionName">All extractors</span>';

			echo '<input id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" value="' . substr ($master[$element['columnname']], 0, 7) . '" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">';

			echo '</div>';

		    } else {

			echo '<div class="nbtFeedbackGood nbtDoubleResult" id="nbtExtractedElement' .  $element['id'] . '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    if ( $extraction[$element['columnname']] == $master[$element['columnname']]) {


				echo '<p>';

				echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . substr ( $extraction[$element['columnname']], 0, 7 ) . '</span>';

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

				echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

				echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			    } else {

				echo '<p>';

				echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . substr ( $extraction[$element['columnname']], 0, 7 ) . '</span>';

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

				echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

				echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			    }

			}

			echo '<input id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" value="' . substr( $master[$element['columnname']], 0, 7) . '" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">';

			echo '</div>';

		    }

		} else { // The final copy is NULL

		    // Test for equality

		    $values = array ();

		    foreach ( $extractions as $extraction ) {

			array_push ( $values, $extraction[$element['columnname']] );

		    }

		    if ( count ( array_unique ( $values ) ) == 1 ) { // If all the extractors got the same result

			nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			echo '<p>';

			echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . substr ( $extractions[0][$element['columnname']], 0, 7 ) . '</span>';

			echo '</p>';

			echo '<span class="nbtExtractionName">All extractors</span>';

			echo '<input id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" value="' . substr ($extractions[0][$element['columnname']], 0, 7) . '" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">';

			echo '</div>';

		    } else {

			echo '<div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement' . $element['id'] . '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    echo '<p>';

			    echo '<span id="nbtExtractedValue' . $element['id'] . '-' . $extraction['userid'] . '">' . substr ( $extraction[$element['columnname']], 0, 7 ) . '</span>';

			    echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>' . '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

			    echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', '.  $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			}

			echo '<input id="nbtFinalOverride' . $element['id'] . '" class="finalOverride" type="text" value="" onblur="nbtUpdateFinalColumn(' . $_GET['form'] .', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'nbtFinalOverride' . $element['id'] . '\', \'' . $element['type'] . '\', ' . $element['id'] . ');">';

			echo '</div>';

		    }

		}

		break;

	    case "single_select":

		$selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

		$values = array ();

		foreach ( $extractions as $extraction ) {

		    array_push ( $values, $extraction[$element['columnname']] );

		}

		if ( ! is_null ($master[$element['columnname']]) ) { // If the final copy is not NULL

		    if ( count ( array_unique ( $values ) ) == 1 ) { // If there was perfect agreement among the extractors

			// nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    if ( $option['dbname'] == $extractions[0][$element['columnname']] ) {

				echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtSingleSelectExtraction' . $extraction['id'] . '" dbname="' . $option['dbname'] . '">';

				echo $option['displayname'];

				echo '</a>';

			    } else {

				echo '<a class="nbtTextOptionSelect nbtSingleSelectExtraction' . $extraction['id'] . '" dbname="' . $option['dbname'] . '">';

				echo $option['displayname'];

				echo '</a>';

			    }

			}

			echo '</p>';

			echo '<span class="nbtExtractionName">All extractors</span>';

			// final copy

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    if ( $option['dbname'] == $master[$element['columnname']] ) {

				echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtElement' . $element['id'] . '" id="nbtElement' . $element['id'] . '-' . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'' . $option['dbname'] . '\', ' . $element['id'] . ', \'' . $element['type'] . '\');">';

				echo $option['displayname'];

				echo '</a>';

			    } else {

				echo '<a class="nbtTextOptionSelect nbtElement' . $element['id'] . '" id="nbtElement' . $element['id'] . '-' . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'' . $option['dbname'] . '\', ' . $element['id'] . ', \'' . $element['type'] . '\');">';

				echo $option['displayname'];

				echo '</a>';

			    }

			}

			echo '</p>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo '</div>';

		    } else { // If there was not perfect agreement among the extractors (final not NULL)

			echo '<div class="nbtFeedbackGood nbtDoubleResult" id="nbtExtractedElement' . $element['id'] . '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    echo '<p>';

			    foreach ( $selectoptions as $option ) {

				if ( $option['dbname'] == $extraction[$element['columnname']] ) {

				    echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtSingleSelectExtraction' . $extraction['id'] . '" dbname="' . $option['dbname'] . '">';

				    echo $option['displayname'];

				    echo '</a>';

				} else {

				    echo '<a class="nbtTextOptionSelect nbtSingleSelectExtraction' . $extraction['id'] . '" dbname="' . $option['dbname'] . '">';

				    echo $option['displayname'];

				    echo '</a>';

				}

			    }

			    if ( $extraction[$element['columnname']] == $master[$element['columnname']]) {

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span>';

			    } else {

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span>';


			    }

			    echo '</p>';

			    echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

			    echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ", " . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			}

			// final copy

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    if ( $option['dbname'] == $master[$element['columnname']] ) {

				echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtElement' . $element['id'] . '" id="nbtElement' . $element['id'] . '-' . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'' . $option['dbname'] . '\', ' . $element['id'] . ', \'' . $element['type'] . '\');">';

				echo $option['displayname'];

				echo '</a>';

			    } else {

				echo '<a class="nbtTextOptionSelect nbtElement' . $element['id'] . '" id="nbtElement' . $element['id'] . '-' . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'' . $option['dbname'] . '\', ' . $element['id'] . ', \'' . $element['type'] . '\');">';

				echo $option['displayname'];

				echo '</a>';

			    }

			}

			echo '</p>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo '</div>';

		    }

		} else { // If the final copy is NULL

		    if ( count ( array_unique ( $values ) ) == 1 ) { // If there was perfect agreement among the extractors

			nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    if ( $option['dbname'] == $extractions[0][$element['columnname']] ) {

				echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtSingleSelectExtraction' . $extraction['id'] . '" dbname="' . $option['dbname'] . '">';

				echo $option['displayname'];

				echo '</a>';

			    } else {

				echo '<a class="nbtTextOptionSelect nbtSingleSelectExtraction' . $extraction['id'] . '" dbname="' . $option['dbname'] . '">';

				echo $option['displayname'];

				echo '</a>';


			    }

			}

			echo '</p>';

			echo '<span class="nbtExtractionName">All extractors</span>';

			// final copy

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    if ( $option['dbname'] == $extractions[0][$element['columnname']] ) {
				
				echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtElement' . $element['id'] . '" id="nbtElement' . $element['id'] . '-' . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'' . $option['dbname'] . '\', ' . $element['id'] . ', \'' . $element['type'] . '\');">';

			    } else {

				echo '<a class="nbtTextOptionSelect nbtElement' . $element['id'] . '" id="nbtElement' . $element['id'] . '-' . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'' . $option['dbname'] . '\', ' . $element['id'] . ', \'' . $element['type'] . '\');">';

			    }

			    echo $option['displayname'];

			    echo '</a>';

			}

			echo '</p>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo '</div>';

		    } else { // If there was not perfect agreement among the extractors (final NULL)

			echo '<div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement' . $element['id'] . '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    echo '<p>';

			    foreach ( $selectoptions as $option ) {

				if ( $option['dbname'] == $extraction[$element['columnname']] ) {

				    echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtSingleSelectExtraction' . $extraction['id'] . '" dbname="' . $option['dbname'] . '">';

				    echo $option['displayname'];

				    echo '</a>';

				} else {

				    echo '<a class="nbtTextOptionSelect nbtSingleSelectExtraction' . $extraction['id'] . '" dbname="' . $option['dbname'] . '">';

				    echo $option['displayname'];

				    echo '</a>';

				}

			    }

			    echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

			    echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

			    echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			}

			// final copy

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    echo '<a class="nbtTextOptionSelect nbtElement' . $element['id'] . '" id="nbtElement' . $element['id'] . '-' . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . '\', \'' . $option['dbname'] . '\', ' . $element['id'] . ', \'' . $element['type'] . '\');">';

			    echo $option['displayname'];

			    echo '</a>';

			}

			echo '</p>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo '</div>';


		    }

		}

		break;

	    case "multi_select":

		$selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

		// Test for equality

		$multivalues = array();

		foreach ($extractions as $extraction) {

		    $extracted = "";

		    foreach ($selectoptions as $option) {
			
			if ( is_null ($extraction[$element['columnname'] . "_" . $option['dbname']]) ) {
			    $extracted .= "0";
			} else {
			    $extracted .= $extraction[$element['columnname'] . "_" . $option['dbname']];
			}
		    }

		    array_push ($multivalues, $extracted);
		    
		}

		// See if there's a non-null value in the final

		$non_null = 0;

		foreach ( $selectoptions as $option ) {

		    if ( ! is_null ( $master[$element['columnname'] . "_" . $option['dbname']] ) ) {

			$non_null++;

		    }

		}

		if ( $non_null != 0 ) { // Reconciliation has been done already

		    if ( count ( array_unique ( $multivalues ) ) == 1 ) { // If they're all the same

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    // nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'] . "_" . $option['dbname'], $extractions[0]['id'] );

			    if ( $extractions[0][$element['columnname'] . "_" . $option['dbname']] == 1 ) {

				echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtExtractedOption' . $element['id'] . '-' . $extraction['userid'] . '" dbname="' . $element['columnname'] . '_' . $option['dbname'] . '">' . $option['displayname'] . '</a>';

			    } else {

				echo '<a class="nbtTextOptionSelect nbtExtractedOption' . $element['id'] . '-' . $extraction['userid'] . '" dbname="' . $element['columnname'] . '_' . $option['dbname'] . '">' . $option['displayname'] . '</a>';

			    }

			}

			echo '</p>';

			echo '<span class="nbtExtractionName">All extractors</span>';

			// final copy

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    if ( $master[$element['columnname'] . "_" . $option['dbname']] == 1 ) {

				echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtElement' . $element['id'] . '" id="nbtElement' . $element[id] . '-' . $element['columnname'] . "_" . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . "_" . $option['dbname'] . '\', \'' . $extraction[$element['columnname'] . "_" . $option['dbname']] . '\', ' . $element['id'] .', \'' . $element['type'] . '\');">' . $option['displayname'] . '</a>';
				
			    } else {

				echo '<a class="nbtTextOptionSelect nbtElement' . $element['id'] . '" id="nbtElement' . $element[id] . '-' . $element['columnname'] . "_" . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . "_" . $option['dbname'] . '\', \'' . $extraction[$element['columnname'] . "_" . $option['dbname']] . '\', ' . $element['id'] .', \'' . $element['type'] . '\');">' . $option['displayname'] . '</a>';

			    }
			}

			echo '</p>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo '</div>';

		    } else { // If they're not all the same

			echo '<div class="nbtFeedbackGood nbtDoubleResult" id="nbtExtractedElement' . $element['id'] . '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    echo '<p>';

			    foreach ( $selectoptions as $option ) {

				if ( $extraction[$element['columnname'] . "_" . $option['dbname']] == 1 ) {

				    echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtExtractedOption' . $element['id'] . '-' . $extraction['userid'] . '" dbname="' . $element['columnname'] . '_' . $option['dbname'] . '">';

				    echo $option['displayname'];

				    echo '</a>';

				} else {

				    echo '<a class="nbtTextOptionSelect nbtExtractedOption' . $element['id'] . '-' . $extraction['userid'] . '" dbname="' . $element['columnname'] . '_' . $option['dbname'] . '">';

				    echo $option['displayname'];

				    echo '</a>';

				}

			    }

			    // Test to see if this is the one that's in the master

			    $same_as_master = 1;

			    foreach ( $selectoptions as $option ) {

				if ( $extraction[$element['columnname'] . "_" . $option['dbname']] != $master[$element['columnname'] . "_" . $option['dbname']] ) {

				    $same_as_master = 0;

				}

			    }

			    if ( $same_as_master == 1 ) {

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

				echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>' . '<button onclick="nbtCopyMultiSelectToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', ' . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			    } else {

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

				echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>' . '<button onclick="nbtCopyMultiSelectToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', ' . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			    }

			}

			// final copy

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    if ( $master[$element['columnname'] . "_" . $option['dbname']] == 1 ) {

				echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtElement' . $element['id'] . '" id="nbtElement' . $element[id] . '-' . $element['columnname'] . "_" . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . "_" . $option['dbname'] . '\', \'' . $extraction[$element['columnname'] . "_" . $option['dbname']] . '\', ' . $element['id'] .', \'' . $element['type'] . '\');">' . $option['displayname'] . '</a>';
				
			    } else {

				echo '<a class="nbtTextOptionSelect nbtElement' . $element['id'] . '" id="nbtElement' . $element[id] . '-' . $element['columnname'] . "_" . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . "_" . $option['dbname'] . '\', \'' . $extraction[$element['columnname'] . "_" . $option['dbname']] . '\', ' . $element['id'] .', \'' . $element['type'] . '\');">' . $option['displayname'] . '</a>';

			    }
			}

			echo '</p>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo '</div>';
			
		    }

		} else { // The reconciliation has not been done yet (first time opening maybe)

		    if ( count ( array_unique ( $multivalues ) ) == 1 ) { // If they're all the same

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'] . "_" . $option['dbname'], $extractions[0]['id'] );

			    if ( $extractions[0][$element['columnname'] . "_" . $option['dbname']] == 1 ) {

				echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtExtractedOption' . $element['id'] . '-' . $extraction['userid'] . '" dbname="' . $element['columnname'] . '_' . $option['dbname'] . '">';

				echo $option['displayname'];
				
				echo '</a>';

			    } else {

				echo '<a class="nbtTextOptionSelect nbtExtractedOption' . $element['id'] . '-' . $extraction['userid'] . '" dbname="' . $element['columnname'] . '_' . $option['dbname'] . '">';

				echo $option['displayname'];

				echo '</a>';
				
			    }

			}

			echo '</p>';

			echo '<span class="nbtExtractionName">All extractors</span>';

			// final copy

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    if ($extractions[0][$element['columnname'] . "_" . $option['dbname']] == 1) {

				echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtElement' . $element['id'] . '" id="nbtElement' . $element[id] . '-' . $element['columnname'] . "_" . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . "_" . $option['dbname'] . '\', \'' . $extraction[$element['columnname'] . "_" . $option['dbname']] . '\', ' . $element['id'] .', \'' . $element['type'] . '\');">' . $option['displayname'] . '</a>';
				
			    } else {

				echo '<a class="nbtTextOptionSelect nbtElement' . $element['id'] . '" id="nbtElement' . $element[id] . '-' . $element['columnname'] . "_" . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . "_" . $option['dbname'] . '\', \'' . $extraction[$element['columnname'] . "_" . $option['dbname']] . '\', ' . $element['id'] .', \'' . $element['type'] . '\');">' . $option['displayname'] . '</a>';
				
			    }
			    
			}

			echo '</p>';

			echo '<span class="nbtExtractionName">Final copy</span>';
			
			echo '</div>';


		    } else { // If they're not all the same

			echo '<div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement' . $element['id'] . '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    echo '<p>';

			    foreach ( $selectoptions as $option ) {

				if ( $extraction[$element['columnname'] . "_" . $option['dbname']] == 1 ) {

				    echo '<a class="nbtTextOptionSelect nbtTextOptionChosen nbtExtractedOption' . $element['id'] . '-' . $extraction['userid'] . '" dbname="' . $element['columnname'] . '_' . $option['dbname'] . '">';

				    echo $option['displayname'];

				    echo '</a>';

				} else {

				    echo '<a class="nbtTextOptionSelect nbtExtractedOption' . $element['id'] . '-' . $extraction['userid'] . '" dbname="' . $element['columnname'] . '_' . $option['dbname'] . '">';

				    echo $option['displayname'];

				    echo '</a>';

				}

			    }

			    echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

			    echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

			    echo '<button onclick="nbtCopyMultiSelectToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', ' . $extraction['id'] . ', ' . $element['id'] . ', ' .  $extraction['userid'] . ');">Copy to final</button>';

			}

			// final copy

			echo '<p>';

			foreach ( $selectoptions as $option ) {

			    echo '<a class="nbtTextOptionSelect nbtElement' . $element['id'] . '" id="nbtElement' . $element[id] . '-' . $element['columnname'] . "_" . $option['dbname'] . '" onclick="nbtUpdateFinalSelector(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ', \'' . $element['columnname'] . "_" . $option['dbname'] . '\', \'' . $extraction[$element['columnname'] . "_" . $option['dbname']] . '\', ' . $element['id'] .', \'' . $element['type'] . '\');">' . $option['displayname'] . '</a>';
			    
			}

			echo '</p>';

			echo '<span class="nbtExtractionName">Final copy</span>';

			echo '</div>';


		    }

		}

		break;

	    case "country_selector":

		$values = array ();

		foreach ( $extractions as $extraction ) {

		    array_push ( $values, $extraction[$element['columnname']] );

		}

		if ( ! is_null ($master[$element['columnname']]) ) {

		    if ( count ( array_unique ( $values ) ) == 1 ) {

			nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			if ( $extractions[0][$element['columnname']] == "" ) {

			    $extractions[0][$element['columnname']] = "[Left blank]";

			}

			echo '<p>' . $extractions[0][$element['columnname']] . '</p>';

			echo '</div>';

		    } else {

			echo '<div class="nbtFeedbackGood nbtDoubleResult" id="nbtExtractedElement' . $element['id'] . '">';
			
			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    if ( $extraction[$element['columnname']] == "" ) {

				$extraction[$element['columnname']] = "[Left blank]";

			    }

			    if ( $extraction[$element['columnname']] == $master[$element['columnname']] ) {

				echo '<p>';

				echo $extraction[$element['columnname']];

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

				echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

				echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			    } else {

				echo '<p>';

				echo $extraction[$element['columnname']];

				echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

				echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

				echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';

			    }

			}

			echo '</div>';

		    }

		} else {

		    if ( count ( array_unique ( $values ) ) == 1 ) {

			nbt_copy_to_master ( $_GET['form'], $_GET['refset'], $_GET['ref'], $element['columnname'], $extractions[0]['id'] );

			echo '<div class="nbtFeedbackGood nbtDoubleResult">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			if ( $extractions[0][$element['columnname']] == "" ) {

			    $extractions[0][$element['columnname']] = "[Left blank]";

			}

			echo '<p>' . $extractions[0][$element['columnname']] . '</p>';

			echo '</div>';

		    } else {

			echo '<div class="nbtFeedbackBad nbtDoubleResult" id="nbtExtractedElement' . $element['id'] . '">';

			nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

			foreach ( $extractions as $extraction ) {

			    if ( $extraction[$element['columnname']] == "" ) {

				$extraction[$element['columnname']] = "[Left blank]";

			    }

			    echo '<p>';

			    echo $extraction[$element['columnname']];

			    echo '<span id="nbtExtractedElement' . $element['id'] . '-' . $extraction['userid'] . '" class="nbtHidden nbtFeedback nbtElement' . $element['id'] . 'Check">&#x2713;</span></p>';

			    echo '<span class="nbtExtractionName">' . $extraction['username'] . '</span>';

			    echo '<button onclick="nbtCopyToMaster(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ", '" . $element['columnname'] . "', " . $extraction['id'] . ', ' . $element['id'] . ', ' . $extraction['userid'] . ');">Copy to final</button>';


			}

			echo '</div>';


		    }

		}

		break;

	    case "table_data":

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		foreach ( $extractions as $extraction ) {

		    echo '<p style="margin-bottom: 5px;"><span class="nbtExtractionName">';

		    echo $extraction['username'];

		    echo '</span></p>';

		    echo '<div id="nbtTableExtraction';

		    echo $element['id'];

		    echo '-';

		    echo $extraction['id'];

		    echo '">';

		    $nbtExtractTableDataID = $element['id'];
		    $nbtExtractRefSet = $_GET['refset'];
		    $nbtExtractRefID = $_GET['ref'];
		    $nbtExtractUserID = $extraction['userid'];

		    $tableformat = "table_data";

		    include ('./tabledata.php');

		    echo '</div>';

		}

		echo '<p style="margin-bottom: 5px;"><span class="nbtExtractionName">Final copy table</span></p>';

		echo '<div id="nbtMasterTable';

		echo $element['id'];

		echo '">';

		$nbtMasterTableID = $element['id'];
		$nbtMasterRefSet = $_GET['refset'];
		$nbtMasterRefID = $_GET['ref'];

		$tableformat = "table_data";

		include ('./finaltable.php');

		echo '</div>';


		break;

	    case "ltable_data":

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		foreach ( $extractions as $extraction ) {

		    echo '<p style="margin-bottom: 5px;"><span class="nbtExtractionName">';

		    echo $extraction['username'];

		    echo '</span></p>';

		    echo '<div id="nbtTableExtraction';

		    echo $element['id'];

		    echo '-';

		    echo $extraction['id'];

		    echo '">';

		    $nbtExtractTableDataID = $element['id'];
		    $nbtExtractRefSet = $_GET['refset'];
		    $nbtExtractRefID = $_GET['ref'];
		    $nbtExtractUserID = $extraction['userid'];

		    $tableformat = "ltable_data";

		    include ('./tabledata.php');

		    echo '</div>';

		}

		echo '<p style="margin-bottom: 5px;"><span class="nbtExtractionName">Final copy table</span></p>';

		echo '<div id="nbtMasterTable';

		echo $element['id'];

		echo '">';

		$nbtMasterTableID = $element['id'];
		$nbtMasterRefSet = $_GET['refset'];
		$nbtMasterRefID = $_GET['ref'];

		$tableformat = "ltable_data";

		include ('./finaltable.php');

		echo '</div>';

		break;

	    case "citations":

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		echo '<div class="nbtCitationList">';

		$nbtListCitationsCitationID = $element['id'];
		$nbtListCitationsRefSetID = $_GET['refset'];
		$nbtListCitationsReference = $_GET['ref'];
		$nbtListCitationsUserID = $extraction['userid'];

		include ("./listcitations.php");

		echo '</div>';

		echo '<p style="margin-bottom: 5px;"><span class="nbtExtractionName">Final citations list</span></p>';

		echo '<div class="nbtCitationList" id="nbtMasterCitations';
		
		echo $element['id'];

		echo '">';

		$nbtListCitationsCitationID = $element['id'];
		$nbtListCitationsRefSetID = $_GET['refset'];
		$nbtListCitationsReference = $_GET['ref'];

		include ("./finalcitations.php");

		echo '</div>';

		break;

	    case "sub_extraction":

		echo '<div>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		echo '<table class="nbtTabledData">';

		echo '<tr>';

		foreach ( $extractions as $extraction ) {

		    echo '<td><p style="margin-bottom: 5px;"><span class="nbtExtractionName">';

		    echo $extraction['username'];

		    echo '</span></p></td>';
		    
		}

		echo '</tr>';

		echo '<tr>';

		foreach ( $extractions as $extraction ) {

		    echo '<td>';

		    echo '<div class="nbtSubExtraction" id="nbtSubExtraction';
		    
		    echo $element['id'];

		    echo '-';

		    echo $extraction['userid'];

		    echo '">';

		    $nbtSubExtractionElementID = $element['id'];
		    $nbtExtractRefSet = $_GET['refset'];
		    $nbtExtractRefID = $_GET['ref'];
		    $nbtExtractUserID = $extraction['userid'];

		    include (ABS_PATH . 'final/subextraction.php');

		    echo '</div>';

		    echo '</td>';

		    

		}

		echo '</tr>';

		echo '</table>';

		echo '<p style="margin-bottom: 5px;"><span class="nbtExtractionName">Final sub-extraction</span></p>';

		echo '<div id="nbtMasterSubExtraction';

		echo $element['id'];

		echo '">';

		$nbtMasterSubExtrID = $element['id'];
		$nbtMasterRefSet = $_GET['refset'];
		$nbtMasterRefID = $_GET['ref'];

		include ('./finalsubextraction.php');

		echo '</div>';

		echo '</div>';

		break;

	    case "reference_data":

		echo '<div class="nbtContentPanel">';

		echo '<h3>';

		echo $element['displayname'];

		if ( $element['codebook'] != "" ) {

		    $element['codebook'] = str_replace ("\n", "<br>", $element['codebook']);

		    echo ' <a href="#" onclick="event.preventDefault();$(this).parent().next(\'.nbtCodebook\').slideToggle(100);">(?)</a></h3>';

		    echo '<div class="nbtCodebook">';

		    echo $element['codebook'];

		    echo '</div>';

		} else {

		    echo '</h3>';

		}

		$refdata = $element['columnname'];

		preg_match_all(
		    '/\$([A-Za-z0-9_-]+)/',
		    $element['columnname'],
		    $cols_to_replace
		);

		foreach ( $cols_to_replace[0] as $col_to_replace ) {

		    $refdata = str_replace (
			$col_to_replace,
			$ref[substr($col_to_replace, 1)],
			$refdata
		    );
		}

		echo "<p>" . $refdata . "</p>";

		echo '</div>';


		break;

	}

    }

    echo '</div>';

    echo '</div>';

    echo '</div>';

    echo '<div style="height: 200px;">&nbsp;</div>';

} else {

    echo '<div class="nbtContentPanel">';

    echo '<h2>Only one extraction done</h2>';

    echo '<p>Reconciliation is only available for references with two completed extractions.</p>';

    echo '</div>';

}

?>
