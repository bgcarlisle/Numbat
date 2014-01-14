<?php

include_once ("../functions.php");

$citations = nbt_get_citations ( $nbtListCitationsCitationDB, $nbtListCitationsRefSetID, $nbtListCitationsReference, $_SESSION['nbt_userid'] );

foreach ( $citations as $citation ) {
	
	$ref = nbt_get_reference_for_refsetid_and_refid ( $nbtListCitationsRefSetID, $citation['citationid'] );
	
	?><div class="nbtGreyGradient nbtCitation nbtCitOrigRef<?php echo $nbtListCitationsCitationID; ?>-<?php echo $ref['id']; ?>" id="nbtCitation<?php echo $citation['id']; ?>">
		<p class="nbtFinePrint" style="float: right;" id="nbtRemoveCite<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>">
			<a href="#" onclick="event.preventDefault();$(this).fadeOut(0);$('#nbtRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $ref['id']; ?>').fadeIn();" id="nbtConfirmRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $ref['id']; ?>">Remove citation</a>
			<span class="nbtHidden" id="nbtRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $ref['id']; ?>">Are you sure? This can't be undone.<br>
				<a href="#" onclick="">Yes, remove</a> |
				<a href="#" onclick="event.preventDefault();$('#nbtRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $ref['id']; ?>').fadeOut(0);$('#nbtConfirmRemoveCitation<?php echo $nbtListCitationsCitationID; ?>-<?php echo $ref['id']; ?>').fadeIn();">No, keep it</a>
			</span>
		</p>
		<span class="nbtCiteNo">#<input type="text" id="nbtCiteNo<?php echo $nbtListCitationsCitationID; ?>-<?php echo $citation['id']; ?>" value="<?php echo $citation['cite_no']; ?>" onblur="nbtUpdateCiteNo(<?php echo $nbtListCitationsCitationID; ?>, <?php echo $citation['id']; ?>);" class="nbtCiteNo"><span id="nbtCiteNoFeedback<?php echo $citation['id']; ?>">&nbsp;</span></span>
		<h4><?php echo $ref['title']; ?>.</h4>
		<p><?php echo $ref['authors']; ?>
		<span class="nbtJournalName"><?php echo $ref['journal']; ?></span>:
		<?php echo $ref['year']; ?>.</p>
		
		<hr>
		
		<p>Citation properties</p>
	</div><?php
	
}

?>