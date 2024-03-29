<?php

$refsets = nbt_get_all_ref_sets ();

$forms = nbt_get_all_extraction_forms ();

if ( count ( $refsets ) > 0 ) {

    foreach ( $refsets as $refset ) {

	echo "<div class=\"nbtContentPanel nbtGreyGradient\">";
	echo '<h2>Export extractions: ' . $refset['name'] . '</h2>';

	echo '<table class="nbtTabledData">';
	echo '<tr class="nbtTableHeaders">';
	echo '<td>Form name</td>';
	echo '<td>Extractions</td>';
	echo '<td>Final copy</td>';
	echo '</tr>';

      	foreach ( $forms as $form ) {

	    $extractedrefs = nbt_get_all_extracted_references_for_refset_and_form ( $refset['id'], $form['id'] );

	    $reconciledrefs = nbt_get_all_reconciled_references_for_refset_and_form ( $refset['id'], $form['id'] );

	    echo '<tr>';
	    echo '<td><h3>' . $form['name'] . '</h3></td>';
	    echo '<td>';

	    if ( count ( $extractedrefs ) > 0 ) {

		// Button for the full form

		echo "<button onclick=\"nbtExportData('extraction', ";

		echo $refset['id'];

		echo ", ";

		echo $form['id'];

		echo ", 0);\">Export \"";
		
		echo $form['name'];

		echo "\" extractions (full form)</button>";

		// Drop-down for IRR options

		$formelements = nbt_get_elements_for_formid ( $form['id'] );

		$form_has_irr_eligible_element = FALSE;

		foreach ($formelements as $element) {
		    switch ($element['type']) {
			case "single_select":
			case "open_text":
			case "multi_select":
			    $form_has_irr_eligible_element = TRUE;
			    break;
			default:
			    // Do nothing
			    break;
		    }
		}

		if ($form_has_irr_eligible_element) {
		    echo '<p>Export extractions for a single element</p>';
		    echo '<select id="nbtIRR' . $form['id'] . '-' . $refset['id'] . '">';

		    echo '<option value="ns">Choose an element for IRR analysis</option>';

		    foreach ($formelements as $element) {

			switch ($element['type']) {
			    case "single_select":
			    case "open_text":
			    case "multi_select":

				echo '<option value="' . $element['id'] . '">';

				echo $element['displayname'];

				echo "</option>";
				
				break;

			    default:
				// Do nothing
				break;
			}
			
		    }

		    echo "</select>";

		    echo ' <button onclick="nbtExportIRR(' . $form['id'] . ', ' . $refset['id'] . ');">Export</button>';
		    
		}

	    } else {

		echo '<p>No references have been extracted for this form.</p>';
		
	    }

	    echo "</td>";

	    echo "<td>";

	    if ( count ( $reconciledrefs ) > 0 ) {

		echo "<button onclick=\"nbtExportData('extraction', ";

		echo $refset['id'];

		echo ", ";
		
		echo $form['id'];

		echo ", 1);\">Export \"";

		echo $form['name'];

		echo "\" final (full form)</button>";

	    } else {

		echo "<p>No extractions have been reconciled for this form.</p>";

	    }

	    echo "</td>";

	    echo "</tr>";
	    
            $elements = nbt_get_elements_for_formid ( $form['id'] );

            $count_special_elements = 0;

            foreach ( $elements as $element ) {

                switch ( $element['type'] ) {

		    case "table_data":

			echo "<tr>";

			echo "<td>&nbsp;</td>";

			$extracted_rows = nbt_get_all_table_data_rows_for_refset ( $element['id'], $refset['id'] );
			
			$reconciled_rows = nbt_get_all_reconciled_table_data_rows_for_refset ( $element['id'], $refset['id'] );

			if ( count ( $extracted_rows ) > 0 ) {

			    echo "<td>";

			    echo "<button onclick=\"nbtExportData('table_data', ";

			    echo $refset['id'];

			    echo ", '";
			    
			    echo $element['columnname'];

			    echo "', 0);\">Export \"";
			    
			    echo $element['displayname'];

			    echo '" table data</button></td>';

			} else {

			    echo '<td><p>Table "';

			    echo $element['displayname'];

			    echo '" has no extracted data.</p></td>';

			}

			if ( count ( $reconciled_rows ) > 0 ) {

			    echo "<td><button onclick=\"nbtExportData('table_data', ";

			    echo $refset['id'];

			    echo ", '";

			    echo $element['columnname'];

			    echo '\', 1);">Export "';

			    echo $element['displayname'];

			    echo '" final table data</button></td>';
			    
			} else {

			    echo '<td><p>The final copy of table "';

			    echo $element['displayname'];

			    echo '" has no reconciled data.</p></td>';

			}
			
			$count_special_elements ++;

			echo "</tr>";

			break;

		    case "ltable_data":

			echo "<tr>";

			echo "<td>&nbsp;</td>";
			
			$extracted_rows = nbt_get_all_table_data_rows_for_refset ( $element['id'], $refset['id'] );

			$reconciled_rows = nbt_get_all_reconciled_table_data_rows_for_refset ( $element['id'], $refset['id'] );

			if ( count ( $extracted_rows ) > 0 ) {

			    echo "<td><button onclick=\"nbtExportData('ltable_data', ";

			    echo $refset['id'];

			    echo ", '";

			    echo $element['columnname'];

			    echo '\', 0);">Export "';
			    
			    echo $element['displayname'];

			    echo '" table data</button></td>';

			} else {

			    echo '<td><p>Table "';

			    echo $element['displayname'];

			    echo '" has no extracted data.</p></td>';

			}

			if ( count ( $reconciled_rows ) > 0 ) {

			    echo "<td><button onclick=\"nbtExportData('ltable_data', ";

			    echo $refset['id'];

			    echo ", '";

			    echo $element['columnname'];

			    echo '\', 1);">Export "';

			    echo $element['displayname'];

			    echo '" final table data</button></td>';
			    
			} else {

			    echo '<td><p>The final copy of table "';

			    echo $element['displayname'];

			    echo '" has no reconciled data.</p></td>';

			}

			$count_special_elements ++;

			echo "</tr>";

			break;

		    case "sub_extraction":

			echo "<tr>";

			echo "<td>&nbsp;</td>";

			$extracted_rows = nbt_get_all_sub_extraction_rows_for_refset ( $element['id'], $refset['id'] );

			$reconciled_rows = nbt_get_all_reconciled_sub_extraction_rows_for_refset ( $element['id'], $refset['id'] );

			if ( count ( $extracted_rows ) > 0 ) {

			    echo "<td><button onclick=\"nbtExportData('sub_extraction', ";

			    echo $refset['id'];

			    echo ", '";
			    
			    echo $element['columnname'];

			    echo '\', 0);">Export "';
			    
			    echo $element['displayname'];

			    echo '" sub-extraction</button></td>';

			} else {

			    echo '<td><p>Sub-extraction "';
			    
			    echo $element['displayname'];

			    echo '" has no extracted data.</p></td>';

			}

			if ( count ( $reconciled_rows ) > 0 ) {

			    echo "<td><button onclick=\"nbtExportData('sub_extraction', ";

			    echo $refset['id'];

			    echo ", '";

			    echo $element['columnname'];

			    echo '\', 1);">Export "';

			    echo $element['displayname'];

			    echo '" final sub-extraction</button></td>';
			    
			} else {

			    echo '<td><p>The final copy of sub-extraction "';

			    echo $element['displayname'];

			    echo '" has no reconciled data.</p></td>';

			}

			$count_special_elements ++;

			echo '</tr>';

			//

			$sub_elements = nbt_get_sub_extraction_elements_for_elementid ( $element['id'] );

			foreach ( $sub_elements as $sub_element ) {

			    switch ( $sub_element['type'] ) {

				case "table_data":

				    $extracted_rows = nbt_get_all_table_data_rows_for_refset ( $sub_element['id'], $refset['id'], TRUE );

				    $reconciled_rows = nbt_get_all_reconciled_table_data_rows_for_refset ( $sub_element['id'], $refset['id'], TRUE );

				    echo '<tr>';

				    echo '<td>&nbsp;</td>';

				    echo '<td>';

				    if ( count ($extracted_rows) > 0 ) {

					echo '<button onclick="nbtExportData(\'sub_table\', ' . $refset['id'] . ', \'' . $sub_element['dbname'] . '\', 0);">Export "' . $sub_element['displayname'] . '" sub-extraction table data</button>';
					
				    } else {

					echo 'Sub-extraction table "' . $sub_element['displayname'] . '" has no extracted data.';
					
				    }

				    echo '</td>';

				    echo '<td>';

				    if ( count ($reconciled_rows) > 0 ) {

					echo '<button onclick="nbtExportData(\'sub_table\', ' . $refset['id'] . ', \'' . $sub_element['dbname'] . '\', 1);">Export "' . $sub_element['displayname'] . '" final sub-extraction table data</button>';
					
				    } else {

					echo 'The final copy of the sub-extraction table "' . $sub_element['displayname'] . '" has no reconciled data.';
				    }

				    echo '</td>';

				    echo '</tr>';
				    
				    break;

			    }
			    
			}

			break;

		    case "citations":

			echo "<tr>";

			echo "<td>&nbsp;</td>";
			
			$extracted_cites = nbt_get_all_citations_for_refset ( $element['id'], $refset['id'] );

			$reconciled_cites = nbt_get_all_reconciled_citations_for_refset ( $element['id'], $refset['id'] );

			if ( count ( $extracted_cites ) > 0 ) {

			    echo "<td><button onclick=\"nbtExportData('citations', ";

			    echo $refset['id'];

			    echo ", '";


			    echo $element['columnname'];

			    echo '\', 0);">Export "';

			    echo $element['displayname'];

			    echo '" citations</button></td>';

			} else {

			    echo '<td><p>Citation set "';

			    echo $element['displayname'];

			    echo '" has no extracted citations.</p></td>';

			}

			if ( count ( $reconciled_cites ) > 0 ) {

			    echo "<td><button onclick=\"nbtExportData('citations', ";

			    echo $refset['id'];

			    echo ", '";

			    echo $element['columnname'];

			    echo '\', 1);">Export "';

			    echo $element['displayname'];

			    echo '" final citations</button></td>';

			} else {

			    echo '<td><p>The final copy of citation set "';

			    echo $element['displayname'];

			    echo '" has no reconciled citations.</p></td>';

			}

			$count_special_elements ++;

			echo "</tr>";

			break;

		}


	    }
	    
	}

	echo "</table>";

	echo "</div>";

    }

} else {

?><div class="nbtContentPanel nbtGreyGradient">
    <h2>Error</h2>
    <p>You haven't got any reference sets.</p>
</div>

<?php } ?>

<div id="nbtCoverup" style="display: none; background: #ccc; opacity: 0.5; z-index: 1; width: 100%; height: 100%; position: fixed; top: 0; left: 0;" onclick="$(this).fadeOut();$('#nbtThinky').fadeOut();">&nbsp;</div>
<div id="nbtThinky" style="display: none; z-index: 2; position: fixed; top: 100px; width: 100%; text-align: center;">
    <div style="padding: 10px 20px 10px 20px; border: 2px solid #666; border-radius: 5px; background: #eee; color: #666; display: inline;"><a href="<?php echo SITE_URL ?>export/result.csv" id="nbtThinkyLinky">Download</a></div>
</div>
