<?php

$columns = nbt_get_all_columns_for_table_data ( $nbtMasterTableID );

$rows = nbt_get_master_table_rows ( $nbtMasterTableID, $nbtMasterRefSet, $nbtMasterRefID );

//echo count ( $rows );

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
				
				?><td><input type="text" id="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>" value="<?php echo $row[$column['dbname']]; ?>" onblur="nbtUpdateMasterExtractionTableData(<?php echo $nbtExtractTableDataID; ?>, <?php echo $row['id']; ?>, '<?php echo $column['dbname']; ?>', 'nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $row['id']; ?>');"></td><?php
				
			}
			
			?><td>
				<button onclick="$(this).fadeOut(0);$('#nbtMasterTableExtractionRowDelete<?php echo $row['id']; ?>').fadeIn();">Delete</button>
				<button class="nbtHidden" id="nbtMasterTableExtractionRowDelete<?php echo $row['id']; ?>" onclick="nbtDeleteMasterTableRow(<?php echo $nbtMasterTableID; ?>, <?php echo $row['id']; ?>);">For real</button></td>
		</tr><?php
		
	}
	
	?>
</table>
<p class="nbtFinePrint" id="nbtTable<?php echo $nbtMasterTableID; ?>Feedback">&nbsp;</p>