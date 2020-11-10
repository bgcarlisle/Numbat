<table class="nbtTabledData">
	<tr class="nbtTableHeaders">
		<td>Display name</td>
		<td>DB value</td>
		<td>Toggle</td>
		<td style="width: 80px;">Move</td>
		<td style="width: 80px;">Delete</td>
	</tr>
	<?php

	$selectoptions = nbt_get_all_select_options_for_element ( $tableelementid );
	
	foreach ( $selectoptions as $select ) {
		
		?><tr>
			<td><input type="text" id="nbtSingleSelect<?php echo $select['id']; ?>displayname" value="<?php echo $select['displayname']; ?>" onblur="nbtUpdateSingleSelect(<?php echo $tableelementid; ?>, <?php echo $select['id']; ?>, 'displayname');"></td>
			<td><input type="text" id="nbtSingleSelect<?php echo $select['id']; ?>dbname" value="<?php echo $select['dbname']; ?>" onblur="nbtUpdateSingleSelect(<?php echo $tableelementid; ?>, <?php echo $select['id']; ?>, 'dbname');"></td>
			<td><input type="text" id="nbtSingleSelect<?php echo $select['id']; ?>toggle" value="<?php echo $select['toggle']; ?>" onblur="nbtUpdateSingleSelect(<?php echo $tableelementid; ?>, <?php echo $select['id']; ?>, 'toggle');"></td>
			<td><button onclick="nbtMoveSelectOption(<?php echo $tableelementid; ?>, <?php echo $select['id']; ?>, 1);">&#8593;</button> <button onclick="nbtMoveSelectOption(<?php echo $tableelementid; ?>, <?php echo $select['id']; ?>, -1);">&#8595;</button></td>
			<td>
				<button onclick="$(this).fadeOut(0);$('#nbtSelectDelete<?php echo $select['id']; ?>').fadeIn();">Delete</button>
				<button id="nbtSelectDelete<?php echo $select['id']; ?>" class="nbtHidden" onclick="nbtRemoveSingleSelectOption(<?php echo $tableelementid; ?>, <?php echo $select['id']; ?>);">For real</button>
			</td>
		</tr><?php
		
	}

	?><tr>
		<td colspan="5"><button onclick="nbtAddSingleSelectOption(<?php echo $tableelementid; ?>);">Add new option</button></td>
	</tr>
</table>
