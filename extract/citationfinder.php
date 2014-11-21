<?php

include_once ("../config.php");

$suggestions = nbt_return_references_for_refset_and_query ( $_POST['citationsid'], $_POST['refset'], $_POST['reference'], $_POST['query'] );

$counter = 0;

foreach ( $suggestions as $suggestion ) {

	if ( $counter < 5 ) {

		?><div>
			<h4><?php echo $suggestion['title']; ?>.</h4>
			<p><?php echo $suggestion['authors']; ?>. <span class="nbtJournalName"><?php echo $suggestion['journal']; ?></span>: <?php echo $suggestion['year']; ?>.</p>
			<button onclick="nbtAddCitation(<?php echo $_POST['citationsid']; ?>, '<?php echo $_POST['citationsuffix']; ?>', <?php echo $_POST['refset']; ?>, <?php echo $_POST['reference']; ?>, <?php echo $suggestion['id']; ?>, <?php echo $_SESSION[INSTALL_HASH . '_nbt_userid']; ?>);">Add citation</button>
		</div><?php

		$counter ++;

	} else {

		?><div>
			<p>There are more than 5 results for the query you entered. You may need to refine your search.</p>
		</div><?php
	}

}

?>
