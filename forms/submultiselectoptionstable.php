<table class="nbtTabledData">
	<tr class="nbtTableHeaders">
		<td>Display name</td>
		<td>DB column</td>
		<td style="width: 80px;">Move</td>
		<td style="width: 80px;">Delete</td>
	</tr>
	<?php

	$selectoptions = nbt_get_all_select_options_for_sub_element ( $tablesubelementid );
	
	foreach ( $selectoptions as $select ) {
		
		?><tr>
			<td><input type="text" id="nbtSingleSelect<?php echo $select['id']; ?>displayname" value="<?php echo $select['displayname']; ?>" onblur="nbtUpdateSubSelect(<?php echo $tablesubelementid; ?>, <?php echo $select['id']; ?>, 'displayname');"></td>
			<td><input type="text" id="nbtMultiSelectColumn<?php echo $select['id']; ?>" value="<?php echo $select['dbname']; ?>" onblur="nbtUpdateSubMultiSelectOptionColumn(<?php echo $tablesubelementid; ?>, <?php echo $select['id']; ?>, '<?php echo $select['dbname']; ?>');"></td>
			<td><button onclick="nbtMoveSubMultiSelectOption(<?php echo $tablesubelementid; ?>, <?php echo $select['id']; ?>, 1);">&#8593;</button> <button onclick="nbtMoveSubMultiSelectOption(<?php echo $tablesubelementid; ?>, <?php echo $select['id']; ?>, -1);">&#8595;</button></td>
			<td>
				<button onclick="$(this).fadeOut(0);$('#nbtSelectDelete<?php echo $select['id']; ?>').fadeIn();">Delete</button>
				<button id="nbtSelectDelete<?php echo $select['id']; ?>" class="nbtHidden" onclick="nbtRemoveSubMultiSelectOption(<?php echo $tablesubelementid; ?>, <?php echo $select['id']; ?>);">For real</button>
			</td>
		</tr><?php
		
	}

	?><tr>
		<td colspan="4"><button onclick="nbtAddSubMultiSelectOption(<?php echo $tablesubelementid; ?>);">Add new option</button></td>
	</tr>
</table>
