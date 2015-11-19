<?php

$columns = nbt_get_all_columns_for_table_data ( $nbtExtractTableDataID, TRUE );

$rows = nbt_get_table_data_rows ( $nbtExtractTableDataID, $nbtExtractRefSet, $nbtExtractRefID, $nbtExtractUserID, TRUE, $nbtSubTableSubextractionID );

$no_of_columns = count ( $columns );

?><table class="nbtTabledData">
	<tr class="nbtTableHeaders">
		<?php

		foreach ( $columns as $column ) {

			?><td><?php echo $column['displayname']; ?></td><?php

		}

		?>
	</tr>
	<?php

	foreach ( $rows as $row ) {

		?><tr class="nbtSubTDRow<?php echo $nbtExtractTableDataID; ?>" id="nbtSubTDRowID<?php echo $nbtExtractTableDataID; ?>-<?php echo $row['id']; ?>"><?php

			foreach ( $columns as $column ) {

				?><td><?php echo $row[$column['dbname']]; ?></td><?php

			}

			?>
		</tr><?php

	}

	?>
</table>
