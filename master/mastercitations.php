<?php

include_once ("../functions.php");

$citations = nbt_get_master_citations ( $nbtListCitationsCitationID, $nbtListCitationsRefSetID, $nbtListCitationsReference );

foreach ( $citations as $citation ) {

	$ref = nbt_get_reference_for_refsetid_and_refid ( $nbtListCitationsRefSetID, $citation['citationid'] );

	?><div class="nbtGreyGradient nbtCitation nbtCitOrigRef<?php echo $nbtListCitationsCitationID; ?>-<?php echo $ref['id']; ?>" id="nbtCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>">
		<p class="nbtFinePrint" style="float: right;" id="nbtRemoveCite<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>">
			<a href="#" onclick="event.preventDefault();$(this).fadeOut(0);$('#nbtRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>').fadeIn();" id="nbtConfirmRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>">Remove citation</a>
			<span class="nbtHidden" id="nbtRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>">Are you sure? This can't be undone.<br>
				<a href="#" onclick="event.preventDefault();nbtRemoveMasterCitation(<?php echo $nbtListCitationsCitationID; ?>, <?php echo $citation['id']; ?>, <?php echo $citation['citationid']; ?>);">Yes, remove</a> |
				<a href="#" onclick="event.preventDefault();$('#nbtRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>').fadeOut(0);$('#nbtConfirmRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>').fadeIn();">No, keep it</a>
			</span>
		</p>

		<h4><?php

		if ( $citation['cite_no'] != "" ) {

			?><span class="nbtCiteNo">#<?php echo $citation['cite_no']; ?></span><?php
		}

		?><?php echo $ref['title']; ?>.</h4>
		<p><?php echo $ref['authors']; ?>
		<span class="nbtJournalName"><?php echo $ref['journal']; ?></span>:
		<?php echo $ref['year']; ?>.</p>

		<?php

		$citationcolumns = nbt_get_all_columns_for_citation_selector ( $nbtListCitationsCitationID );

		if ( count ( $citationcolumns ) > 0 ) {

			?><hr>
			<p>Citation properties</p><?php

			foreach ( $citationcolumns as $column ) {

				?><p><?php echo $column['displayname']; ?><span class="nbtFeedback"><?php

				if ( $citation[$column['dbname']] != "" ) {

					echo $citation[$column['dbname']];

				} else {

					echo "[Left blank]";

				}

				?></span></p><?php

			}

		}

		?>

	</div><?php

}

?>
