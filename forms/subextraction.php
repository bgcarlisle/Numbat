<?php

$subelements = nbt_get_sub_extraction_elements_for_elementid ( $subelementid );

foreach ( $subelements as $subelement ) {

?><div style="border: 1px solid #999; border-radius: 3px; padding: 10px; margin: 5px 0 20px 0; background: #ddd;" id="nbtSubElement<?php echo $subelement['id']; ?>" class="nbtSubElementEditor" subelementid="<?php echo $subelement['id']; ?>">
    <button style="float: right;" onclick="$(this).fadeOut(0);$('#nbtDeleteSubElement<?php echo $subelement['id']; ?>').fadeIn();">Delete</button>
    <button class="nbtHidden" id="nbtDeleteSubElement<?php echo $subelement['id']; ?>" style="float: right;" onclick="nbtDeleteSubElement(<?php echo $subelement['id']; ?>);">For real</button>
    <?php

    switch ( $subelement['type'] ) {

	case "open_text":

	    echo '<h4>Open text field</h4>';
	    echo '<p>Display name: <input type="text" id="nbtSubElementDisplayName' . $subelement['id'] . '" value="' . $subelement['displayname'] . '" onblur="nbtChangeSubDisplayName(' . $subelement['id'] . ');"></p>';
    ?>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column name: <input type="text" id="nbtSubElementColumnName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['dbname']; ?>" onblur="nbtChangeSubColumnName(<?php echo $subelement['id']; ?>);"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>

	<p>Regex validation: <input type="text" id="nbtSubElementRegex<?php echo $subelement['id']; ?>" value="<?php echo $subelement['regex']; ?>" onblur="nbtChangeSubElementRegex(<?php echo $subelement['id']; ?>);" maxlength="500"></p>
	<p class="nbtFinePrint">Will not save extractor input unless the text matches the regex supplied; leave blank for no regex validation</p>
	<p>Prompt extractor to copy value from the previous sub-extraction: <a href="#" id="nbtSubElementPromptCopyFromPrev<?php echo $subelement['id']; ?>" onclick="event.preventDefault();nbtSubElementPromptCopyFromPrevToggle(<?php echo $subelement['id']; ?>);" class="nbtTextOptionSelect<?php if ($subelement['copypreviousprompt'] == 1) { echo " nbtTextOptionChosen"; } ?>">Show prompt</a></p>
	<?php							       
	break;

	case "text_area":

	echo '<h4>Text area field</h4>';
	echo '<p>Display name: <input type="text" id="nbtSubElementDisplayName' . $subelement['id'] . '" value="' . $subelement['displayname'] . '" onblur="nbtChangeSubDisplayName(' . $subelement['id'] . ');"></p>';

	?>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column name: <input type="text" id="nbtSubElementColumnName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['dbname']; ?>" onblur="nbtChangeSubColumnName(<?php echo $subelement['id']; ?>);"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
	
	<?php
	
	break;

	case "single_select":

	?><h4>Single select</h4>
	<p>Display name: <input type="text" id="nbtSubElementDisplayName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['displayname']; ?>" onblur="nbtChangeSubDisplayName(<?php echo $subelement['id']; ?>);"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column name: <input type="text" id="nbtSubElementColumnName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['dbname']; ?>" onblur="nbtChangeSubColumnName(<?php echo $subelement['id']; ?>);"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
	<p>Prompt extractor to copy value from the previous sub-extraction: <a href="#" id="nbtSubElementPromptCopyFromPrev<?php echo $subelement['id']; ?>" onclick="event.preventDefault();nbtSubElementPromptCopyFromPrevToggle(<?php echo $subelement['id']; ?>);" class="nbtTextOptionSelect<?php if ($subelement['copypreviousprompt'] == 1) { echo " nbtTextOptionChosen"; } ?>">Show prompt</a></p>
	<p>Options</p>
	<p class="nbtFinePrint">Display name will appear on extraction form; DB name will appear on exported spreadsheet</p>
	<div id="nbtSubSingleSelectOptionsTable<?php echo $subelement['id']; ?>">
	    <?php

	    $tablesubelementid = $subelement['id'];

	    include ('./subsingleselectoptionstable.php');

	    ?>
	</div>
	<?php

	break;

	case "multi_select":

	?><h4>Multi select</h4>
	<p>Display name: <input type="text" id="nbtSubElementDisplayName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['displayname']; ?>" onblur="nbtChangeSubDisplayName(<?php echo $subelement['id']; ?>);"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column prefix: <input type="text" id="nbtSubElementColumnPrefix<?php echo $subelement['id']; ?>" value="<?php echo $subelement['dbname']; ?>" onblur="nbtChangeSubMultiSelectColumnPrefix(<?php echo $subelement['id']; ?>);"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
	<p>Prompt extractor to copy value from the previous sub-extraction: <a href="#" id="nbtSubElementPromptCopyFromPrev<?php echo $subelement['id']; ?>" onclick="event.preventDefault();nbtSubElementPromptCopyFromPrevToggle(<?php echo $subelement['id']; ?>);" class="nbtTextOptionSelect<?php if ($subelement['copypreviousprompt'] == 1) { echo " nbtTextOptionChosen"; } ?>">Show prompt</a></p>
	<p>Options</p>
	<p class="nbtFinePrint">Display name will appear on extraction form; DB name will appear on exported spreadsheet</p>
	<div id="nbtSubMultiSelectOptionsTable<?php echo $subelement['id']; ?>">
	    <?php

	    $tablesubelementid = $subelement['id'];

	    include ('./submultiselectoptionstable.php');

	    ?>
	</div>
	<?php

	break;

	case "tags":

	echo '<h4>Tags</h4>';
	echo '<p class="nbtFinePrint">Extractors will be prompted a searchable list of text tags that can be selected from, or added to on the fly. You may optionally pre-populate the list of tag prompts by adding a semicolon-delimited list in the box below, or you can generate this list at the point of extraction. Tags may not be empty, and may not contain semicolons or line breaks.</p>';
	
	echo '<p>Display name: <input type="text" id="nbtSubElementDisplayName' . $subelement['id'] . '" value="' . $subelement['displayname'] . '" onblur="nbtChangeSubDisplayName(' . $subelement['id'] . ');"></p>';
	echo '<p class="nbtFinePrint">Will appear on extraction form</p>';

	echo '<p>Semicolon-delimited tag prompts:</p>';
	echo '<textarea style="width: 100%; height: 80px;" id="nbtSubElementTagsPrompts' . $subelement['id']. '" onblur="nbtChangeSubTagsPrompts(' . $subelement['id'] . ');">' . $subelement['tagprompts'] . '</textarea>';
	
	echo '<p>Column name: <input type="text" id="nbtSubElementColumnName' . $subelement['id'] . '" value="' . $subelement['dbname'] . '" onblur="nbtChangeSubColumnName(' . $subelement['id'] . ');"></p>';
	echo '<p class="nbtFinePrint">Will appear on exported spreadsheet</p>';
	
	break;
					
			case "date_selector":

				?><h4>Date selector</h4>
				<p>Display name: <input type="text" id="nbtSubElementDisplayName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['displayname']; ?>" onblur="nbtChangeSubDisplayName(<?php echo $subelement['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Column name: <input type="text" id="nbtSubElementColumnName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['dbname']; ?>" onblur="nbtChangeSubColumnName(<?php echo $subelement['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
				<p>Prompt extractor to copy value from the previous sub-extraction: <a href="#" id="nbtSubElementPromptCopyFromPrev<?php echo $subelement['id']; ?>" onclick="event.preventDefault();nbtSubElementPromptCopyFromPrevToggle(<?php echo $subelement['id']; ?>);" class="nbtTextOptionSelect<?php if ($subelement['copypreviousprompt'] == 1) { echo " nbtTextOptionChosen"; } ?>">Show prompt</a></p><?php

			break;

			case "table_data":

				?><h4>Table data</h4>
				<p class="nbtFinePrint">Cells in tables of this type may only contain up to 200 characters each.</p>
				<p>Table display name: <input type="text" id="nbtSubElementDisplayName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['displayname']; ?>" onblur="nbtChangeSubDisplayName(<?php echo $subelement['id']; ?>);" maxlength="200"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Table suffix: <input type="text" id="nbtSubTableSuffix<?php echo $subelement['id']; ?>" value="<?php echo $subelement['dbname']; ?>" onblur="nbtChangeSubTableSuffix(<?php echo $subelement['id']; ?>);" maxlength="25"></p>
				<p class="nbtFinePrint">Suffix for table in database</p>
				<p>Table columns</p>
				<p class="nbtFinePrint">Display name will appear as a column of the table on extraction form; DB name will appear on exported spreadsheet</p>
				<div id="nbtSubTableDataColumnsTable<?php echo $subelement['id']; ?>"><?php

				$tableelementid = $subelement['id'];

				include ('./subtabledata.php');

				?></div><?php

			break;

			case "reference_data":

				?><h4>Reference data prompt</h4>
				<p class="nbtFinePrint">A "reference data prompt" does not accept input from the extractor. Rather, it displays data about the sub-extraction. To populate this field, select "Import extractions" from the Numbat menu.</p>
				<p class="nbtFinePrint">By default, the extractors will be presented with whatever data this field has been populated with, however you can change the way that it is presented by adding text before or after it and using `$data` (without the quotes) to indicate where the data should be inserted.</p>
				<p>Display name: <input type="text" id="nbtSubElementDisplayName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['displayname']; ?>" onblur="nbtChangeSubDisplayName(<?php echo $subelement['id']; ?>);" maxlength="200"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Column name: <input type="text" id="nbtSubElementColumnName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['dbname']; ?>" onblur="nbtChangeSubColumnName(<?php echo $subelement['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
				<p>Reference data:</p>
				<textarea style="width: 100%; height: 80px;" id="nbtElementColumnName<?php echo $subelement['id']; ?>" onblur="nbtChangeSubRefdataFormat(<?php echo $subelement['id']; ?>);" maxlength="2500"><?php echo $subelement['reference_data_format']; ?></textarea>
				<p class="nbtFinePrint">The text `$data` (without the quotes) will be replaced with the reference data.</p>
<?php

												     

			break;

		}

		?>
		<p>Codebook</p>
		<p class="nbtFinePrint">Will appear on extraction sheet when (?) is clicked</p>
		<textarea style="width: 100%; height: 80px;" id="nbtSubElementCodebook<?php echo $subelement['id']; ?>" onblur="nbtChangeSubElementCodebook(<?php echo $subelement['id']; ?>);"><?php echo $subelement['codebook']; ?></textarea>
		<div class="nbtConditionalDisplayEditor" style="border: 1px solid #999; border-radius: 3px; padding: 10px; margin: 5px 0 20px 0; background-color: #eee;">
		    <p>When the form is first opened, this item should be:</p>
		    <a id="nbtCondDispStartStatusVisibleSub<?php echo $subelement['id']; ?>" class="nbtTextOptionSelect<?php if ($subelement['startup_visible'] == 1) { echo ' nbtTextOptionChosen'; } ?>" onclick="event.preventDefault();nbtSubElementToggleStartupVisible(<?php echo $subelement['id']; ?>);">Visible</a>
		    <a id="nbtCondDispStartStatusHiddenSub<?php echo $subelement['id']; ?>" class="nbtTextOptionSelect<?php if ($subelement['startup_visible'] != 1) { echo ' nbtTextOptionChosen'; } ?>" onclick="event.preventDefault();nbtSubElementToggleStartupVisible(<?php echo $subelement['id']; ?>);">Hidden</a>
		    <p id="nbtConditionLogicDescriptionSub<?php echo $subelement['id']; ?>" <?php if ($subelement['startup_visible'] == 1) { echo ' class="nbtHidden"'; } ?>>
			Show this element when
			<select id="nbtSubCondDispLogic<?php echo $subelement['id']; ?>" onchange="nbtUpdateSubCondDispLogic(<?php echo $subelement['id']; ?>);">
			    <option value="any"<?php if ($subelement['conditional_logical_operator'] == "any") { echo " selected"; } ?>>any</option>
			    <option value="all"<?php if ($subelement['conditional_logical_operator'] == "all") { echo " selected"; } ?>>all</option>
			</select>
			of the following conditions are met:
		    </p>
		    <div id="nbtCondDispEventsContainerSub<?php echo $subelement['id']; ?>" <?php if ($subelement['startup_visible'] == 1) { echo ' class="nbtHidden"'; } ?>>
			<?php
			$subelementid = $subelement['id'];
			include (ABS_PATH . "forms/conditionals-subextractions.php");
			?>
		    </div>
		    <button id="nbtAddConditionalDisplayEventSub<?php echo $subelement['id']; ?>" style="margin-top: 10px;" onclick="nbtAddCondDispEventSub(<?php echo $subelement['id']; ?>);" <?php if ($subelement['startup_visible'] == 1) { echo ' class="nbtHidden"'; } ?>>Add condition</button>
		    <p id="nbtDestructiveHidingDescriptionSub<?php echo $subelement['id']; ?>" <?php if ($subelement['startup_visible'] == 1) { echo ' class="nbtHidden"'; } ?>>
			In the case that this element is hidden by a conditional display event after a response has been entered:
			<select id="nbtSubCondDispHideAction<?php echo $subelement['id']; ?>" onchange="nbtUpdateSubCondDispHideAction(<?php echo $subelement['id']; ?>);">
			    <option value="1"<?php if ($subelement['destructive_hiding'] == 1) { echo " selected"; } ?>>Clear response</option>
			    <option value="0"<?php if ($subelement['destructive_hiding'] == 0) { echo " selected"; } ?>>Preserve response</option>
			</select>
		    </p>
		</div>
		<p id="nbtSubElementFeedback<?php echo $subelement['id']; ?>" class="nbtHidden">&nbsp;</p>
	</div><?php

}

?>
