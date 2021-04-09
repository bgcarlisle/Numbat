<?php

$events = nbt_get_conditional_events ($elementid);

$element = nbt_get_form_element_for_elementid ($elementid);

$all_elements = nbt_get_elements_for_formid ($element['formid']);

if ($events) { foreach ($events as $event) { ?>

    <div style="border: 1px solid #999; border-radius: 3px; padding: 10px; margin: 10px 0 10px 0; background-color: #ddd;" id="nbtCondDispEvent<?php echo $event['id']; ?>">
	<button id="nbtRemoveCDEvent<?php echo $event['id']; ?>" onclick="$('#nbtRemoveCDEventContainer<?php echo $event['id']; ?>').slideDown();$('#nbtRemoveCDEvent<?php echo $event['id']; ?>').slideUp(0);" style="float: right;">Remove</button>
	<div class="nbtHidden" id="nbtRemoveCDEventContainer<?php echo $event['id']; ?>" style="float: right;">
	    <button onclick="nbtRemoveCondDispEvent(<?php echo $elementid ?>, <?php echo $event['id']; ?>);">Confirm remove</button>
	    <button onclick="$('#nbtRemoveCDEventContainer<?php echo $event['id']; ?>').slideUp(0);$('#nbtRemoveCDEvent<?php echo $event['id']; ?>').slideDown();">Cancel</button>
	</div>

	<select onchange="nbtUpdateCondDispTriggerElement(<?php echo $event['id']; ?>);" id="nbtCondDispTriggerElement<?php echo $event['id']; ?>">
	    <option value="ns">Choose a form element</option>
	    <?php

	    foreach ($all_elements as $ele) {

		if ($ele['id'] != $event['elementid']) {

		    switch ($ele['type']) {

			case "single_select":
			case "multi_select":

			    if ($event['trigger_element'] == $ele['id']) {
				echo '<option value="' . $ele['id'] . '" selected>';
				$selected_trigger_element = $ele['id'];
			    } else {
				echo '<option value="' . $ele['id'] . '">';
			    }
			    echo $ele['displayname'];
			    echo "</option>";
			    
			    break;
			    
		    }
		    
		}
		
	    }

	    ?>
	</select>

	<select onchange="nbtUpdateCondDispType(<?php echo $event['id']; ?>);" id="nbtCondDispType<?php echo $event['id']; ?>">
	    <option value="is"<?php if ($event['type'] == "is") { echo " selected"; } ?>>is</option>
	    <option value="is-not"<?php if ($event['type'] == "is-not") { echo " selected"; } ?>>is not</option>
	    <option value="has-response"<?php if ($event['type'] == "has-response") { echo " selected"; } ?>>has a response</option>
	    <option value="no-response"<?php if ($event['type'] == "no-response") { echo " selected"; } ?>>has no response</option>
	</select>

	<?php

	$options = nbt_get_all_select_options_for_element ($selected_trigger_element);

	switch ($event['type']) {
	    case "is":
	    case "is-not":
		$show_trigger_options = TRUE;
		break;
	    case "has-response":
	    case "no-response":
		$show_trigger_options = FALSE;
		break;
	}

	?>

	<select onchange="nbtUpdateCondDispTriggerOption(<?php echo $event['id']; ?>);" id="nbtCondDispTriggerOption<?php echo $event['id']; ?>"<?php if (! $show_trigger_options) { echo ' class="nbtHidden"'; } ?>>
	    <option value="ns">Choose an option</option>
	    <?php

	    foreach ($options as $opt) {
		if ($event['trigger_option'] == $opt['id']) {
		    echo '<option value="' . $opt['id'] . '" selected>';
		} else {
		    echo '<option value="' . $opt['id'] . '">';
		}
		echo $opt['displayname'];
		echo '</option>';
	    }

	    ?>
	</select>
	
    </div>
    
<?php } } else { echo "<p>[No conditions selected]</p>"; } ?>
