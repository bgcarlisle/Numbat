<?php

$columns = nbt_get_all_columns_for_table_data ( $nbtExtractTableDataID );

$rows = nbt_get_table_data_rows ( $nbtExtractTableDataID, $nbtExtractRefSet, $nbtExtractRefID, $_SESSION['nbt_userid'] );

$no_of_columns = count ( $columns );

?><table class="nbtTabledData">
	<tr class="nbtTableHeaders">
		<?php
		
		foreach ( $columns as $column ) {
			
			?><td><?php echo $column['displayname']; ?></td><?php
			
		}
		
		?>
		<td style="width: 100px;">Delete</td>
	</tr>
	<?php
	
	foreach ( $rows as $row ) {
		
		?><tr>
			
		</tr><?php
		
	}
	
	?>
	<tr>
		<td colspan="<?php echo $no_of_columns + 1; ?>"><button>Add a new row</button></td>
	</tr>
</table>