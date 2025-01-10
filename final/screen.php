<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );
$refset = nbt_get_refset_for_id ( $_GET['refset'] );
$form = nbt_get_form_for_id ( $_GET['form'] );

if (! isset($_GET['screeningpage'])) {
  $_GET['screeningpage'] = 1;
}

$assignments = nbt_get_assignments_for_refset_form_paginated (
    $_GET['refset'],
    $_GET['form'],
    $_GET['screeningpage']
);

$refs = nbt_get_all_references_for_refset ($_GET['refset']);
$extractions = nbt_get_all_extractions_for_refset_and_form ($_GET['refset'], $_GET['form']);
$count_of_assignments = nbt_count_assignments_for_refset_form ($_GET['refset'], $_GET['form']);

foreach ($formelements as $element) {
    if ($element['columnname'] == "exclusion_reason") {
	$exclusion_reasons = nbt_get_all_select_options_for_element($element['id']);
    }
}

?>
<div class="nbtNonsidebar">
  <div class="nbtContentPanel">
      <h2>Reconcile screened reference set: <?php echo $refset['name']; ?> (<?php echo $count_of_assignments; ?> total)</h2>

	  <div>
	      Page:
	      <?php $i = 1; ?>
	      <?php while ($i <= ceil($count_of_assignments / 100)) { ?>
		  <?php if ($i != $_GET['screeningpage']) { ?>
		      <span style="margin: 0 10px; padding: 2px 4px;"><a href="<?php echo SITE_URL; ?>extract/?action=screen&form=<?php echo $_GET['form']; ?>&refset=<?php echo $_GET['refset']; ?>&screeningpage=<?php echo $i; ?>"><?php echo $i; ?></a></span>
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
		  <?php if ($assignment['formid'] == $form['id']) { ?>
		      <?php

		      foreach ($extractions as $ext) {
			  if ($assignment['referenceid'] == $ext['referenceid']) {
			      echo $ext['userid'] . "<br>";
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
			      <div><?php
				   
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

				   ?></div>
			  </td>
			  <td class="nbtScreeningIncludeBox<?php echo $includeboxclass; ?>" data-referenceid="<?php echo $assignment['referenceid']; ?>">
			      
			  </td>
		      </tr>
		  <?php } ?>
	      <?php } ?>
