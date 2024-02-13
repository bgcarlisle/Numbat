<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );
$refset = nbt_get_refset_for_id ( $_GET['refset'] );
$form = nbt_get_form_for_id ( $_GET['form'] );
$assignments = nbt_get_assignments_for_user_and_refset ($_SESSION[INSTALL_HASH . '_nbt_userid'], $_GET['refset']);
$refs = nbt_get_all_references_for_refset ($_GET['refset']);
$extractions = nbt_get_all_extractions_for_refset_and_form ($_GET['refset'], $_GET['form']);

foreach ($formelements as $element) {
  if ($element['columnname'] == "exclusion_reason") {
    $exclusion_reasons = nbt_get_all_select_options_for_element($element['id']);
  }
}

?>
<div class="nbtNonsidebar">
  <div class="nbtContentPanel">
    <h2>Screening reference set: <?php echo $refset['name']; ?></h2>
    <p>Form: <?php echo $form['name']; ?></p>
    <p>Keyboard shortcuts: j (next row); k (previous row); n (next unfinished row); 1 (include); 2-9 (exclusion reasons); 0 (focus notes)</p>
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

          foreach ($extractions as $ext) {
            if ($assignment['referenceid'] == $ext['referenceid']) {

              switch ($ext['include']) {
                case NULL:
                  $includeboxstyle = '';
                  $includeboxcontent = "Include?";
                  break;
                case 1:
                  $includeboxstyle = ' style="background-color: #ccffcc;"';
                  $includeboxcontent = "Include";
                  break;
                case 0:
                  $includeboxstyle = ' style="background-color: #ffcccc;"';
                  $includeboxcontent = "Exclude";
                  break;
              }

              $exclusion_reason = $ext['exclusion_reason'];

            }
          }

          if ( is_null($includeboxstyle) ) {
            $includeboxstyle = '';
            $includeboxcontent = "Include?";
          }

          ?>
          <tr class="nbtFocusableScreeningRow">
            <td>
              <h3><?php echo $refs[$assignment['referenceid']][$refset['title']]; ?></h3>
              <p>
                <?php echo $refs[$assignment['referenceid']][$refset['authors']]; ?>
                <?php echo $refs[$assignment['referenceid']][$refset['journal']]; ?>
                <?php echo $refs[$assignment['referenceid']][$refset['year']]; ?>
              </p>
              <p><?php echo str_replace("\n", "<br>", $refs[$assignment['referenceid']][$refset['abstract']]); ?></p>
            </td>
            <td class="nbtScreeningIncludeBox" data-referenceid="<?php echo $assignment['referenceid']; ?>"<?php echo $includeboxstyle; ?>>
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
              <input type="text" value="" class="nbtScreeningNotes" data-referenceid="<?php echo $assignment['referenceid']; ?>">
            </td>
          </tr>
        <?php } ?>
      <?php } ?>
    </table>
  </div>
</div>
