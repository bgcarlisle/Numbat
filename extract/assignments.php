<?php

$referencesets = nbt_get_all_ref_sets();

?>
<div class="nbtContentPanel nbtGreyGradient">
  <h2>
    <img src="<?php echo SITE_URL; ?>images/extract.png" class="nbtTitleImage">
	  Do screening
  </h2>
    <table class="nbtTabledData">
      <tr class="nbtTableHeaders">
        <td>Reference set</td>
        <td>Form</td>
        <td>Screen</td>
      </tr>
      <?php foreach ($referencesets as $refset) { ?>
        <?php $assignments = nbt_get_assignments_for_user_and_refset ( $_SESSION[INSTALL_HASH . '_nbt_userid'], $refset['id'], $_GET['sort'], $_GET['sortdirection'], "screening", TRUE ); ?>
        <?php if (count ($assignments) > 0) { ?>
          <?php foreach ($assignments as $assignment) { ?>
              <tr>
                <td><?php echo $refset['name']; ?></td>
                <td><?php echo $assignment['formname']; ?></td>
                <td><a href="<?php echo SITE_URL; ?>extract/?action=screen&form=<?php echo $assignment['formid']; ?>&refset=<?php echo $assignment['refsetid']; ?>" target="_blank">Screen</a></td>
              </tr>
          <?php } ?>
        <?php } ?>
      <?php } ?>
    </table>
</div>

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
      <option value="whenassigned"<?php if ($_GET['sort'] == "whenassigned" | $_GET['sort'] == "") echo " selected"; ?>>Assignment date</option>
      <option value="formid"<?php if ($_GET['sort'] == "formid") echo " selected"; ?>>Form</option>
      <option value="referenceid"<?php if ($_GET['sort'] == "referenceid") echo " selected"; ?>>Reference</option>
  	</select>
  	<select name="sortdirection">
      <option value="ASC"<?php if ($_GET['sortdirection'] == "ASC") echo " selected"; ?>>Ascending</option>
      <option value="DESC"<?php if ($_GET['sortdirection'] == "DESC" | $_GET['sortdirection'] == "") echo " selected"; ?>>Descending</option>
  	</select>
  	<button>Sort</button>
  </form>
  <?php

  foreach ( $referencesets as $refset ) {

	$assignments = nbt_get_assignments_for_user_and_refset ( $_SESSION[INSTALL_HASH . '_nbt_userid'], $refset['id'], $_GET['sort'], $_GET['sortdirection'], "extraction" );

	if ( count ( $assignments ) > 0 ) {

    echo '<h3>' . $refset['name'] . '</h3>';

    ?><table class="nbtTabledData">
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

	      } ?>
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

      } ?>
	    </td>
	    <td>
		      <a href="<?php echo SITE_URL; ?>extract/?action=extract&form=<?php echo $assignment['formid'] ?>&refset=<?php echo $assignment['refsetid']; ?>&ref=<?php echo $assignment['referenceid']; ?>" target="_blank">Extract</a>
	    </td>
    </tr>
	    <?php } ?>
  </table>
<?php } ?>

<?php } ?>

<div class="nbtHidden nbtFeedbackGood nbtFeedback nbtFinePrint" id="nbtPrivilegeFeedback">&nbsp;</div>

</div>
