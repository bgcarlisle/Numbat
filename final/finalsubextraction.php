<?php

$subelements = nbt_get_sub_extraction_elements_for_elementid ( $nbtMasterSubExtrID );

$no_of_columns = count ( $subelements );

$subextractions = nbt_get_master_sub_extractions ( $nbtMasterSubExtrID, $nbtMasterRefSet, $nbtMasterRefID );

foreach ( $subextractions as $subextraction ) {

    echo '<div class="nbtSubExtraction" id="nbtMasterSubExtractionInstance' . $nbtMasterSubExtrID . '-' . $subextraction['id'] . '">';

    echo '<button style="float: right;" onclick="$(this).fadeOut(0);$(\'#nbtRemoveSE' . $nbtMasterSubExtrID . '-' . $subextraction['id'] . '\').fadeIn();">Delete</button>';

    echo '<button id="nbtRemoveSE' . $nbtMasterSubExtrID . '-' . $subextraction['id'] . '" class="nbtHidden" style="float: right;" onclick="nbtDeleteMasterSubExtraction(' . $nbtMasterSubExtrID . ', ' . $subextraction['id'] . ');">For real</button>';

    foreach ( $subelements as $subelement ) {

	nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );

	switch ( $subelement['type'] ) {

	    case "open_text":
		
		nbt_echo_msubextraction_text_field ($nbtMasterSubExtrID, $subextraction, $subelement['dbname'], 500, FALSE);

		break;
		
	    case "text_area":
		
		nbt_echo_msubextraction_text_area ($nbtMasterSubExtrID, $subextraction, $subelement['dbname']);

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

		nbt_echo_msubextraction_multi_select ($nbtMasterSubExtrID, $subextraction, $subelement['dbname'], $answers, $toggles );

		break;

	    case "table_data":

		echo '<div id="nbtMasterSubTable' . $subelement['id'] . '-' . $subextraction['id'] . '">';
		
		$nbtMasterTableID = $subelement['id'];

		if ( ! isset ( $nbtMasterRefSet ) ) {

		    $nbtMasterRefSet = $_GET['refset'];

		}

		if ( ! isset ( $nbtMasterRefID ) ) {

		    $nbtMasterRefID = $_GET['ref'];

		}


		$tableformat = "table_data";
		$nbtSubTableSubextractionID = $subextraction['id'];

		include ('./finalsubtable.php');

		echo '</div>';

		break;

	    case "tags":

		$selectedtags = explode(";", $subextraction[$subelement['dbname']]);
		$selectedtags = array_map('trim', $selectedtags);

		echo '<input type="hidden" id="SelectedSubTagsText' . $subelement['id'] . '-' . $subextraction['id'] . '" value="' . $subextraction[$subelement['dbname']] . '">';
		
		echo '<table class="nbtTabledData" id="SelectedSubTagsTable' . $subelement['id'] . '-' . $subextraction['id'] . '">';

		echo '<tr class="nbtTableHeaders"><td colspan="2">Selected tags</td></tr>';
		
		foreach ($selectedtags as $selectedtag) {

		    if ($selectedtag != "") {

			echo '<tr><td><input type="text" value="' . $selectedtag . '" onblur="nbtRemoveSubTagFromSelectedFinal(' . $element['id'] . ', ' . $subelement['id'] . ', \'' . addslashes($selectedtag) . '\', ' . $subextraction['id'] . ', \'' . $subelement['dbname'] . '\');nbtAddSubTagToSelectedFinal(' . $element['id'] . ', ' . $subelement['id'] . ', $(this).val(), ' . $subextraction['id'] . ', \'' . $subelement['dbname'] . '\');"></td><td style="text-align: right;"><button onclick="nbtRemoveSubTagFromSelectedFinal(' . $element['id'] . ', ' . $subelement['id'] . ', \'' . addslashes($selectedtag) . '\', ' . $subextraction['id'] . ', \'' . $subelement['dbname'] . '\');">Remove</button></td></tr>';
			
		    }
		    
		}

		echo '<tr><td><input type="text" placeholder="Add new tag" value="" onblur="nbtAddSubTagToSelectedFinal(' . $element['id'] . ', ' . $subelement['id'] . ', $(this).val(), ' . $subextraction['id'] . ', \'' . $subelement['dbname'] . '\');" onkeyup="if (event.keyCode == 13) {nbtAddSubTagToSelectedFinal(' . $element['id'] . ', ' . $subelement['id'] . ', $(this).val(), ' . $subextraction['id'] . ', \'' . $subelement['dbname'] . '\');}"></td><td>&nbsp;</td></tr>';

		echo '</table>';

		echo '<p class="nbtHidden" id="TagFeedback' . $subelement['id'] . '-' . $subextraction['id'] . '">&nbsp;</p>';
		
		break;

	}
	
    }

    echo '</div>';

}
