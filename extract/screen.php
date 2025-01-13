<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );
$refset = nbt_get_refset_for_id ( $_GET['refset'] );
$form = nbt_get_form_for_id ( $_GET['form'] );
if (isset($_GET['screeningpage'])) {
    $assignments = nbt_get_assignments_for_user_refset_form_paginated ($_SESSION[INSTALL_HASH . '_nbt_userid'], $_GET['refset'], "", FALSE, $_GET['screeningpage'], $_GET['form']);
} else {
    $assignments = nbt_get_assignments_for_user_and_refset ($_SESSION[INSTALL_HASH . '_nbt_userid'], $_GET['refset']);
}
$refs = nbt_get_all_references_for_refset ($_GET['refset']);
$extractions = nbt_get_all_extractions_for_refset_and_form ($_GET['refset'], $_GET['form']);
$count_of_assignments = nbt_count_assignments_for_user_refset_form ($_SESSION[INSTALL_HASH . '_nbt_userid'], $_GET['refset'], $_GET['form']);

foreach ($formelements as $element) {
    if ($element['columnname'] == "exclusion_reason") {
	$exclusion_reasons = nbt_get_all_select_options_for_element($element['id']);
    }
}

?>
<div class="nbtNonsidebar">
  <div class="nbtContentPanel">
      <h2>Screening reference set: <?php echo $refset['name']; ?> (<?php echo $count_of_assignments; ?> total)</h2>
      <?php if (isset($_GET['screeningpage'])) { ?>
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
      <?php } ?>
    <p>Form: <?php echo $form['name']; ?></p>
    <p>Keyboard shortcuts: j (next row); k (previous row); 1 (include); 2-9 (exclusion reasons); 0 (focus notes); h (hide completed)</p>
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

          $includeboxstyle = NULL;
          $includeboxcontent = NULL;
          $exclusion_reason = NULL;
          $notes = "";

          foreach ($extractions as $ext) {
            if ($assignment['referenceid'] == $ext['referenceid'] & $ext['userid'] == $_SESSION[INSTALL_HASH . '_nbt_userid']) {

              switch ($ext['include']) {
                case NULL:
                  $includeboxstyle = '';
                  $includeboxclass = "";
                  $includeboxcontent = "Include?";
                  break;
                case 1:
                  $includeboxstyle = ' style="background-color: #ccffcc;"';
                  $includeboxclass = " nbtScreeningDone";
                  $includeboxcontent = "Include";
                  break;
                case 0:
                  $includeboxstyle = ' style="background-color: #ffcccc;"';
                  $includeboxclass = " nbtScreeningDone";
                  $includeboxcontent = "Exclude";
                  break;
              }

              $exclusion_reason = $ext['exclusion_reason'];

              $notes = $ext['extractor_notes'];
            }

          }

          if ( is_null($includeboxstyle) ) {
            $includeboxstyle = '';
            $includeboxclass = "";
            $includeboxcontent = "Include?";
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
            <td class="nbtScreeningIncludeBox<?php echo $includeboxclass; ?>" data-referenceid="<?php echo $assignment['referenceid']; ?>"<?php echo $includeboxstyle; ?>>
              <?php echo $includeboxcontent; ?>
            </td>
            <?php $exclude_counter = 0; ?>
            <?php foreach ($exclusion_reasons as $er) { ?>
              <?php
              if ($exclusion_reason == $er['dbname']) {
                $excludeboxstyle = ' style = "background-color: #ffcccc;"';
              } else {
                $excludeboxstyle = '';
              }
              ?>
              <td class="nbtScreeningExcludeBox nbtScreeningExcludeBox<?php echo $exclude_counter; ?>" data-referenceid="<?php echo $assignment['referenceid']; ?>" data-excludereason="<?php echo $er['dbname']; ?>"<?php echo $excludeboxstyle; ?>><?php echo $er['displayname']; ?></td>
              <?php $exclude_counter++; ?>
            <?php } ?>
            <td style="width: 250px;">
              <input type="text" value="<?php echo $notes; ?>" class="nbtScreeningNotes" data-referenceid="<?php echo $assignment['referenceid']; ?>">
            </td>
          </tr>
        <?php } ?>
      <?php } ?>
    </table>
    <?php if (isset($_GET['screeningpage'])) { ?>
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
    <?php } ?>
  </div>
</div>
