<?php

$forms = nbt_get_all_extraction_forms ();
$allforms = [];
foreach ($forms as $form) {
    array_push($allforms, $form['id']);
}
$allforms = implode(",", $allforms);

$users = nbt_get_all_users ();
$allusers = [];
foreach ($users as $user) {
    array_push($allusers, $user['id']);
}
$allusers = implode(",", $allusers);

$refset = nbt_get_refset_for_id ($_GET['refset']);

$allassignments = nbt_get_all_assignments_for_refset ( $_GET['refset'] );

$references = nbt_get_all_references_for_refset($refset['id']);

?><div class="nbtContentPanel nbtGreyGradient">
    <h2>Assignments for: <?php echo $refset['name']; ?> (<?php echo count($references); ?> references)</h2>

    <input type="hidden" id="nbtRefSetID" value="<?php echo $refset['id']; ?>">
    <input type="hidden" id="nbtAllFormIDs" value="<?php echo $allforms; ?>">
    <input type="hidden" id="nbtAllUserIDs" value="<?php echo $allusers; ?>">

    <p>Select reference(s):</p>

    <button onclick="$('input.nbtAssignSelect').prop('checked', true);">All</button>
    <button onclick="$('input.nbtAssignSelect').prop('checked', false);">None</button>
    <button onclick="$('input.nbtAssignSelect').click();">Invert</button>

    <span>Select k random references:</span>
    <input type="text" id="nbtRandomK" value="<?php echo floor(count($references) / 2); ?>">
    <button onclick="$('input.nbtAssignSelect').prop('checked', false);$('input.nbtAssignSelect').sort(function(){return (Math.round(Math.random())-0.5);}).slice(0,$('#nbtRandomK').val()).prop('checked', true);">Select</button>

    <p>For the following form:</p>

    <select id="nbtAssignFormChooser">
	<option value="ns">Choose a form</option>
	<?php

	foreach ( $forms as $form ) {

	    if ( count ($forms) == 1 ) {
		echo '<option value="' . $form['id'] . '" selected>' . $form['name'] . '</option>';
	    } else {
		echo '<option value="' . $form['id'] . '">' . $form['name'] . '</option>';
	    }

	    
	}

	?>
	<option value="all">[All forms]</select>
    </select>

    <p>For the following user:</p>

    <select id="nbtAssignUserChooser">
	<option value="ns">Choose a user</option>
	<?php

	foreach ( $users as $user ) {

	    if ( count ($users) == 1 ) {
		echo '<option value="' . $user['id'] . '" selected>' . $user['username'] . '</option>';
	    } else {
		echo '<option value="' . $user['id'] . '">' . $user['username'] . '</option>';
	    }

	    
	}

	?>
	<option value="all">[All users]</select>
    </select>

    <p>Perform the following action:</p>

    <button onclick="nbtAssign();">Assign to user</button>
    <button onclick="nbtRemoveAssign();">Remove assignments</button>

    <table class="nbtTabledData" style="margin-top: 20px;">
	<tr class="nbtTableHeaders">
	    <td colspan="2">Reference</td>
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

	    echo '<input type="checkbox" class="nbtAssignSelect" value="' . $reference['id'] . '" style="margin: 8px 12px 8px 2px;">';

	    echo "</td><td>";
	    
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

