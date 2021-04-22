<?php

header("Content-type: text/tsv");
header("Content-Disposition: attachment; filename=" . date("Y-m-d") . "-assignments-refset-" . $_GET['refset'] . ".tsv");

include ("../config.php");

$assignments = nbt_assignments_for_export ($_GET['refset']);

echo "referenceid\ttitle\tusername\tform\n";

foreach ($assignments as $as) {

    echo $as['referenceid'] . "\t" . $as['title'] . "\t" . $as['username'] . "\t" . $as['form'] . "\n";
   
}

?>
