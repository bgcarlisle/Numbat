<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );

?><button onclick="$('.nbtSidebar').fadeIn(200);$(this).fadeOut(0);" id="nbtUnhideSidebar" style="display: none; position: fixed; left: 20px; top: 100px;">Unhide notes</button>
<div class="nbtSidebar">
	<h3>Extraction notes</h3>
	<p class="nbtFinePrint">These notes are for your own reference. These will not be included in the final report, and they are not visible to other extractors. <a href="#" onclick="event.preventDefault();$(this).parent().parent().fadeOut(200);$('button#nbtUnhideSidebar').fadeIn(200);">[Hide]</a></p>
	<textarea id="nbtExtractionNotes" onblur="nbtSaveTextField(<?php echo $_GET['form']; ?>, <?php echo $extraction['id']; ?>, 'notes', 'nbtExtractionNotes');"><?php

	echo $extraction['notes'];

	?></textarea>

	<h3>Manual citations</h3>
	<button onclick="nbtAddNewReferenceToRefSet(<?php echo $extraction['refsetid']; ?>);">Add a new reference</button>
	<button onclick="window.open('<?php echo SITE_URL . "references/manual/?refset=" . $extraction['refsetid']; ?>');">View manually added references</button>
</div>
<div class="nbtCoverup" id="nbtManualRefsCoverup">&nbsp;</div>
<div id="nbtManualRefs" class="nbtInlineManualNewRef">&nbsp;</div>
<div class="nbtNonsidebar">
	<div class="nbtContentPanel">
		<h2><?php echo $ref['title']; ?></h2>
		<p><?php echo $ref['authors']; ?></p>
		<?php

		if (( $ref['journal'] != "") && ($ref['year'] != "")) {

			?><p><span class="nbtJournalName"><?php echo $ref['journal']; ?></span>: <?php echo $ref['year']; ?></p><?php

		}

		$files = scandir ( ABS_PATH . "attach/files/" . $_GET['refset'] . "/");

		foreach ( $files as $file ) {

			if ( substr ($file, 0, 1) != "." ) {

				$file_ref = explode(".", $file);

				if ( $file_ref[0] == $_GET['ref']) {

					?><span class="nbtAttachment"><a href="<?php echo SITE_URL; ?>attach/files/<?php echo $_GET['refset']; ?>/<?php echo $file; ?>">Attached <?php echo $file_ref[1] ?></a></span><?php

				}

			}

		}

		if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {

			?><button onclick="window.open('<?php echo SITE_URL; ?>attach/?action=new&refset=<?php echo $_GET['refset']; ?>&ref=<?php echo $_GET['ref']; ?>','_self');">Attach a file to this reference</button><?php
			
		}

		?>
	</div>
	<div class="nbtContentPanel">
		<h3>Abstract</h3>
		<p class="nbtFinePrint"><a href="#" onclick="event.preventDefault();$('#nbtAbstract').slideToggle(200);">Show / hide abstract</a></p>
		<p id="nbtAbstract"><?php

		if ( $ref['abstract'] != NULL) {

			echo $ref['abstract'];

		} else {

			echo "[No abstract]";

		}

		?></p>
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

		?><div class="nbtContentPanel"><?php

	}

		foreach ( $formelements as $element ) {

			switch ( $element['type'] ) {

				case "section_heading":

					if ( $element['id'] != $formelements[0]['id'] ) {

						?></div><?php

					}

					?><div class="nbtContentPanel<?php

					if ( $element['toggle'] != "" ) {

						?> nbtHidden <?php echo $element['toggle']; ?><?php

					}

					?>">
						<h3><?php echo $element['displayname']; ?><?php

						if ( $element['codebook'] != "" ) {

							$element['codebook'] = str_replace ("\n", "<br>", $element['codebook']);

							?> <a href="#" onclick="event.preventDefault();$(this).parent().next('.nbtCodebook').slideToggle(100);">(?)</a></h3>
							<div class="nbtCodebook"><?php echo $element['codebook']; ?></div><?php

						} else {

							?></h3><?php

						}

				break;

				case "open_text":

					?><div<?php

					if ( $element['toggle'] != "" ) {

						?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php

					}

					?>><?php

						nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

						nbt_echo_text_field ($_GET['form'], $extraction, $element['columnname'], 200, FALSE);

					?></div><?php

				break;

				case "text_area":

					?><div<?php

					if ( $element['toggle'] != "" ) {

						?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php

					}

					?>><?php

						nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

						nbt_echo_text_area_field ($_GET['form'], $extraction, $element['columnname'], 5000, FALSE);

					?></div><?php

				break;

				case "date_selector":

					?><div<?php

					if ( $element['toggle'] != "" ) {

						?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php

					}

					?>><?php

						nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

						nbt_echo_date_selector ($_GET['form'], $extraction, $element['columnname']);

					?></div><?php

				break;

				case "single_select":

					?><div<?php

					if ( $element['toggle'] != "" ) {

						?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php

					}

					?>><?php

						nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

						$answers = array ();
						$toggles = array ();

						$selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

						foreach ( $selectoptions as $option ) {

							$answers[$option['dbname']] = $option['displayname'];
							$toggles[$option['dbname']] = $option['toggle'];

						}

						nbt_echo_single_select ( $_GET['form'], $extraction, $element['columnname'], $answers, $toggles );

					?></div><?php

				break;

				case "multi_select":

					?><div<?php

					if ( $element['toggle'] != "" ) {

						?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php

					}

					?>><?php

						nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

						$answers = array ();
						$toggles = array ();

						$selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );

						foreach ( $selectoptions as $option ) {

							$answers[$option['dbname']] = $option['displayname'];
							$toggles[$option['dbname']] = $option['toggle'];

						}

						nbt_echo_multi_select ($_GET['form'], $extraction, $element['columnname'], $answers, $toggles );

					?></div><?php

				break;

				case "country_selector":

					?><div<?php

					if ( $element['toggle'] != "" ) {

						?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php

					}

					?>><?php

						nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

						$countries = nbt_return_country_array (); ?>

						<select id="nbtCountrySelect<?php echo $element['columnname']; ?>" onblur="nbtSaveTextField(<?php echo $_GET['form']; ?>, <?php echo $extraction['id']; ?>, '<?php echo $element['columnname']; ?>', 'nbtCountrySelect<?php echo $element['columnname']; ?>', 'sigCountrySelect<?php echo $element['columnname']; ?>Feedback');">
							<?php

							foreach ( $countries as $country ) {

								?><option value="<?php echo $country; ?>"<?php

									if ( $extraction[$element['columnname']] == $country ) {

										?> selected<?php

									}

								?>><?php echo $country; ?></option><?php

							}

							?>
						</select>
						<span class="nbtInputFeedback" id="sigCountrySelect<?php echo $element['columnname']; ?>Feedback">&nbsp;</span>
					</div><?php

				break;

				case "table_data":

					?><div<?php

					if ( $element['toggle'] != "" ) {

						?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php

					}

					?>><?php

						nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

						?><div id="nbtTableExtraction<?php echo $element['id']; ?>"><?php

						$nbtExtractTableDataID = $element['id'];
						$nbtExtractRefSet = $_GET['refset'];
						$nbtExtractRefID = $_GET['ref'];

						include ('./tabledata.php');

						?></div>

					</div><?php

				break;

				case "citations":

					?><div<?php

					if ( $element['toggle'] != "" ) {

						?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php

					}

					?>><?php

						nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

						?><p>
							Add a new citation:
							<a href="#" onclick="event.preventDefault();$('#nbtCitationSuggestions<?php echo $element['id']; ?>').html('&nbsp;');$('#nbtCitationFinder<?php echo $element['id']; ?>').val('');">(Clear field)</a>
							<span class="nbtDoubleCitationFeedback nbtHidden" id="nbtDoubleCitationFeedback<?php echo $element['id']; ?>">You have already cited this reference here!</span>
						</p>
						<input type="text" class="nbtCitationFinder" id="nbtCitationFinder<?php echo $element['id']; ?>" onkeyup="nbtFindCitation(<?php echo $element['id']; ?>, '<?php echo $element['columnname']; ?>', 'nbtCitationSuggestions<?php echo $element['id']; ?>', <?php echo $element['id']; ?>, <?php echo $_GET['refset']; ?>, <?php echo $_GET['ref']; ?>);">
						<div class="nbtCitationSuggestions" id="nbtCitationSuggestions<?php echo $element['id']; ?>">&nbsp;</div>
						<div class="nbtCitationList" id="nbtCitationList<?php echo $element['id']; ?>"><?php

							$nbtListCitationsCitationID = $element['id'];
							$nbtListCitationsCitationDB = $element['columnname'];
							$nbtListCitationsRefSetID = $_GET['refset'];
							$nbtListCitationsReference = $_GET['ref'];

							include ("./listcitations.php");

						?></div>

					</div><?php

				break;

				case "sub_extraction":

					?><div<?php

					if ( $element['toggle'] != "" ) {

						?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php

					}

					?>><?php

						nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

						?><div class="nbtSubExtraction" id="nbtSubExtraction<?php echo $element['id']; ?>-<?php echo $_SESSION['nbt_userid'] ?>"><?php

						$nbtSubExtractionElementID = $element['id'];
						$nbtExtractRefSet = $_GET['refset'];
						$nbtExtractRefID = $_GET['ref'];

						include ('./subextraction.php');

						?></div>

					</div><?php

				break;

				case "assignment_editor":

					?><div<?php

					if ( $element['toggle'] != "" ) {

						?> class="nbtHidden <?php echo $element['toggle']; ?>"<?php

					}

					?>><?php

						nbt_echo_display_name_and_codebook ( $element['displayname'], $element['codebook'] );

						?><select id="nbtAssignUser">
							<option value="NULL">Choose a user to assign</option>
							<?php

							$users = nbt_get_all_users ();

							foreach ( $users as $user ) {

								?><option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option><?php

							}

							?>
						</select>
						<select id="nbtAssignForm">
							<option value="NULL">Choose a form to use</option>
							<?php

							$forms = nbt_get_all_extraction_forms ();

							foreach ( $forms as $form ) {

								?><option value="<?php echo $form['id']; ?>"><?php echo $form['name']; ?></option><?php

							}

							?>
						</select>
						<button onclick="nbtAddAssignmentInExtraction( <?php echo $_GET['refset']; ?>, <?php echo $_GET['ref']; ?>, <?php echo $element['id']; ?> );">Assign this reference</button>
						<p class="nbtFinePrint nbtHidden" id="nbtAddAssignmentFeedback<?php echo $element['id']; ?>">&nbsp;</p><?php

					?></div><?php

				break;

			}

		}

		?></div>
	</div>

	<div style="height: 200px;">&nbsp;</div>
</div>
<script type="text/javascript">

	nbtUpdateConditionalDisplays ();

	nbtCheckLogin();

</script>
