<?php

$files = scandir ( ABS_PATH . "backup/dumpfiles/");

?><table class="nbtTabledData">
<tr class="nbtTableHeaders">
	<td>Backup file</td>
	<?php
	
	if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {
		
		?><td>Restore from backup</td><?php
		
	}
	
	?>
</tr>
<?php

foreach ( $files as $file ) {
	
	if ( substr ($file, 0, 1) != "." ) {
		
		switch ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) ) {
			
			case 4:
				
				?><tr>
					<td><a href="<?php echo SITE_URL; ?>backup/dumpfiles/<?php echo $file; ?>"><?php echo $file; ?></a></td>
					<td><button>Restore</button></td>
				</tr><?php
				
			break;
			
			case 2:
				
				?><tr>
					<td><?php echo $file; ?></td>
				</tr><?php
				
			break;
			
		}
		
	}
	
}

?></table>