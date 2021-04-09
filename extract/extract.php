<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );
$alltoggles = nbt_get_toggles_for_formid ($_GET['form']);

?><button onclick="$('.nbtSidebar').fadeIn(200);$(this).fadeOut(0);$('#nbtExtractionNotes').focus();" id="nbtUnhideSidebar" style="position: fixed; right: 20px; top: 60px;">Show notes</button>
<div class="nbtSidebar" style="display: none;">
    <h3>Extraction notes</h3>
    <p class="nbtFinePrint">These notes are for your own reference. These will not be reconciled with other extractors. <a href="#" onclick="event.preventDefault();$(this).parent().parent().fadeOut(200);$('button#nbtUnhideSidebar').fadeIn(200);">[Hide]</a></p>
    <textarea id="nbtExtractionNotes" onblur="nbtSaveTextField(<?php echo $_GET['form']; ?>, <?php echo $extraction['id']; ?>, 'notes', 'nbtExtractionNotes');"><?php echo $extraction['notes']; ?></textarea>
</div>
<div class="nbtCoverup" id="nbtManualRefsCoverup">&nbsp;</div>
<div id="nbtManualRefs" class="nbtInlineManualNewRef">&nbsp;</div>
<div class="nbtNonsidebar">
    <div class="nbtContentPanel">
	<h2><?php echo $ref[$refset['title']]; ?></h2>
	<p><?php echo $ref[$refset['authors']]; ?></p>
	<?php

	if (( $ref[$refset['journal']] != "") && ($ref[$refset['year']] != "")) {

	    echo '<p><span class="nbtJournalName">';

	    echo $ref[$refset['journal']];

	    echo '</span>: ';

	    echo $ref[$refset['year']];

	    echo '</p>';

	}

	if ( is_dir ( ABS_PATH . "attach/files/" . $_GET['refset'] . "/" ) ) {

	    $files = scandir ( ABS_PATH . "attach/files/" . $_GET['refset'] . "/" );

	    foreach ( $files as $file ) {

		if ( substr ($file, 0, 1) != "." ) {

		    $file_ref = explode(".", $file);

		    if ( $file_ref[0] == $_GET['ref']) {

			echo '<span class="nbtAttachment"><a href="';

			echo SITE_URL;

			echo 'attach/files/';

			echo $_GET['refset'];

			echo '/';

			echo $file;

			echo '">Attached ';

			echo $file_ref[1];

			echo '</a></span>';

		    }

		}

	    }

	}

	if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

	    echo '<button onclick="window.open(\'';

	    echo SITE_URL;

	    echo 'attach/?action=new&refset=';

	    echo $_GET['refset'];

	    echo '&ref=';

	    echo $_GET['ref'];

	    echo '\',\'_self\');">Attach a file to this reference</button>';

	}

	?>
    </div>
    <div class="nbtContentPanel">
	<h3>Abstract</h3>
	<p class="nbtFinePrint"><a href="#" onclick="event.preventDefault();$('#nbtAbstract').slideToggle(200);">Show / hide abstract</a></p>
	<p id="nbtAbstract">
	    <?php

	    if ( $ref[$refset['abstract']] != NULL) {

		echo $ref[$refset['abstract']];

	    } else {

		echo "[No abstract]";

	    }

	    ?>
	</p>
    </div>
    <div class="nbtContentPanel">
	<h3>Status of extraction</h3>
	<?php nbt_echo_single_select ($_GET['form'], $extraction, "status", array (
	    0 => "Not yet started",
	    1 => "In progress",
	    2 => "Completed"
	), array () ); ?>
    </div>
    <?php

    if ( $formelements[0]['type'] != "section_heading" ) {

	echo '<div class="nbtContentPanel">';
	
    }
    
    foreach ( $formelements as $element ) {

	switch ( $element['type'] ) {

	    case "section_heading":

		if ( $element['id'] != $formelements[0]['id'] ) {

		    echo '</div>';

		}

		echo '<div class="nbtContentPanel';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' nbtHidden';
		    
		}

		echo '">';

		echo '<h3>';
		
		echo $element['displayname'];
		
		if ( $element['codebook'] != "" ) {

		    $element['codebook'] = str_replace ("\n", "<br>", $element['codebook']);

		    echo ' <a href="#" onclick="event.preventDefault();$(this).parent().next(\'.nbtCodebook\').slideToggle(100);">(?)</a>';

		    echo '</h3>';

		    echo '<div class="nbtCodebook">';

		    echo $element['codebook'];

		    echo '</div>';

		} else {

		    echo '</h3>';

		}

		break;

	    case "timer":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( "Extraction timer", $element['codebook'] );

		if ( ! $form_preview ) {

		    $extraction_times = nbt_get_times_for_extraction ( $_GET['form'], $_GET['refset'], $_GET['ref'], $_SESSION[INSTALL_HASH . '_nbt_userid'] );
		    
		} else {

		    $extraction_times['time_started'] = 0;
		    $extraction_times['time_finished'] = "NaN";
		    
		}

		echo '<input type="hidden" id="time_started" value="' . $extraction_times['time_started'] . '">';

		echo '<p>Started: <span id="time_started_display"></span></p>';

		echo '<button onclick="nbtRestartExtractionTimer(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ');">Restart timer</button>';

		echo '<input type="hidden" id="time_finished" value="' . $extraction_times['time_finished'] . '">';

		echo '<p>Finished: <span id="time_finished_display"></span></p>';

		echo '<button id="nbtFinishedTimeClearButton" onclick="nbtClearFinishedTime(' . $_GET['form'] . ', ' . $_GET['refset'] . ', ' . $_GET['ref'] . ');">Clear finished time</button>';

		echo '<p class="nbtFinePrint">"Time started" is set when the extraction is opened for the first time, and "time finished" is set the first time the extraction is marked as complete. To resume timing your extraction after marking it as complete, clear the finished time. To re-start the timer, click "Restart timer."</p>';

		echo '<script>$(document).ready( function () { nbtUpdateExtractionTimer(1); } );</script>';

		break;
		
	    case "open_text":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		nbt_echo_text_field ($_GET['form'], $extraction, $element['columnname'], 200, FALSE, $element['regex']);

		echo '</div>';

		break;

	    case "text_area":

		echo '<div';
		
		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		nbt_echo_text_area_field ($_GET['form'], $extraction, $element['columnname'], 5000, FALSE);

		echo '</div>';

		break;

	    case "date_selector":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';
		    
		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		nbt_echo_date_selector ($_GET['form'], $extraction, $element['columnname']);

		echo '</div>';

		break;

	    case "single_select":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		$answers = array ();
		$toggles = array ();

		$selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

		foreach ( $selectoptions as $option ) {

		    $answers[$option['dbname']] = $option['displayname'];
		    $toggles[$option['dbname']] = $option['toggle'];

		}

		nbt_echo_single_select ( $_GET['form'], $extraction, $element['columnname'], $answers, $toggles );

		echo '</div>';

		break;

	    case "multi_select":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		$answers = array ();
		$toggles = array ();

		$selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

		foreach ( $selectoptions as $option ) {

		    $answers[$option['dbname']] = $option['displayname'];
		    $toggles[$option['dbname']] = $option['toggle'];

		}

		nbt_echo_multi_select ($_GET['form'], $extraction, $element['columnname'], $answers, $toggles );

		echo '</div>';

		break;

	    case "country_selector":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		$countries = nbt_return_country_array ();

		echo '<select id="nbtCountrySelect';

		echo $element['columnname'];

		echo '" onblur="nbtSaveTextField(';

		echo $_GET['form'];

		echo ', ';

		echo $extraction['id'];

		echo ", '";

		echo $element['columnname'];

		echo "', 'nbtCountrySelect";

		echo $element['columnname'];

		echo "', 'nbtCountrySelect";

		echo $element['columnname'];

		echo 'Feedback\');">';

		foreach ( $countries as $country ) {

		    echo '<option value="';

		    echo $country;

		    echo '"';

		    if ( $extraction[$element['columnname']] == $country ) {

			echo ' selected';

		    }

		    echo '>';

		    echo $country;

		    echo '</option>';

		}

		echo '</select>';

		echo '<span class="nbtInputFeedback" id="nbtCountrySelect';

		echo $element['columnname'];

		echo 'Feedback">&nbsp;</span>';

		echo '</div>';
		
		break;

	    case "table_data":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';
		    
		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		echo '<div id="nbtTableExtraction';

		echo $element['id'];

		echo '">';

		$nbtExtractTableDataID = $element['id'];
		$nbtExtractRefSet = $_GET['refset'];
		$nbtExtractRefID = $_GET['ref'];

		$tableformat = "table_data";

		include ('./tabledata.php');

		echo '</div>';

		echo '</div>';

		break;

	    case "ltable_data":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		echo '<div id="nbtTableExtraction';

		echo $element['id'];

		echo '">';

		$nbtExtractTableDataID = $element['id'];
		$nbtExtractRefSet = $_GET['refset'];
		$nbtExtractRefID = $_GET['ref'];

		$tableformat = "ltable_data";

		include ('./tabledata.php');

		echo '</div>';

		echo '</div>';

		break;

	    case "citations":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		echo '<p class="nbtFinePrint">';

		echo '<span>Start typing in the field below to add a new citation to this extraction</span>';

		echo '<span class="nbtDoubleCitationFeedback nbtFeedbackBad nbtHidden" id="nbtDoubleCitationFeedback';
		
		echo $element['id'];

		echo '">You have already cited this reference here!</span>';

		echo '</p>';

		echo '<button onclick="event.preventDefault();nbtCiteClearField(';

		echo $element['id'];

		echo ');" id="nbtCiteClearField';

		echo $element['id'];

		echo '">Clear field</button>';

		echo '<button onclick="nbtAddNewReferenceToRefSet(';
		
		echo $extraction['refsetid'];

		echo ');">Add a new reference</button>';

		echo '<button onclick="window.open(\'';

		echo SITE_URL . "references/manual/?refset=" . $extraction['refsetid'];

		echo '\');">View manually added references</button>';

		echo '<input type="text" class="nbtCitationFinder" id="nbtCitationFinder';
		
		echo $element['id'];

		echo '" onkeyup="nbtFindCitation(event, ';

		echo $element['id'];

		echo ", '";

		echo $element['columnname'];

		echo "', 'nbtCitationSuggestions";

		echo $element['id'];

		echo "', ";

		echo $element['id'];

		echo ', ';

		echo $_GET['refset'];

		echo ', ';

		echo $_GET['ref'];

		echo ');">';

		echo '<div class="nbtCitationSuggestions" id="nbtCitationSuggestions';

		echo $element['id'];

		echo '">&nbsp;</div>';

		echo '<div class="nbtCitationList" id="nbtCitationList';

		echo $element['id'];

		echo '">';
		
		$nbtListCitationsCitationID = $element['id'];
		$nbtListCitationsCitationDB = $element['columnname'];
		$nbtListCitationsRefSetID = $_GET['refset'];
		$nbtListCitationsReference = $_GET['ref'];

		include ("./listcitations.php");

		echo '</div>';

		echo '</div>';

		break;

	    case "sub_extraction":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		echo '<div class="nbtSubExtraction" id="nbtSubExtraction';

		echo $element['id'];

		echo '-';

		echo $_SESSION[INSTALL_HASH . '_nbt_userid'];

		echo '">';

		$nbtSubExtractionElementID = $element['id'];
		$nbtExtractRefSet = $_GET['refset'];
		$nbtExtractRefID = $_GET['ref'];

		include ('./subextraction.php');

		echo '</div>';

		echo '</div>';

		break;

	    case "assignment_editor":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		echo '<select id="nbtAssignUser">';

		echo '<option value="NULL">Choose a user to assign</option>';

		$users = nbt_get_all_users ();

		foreach ( $users as $user ) {

		    echo '<option value="';

		    echo $user['id'];

		    echo '">';

		    echo $user['username'];

		    echo '</option>';
		    
		}

		echo '</select>';

		echo '<select id="nbtAssignForm">';

		echo '<option value="NULL">Choose a form to use</option>';

		$forms = nbt_get_all_extraction_forms ();

		foreach ( $forms as $form ) {

		    echo '<option value="';

		    echo $form['id'];

		    echo '">';

		    echo $form['name'];

		    echo '</option>';

		}

		echo '</select>';

		echo '<button onclick="nbtAddAssignmentInExtraction(';
		
		echo $_GET['refset'];

		echo ', ';

		echo $_GET['ref'];

		echo ', ';

		echo $element['id'];

		echo ');">Assign this reference</button>';

		echo '<p class="nbtFinePrint nbtHidden" id="nbtAddAssignmentFeedback';

		echo $element['id'];

		echo '">&nbsp;</p>';

		echo '</div>';
		
		break;

	    case "reference_data":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';

		}

		echo '>';

		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		$refdata = $element['columnname'];
		
		preg_match_all(
		    '/\$([A-Za-z0-9_-]+)/',
		    $element['columnname'],
		    $cols_to_replace
		);

		foreach ( $cols_to_replace[0] as $col_to_replace ) {

		    $refdata = preg_replace (
			"/\\" . $col_to_replace . "\b/",
			$ref[substr($col_to_replace, 1)],
			$refdata
		    );
		}

		$refdata = str_replace("\n", "<br>", $refdata);

		echo "<p>" . $refdata . "</p>";

		echo '</div>';

		break;

	    case "prev_select":

		echo '<div';

		if ( $element['startup_visible'] != 1 ) {

		    echo ' class="nbtHidden';

		    echo '"';
		    
		}

		echo '>';
		
		nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

		$unique_previous_entries = nbt_get_unique_entries_for_prev_select ( $element['id'], $extraction['refsetid'], $extraction['id'] );

		if ( count ( $unique_previous_entries ) > 0 ) {

		    echo '<p>Other extractions have provided the following responses:</p>';

		    echo '<table class="nbtTabledData">';

		    echo '<tr class="nbtTableHeaders">';

		    echo '<td>Previously extracted entry</td>';

		    echo '<td>Action</td>';

		    echo '</tr>';

		    foreach ( $unique_previous_entries as $prev_select ) {

			echo '<tr>';

			echo '<td>';

			echo $prev_select[0];

			echo '</td>';

			echo '<td><button onclick="nbtChoosePrevSelect(\'';

			echo $element['columnname'];

			echo "', '";

			echo $prev_select[0];

			echo '\')">Choose</button></td>';

			echo '</tr>';
			
		    }

		    echo '</table>';

		    echo '<p>If none of the above responses is accurate, you may enter your own below:</p>';

		}

		nbt_echo_text_field ($_GET['form'], $extraction, $element['columnname'], 200, FALSE);

		echo '</div>';

		break;

	}

    }

    echo "<p>Extraction status: <span id=\"nbtStatusAtBottomOfExtraction\">";

    switch ($extraction['status']) {

	case 0:

	    echo "Not yet started";

	    break;

	case 1:

	    echo "In progress";

	    break;

	case 2:

	    echo "Completed";

	    break;
	    
    }

    echo "</span>. (Change this using the selector at the top)</p>";

    ?></div>
</div>

<div style="height: 200px;">&nbsp;</div>
</div>
<input type="hidden" id="nbtExtractionInProgress" value="1">
