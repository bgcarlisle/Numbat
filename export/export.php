<?php

$refsets = nbt_get_all_ref_sets ();

$forms = nbt_get_all_extraction_forms ();

if ( count ( $refsets ) > 0 ) {

	foreach ( $refsets as $refset ) {

		?><div class="nbtContentPanel nbtGreyGradient">
			<h2><?php echo $refset['name']; ?></h2>

                  <table class="nbtTabledData">
                        <tr class="nbtTableHeaders">
                              <td>Form name</td>
                              <td>Extractions</td>
                              <td>Final copy</td>
                        </tr>

                        <?php

      			foreach ( $forms as $form ) {

            				$extractedrefs = nbt_get_all_extracted_references_for_refset_and_form ( $refset['id'], $form['id'] );

                                    $reconciledrefs = nbt_get_all_reconciled_references_for_refset_and_form ( $refset['id'], $form['id'] );

                              ?><tr>

                                    <td><h3><?php echo $form['name']; ?></h3></td>
                                    <td><?php

                                    if ( count ( $extractedrefs ) > 0 ) {

                                          ?><button onclick="nbtExportData('extraction', <?php echo $refset['id'] ?>, <?php echo $form['id'] ?>, 0);">Export "<?php echo $form['name']; ?>" extractions</button><?php

                                    } else {

                                          ?><p>No references have been extracted for this form.</p><?php

                                    }

                                    ?></td>
                                    <td><?php

                                    if ( count ( $reconciledrefs ) > 0 ) {

                                          ?><button onclick="nbtExportData('extraction', <?php echo $refset['id'] ?>, <?php echo $form['id'] ?>, 1);">Export "<?php echo $form['name']; ?>" final</button><?php

                                    } else {

                                          ?><p>No extractions have been reconciled for this form.</p><?php

                                    }

                                    ?></td>


                              </tr>
                              <?php

                              $elements = nbt_get_elements_for_formid ( $form['id'] );

                              $count_special_elements = 0;

                              foreach ( $elements as $element ) {

                                    switch ( $element['type'] ) {

                                          case "table_data":

                                                ?><tr>
                                                      <td>&nbsp;</td><?php

                                                      $extracted_rows = nbt_get_all_table_data_rows_for_refset ( $element['id'], $refset['id'] );

                                                      $reconciled_rows = nbt_get_all_reconciled_table_data_rows_for_refset ( $element['id'], $refset['id'] );

                                                      if ( count ( $extracted_rows ) > 0 ) {

                                                            ?><td><button onclick="nbtExportData('table_data', <?php echo $refset['id']; ?>, '<?php echo $element['columnname']; ?>', 0);">Export "<?php echo $element['displayname']; ?>" table data</button></td><?php

                                                      } else {

                                                            ?><td><p>Table "<?php echo $element['displayname']; ?>" has no extracted data.</p></td><?php

                                                      }

                                                      if ( count ( $reconciled_rows ) > 0 ) {

                                                            ?><td><button onclick="nbtExportData('table_data', <?php echo $refset['id']; ?>, '<?php echo $element['columnname']; ?>', 1);">Export "<?php echo $element['displayname']; ?>" final table data</button></td><?php

                                                      } else {

                                                            ?><td><p>The final copy of table "<?php echo $element['displayname']; ?>" has no reconciled data.</p></td><?php

                                                      }


                                                      $count_special_elements ++;

                                                ?></tr><?php

                                          break;

                                          case "ltable_data":

                                                ?><tr>
                                                      <td>&nbsp;</td><?php

                                                      $extracted_rows = nbt_get_all_table_data_rows_for_refset ( $element['id'], $refset['id'] );

                                                      $reconciled_rows = nbt_get_all_reconciled_table_data_rows_for_refset ( $element['id'], $refset['id'] );

                                                      if ( count ( $extracted_rows ) > 0 ) {

                                                            ?><td><button onclick="nbtExportData('ltable_data', <?php echo $refset['id']; ?>, '<?php echo $element['columnname']; ?>', 0);">Export "<?php echo $element['displayname']; ?>" table data</button></td><?php

                                                      } else {

                                                            ?><td><p>Table "<?php echo $element['displayname']; ?>" has no extracted data.</p></td><?php

                                                      }

                                                      if ( count ( $reconciled_rows ) > 0 ) {

                                                            ?><td><button onclick="nbtExportData('ltable_data', <?php echo $refset['id']; ?>, '<?php echo $element['columnname']; ?>', 1);">Export "<?php echo $element['displayname']; ?>" final table data</button></td><?php

                                                      } else {

                                                            ?><td><p>The final copy of table "<?php echo $element['displayname']; ?>" has no reconciled data.</p></td><?php

                                                      }


                                                      $count_special_elements ++;

                                                ?></tr><?php

                                          break;

                                          case "sub_extraction":

                                                ?><tr>
                                                      <td>&nbsp;</td><?php

                                                      $extracted_rows = nbt_get_all_sub_extraction_rows_for_refset ( $element['id'], $refset['id'] );

                                                      $reconciled_rows = nbt_get_all_reconciled_sub_extraction_rows_for_refset ( $element['id'], $refset['id'] );

                                                      if ( count ( $extracted_rows ) > 0 ) {

                                                            ?><td><button onclick="nbtExportData('sub_extraction', <?php echo $refset['id']; ?>, '<?php echo $element['columnname']; ?>', 0);">Export "<?php echo $element['displayname']; ?>" sub-extraction</button></td><?php

                                                      } else {

                                                            ?><td><p>Sub-extraction "<?php echo $element['displayname']; ?>" has no extracted data.</p></td><?php

                                                      }

                                                      if ( count ( $reconciled_rows ) > 0 ) {

                                                            ?><td><button onclick="nbtExportData('sub_extraction', <?php echo $refset['id']; ?>, '<?php echo $element['columnname']; ?>', 1);">Export "<?php echo $element['displayname']; ?>" final sub-extraction</button></td><?php

                                                      } else {

                                                            ?><td><p>The final copy of sub-extraction "<?php echo $element['displayname']; ?>" has no reconciled data.</p></td><?php

                                                      }


                                                      $count_special_elements ++;

                                                ?></tr><?php

                                          break;

                                          case "citations":

                                                ?><tr>
                                                      <td>&nbsp;</td><?php

                                                      $extracted_cites = nbt_get_all_citations_for_refset ( $element['id'], $refset['id'] );

                                                      $reconciled_cites = nbt_get_all_reconciled_citations_for_refset ( $element['id'], $refset['id'] );

                                                      if ( count ( $extracted_cites ) > 0 ) {

                                                            ?><td><button onclick="nbtExportData('citations', <?php echo $refset['id'] ?>, '<?php echo $element['columnname']; ?>', 0);">Export "<?php echo $element['displayname'] ?>" citations</button></td><?php

                                                      } else {

                                                            ?><td><p>Citation set "<?php echo $element['displayname']; ?>" has no extracted citations.</p></td><?php

                                                      }

                                                      if ( count ( $reconciled_cites ) > 0 ) {

                                                            ?><td><button onclick="nbtExportData('citations', <?php echo $refset['id'] ?>, '<?php echo $element['columnname']; ?>', 1);">Export "<?php echo $element['displayname']; ?>" final citations</button></td><?php

                                                      } else {

                                                            ?><td><p>The final copy of citation set "<?php echo $element['displayname']; ?>" has no reconciled citations.</p></td><?php

                                                      }


                                                      $count_special_elements ++;

                                                ?></tr><?php

                                          break;

                                    }

                              }

      			}

      			?>
                  </table>
		</div><?php

	}

} else {

	?><div class="nbtContentPanel nbtGreyGradient">
		<h2>Error</h2>
		<p>You haven't got any reference sets.</p>
	</div><?php

}

?><div id="nbtCoverup" style="display: none; background: #ccc; opacity: 0.5; z-index: 1; width: 100%; height: 100%; position: fixed; top: 0; left: 0;" onclick="$(this).fadeOut();$('#nbtThinky').fadeOut();">&nbsp;</div>
<div id="nbtThinky" style="display: none; z-index: 2; position: fixed; top: 100px; width: 100%; text-align: center;">
	<div style="padding: 10px 20px 10px 20px; border: 2px solid #666; border-radius: 5px; background: #eee; color: #666; display: inline;"><a href="<?php echo SITE_URL ?>export/result.csv" id="nbtThinkyLinky">Download</a></div>
</div>
