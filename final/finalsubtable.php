<?php

$columns = nbt_get_all_columns_for_table_data ( $nbtMasterTableID, TRUE );

$rows = nbt_get_master_table_rows ( $nbtMasterTableID, $nbtMasterRefSet, $nbtMasterRefID, TRUE, $nbtSubTableSubextractionID );

$nbtExtractTableDataID = $nbtMasterTableID;

$no_of_columns = count ( $columns );

?><table class="nbtTabledData">
	<tr class="nbtTableHeaders">
		<?php

		foreach ( $columns as $column ) {

			?><td><?php echo $column['displayname']; ?></td><?php

		}

		?>
		<td style="width: 70px;">Delete</td>
	</tr>
	<?php

	foreach ( $rows as $row ) {

		?><tr class="nbtTDRow<?php echo $nbtExtractTableDataID; ?>" id="nbtMasterTD<?php echo $nbtMasterTableID; ?>RowID<?php echo $row['id']; ?>"><?php

			foreach ( $columns as $column ) {

				?><td><input type="text" id="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>" class="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>" value="<?php echo $row[$column['dbname']]; ?>" onkeyup="nbtMasterTableDataKeyHandle(event, this, <?php echo $nbtExtractTableDataID; ?>, <?php echo $column['id']; ?>);" onblur="nbtUpdateMasterSubTableData(<?php echo $nbtExtractTableDataID; ?>, <?php echo $row['id']; ?>, '<?php echo $column['dbname']; ?>', 'nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>');"></td><?php

			}

			?><td>
				<button onclick="$(this).fadeOut(0);$('#nbtMasterTableExtractionRowDelete<?php echo $nbtMasterTableID; ?>-<?php echo $row['id']; ?>').fadeIn();">Delete</button>
				<button class="nbtHidden" id="nbtMasterTableExtractionRowDelete<?php echo $nbtMasterTableID; ?>-<?php echo $row['id']; ?>" onclick="nbtDeleteMasterSubTableRow(<?php echo $nbtMasterTableID; ?>, <?php echo $row['id']; ?>);">For real</button></td>
		</tr><?php

	}

	?><tr>
		<td colspan="<?php

		echo count ($columns) + 1;

		?>"><button onclick="nbtAddSubTableRowToMaster(<?php echo $nbtMasterTableID; ?>, <?php echo $nbtMasterRefSet; ?>, <?php echo $nbtMasterRefID; ?>, <?php echo $nbtSubTableSubextractionID; ?>);">Add new row to final</button></td>
	</tr>
</table>
