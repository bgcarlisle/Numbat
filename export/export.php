<?php

$refsets = nbt_get_all_ref_sets ();

$forms = nbt_get_all_extraction_forms ();

if ( count ( $refsets ) > 0 ) {

    echo "<h2>Export extractions</h2>";

    foreach ( $refsets as $refset ) {

	echo "<div class=\"nbtContentPanel nbtGreyGradient\">";
	echo '<h2>' . $refset['name'] . '</h2>';

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

		echo "<button onclick=\"nbtExportData('extraction', ";

		echo $refset['id'];

		echo ", ";

		echo $form['id'];

		echo ", 0);\">Export \"";
		
		echo $form['name'];

		echo "\" extractions</button>";

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

		echo "\" final</button>";

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
