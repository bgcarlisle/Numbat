<?php

$columns = nbt_get_all_columns_for_table_data ( $nbtExtractTableDataID );

$rows = nbt_get_table_data_rows ( $nbtExtractTableDataID, $nbtExtractRefSet, $nbtExtractRefID, $_SESSION[INSTALL_HASH . '_nbt_userid'] );

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

		?><tr class="nbtTDRow<?php echo $nbtExtractTableDataID; ?>" id="nbtTDRowID<?php echo $nbtExtractTableDataID; ?>-<?php echo $row['id']; ?>"><?php

			foreach ( $columns as $column ) {

				?><td><?php

				if ( $tableformat == "table_data" ) {

					?><input type="text" id="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>" class="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>" value="<?php echo $row[$column['dbname']]; ?>" onkeyup="nbtExtractionTableDataKeyHandle(event, this, <?php echo $nbtExtractTableDataID; ?>, <?php echo $column['id']; ?>, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>);" onblur="nbtUpdateExtractionTableData(<?php echo $nbtExtractTableDataID; ?>, <?php echo $row['id']; ?>, '<?php echo $column['dbname']; ?>', 'nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>');"><?php

				} else {

					?><textarea id="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>" onblur="nbtUpdateExtractionTableData(<?php echo $nbtExtractTableDataID; ?>, <?php echo $row['id']; ?>, '<?php echo $column['dbname']; ?>', 'nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>');"><?php echo $row[$column['dbname']]; ?></textarea><?php

				}

				?></td><?php

			}

			?><td><button onclick="$(this).fadeOut(0);$('button#nbtTableExtractionRowDelete<?php echo $nbtExtractTableDataID; ?>-<?php echo $row['id']; ?>').fadeIn();">Delete</button>
			<button id="nbtTableExtractionRowDelete<?php echo $nbtExtractTableDataID; ?>-<?php echo $row['id']; ?>" class="nbtHidden" onclick="nbtRemoveExtractionTableDataRow(<?php echo $nbtExtractTableDataID; ?>, <?php echo $row['id']; ?>);">For real</button></td>
		</tr><?php

	}

	?>
	<tr>
		<td colspan="<?php echo $no_of_columns + 1; ?>">
			<button onclick="nbtAddExtractionTableDataRow('<?php echo $tableformat; ?>', <?php echo $nbtExtractTableDataID; ?>, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>);">Add a new row</button></td>
	</tr>
</table>
