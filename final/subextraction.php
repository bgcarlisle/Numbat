<?php

$subelements = nbt_get_sub_extraction_elements_for_elementid ( $nbtSubExtractionElementID );

$subextractions = nbt_get_sub_extractions ( $nbtSubExtractionElementID, $nbtExtractRefSet, $nbtExtractRefID, $nbtExtractUserID );

foreach ( $subextractions as $subextraction ) {

?><div class="nbtSubExtraction" id="nbtSubExtractionInstance<?php echo $nbtSubExtractionElementID; ?>-<?php echo $subextraction['id']; ?>">
    <button style="float: right;" onclick="nbtCopySubExtraction(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>, <?php echo $subextraction['id']; ?>);">Copy to final</button>
    <button style="float: right;" onclick="nbtMasterMoveSubExtraction(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $subextraction['id']; ?>, -1, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>, <?php echo $subextraction['userid']; ?>);">&#8595;</button>
    <button style="float: right;" onclick="nbtMasterMoveSubExtraction(<?php echo $nbtSubExtractionElementID; ?>, <?php echo $subextraction['id']; ?>, 1, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>, <?php echo $subextraction['userid']; ?>);">&#8593;</button>
    <?php

    foreach ( $subelements as $subelement ) {

	nbt_echo_display_name_and_codebook ( $subelement['displayname'], $subelement['codebook'] );

	switch ( $subelement['type'] ) {

	    case "open_text":

		echo $subextraction[$subelement['dbname']];

		break;

	    case "date_selector":

		echo $subextraction[$subelement['dbname']];

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

			echo '<span class="nbtDoubleMultiAnswers">';

			echo $option['displayname'];

			echo '</span>';

		    }

		}

		break;

	    case "table_data":

		echo '<div id="nbtSubTableExtraction';
		echo $subelement['id']; 
		echo '-';
		echo $subextraction['id'];
		echo '">';

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

		echo '</div>';
		
		break;

	    case "tags":

		$selectedtags = explode(";", $subextraction[$subelement['dbname']]);
		$selectedtags = array_map('trim', $selectedtags);

		echo '<table class="nbtTabledData">';

		echo '<tr class="nbtTableHeaders"><td>Selected tags</td></tr>';
		
		foreach ($selectedtags as $selectedtag) {

		    if ($selectedtag != "") {

			if ( in_array($selectedtag, $tagprompts) ) {
			    $addtopromptsbutton = "";
			} else {
			    $addtopromptsbutton = '<button onclick="nbtAddSubTagToPrompts(' . $subelement['id'] . ', $(this));">Add to prompts</button> ';
			}

			echo '<tr><td>' . $selectedtag . '</td></tr>';
			
		    }
		    
		}

		echo '</table>';

		break;

	}

    }

    echo '</div>';
    
    }

    ?>
