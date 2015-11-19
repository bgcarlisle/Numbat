<?php

$columns = nbt_get_all_columns_for_table_data ( $nbtExtractTableDataID, TRUE );

$rows = nbt_get_table_data_rows ( $nbtExtractTableDataID, $nbtExtractRefSet, $nbtExtractRefID, $_SESSION[INSTALL_HASH . '_nbt_userid'], TRUE, $nbtSubTableSubextractionID );

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

		?><tr class="nbtSubTDRow<?php echo $nbtExtractTableDataID; ?>" id="nbtSubTDRowID<?php echo $nbtExtractTableDataID; ?>-<?php echo $row['id']; ?>"><?php

			foreach ( $columns as $column ) {

				?><td><input type="text" id="nbtSubTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>" class="nbtSubTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>" value="<?php echo $row[$column['dbname']]; ?>" onkeyup="nbtSubTableDataKeyHandle(event, this, <?php echo $nbtExtractTableDataID; ?>, <?php echo $column['id']; ?>, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>, <?php echo $nbtSubTableSubextractionID; ?>);" onblur="nbtUpdateSubTableData(<?php echo $nbtExtractTableDataID; ?>, <?php echo $row['id']; ?>, '<?php echo $column['dbname']; ?>', 'nbtSubTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>');"></td><?php

			}

			?><td><button onclick="$(this).fadeOut(0);$('button#nbtTableExtractionRowDelete<?php echo $nbtExtractTableDataID; ?>-<?php echo $row['id']; ?>').fadeIn();">Delete</button>
			<button id="nbtTableExtractionRowDelete<?php echo $nbtExtractTableDataID; ?>-<?php echo $row['id']; ?>" class="nbtHidden" onclick="nbtRemoveSubTableDataRow(<?php echo $nbtExtractTableDataID; ?>, <?php echo $row['id']; ?>);">For real</button></td>
		</tr><?php

	}

	?>
	<tr>
		<td colspan="<?php echo $no_of_columns + 1; ?>">
			<button onclick="nbtAddSubTableDataRow(<?php echo $nbtExtractTableDataID; ?>, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>, <?php echo $nbtSubTableSubextractionID; ?>);">Add a new row</button></td>
	</tr>
</table>
