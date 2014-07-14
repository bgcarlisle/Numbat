<table class="nbtTabledData">
	<tr class="nbtTableHeaders">
		<td>Display name</td>
		<td>DB name</td>
		<td style="width: 80px;">Move</td>
		<td style="width: 80px;">Delete</td>
	</tr>
	<?php

	$citationcolumns = nbt_get_all_columns_for_citation_selector ( $citationelementid );
	
	foreach ( $citationcolumns as $column ) {
		
		?><tr>
			<td><input type="text" id="nbtCitationPropertyDisplay<?php echo $column['id']; ?>" value="<?php echo $column['displayname']; ?>" onblur="nbtUpdateCitationPropertyDisplay(<?php echo $citationelementid; ?>, <?php echo $column['id']; ?>);"></td>
			<td><input type="text" id="nbtCitationPropertyDB<?php echo $column['id']; ?>" value="<?php echo $column['dbname']; ?>" onblur="nbtUpdateCitationPropertyDB(<?php echo $citationelementid; ?>, <?php echo $column['id']; ?>);"></td>
			<td><button onclick="nbtMoveCitationProperty(<?php echo $citationelementid; ?>, <?php echo $column['id']; ?>, 1);">&#8593;</button> <button onclick="nbtMoveCitationProperty(<?php echo $citationelementid; ?>, <?php echo $column['id']; ?>, -1);">&#8595;</button></td>
			<td>
				<button onclick="$(this).fadeOut(0);$('#nbtColumnDelete<?php echo $column['id']; ?>').fadeIn();">Delete</button>
				<button id="nbtCitationPropertyDelete<?php echo $column['id']; ?>" class="nbtHidden" onclick="nbtRemoveCitationProperty(<?php echo $citationelementid; ?>, <?php echo $column['id']; ?>);">For real</button>
			</td>
		</tr><?php
		
	}

	?><tr>
		<td colspan="4"><button onclick="nbtAddCitationProperty(<?php echo $citationelementid; ?>);">Add new citation property</button></td>
	</tr>
</table>