<?php

$subelements = nbt_get_sub_extraction_elements_for_elementid ( $nbtSubExtractionElementID );

$rows = nbt_get_table_data_rows ( $nbtExtractTableDataID, $nbtExtractRefSet, $nbtExtractRefID, $nbtExtractUserID );

$no_of_columns = count ( $columns );

?><table class="nbtTabledData">
	<tr class="nbtTableHeaders">
		<?php
		
		foreach ( $columns as $column ) {
			
			?><td><?php echo $column['displayname']; ?></td><?php
			
		}
		
		?>
		<td style="width: 70px;">Copy to master</td>
	</tr>
	<?php
	
	foreach ( $rows as $row ) {
		
		?><tr class="nbtTDRow<?php echo $nbtExtractTableDataID; ?>" id="nbtTDRowID<?php echo $row['id']; ?>"><?php
			
			foreach ( $columns as $column ) {
				
				?><td><?php echo $row[$column['dbname']]; ?></td><?php
				
			}
			
			?><td><button id="nbtTableExtractionRowDelete<?php echo $row['id']; ?>" onclick="nbtCopyTableDataRow(<?php echo $nbtExtractTableDataID; ?>, <?php echo $nbtExtractRefSet ?>, <?php echo $nbtExtractRefID ?>, <?php echo $row['id']; ?>);">Copy</button></td>
		</tr><?php
		
	}
	
	?>
</table>