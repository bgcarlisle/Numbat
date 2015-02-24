<?php

include_once ("../functions.php");

$citations = nbt_get_citations ( $nbtListCitationsCitationDB, $nbtListCitationsRefSetID, $nbtListCitationsReference, $_SESSION[INSTALL_HASH . '_nbt_userid'] );

foreach ( $citations as $citation ) {

	$ref = nbt_get_reference_for_refsetid_and_refid ( $nbtListCitationsRefSetID, $citation['citationid'] );

	?><div class="nbtGreyGradient nbtCitation nbtCitOrigRef<?php echo $nbtListCitationsCitationID; ?>-<?php echo $ref['id']; ?>" id="nbtCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>">
		<p class="nbtFinePrint" style="float: right;" id="nbtRemoveCite<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>">
			<a href="#" onclick="event.preventDefault();$(this).fadeOut(0);$('#nbtRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>').fadeIn();" id="nbtConfirmRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>">Remove citation</a>
			<span class="nbtHidden" id="nbtRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>">Are you sure? This can't be undone.<br>
				<a href="#" onclick="event.preventDefault();nbtRemoveCitation(<?php echo $nbtListCitationsCitationID; ?>, <?php echo $citation['id']; ?>);">Yes, remove</a> |
				<a href="#" onclick="event.preventDefault();$('#nbtRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>').fadeOut(0);$('#nbtConfirmRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>').fadeIn();">No, keep it</a>
			</span>
		</p>
		<span class="nbtCiteNo">#<input type="text" id="nbtCiteNo<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>" value="<?php echo $citation['cite_no']; ?>" onblur="nbtUpdateCiteNo(<?php echo $nbtListCitationsCitationID; ?>, <?php echo $citation['id']; ?>);" class="nbtCiteNo"></span>
		<h4><?php echo $ref['title']; ?>.</h4>
		<p><?php echo $ref['authors']; ?>
		<span class="nbtJournalName"><?php echo $ref['journal']; ?></span>:
		<?php echo $ref['year']; ?>.</p>

		<?php

		$citationcolumns = nbt_get_all_columns_for_citation_selector ( $nbtListCitationsCitationID );

		if ( count ( $citationcolumns ) > 0 ) {

			?><hr>
			<p>Citation properties</p><?php

			foreach ( $citationcolumns as $column ) {

				?><p><?php echo $column['displayname']; ?> <?php

				?><input type="text" value="<?php

				echo $citation[$column['dbname']];

				?>" id="nbtCitationTextField<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?><?php echo $column['dbname']; ?>" onblur="<?php

				if ( $column['caps'] == 1 ) {

					?>$(this).val($(this).val().toUpperCase());<?php

				}

				?>nbtSaveCitationTextField(<?php echo $nbtListCitationsCitationID; ?>, <?php echo $citation['id']; ?>, '<?php echo $column['dbname']; ?>', 'nbtCitationTextField<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?><?php echo $column['dbname']; ?>');" maxlength="500">
				<span class="nbtInputFeedback" id="nbtCitationTextField<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?><?php echo $column['dbname']; ?>Feedback">&nbsp;</span></p><?php

				if ( $column['remind'] == 1 ) {

					$reminders = nbtGetCitationPropertyReminders ( $nbtListCitationsCitationDB, $nbtListCitationsRefSetID, $nbtListCitationsReference, $citation['citationid'], $column['dbname'] );

					if ( count ($reminders) > 0 ) {

						?><p class="nbtFinePrint">In previous extractions, you have provided the following response(s): <?php

						foreach ( $reminders as $reminder ) {

							?><span class="nbtExtractionName" onclick="$('#nbtCitationTextField<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?><?php echo $column['dbname']; ?>').val('<?php echo $reminder[$column['dbname']]; ?>').focus().blur();"><?php echo $reminder[$column['dbname']]; ?></span><?php

						}

						?></p><?php

					}

				}

			}

		}

		?>

	</div><?php

}

?>
