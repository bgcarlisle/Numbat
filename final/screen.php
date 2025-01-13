<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );
$refset = nbt_get_refset_for_id ( $_GET['refset'] );
$form = nbt_get_form_for_id ( $_GET['form'] );

if (! isset($_GET['screeningpage'])) {
    $_GET['screeningpage'] = 1;
}

$assignments = nbt_get_unique_references_assigned_by_refset_and_form_paginated (
    $_GET['refset'],
    $_GET['form'],
    $_GET['screeningpage']
);

$refs = nbt_get_all_references_for_refset ($_GET['refset']);
$extractions = nbt_get_all_extractions_for_refset_and_form ($_GET['refset'], $_GET['form'], 2);
$count_of_assignments = nbt_count_unique_references_assigned_by_refset_and_form ($_GET['refset'], $_GET['form']);

foreach ($formelements as $element) {
    if ($element['columnname'] == "exclusion_reason") {
	$exclusion_reasons = nbt_get_all_select_options_for_element($element['id']);
    }
}

?>
<div class="nbtNonsidebar">
  <div class="nbtContentPanel">
      <h2>Reconcile screened reference set: <?php echo $refset['name']; ?> (<?php echo $count_of_assignments; ?> assigned)</h2>

	  <div>
	      Page:
	      <?php $i = 1; ?>
	      <?php while ($i <= ceil($count_of_assignments / 100)) { ?>
		  <?php if ($i != $_GET['screeningpage']) { ?>
		      <span style="margin: 0 10px; padding: 2px 4px;"><a href="<?php echo SITE_URL; ?>final/?action=reconcilescreened&form=<?php echo $_GET['form']; ?>&refset=<?php echo $_GET['refset']; ?>&screeningpage=<?php echo $i; ?>"><?php echo $i; ?></a></span>
		  <?php } else { ?>
		      <span style="margin: 0 10px; padding: 2px 4px; border: 1px solid #333;"><?php echo $i; ?></span>
		  <?php } ?>
		  <?php $i++; ?>
	      <?php } ?>
	  </div>
	  <p>Form: <?php echo $form['name']; ?></p>

	  <table class="nbtTabledData" id="nbtScreeningGrid" data-formid="<?php echo $form['id']; ?>" data-refsetid="<?php echo $refset['id']; ?>">
	      <tr class="nbtTableHeaders">
		  <td>Reference</td>
		  <td>Include</td>
		  <?php foreach ($exclusion_reasons as $er) { ?>
		      <td><?php echo $er['displayname']; ?></td>
		  <?php } ?>
		  <td>Notes</td>
	      </tr>
	      <?php foreach ($assignments as $assignment) { ?>
		  <?php

		  $row_include = NULL;
		  $row_exclude = NULL;
		  $row_notes = NULL;

		  // Pulling out the extractions
		  foreach ($extractions as $ext) {
		      if ($assignment['referenceid'] == $ext['referenceid']) {
			  if ($ext['extractor_notes'] != "") {
			      $row_notes[$ext['username']] = $ext['extractor_notes'];
			  }
			  if ($ext['include'] == 1) {
			      $row_include .= "<span class=\"nbtExtractionName\">" . $ext['username'] . "</span>";
			  } else {
			      $row_exclude[$ext['username']] = $ext['exclusion_reason'];
			  }
		      }
		  }
		  
		  ?>
		  <tr class="nbtFocusableScreeningRow">
		      <td>
			  <h3><?php echo $refs[array_search($assignment['referenceid'], array_column($refs, "id"))][$refset['title']]; ?></h3>
			  <p>
			      <?php echo $refs[array_search($assignment['referenceid'], array_column($refs, "id"))][$refset['authors']]; ?>
			      <?php echo $refs[array_search($assignment['referenceid'], array_column($refs, "id"))][$refset['journal']]; ?>
			      <?php echo $refs[array_search($assignment['referenceid'], array_column($refs, "id"))][$refset['year']]; ?>
			  </p>
			  <p><?php echo str_replace("\n", "<br>", $refs[array_search($assignment['referenceid'], array_column($refs, "id"))][$refset['abstract']]); ?></p>
			  <div>
			      <?php
			       
			       foreach ($formelements as $ele) {
				   if ($ele['type'] == "reference_data") {
				       $element = $ele;
				       
				   }
			       }
			       
			       $refdata = $element['columnname'];
			       
			       preg_match_all(
				   '/\$([A-Za-z0-9_-]+)/',
				   $element['columnname'],
				   $cols_to_replace
			       );

			       foreach ( $cols_to_replace[0] as $col_to_replace ) {
				   $refdata = preg_replace (
				       "/\\" . $col_to_replace . "\b/",
				       $refs[array_search($assignment['referenceid'], array_column($refs, "id"))][substr($col_to_replace, 1)],
				       // $ref[substr($col_to_replace, 1)],
				       $refdata
				   );
			       }

			       $refdata = str_replace("\n", "<br>", $refdata);

			       echo $refdata;

			       ?>
			  </div>
		      </td>
		      <td>
			  <?php echo $row_include; ?>
		      </td>
		      <?php foreach ($exclusion_reasons as $er) { ?>
			  <td>
			      <?php

			      foreach ($row_exclude as $exclude_user => $re) {
				  if ($re == $er['dbname']) {
				      echo "<span class=\"nbtExtractionName\">" . $exclude_user . "</span>";
				  }
			      }
			      
			      ?>
			  </td>
		      <?php } ?>
		      <td>
			  <?php

			  if (! is_null($row_notes)) {
			      foreach ($row_notes as $notes_user => $rn) {
				  echo "<p>" . $notes_user . ": " . $rn . "</p>";
			      }
			  }

			  ?>
		      </td>
		  </tr>
	      <?php } ?>
