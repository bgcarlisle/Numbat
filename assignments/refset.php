<?php

$forms = nbt_get_all_extraction_forms ();

$users = nbt_get_all_users ();

$refset = nbt_get_refset_for_id ($_GET['refset']);

$allassignments = nbt_get_all_assignments_for_refset ( $_GET['refset'] );

$references = nbt_get_all_references_for_refset($refset['id']);

?><div class="nbtContentPanel nbtGreyGradient">
    <h2>Assignments for: <?php echo $refset['name']; ?></h2>

    <table class="nbtTabledData">
	<tr class="nbtTableHeaders">
	    <td>Reference</td>
	    <?php

	    foreach ( $forms as $form ) {

		echo "<td>";

		echo $form['name'];

		echo "</td>";
	    }

	    ?>
	</tr>
	<?php

	foreach ( $references as $reference ) {

	    echo "<tr>";

	    echo  "<td>";
	    
	    echo "<h4>" . $reference['title'] . "</h4>";

	    echo "<p>" . $reference['authors'] . "</p>";

	    echo "<p>" . $reference['journal'] . ": " . $reference['year'] ."</p>";

	    echo "</td>";

	    foreach ( $forms as $form ) {
		
		echo "<td>";

		foreach ( $users as $user ) {

		    $assignmentfound = FALSE;

		    foreach ( $allassignments as $assign ) {

			if (
			    $assign['referenceid'] == $reference['id'] &
			    $assign['formid'] == $form['id'] &
			    $assign['username'] == $user['username']
			) {
			    echo '<span class="nbtAssignmentName nbtAssigned" id="nbtAssignment-' . $reference['id'] . '-' . $form['id'] . '-' . $user['id'] . '" onclick="nbtToggleAssignment(' . $user['id'] . ', ' . $form['id'] . ', ' . $refset['id'] . ', ' . $reference['id'] . ');">' . $user['username'] . ' <span class="nbtAssignCheck">&#x2713;</span><span class="nbtAssignCross">&#x2717;</span></span>';
			    $assignmentfound = TRUE;
			}
			
		    }

		    if ( ! $assignmentfound ) {
			echo '<span class="nbtAssignmentName nbtNotAssigned" id="nbtAssignment-' . $reference['id'] . '-' . $form['id'] . '-' . $user['id'] . '" onclick="nbtToggleAssignment(' . $user['id'] . ', ' . $form['id'] . ', ' . $refset['id'] . ', ' . $reference['id'] . ');">' . $user['username'] . ' <span class="nbtAssignCheck">&#x2713;</span><span class="nbtAssignCross">&#x2717;</span></span>';
		    }
		    
		}
		
		echo "</td>";  
	    }

	    echo "</tr>";
	}
	
	?>
    </table>
</div>

