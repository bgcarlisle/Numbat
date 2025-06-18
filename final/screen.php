<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );
$refset = nbt_get_refset_for_id ( $_GET['refset'] );
$form = nbt_get_form_for_id ( $_GET['form'] );
$final = nbt_get_all_final_for_refset_and_form ( $_GET['refset'], $_GET['form'] );

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
	  <p>Keyboard shortcuts: j (next row); k (previous row); 1 (include); 2-9 (exclusion reasons); m (toggle references with less than 1 screening complete) u (show unanimous rows only); h (hide completed); a (show all)</p>
	  <table class="nbtTabledData" id="nbtScreeningReconcileGrid" data-formid="<?php echo $form['id']; ?>" data-refsetid="<?php echo $refset['id']; ?>">
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

		  $row_done = "";
		  $row_final = NULL;
		  foreach ($final as $fi) {
		      if ($fi['referenceid'] == $assignment['referenceid']) {
			  $row_done = " nbtScreeningReconcileDone";
			  $row_final = $fi;
		      }
		  }

		  $row_include = NULL;
		  $row_exclude = NULL;
		  $row_notes = NULL;

		  // Pulling out the extraction data
		  $row_extraction_count = 0;
		  $row_include_count = 0;
		  $row_exclusion_reasons = Array();
		  foreach ($extractions as $ext) {
		      if ($assignment['referenceid'] == $ext['referenceid']) {
			  $row_extraction_count++;
			  if ($ext['extractor_notes'] != "") {
			      $row_notes[$ext['username']] = $ext['extractor_notes'];
			  }
			  if ($ext['include'] == 1) {
			      $row_include_count++;
			      $row_include .= "<span class=\"nbtExtractionName\">" . $ext['username'] . "</span>";
			  } else {
			      if (! is_null($ext['exclusion_reason'])) {
				  $row_exclusion_reasons[] = $ext['exclusion_reason'];
			      }
			      $row_exclude[$ext['username']] = $ext['exclusion_reason'];
			  }
		      }
		  }

		  // Check whether there's more than 1 complete
		  if ($row_extraction_count > 1) {
		      $row_multiple_screens_css = " nbtMultipleScreeningComplete";
		  } else {
		      $row_multiple_screens_css = "";
		  }

		  // Check whether it's unanimous
		  if ($row_extraction_count > 0) {
		      if ($row_include_count == $row_extraction_count | ($row_include_count == 0 & count(array_unique($row_exclusion_reasons)) == 1)) {
			  $row_unanimous = TRUE;
		      } else {
			  $row_unanimous = FALSE;
		      }
		  } else {
		      $row_unanimous = FALSE;
		  }

		  if ($row_unanimous) {
		      $unanimous_css = " nbtUnanimous";
		  } else {
		      $unanimous_css = "";
		  }
		  
		  ?>
		  <tr class="nbtFocusableScreeningRow<?php echo $row_done . $unanimous_css . $row_multiple_screens_css; ?>">
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
		      <?php

		      $includeboxcss = "";
		      if (is_null($row_final['include'])) {
			  $includeboxlabel = "Include?";
			  $includeboxcss = "";
		      } else {

			  if ($row_final['include'] == 1) {
			      $includeboxlabel = "&#10003; Include";
			      $includeboxcss = 'style="background-color: #ccffcc;"';
			  }

			  if ($row_final['include'] == 0) {
			      $includeboxlabel = "&#10007; Exclude";
			      $includeboxcss = 'style="background-color: #ffcccc;"';
			  }
			  
		      }
		      ?>
		      <td class="nbtScreeningReconcileIncludeBox" data-referenceid="<?php echo $assignment['referenceid']; ?>"<?php echo $includeboxcss; ?>>
			  <div class="nbtFinalLabel">
			      <?php echo $includeboxlabel; ?>
			  </div>
			  <?php echo $row_include; ?>
		      </td>
		      <?php $excl_count = 0; ?>
		      <?php foreach ($exclusion_reasons as $er) { ?>
			  <?php

			  if ($row_final['include'] == 0 & $row_final['exclusion_reason'] == $er['dbname']) {
			      $excludeboxcss = 'style="background-color: #ffcccc;"';
			  } else {
			      $excludeboxcss = '';
			  }
			  
			  ?>
			  <td class="nbtScreeningReconcileExcludeBox nbtScreeningReconcileExcludeBox<?php echo $excl_count; ?>" data-excludereason="<?php echo $er['dbname']; ?>" data-referenceid="<?php echo $assignment['referenceid']; ?>"<?php echo $excludeboxcss; ?>>
			      <div><?php echo $er['displayname']; ?></div>
			      <?php

			      foreach ($row_exclude as $exclude_user => $re) {
				  if ($re == $er['dbname']) {
				      echo "<span class=\"nbtExtractionName\">" . $exclude_user . "</span>";
				  }
			      }
			      
			      ?>
			  </td>
			  <?php $excl_count++; ?>
		      <?php } ?>
		      <td>
			  <?php

			  if (! is_null($row_notes)) {
			      foreach ($row_notes as $notes_user => $rn) {
				  echo "<p><span style=\"font-weight: 800\">" . $notes_user . "</span>: " . $rn . "</p>";
			      }
			  }

			  ?>
		      </td>
		  </tr>
	      <?php } ?>
