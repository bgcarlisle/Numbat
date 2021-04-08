<button onclick="collapseAllFormElements();">Collapse all form elements</button>
<button onclick="expandAllFormElements();">Expand all form elements</button>
<p>Drag elements to re-order.</p>
<?php

$elements = nbt_get_elements_for_formid ($_GET['id']);

if ( count ( $elements ) > 0 ) {

    foreach ( $elements as $element ) {

	echo '<div class="nbtFormEditorElement" id="nbtFormElement' . $element['id'] . '" elementid="' . $element['id'] . '">'; ?>
    <div style="border: 1px solid #999; border-radius: 3px; padding: 10px; margin: 5px 0 20px 0; background-color: #eee;">
	<button style="float: right;" id="nbtFormEditorElementDelete<?php echo $element['id']; ?>" onclick="$(this).fadeOut(0);$('#nbtDeleteFormElement<?php echo $element['id']; ?>').fadeIn();">Delete</button>
    <div class="nbtFormElementDeleterContainer nbtHidden" id="nbtDeleteFormElement<?php echo $element['id']; ?>" class="nbtHidden">
	<button style="float: right;" onclick="$('#nbtDeleteFormElement<?php echo $element['id']; ?>').fadeOut(0);$('#nbtFormEditorElementDelete<?php echo $element['id']; ?>').fadeIn();">Do not delete</button>
	<button style="float: right;" onclick="nbtDeleteFormElement(<?php echo $element['id']; ?>);">Yes, delete</button>
    </div>
    <button style="margin: 0 10px; float: right;" class="nbtFormEditorCollapse">Collapse/expand</button>
    <?php

    switch ( $element['type'] ) {

	case "section_heading":

    ?><h4>Section heading <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form. This form element is purely aesthetic, and will not appear on the exported spreadsheet.</p>
	<?php

	break;

	case "open_text":

	?><h4>Open text field <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p class="nbtFinePrint">Maximum entry length: 200 characters</p>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>);" maxlength="50"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
	<p>Regex validation: <input type="text" id="nbtElementRegex<?php echo $element['id']; ?>" value="<?php echo $element['regex']; ?>" onblur="nbtChangeRegex(<?php echo $element['id']; ?>);" maxlength="500"></p>
	<p class="nbtFinePrint">Will not save extractor input unless the text matches the regex supplied; leave blank for no regex validation</p>
	
	<?php
	
	break;

	case "text_area":

	?><h4>Text area field <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p class="nbtFinePrint">Maximum entry length: 5000 characters</p>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>);" maxlength="50"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
	<?php

	break;

	case "single_select":

	?><h4>Single select <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>);" maxlength="50"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
	<p>Options</p>
	<p class="nbtFinePrint">Display name will appear on extraction form; DB name will appear on exported spreadsheet</p>
	<div id="nbtSingleSelectOptionsTable<?php echo $element['id']; ?>">
	    <?php

	    $tableelementid = $element['id'];

	    include ('./singleselectoptionstable.php');

	    ?>
	</div>
	<?php

	break;

	case "multi_select":

	?><h4>Multi select <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column prefix: <input type="text" id="nbtElementColumnPrefix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeMultiSelectColumnPrefix(<?php echo $element['id']; ?>);" maxlength="25"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
	<p>Options</p>
	<p class="nbtFinePrint">Display name will appear on extraction form; DB name will appear on exported spreadsheet</p>
	<div id="nbtMultiSelectOptionsTable<?php echo $element['id']; ?>">
	    <?php

	    $tableelementid = $element['id'];

	    include ('./multiselectoptionstable.php');

	    ?>
	</div>
	<?php

	break;

	case "table_data":

	?><h4>Table data <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p class="nbtFinePrint">Cells in tables of this type may only contain up to 200 characters each. If you need more than 200 characters per cell, use a "large table data" element.</p>
	<p>Table display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Table suffix: <input type="text" id="nbtTableSuffix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeTableSuffix(<?php echo $element['id']; ?>);" maxlength="25"></p>
	<p class="nbtFinePrint">Suffix for table in database</p>
	<p>Table columns</p>
	<p class="nbtFinePrint">Display name will appear as a column of the table on extraction form; DB name will appear on exported spreadsheet</p>
	<div id="nbtTableDataColumnsTable<?php echo $element['id']; ?>">
	    <?php

	    $tableelementid = $element['id'];
	    $tableformat = "table_data";

	    include ('./tabledata.php');

	    ?>
	</div>
	<?php

	break;

	case "ltable_data":

	?><h4>Large table data <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p class="nbtFinePrint">Cells in tables of this type may contain text more than 200 characters in length.</p>
	<p>Table display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Table suffix: <input type="text" id="nbtTableSuffix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeTableSuffix(<?php echo $element['id']; ?>);" maxlength="25"></p>
	<p class="nbtFinePrint">Suffix for table in database</p>
	<p>Table columns</p>
	<p class="nbtFinePrint">Display name will appear as a column of the table on extraction form; DB name will appear on exported spreadsheet</p>
	<div id="nbtTableDataColumnsTable<?php echo $element['id']; ?>">
	    <?php

	    $tableelementid = $element['id'];
	    $tableformat = "ltable_data";

	    include ('./tabledata.php');

	    ?>
	</div>
	<?php

	break;

	case "citations":

	?><h4>Citation selector <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Table suffix: <input type="text" id="nbtCitationSelectorSuffix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeCitationSelectorSuffix(<?php echo $element['id']; ?>);" maxlength="25"></p>
	<p class="nbtFinePrint">Suffix for citations table in database</p>
	<p>Citation properties</p>
	<p class="nbtFinePrint">You can add properties to be collected regarding each citation. Display name will appear as an open text field for each citation added on the extraction form; DB name will appear on exported spreadsheet. For elements with "reminder" selected, the previously chosen values for that user and reference ID within the same reference set will be displayed when a reference is selected.</p>
	<div id="nbtCitationSelectorTable<?php echo $element['id']; ?>">
	    <?php

	    $citationelementid = $element['id'];

	    include ('./citationproperties.php');

	    ?>
	</div>
	<?php

	break;

	case "country_selector":

	?><h4>Country selector <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>);" maxlength="50"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
	<?php

	break;

	case "date_selector":

	?><h4>Date selector <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeDateColumnName(<?php echo $element['id']; ?>);" maxlength="50"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
	<?php

	break;

	case "sub_extraction":

	?><h4>Sub-extraction <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p class="nbtFinePrint">A sub-extraction is a form element that contains other form elements and can be repeated by the extractor as many times as necessary within an extraction. E.g. if you were extracting a set of papers that each contained a different number of experiments to be extracted, you could make an "experiment" sub-extraction that the extractor could repeat as many times as she needs.</p>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Table suffix: <input type="text" id="nbtTableSuffix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeSubExtractionSuffix(<?php echo $element['id']; ?>);" maxlength="25"></p>
	<p class="nbtFinePrint">Suffix for table in database</p>
	<p>Sub-extraction elements</p>
	<div class="nbtSubExtractionEditor" id="nbtSubExtractionElements<?php echo $element['id']; ?>" style="margin-bottom: 10px;">
	    <?php

	    $subelementid = $element['id'];

	    include ('./subextraction.php');

	    ?>
	</div>
	<div><button onclick="$(this).fadeOut(0);$('#nbtNewSubElementSelector<?php echo $subelementid; ?>').fadeIn();">Add new sub-extraction element</button>

	    <div id="nbtNewSubElementSelector<?php echo $subelementid; ?>" class="nbtHidden">
		<h3>Add new sub-extraction element</h3>
		<button onclick="nbtAddNewSubOpenText(<?php echo $subelementid; ?>);">Open text</button>
		<button onclick="nbtAddNewSubDateSelector(<?php echo $subelementid; ?>);">Date selector</button>
		<button onclick="nbtAddNewSubSingleSelect(<?php echo $subelementid; ?>);">Single select</button>
		<button onclick="nbtAddNewSubMultiSelect(<?php echo $subelementid; ?>);">Multi select</button>
		<button onclick="nbtAddNewSubTable(<?php echo $subelementid; ?>);">Table data</button>
	    </div>
	</div>


	<?php

	break;

	case "assignment_editor":

	?><h4>Assignment editor <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">This element will allow an extractor to assign the reference being extracted to herself or another user, using this or a different form.</p>
	<?php

	break;

	case "reference_data":

	?><h4>Reference data prompt <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p class="nbtFinePrint">A "reference data prompt" does not accept input from the extractor and will not appear on the extraction export. Rather, it displays data about the reference being extracted that is populated from the selected column in the reference set table. For example, if the "year" column from the reference set were chosen in the field below, the year of the reference being extracted would appear at this point in the extraction.</p>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Reference data:</p>
	<textarea style="width: 100%; height: 80px;" id="nbtElementColumnName<?php echo $element['id']; ?>" onblur="nbtChangeRefdataColumnName(<?php echo $element['id']; ?>);" maxlength="2500"><?php echo $element['columnname']; ?></textarea>
	<p class="nbtFinePrint">Enter one or more column names from the reference set, preceded by a dollar sign, and they will be replaced with the value of that column for the reference being extracted. E.g. if you have a "phase" column on your reference set and you enter $phase below, it will be replaced by the value of that column for that reference.</p>
	<?php

	break;

	case "prev_select":

	?><h4>Previously extracted entry selector <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>
	<p class="nbtFinePrint">Maximum entry length: 200 characters; this element acts like an Open text element, except that it also provides a drop-down menu of all the unique entries provided by any extractor from within the same reference set</p>
	<p>Display name: <input type="text" class="nbtDisplayName" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
	<p class="nbtFinePrint">Will appear on extraction form</p>
	<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>);" maxlength="50"></p>
	<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
	<?php

	break;

	case "timer":

	echo '<h4>Extraction timer <span class="nbtDisplayNameHidden nbtHidden">&nbsp;</span></h4>';

	echo '<p>Numbat automatically times all extractions starting from the first time a user opens the extraction, until the first time they click "Complete". This element displays a timer to the user when the extraction is on-going, and allows the user to re-start the timer.</p>';

	break;
	}

	?>
	<p>Codebook</p>
	<p class="nbtFinePrint">Will appear on extraction sheet when (?) is clicked</p>
	<textarea style="width: 100%; height: 80px;" id="nbtElementCodebook<?php echo $element['id']; ?>" onblur="nbtChangeElementCodebook(<?php echo $element['id']; ?>);"><?php echo $element['codebook']; ?></textarea>
	<div class="nbtConditionalDisplayEditor" style="border: 1px solid #999; border-radius: 3px; padding: 10px; margin: 5px 0 20px 0; background-color: #eee;">
	    <p>When the form is first opened, this item should be </p>
	    <a id="nbtCondDispStartStatusVisible<?php echo $element['id']; ?>" class="nbtTextOptionSelect<?php if ($element['startup_visible'] == 1) { echo ' nbtTextOptionChosen'; } ?>" onclick="event.preventDefault();nbtFormElementToggleStartupVisible(<?php echo $element['id']; ?>);">Visible</a>
	    <a id="nbtCondDispStartStatusHidden<?php echo $element['id']; ?>" class="nbtTextOptionSelect<?php if ($element['startup_visible'] != 1) { echo ' nbtTextOptionChosen'; } ?>" onclick="event.preventDefault();nbtFormElementToggleStartupVisible(<?php echo $element['id']; ?>);">Hidden</a>
	    <div id="nbtCondDispEventsContainer"></div>
	    <button>Add conditional display event</button>
	</div>
	<p id="nbtFormElementFeedback<?php echo $element['id']; ?>" class="nbtHidden">&nbsp;</p>
</div>
<button style="margin-bottom: 10px;" onclick="$(this).fadeOut(0);$('#nbtNewElementSelector<?php echo $element['id']; ?>').fadeIn();">Add new element</button>
<div id="nbtNewElementSelector<?php echo $element['id']; ?>" class="nbtHidden">
    <h3>Add new form element</h3>
    <button onclick="nbtAddNewSectionHeading(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Section heading</button>
    <button onclick="nbtAddNewOpenText(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Open text</button>
    <button onclick="nbtAddNewTextArea(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Text area</button>
    <button onclick="nbtAddNewDateSelector(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Date selector</button>
    <button onclick="nbtAddNewSingleSelect(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Single select</button>
    <button onclick="nbtAddNewMultiSelect(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Multi select</button>
    <button onclick="nbtAddNewTableData(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>, 'table_data');">Table data</button>
    <button onclick="nbtAddNewTableData(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>, 'ltable_data');">Large table data</button>
    <button onclick="nbtAddNewCountrySelector(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Country selector</button>
    <button onclick="nbtAddNewCitationSelector(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Citations</button>
    <button onclick="nbtAddNewSubExtraction(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Sub-extraction</button>
    <button onclick="nbtAddNewAssignmentEditor(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Assignment editor</button>
    <button onclick="nbtAddNewRefdata(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>)">Reference data prompt</button>
    <button onclick="nbtAddNewPrevSelect(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>)">Previously extracted entry selector</button>
    <button onclick="nbtAddNewExtractionTimer(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>)">Extraction timer</button>
</div>
    <?php

    echo '</div>';

    }

    } else {

	$element['id'] = 0;

    ?>
	<button style="margin-bottom: 10px;" onclick="$(this).fadeOut(0);$('#nbtNewElementSelector<?php echo $element['id']; ?>').fadeIn();">Add new element</button>
	<div id="nbtNewElementSelector<?php echo $element['id']; ?>" class="nbtHidden">
	    <h3>Add new form element</h3>
	    <button onclick="nbtAddNewSectionHeading(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Section heading</button>
	    <button onclick="nbtAddNewOpenText(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Open text</button>
	    <button onclick="nbtAddNewTextArea(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Text area</button>
	    <button onclick="nbtAddNewDateSelector(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Date selector</button>
	    <button onclick="nbtAddNewSingleSelect(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Single select</button>
	    <button onclick="nbtAddNewMultiSelect(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Multi select</button>
	    <button onclick="nbtAddNewTableData(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>, 'table_data');">Table data</button>
	    <button onclick="nbtAddNewTableData(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>, 'ltable_data');">Large table data</button>
	    <button onclick="nbtAddNewCountrySelector(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Country selector</button>
	    <button onclick="nbtAddNewCitationSelector(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Citations</button>
	    <button onclick="nbtAddNewSubExtraction(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Sub-extraction</button>
	    <button onclick="nbtAddNewAssignmentEditor(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>);">Assignment editor</button>
	    <button onclick="nbtAddNewRefdata(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>)">Reference data prompt</button>
	    <button onclick="nbtAddNewPrevSelect(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>)">Previously extracted entry selector</button>
	    <button onclick="nbtAddNewExtractionTimer(<?php echo $_GET['id']; ?>, <?php echo $element['id']; ?>)">Extraction timer</button>
	</div>
    <?php

    }

    ?>
    
