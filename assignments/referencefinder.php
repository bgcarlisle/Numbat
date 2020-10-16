<?php

include_once ("../config.php");

$suggestions = nbt_return_references_for_assignment_search ( $_POST['refset'], $_POST['query'] );

$refset = nbt_get_refset_for_id ($_POST['refset']);

$counter = 0;

foreach ( $suggestions as $suggestion ) {

	if ( $counter < 5 ) {

		?><div>
			<h4>[<?php echo $suggestion['id']; ?>] <?php echo $suggestion[$refset['title']]; ?>.</h4>
			<p><?php echo $suggestion[$refset['authors']]; ?>.
			<?php

			if ( $suggestion[$refset['journal']] != "" && $suggestion[$refset['year']] != "" ) {

				?><span class="nbtJournalName"><?php echo $suggestion[$refset['journal']]; ?></span>: <?php echo $suggestion[$refset['year']]; ?>.<?php

			}

			?></p>
			<span id="nbtAddAssignmentFeedback<?php echo $suggestion['id']; ?>"><button onclick="nbtAddAssignment(<?php echo $_POST['userid']; ?>, <?php echo $_POST['formid']; ?>, <?php echo $_POST['refset']; ?>, <?php echo $suggestion['id']; ?>);">Assign this reference to be extracted</button></span>
		</div><?php

		$counter ++;

	} else {

		?><div>
			<p>There are more than 5 results for the query you entered. You may need to refine your search.</p>
		</div><?php
	}

}

?>
