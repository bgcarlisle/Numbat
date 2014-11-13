<?php

$elements = nbt_get_elements_for_formid ($_GET['id']);

if ( count ( $elements ) > 0 ) {

	foreach ( $elements as $element ) {

		?><div id="nbtFormElement<?php echo $element['id']; ?>">
			<div style="border: 1px solid #999; border-radius: 3px; padding: 10px; margin: 5px 0 20px 0;">
				<button style="float: right;" onclick="$(this).fadeOut(0);$('#nbtDeleteFormElement<?php echo $element['id']; ?>').fadeIn();">Delete</button>
				<button class="nbtHidden" id="nbtDeleteFormElement<?php echo $element['id']; ?>" style="float: right;" onclick="nbtDeleteFormElement(<?php echo $element['id']; ?>);">For real</button>
				<?php

				switch ( $element['type'] ) {

					case "section_heading":

						?><h4>Section heading</h4>
						<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form. This form element is purely aesthetic, and will not appear on the exported spreadsheet.</p><?php

					break;

					case "open_text":

						?><h4>Open text field</h4>
						<p class="nbtFinePrint">Maximum entry length: 200 characters</p>
						<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form</p>
						<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>, 200);" maxlength="50"></p>
						<p class="nbtFinePrint">Will appear on exported spreadsheet</p><?php

					break;

					case "text_area":

						?><h4>Text area field</h4>
						<p class="nbtFinePrint">Maximum entry length: 5000 characters</p>
						<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form</p>
						<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>, 5000);" maxlength="50"></p>
						<p class="nbtFinePrint">Will appear on exported spreadsheet</p><?php

					break;

					case "single_select":

						?><h4>Single select</h4>
						<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form</p>
						<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>, 50);" maxlength="50"></p>
						<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
						<p>Options</p>
						<p class="nbtFinePrint">Display name will appear on extraction form; DB name will appear on exported spreadsheet; Other form elements marked with this toggle class will appear only if this element is selected</p>
						<div id="nbtSingleSelectOptionsTable<?php echo $element['id']; ?>"><?php

						$tableelementid = $element['id'];

						include ('./singleselectoptionstable.php');

						?></div><?php

					break;

					case "multi_select":

						?><h4>Multi select</h4>
						<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form</p>
						<p>Column prefix: <input type="text" id="nbtElementColumnPrefix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeMultiSelectColumnPrefix(<?php echo $element['id']; ?>);" maxlength="25"></p>
						<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
						<p>Options</p>
						<p class="nbtFinePrint">Display name will appear on extraction form; DB name will appear on exported spreadsheet; Other form elements marked with this toggle class will appear only if this element is selected</p>
						<div id="nbtMultiSelectOptionsTable<?php echo $element['id']; ?>"><?php

						$tableelementid = $element['id'];

						include ('./multiselectoptionstable.php');

						?></div><?php

					break;

					case "table_data":

						?><h4>Table data</h4>
						<p class="nbtFinePrint">Cells in tables of this type may only contain up to 200 characters each. If you need more than 200 characters per cell, use a "large table data" element.</p>
						<p>Table display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form</p>
						<p>Table suffix: <input type="text" id="nbtTableSuffix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeTableSuffix(<?php echo $element['id']; ?>);" maxlength="25"></p>
						<p class="nbtFinePrint">Suffix for table in database</p>
						<p>Table columns</p>
						<p class="nbtFinePrint">Display name will appear as a column of the table on extraction form; DB name will appear on exported spreadsheet</p>
						<div id="nbtTableDataColumnsTable<?php echo $element['id']; ?>"><?php

						$tableelementid = $element['id'];
						$tableformat = "table_data";

						include ('./tabledata.php');

						?></div><?php

					break;

					case "ltable_data":

						?><h4>Large table data</h4>
						<p class="nbtFinePrint">Cells in tables of this type may contain text more than 200 characters in length.</p>
						<p>Table display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form</p>
						<p>Table suffix: <input type="text" id="nbtTableSuffix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeTableSuffix(<?php echo $element['id']; ?>);" maxlength="25"></p>
						<p class="nbtFinePrint">Suffix for table in database</p>
						<p>Table columns</p>
						<p class="nbtFinePrint">Display name will appear as a column of the table on extraction form; DB name will appear on exported spreadsheet</p>
						<div id="nbtTableDataColumnsTable<?php echo $element['id']; ?>"><?php

						$tableelementid = $element['id'];
						$tableformat = "ltable_data";

						include ('./tabledata.php');

						?></div><?php

					break;

					case "citations":

						?><h4>Citation selector</h4>
						<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form</p>
						<p>Table suffix: <input type="text" id="nbtCitationSelectorSuffix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeCitationSelectorSuffix(<?php echo $element['id']; ?>);" maxlength="25"></p>
						<p class="nbtFinePrint">Suffix for citations table in database</p>
						<p>Citation properties</p>
						<p class="nbtFinePrint">You can add properties to be collected regarding each citation. Display name will appear as an open text field for each citation added on the extraction form; DB name will appear on exported spreadsheet.</p>
						<div id="nbtCitationSelectorTable<?php echo $element['id']; ?>"><?php

						$citationelementid = $element['id'];

						include ('./citationproperties.php');

						?></div><?php

					break;

					case "country_selector":

						?><h4>Country selector</h4>
						<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form</p>
						<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>, 50);" maxlength="50"></p>
						<p class="nbtFinePrint">Will appear on exported spreadsheet</p><?php

					break;

					case "date_selector":

						?><h4>Date selector</h4>
						<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form</p>
						<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeDateColumnName(<?php echo $element['id']; ?>);" maxlength="50"></p>
						<p class="nbtFinePrint">Will appear on exported spreadsheet</p><?php

					break;

					case "sub_extraction":

						?><h4>Sub-extraction</h4>
						<p class="nbtFinePrint">A sub-extraction is a form element that contains other form elements and can be repeated by the extractor as many times as necessary within an extraction. E.g. if you were extracting a set of papers that each contained a different number of experiments to be extracted, you could make an "experiment" sub-extraction that the extractor could repeat as many times as she needs.</p>
						<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">Will appear on extraction form</p>
						<p>Table suffix: <input type="text" id="nbtTableSuffix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeSubExtractionSuffix(<?php echo $element['id']; ?>);" maxlength="25"></p>
						<p class="nbtFinePrint">Suffix for table in database</p>
						<p>Sub-extraction elements</p>
						<div class="nbtSubExtractionEditor" id="nbtSubExtractionElements<?php echo $element['id']; ?>"><?php

						$subelementid = $element['id'];

						include ('./subextraction.php');

						?></div><?php

					break;

					case "assignment_editor":

						?><h4>Assignment editor</h4>
						<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);" maxlength="200"></p>
						<p class="nbtFinePrint">This element will allow an extractor to assign the reference being extracted to herself or another user, using this or a different form.</p><?php

					break;

				}

				?>
				<p>Codebook</p>
				<p class="nbtFinePrint">Will appear on extraction sheet when (?) is clicked</p>
				<textarea style="width: 100%; height: 80px;" id="nbtElementCodebook<?php echo $element['id']; ?>" onblur="nbtChangeElementCodebook(<?php echo $element['id']; ?>);"><?php echo $element['codebook']; ?></textarea>
				<p>Toggle: <input type="text" id="nbtElementToggle<?php echo $element['id']; ?>" value="<?php echo $element['toggle']; ?>" onblur="nbtChangeElementToggle(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">If the toggle field is filled in, this form element will be hidden until an extractor clicks a selector marked with the same toggle class. Do not put spaces, #'s or periods here.</p>
				<button onclick="nbtMoveFeedElement(<?php echo $element['formid']; ?>, <?php echo $element['id']; ?>, 1);">&#8593;</button>
				<button onclick="nbtMoveFeedElement(<?php echo $element['formid']; ?>, <?php echo $element['id']; ?>, -1);">&#8595;</button>
			<p id="nbtFormElementFeedback<?php echo $element['id']; ?>" class="nbtHidden nbtFinePrint">&nbsp;</p>
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
			</div>
		</div><?php

	}

} else {

	$element['id'] = 0;

	?><button style="margin-bottom: 10px;" onclick="$(this).fadeOut(0);$('#nbtNewElementSelector<?php echo $element['id']; ?>').fadeIn();">Add new element</button>
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
	</div><?php

}

?>
