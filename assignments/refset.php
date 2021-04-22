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

$columns = nbt_get_columns_for_refset ( $refset['id'] );

// Get extractions for each form
$finished_extractions = [];
foreach ($forms as $form) {
    array_push(
	$finished_extractions,
	nbt_get_all_extractions_for_refset_and_form ($refset['id'], $form['id'])
    );
}

?><div class="nbtContentPanel nbtGreyGradient">
    <h2>Assignments for: <?php echo $refset['name']; ?> (<?php echo count($references); ?> references)</h2>

    <input type="hidden" id="nbtRefSetID" value="<?php echo $refset['id']; ?>">
    <input type="hidden" id="nbtAllFormIDs" value="<?php echo $allforms; ?>">
    <input type="hidden" id="nbtAllUserIDs" value="<?php echo $allusers; ?>">

    <p>Select reference(s):</p>

    <button onclick="$('input.nbtAssignSelect').prop('checked', true);">All</button>
    <button onclick="$('input.nbtAssignSelect').prop('checked', false);">None</button>
    <button onclick="$('input.nbtAssignSelect').click();">Invert</button>

    <p>Select
	<input type="text" id="nbtRandomK" value="<?php echo floor(count($references) / 2); ?>" style="width: 75px;">
	random reference(s)
	<button onclick="nbtAssignerSelectKRandom(<?php echo $_GET['refset']; ?>, '');">Select</button>
    </p>
    
    <hr>

    <p>Select
	<input type="text" id="nbtRandomKAssigned" value="<?php echo floor(count($references) / 2); ?>" style="width: 75px;">
	random reference(s) from those with
	<select id="nbtRandomCompAssigned">
	    <option value="exactly">exactly</option>
	    <option value="fewerthan">fewer than</option>
	    <option value="morethan">more than</option>
	</select>
	<input type="text" id="nbtRandomNAssigned" value="2" style="width: 75px;">
	extractor(s) assigned form:
	<select id="nbtRandomFormAssigned">
	    <option value="ns">Choose a form</option>
	    <?php

	    foreach ($forms as $form) {

	    ?><option value="<?php echo $form['id']; ?>"><?php echo $form['name']; ?></option><?php
		
	    }
	    
	    ?>
	</select>
	<button onclick="nbtAssignerSelectKRandom(<?php echo $_GET['refset']; ?>, 'Assigned');">Select</button>
    </p>
    <hr>
    <p>Select
	<input type="text" id="nbtRandomKExtracted" value="<?php echo floor(count($references) / 2); ?>" style="width: 75px;">
	random reference(s) from those with
	<select id="nbtRandomCompExtracted">
	    <option value="exactly">exactly</option>
	    <option value="fewerthan">fewer than</option>
	    <option value="morethan">more than</option>
	</select>
	<input type="text" id="nbtRandomNExtracted" value="2" style="width: 75px;">
	extraction(s) completed with form:
	<select id="nbtRandomFormExtracted">
	    <option value="ns">Choose a form</option>
	    <?php

	    foreach ($forms as $form) {

	    ?><option value="<?php echo $form['id']; ?>"><?php echo $form['name']; ?></option><?php
		
	    }
	    
	    ?>
	</select>
	<button onclick="nbtAssignerSelectKRandom(<?php echo $_GET['refset']; ?>, 'Extracted');">Select</button>
    </p>
    <hr>
    <p>Select
	<input type="text" id="nbtRandomKByUserK" value="<?php echo floor(count($references) / 2); ?>" style="width: 75px;">
	random reference(s) from those that have the form
	<select id="nbtRandomKByUserForm">
	    <option value="ns">Choose a form</option>
	    <?php

	    foreach ($forms as $form) {

	    ?><option value="<?php echo $form['id']; ?>"><?php echo $form['name']; ?></option><?php
		
	    }
	    
	    ?>
	</select>
	<select id="nbtRandomKByUserYN">
	    <option value="alreadyassigned">already assigned to</option>
	    <option value="notalreadyassigned">not already assigned to</option>
	</select>
	<select id="nbtRandomKByUserUser">
	    <?php

	    foreach ($users as $user) {

		echo '<option value="' . $user['id'] . '">' . $user['username'] . "</option>";
		
	    }

	    ?>
	</select>
	<button onclick="nbtAssignerSelectKRandomByUser(<?php echo $_GET['refset']; ?>);">Select</button>
    </p>
    <hr>
    <p>Select
	<input type="text" id="nbtRandomKByUserAndUsersK" value="<?php echo floor(count($references) / 2); ?>" style="width: 75px;">
	random reference(s) from those that have the form
	<select id="nbtRandomKByUserAndUsersForm">
	    <option value="ns">Choose a form</option>
	    <?php

	    foreach ($forms as $form) {

	    ?><option value="<?php echo $form['id']; ?>"><?php echo $form['name']; ?></option>
	    <?php
											      
	    }
	    
	    ?>
	</select>
	<select id="nbtRandomKByUserAndUsersFormYN">
	    <option value="alreadyassigned">already assigned to</option>
	    <option value="notalreadyassigned">not already assigned to</option>
	</select>
	<select id="nbtRandomKByUserAndUsersUser">
	    <?php

	    foreach ($users as $user) {

		echo '<option value="' . $user['id'] . '">' . $user['username'] . "</option>";
		
	    }

	    ?>
	</select>
	and
	<select id="nbtRandomKByUserAndUsersComp">
	    <option value="exactly">exactly</option>
	    <option value="fewerthan">fewer than</option>
	    <option value="morethan">more than</option>
	</select>
	<input type="text" id="nbtRandomKByUserAndUsersUserN" value="1" style="width: 75px;">
	other extractor(s) assigned
	<button onclick="nbtAssignerSelectKRandomByUserAndUsers(<?php echo $_GET['refset']; ?>);">Select</button>
    </p>
    <hr>
    <!-- <p>Select
	 <input type="text" id="nbtRandomKFinal" value="<?php echo floor(count($references) / 2); ?>" style="width: 75px;">
	 random reference(s) from those with
	 <select id="nbtRandomCompFinal">
	 <option value="exactly">exactly</option>
	 <option value="fewerthan">fewer than</option>
	 <option value="morethan">more than</option>
	 </select>
	 <input type="text" id="nbtRandomNFinal" value="2" style="width: 75px;">
	 extraction(s) reconciled with form:
	 <select id="nbtRandomFormFinal">
	 <option value="ns">Choose a form</option>
	 <?php

	 foreach ($forms as $form) {

	 ?><option value="<?php echo $form['id']; ?>"><?php echo $form['name']; ?></option><?php
											   
											   }
											   
											   ?>
	 </select>
	 <button onclick="nbtAssignerSelectKRandom(<?php echo $_GET['refset']; ?>, 'Final');">Select</button>
	 </p>
	 <hr> -->

    <p>Select all references where 

	<select id="nbtRefsetColumnSelect" onchange="nbtAssignerChooseColumn(<?php echo $_GET['refset']; ?>);">
	    <option>Choose a column</option>
	    <?php

	    foreach ($columns as $col) {

		if ( $col[0] != "id" && $col[0] != "manual" ) {

		    echo '<option value="' . $col[0] . '">' . $col[0] . '</option>';
		    
		}
		
	    }
	    
	    ?>
	</select>

	is

	<select id="nbtRefsetColumnSelectValues">
	    <option value="na">No column chosen</option>
	</select>

	<button onclick="nbtAssignerSelectByColumn(<?php echo $refset['id']; ?>);">Select</button>

    </p>

    <hr>

    <p>To check that the above selections have been made correctly, you can hide the non-selected references.</p>

    <button onclick="$('input:not(input:checked)').parent().parent('tr').slideUp();">Hide non-selected references</button>
    <button onclick="$('tr').slideDown();">Show all references</button>

    <hr>

    <p>With the selected references</p>
    
    <p>For the following form:</p>

    <select id="nbtAssignFormChooser">
	<?php

	if ( count ($forms) > 1 ) {
	    echo '<option value="ns">Choose a form</option>';
	}

	foreach ( $forms as $form ) {

	    if ( count ($forms) == 1 ) {
		echo '<option value="' . $form['id'] . '" selected>' . $form['name'] . '</option>';
	    } else {
		echo '<option value="' . $form['id'] . '">' . $form['name'] . '</option>';
	    }

	    
	}

	if ( count ($forms) > 1 ) {
	    echo '<option value="all">[All forms]</select>';
	}

	?>
    </select>

    <p>For the following user:</p>

    <select id="nbtAssignUserChooser">
	<?php

	if ( count ($users) > 1 ) {
	    echo '<option value="ns">Choose a user</option>';
	}

	foreach ( $users as $user ) {

	    if ( count ($users) == 1 ) {
		echo '<option value="' . $user['id'] . '" selected>' . $user['username'] . '</option>';
	    } else {
		echo '<option value="' . $user['id'] . '">' . $user['username'] . '</option>';
	    }

	    
	}

	if ( count ($users) > 1 ) {
	    echo '<option value="all">[All users]</select>';
	}

	?>
    </select>

    <hr>

    <p>Perform the following action:</p>

    <button onclick="nbtAssign();">Assign to user</button>
    <button onclick="nbtRemoveAssign();">Remove assignments</button>

    <hr>

    <a href="<?php echo SITE_URL; ?>assignments/save-assignments.php?refset=<?php echo $refset['id']; ?>" style="float: right;">Download assignments</a>

    <table class="nbtTabledData" style="margin-top: 20px;">
	<tr class="nbtTableHeaders">
	    <td colspan="2">Reference</td>
	    <?php

	    foreach ( $forms as $form ) {

		echo "<td>";

		echo $form['name'];

		echo '<span id="nbtAssignmentsNotCompletedCountForForm-' . $form['id'] . '" onclick="nbtUpdateCompletedAssignmentsCount(' . $form['id'] . ', ' . $refset['id'] . ');" style="display: block; float: right;">';

		echo '</span>';

		echo "</td>";
	    }

	    ?>
	</tr>
	<?php

	foreach ( $references as $reference ) {

	    echo "<tr>";

	    echo  "<td>";

	    echo '<input type="checkbox" id="nbtAssignSelectRefID' . $reference['id'] . '" class="nbtAssignSelect" value="' . $reference['id'] . '" style="margin: 8px 12px 8px 2px;">';

	    echo "</td><td>";
	    
	    echo "<h4>" . $reference[$refset['title']] . "</h4>";

	    echo "<p>" . $reference[$refset['authors']] . "</p>";

	    echo "<p>" . $reference[$refset['journal']] . ": " . $reference[$refset['year']] ."</p>";

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
			    echo '<span class="nbtAssignmentName nbtAssigned nbtAssignmentNameForForm' . $form['id'] . '" id="nbtAssignment-' . $reference['id'] . '-' . $form['id'] . '-' . $user['id'] . '" onclick="nbtToggleAssignment(' . $user['id'] . ', ' . $form['id'] . ', ' . $refset['id'] . ', ' . $reference['id'] . ');">' . $user['username'] . '&nbsp;<span class="nbtAssignCheck">&#x2713;</span><span class="nbtAssignCross">&#x2717;</span></span> ';
			    $assignmentfound = TRUE;
			}
			
		    }

		    if ( ! $assignmentfound ) {
			echo '<span class="nbtAssignmentName nbtNotAssigned nbtAssignmentNameForForm' . $form['id'] . '" id="nbtAssignment-' . $reference['id'] . '-' . $form['id'] . '-' . $user['id'] . '" onclick="nbtToggleAssignment(' . $user['id'] . ', ' . $form['id'] . ', ' . $refset['id'] . ', ' . $reference['id'] . ');">' . $user['username'] . '&nbsp;<span class="nbtAssignCheck">&#x2713;</span><span class="nbtAssignCross">&#x2717;</span></span> ';
		    }
		    
		}

		// The number of finished extractions

		$n_finished = 0;

		foreach ($finished_extractions as $fes) {
		    foreach ($fes as $fe) {
			if ($fe['formid'] == $form['id'] && $fe['referenceid'] == $reference['id']) {
			    $n_finished++;
			}
		    }
		}
		
		if ($n_finished > 0) {
		    echo "<p>" . $n_finished . " finished extraction(s)</p>";
		}
		
		echo "</td>";  
	    }

	    echo "</tr>";
	}
	
	?>
    </table>
</div>
<script>
 $(document).ready ( function () {

     <?php
     foreach ($forms as $form) {
	 ?>
	 nbtUpdateCompletedAssignmentsCount (<?php echo $form['id']; ?>, <?php echo $refset['id']; ?>);
	 <?php
     }
     ?>

 });
</script>
