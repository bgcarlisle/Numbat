<?php

$columns = nbt_get_all_columns_for_table_data ( $nbtExtractTableDataID );

$rows = nbt_get_table_data_rows ( $nbtExtractTableDataID, $nbtExtractRefSet, $nbtExtractRefID, $nbtExtractUserID );

$no_of_columns = count ( $columns );

?><table class="nbtTabledData">
    <tr class="nbtTableHeaders">
	<?php

	foreach ( $columns as $column ) {

	?><td><?php echo $column['displayname']; ?></td><?php

							}

							?>
	<td style="width: 70px;">Copy to final</td>
    </tr>
    <?php

    foreach ( $rows as $row ) {

    ?>
	<tr class="nbtTDRow<?php echo $nbtExtractTableDataID; ?>" id="nbtTDRowID<?php echo $nbtExtractTableDataID; ?>-<?php echo $row['id']; ?>">
	    <?php

	    foreach ( $columns as $column ) {

	    ?>
		<td><?php echo $row[$column['dbname']]; ?></td>
	    <?php

	    }

	    ?><td><button id="nbtTableExtractionRowCopy<?php echo $nbtExtractTableDataID; ?>-<?php echo $row['id']; ?>" onclick="nbtCopyTableDataRow('<?php echo $tableformat; ?>', <?php echo $nbtExtractTableDataID; ?>, <?php echo $nbtExtractRefSet ?>, <?php echo $nbtExtractRefID ?>, <?php echo $row['id']; ?>);">Copy</button></td>
	</tr><?php

	     }

	     ?>
</table>
<button id="nbtTableExtractionRowCopyAll<?php echo $nbtExtractTableDataID; ?>" onclick="nbtCopyTableDataAllRows('<?php echo $tableformat; ?>', <?php echo $nbtExtractTableDataID; ?>, <?php echo $nbtExtractRefSet ?>, <?php echo $nbtExtractRefID ?>, <?php echo $nbtExtractUserID; ?>);">Copy all rows to final</button>
