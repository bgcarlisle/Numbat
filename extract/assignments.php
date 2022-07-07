<div class="nbtContentPanel nbtGreyGradient">
    <h2>
	<img src="<?php echo SITE_URL; ?>images/extract.png" class="nbtTitleImage">
	Do extractions
    </h2>
    <button onclick="$('.nbtCompleteAssignment').fadeToggle();">Show / hide complete</button>
    <button onclick="$('.nbtInProgressAssignment').fadeToggle();">Show / hide in progress</button>
    <button onclick="$('.nbtNotStartedAssignment').fadeToggle();">Show / hide not yet started</button>
    <p class="nbtFinePrint">Complete assignments are hidden automatically. Click the button above to show them.</p>
    <p>Sort extractions by:</p>
    <form action="<?php echo SITE_URL; ?>extract/" method="get">
	<select name="sort">
	    <option value="whenassigned"<?php if ($_GET['sort'] == "whenassigned") echo " selected"; ?>>Assignment date</option>
	    <option value="referenceid"<?php if ($_GET['sort'] == "referenceid") echo " selected"; ?>>Reference</option>
	</select>
	<button>Sort</button>
    </form>
    <?php

    $referencesets = nbt_get_all_ref_sets ();

    foreach ( $referencesets as $refset ) {

	$assignments = nbt_get_assignments_for_user_and_refset ( $_SESSION[INSTALL_HASH . '_nbt_userid'], $refset['id'], $_GET['sort'] );

	if ( count ( $assignments ) > 0 ) {

	    echo '<h3>' . $refset['name'] . '</h3>';

    ?>
	<table class="nbtTabledData">
	    <tr class="nbtTableHeaders">
		<td>When assigned</td>
		<td>Assignment</td>
		<td>Form</td>
		<td>Status</td>
		<td>Extract</td>
	    </tr>
	    <?php

	    foreach ( $assignments as $assignment ) {

		$ref = nbt_get_reference_for_refsetid_and_refid ($assignment['refsetid'], $assignment['referenceid']);

		$assignment_status = nbt_get_status_for_assignment ( $assignment );

		echo '<tr';

		switch ( $assignment_status ) {

		    case 0:

			echo ' class="nbtNotStartedAssignment"';

			break;

		    case 1:

			echo ' class="nbtInProgressAssignment"';

			break;

		    case 2:

			echo ' class="nbtCompleteAssignment"';

			break;
			
		}

		echo ' id="nbtAssignment';
		
		echo $assignment['id'];
		
		echo '">';

	    ?>
	    <td><?php echo substr ($assignment['whenassigned'], 0, 10); ?></td>
	    <td>
		<h4><?php echo $ref[$refset['title']]; ?></h4>
		<p><?php echo $ref[$refset['authors']]; ?></p>
		<?php

		if ( $ref[$refset['journal']] != "" && $ref[$refset['year']] != "" ) {

		?><p><?php echo $ref[$refset['journal']]; ?>: <?php echo $ref[$refset['year']]; ?></p><?php

												      }

												      ?>
	    </td>
	    <td><?php

		$form = nbt_get_form_for_id ( $assignment['formid'] );

		echo $form['name'];

		?></td>
	    <td>
		<?php

		switch ( $assignment_status ) {

		    case 0:

			echo 'Not yet started';

			break;

		    case 1:

			echo 'In progress';

			break;

		    case 2:

			echo 'Complete';

			break;

		}

		?>
	    </td>
	    <td>
		<a href="<?php echo SITE_URL; ?>extract/?action=extract&form=<?php echo $assignment['formid'] ?>&refset=<?php echo $assignment['refsetid']; ?>&ref=<?php echo $assignment['referenceid']; ?>" target="_blank">Extract</a>
	    </td>
	    <?php

	    echo '</tr>';

	    }

	    ?></table>
    <?php }
    
    }

    ?>

    <div class="nbtHidden nbtFeedbackGood nbtFeedback nbtFinePrint" id="nbtPrivilegeFeedback">&nbsp;</div>

</div>
