<?php

header("Content-type: text/tsv");
header("Content-Disposition: attachment; filename=" . date("Y-m-d") . "-assignments-refset-" . $_GET['refset'] . ".tsv");

include ("../config.php");

$assignments = nbt_assignments_for_export ($_GET['refset']);

$completions = nbt_get_completions_for_assignment_export ($_GET['refset']);

echo "when_assigned\treferenceid\ttitle\tusername\tform\tstatus\n";

foreach ($assignments as $as) {

    $status = "Not yet started";

    foreach ($completions as $comp) {

	if ($comp['referenceid'] == $as['referenceid'] & $comp['userid'] == $as['userid']) {

	    if ($comp['status'] == 0) {
		$status = "Not yet started";
	    }

	    if ($comp['status'] == 1) {
		$status = "In progress";
	    }

	    if ($comp['status'] == 2) {
		$status = "Completed";
	    }
	    
	}

    }

    echo $as['whenassigned'] . "\t" . $as['referenceid'] . "\t" . $as['title'] . "\t" . $as['username'] . "\t" . $as['form'] . "\t" . $status . "\n";
    
}

?>
