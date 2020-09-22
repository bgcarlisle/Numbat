<?php

$columns = nbt_get_all_columns_for_table_data ( $nbtMasterTableID );

$rows = nbt_get_master_table_rows ( $nbtMasterTableID, $nbtMasterRefSet, $nbtMasterRefID );

//echo count ( $rows );

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

				?><td><?php

				if ( $tableformat == "table_data" ) {

					?><input type="text" id="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>" class="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>" value="<?php echo $row[$column['dbname']]; ?>" onkeyup="nbtMasterTableDataKeyHandle(event, this, <?php echo $nbtExtractTableDataID; ?>, <?php echo $column['id']; ?>);" onblur="nbtUpdateMasterExtractionTableData(<?php echo $nbtExtractTableDataID; ?>, <?php echo $row['id']; ?>, '<?php echo $column['dbname']; ?>', 'nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>');"><?php

				} else {

					?><textarea id="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>" onblur="nbtUpdateMasterExtractionTableData(<?php echo $nbtExtractTableDataID; ?>, <?php echo $row['id']; ?>, '<?php echo $column['dbname']; ?>', 'nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>');"><?php echo $row[$column['dbname']]; ?></textarea><?php

				}

				?></td><?php

			}

			?><td>
				<button onclick="$(this).fadeOut(0);$('#nbtMasterTableExtractionRowDelete<?php echo $nbtMasterTableID; ?>-<?php echo $row['id']; ?>').fadeIn();">Delete</button>
				<button class="nbtHidden" id="nbtMasterTableExtractionRowDelete<?php echo $nbtMasterTableID; ?>-<?php echo $row['id']; ?>" onclick="nbtDeleteMasterTableRow(<?php echo $nbtMasterTableID; ?>, <?php echo $row['id']; ?>);">For real</button></td>
		</tr><?php

	}

	?><tr>
		<td colspan="<?php

		echo count ($columns) + 1;

		?>"><button onclick="nbtAddTableDataRowToMaster('<?php echo $tableformat; ?>', <?php echo $nbtMasterTableID; ?>, <?php echo $nbtMasterRefSet; ?>, <?php echo $nbtMasterRefID; ?>);">Add new row to master</button></td>
	</tr>
</table>
