<?php

$elements = nbt_get_elements_for_formid ($_GET['id']);

foreach ( $elements as $element ) {
	
	?><div style="border: 1px solid #999; border-radius: 3px; padding: 10px; margin: 5px 0 20px 0;" id="nbtFormElement<?php echo $element['id']; ?>">
		<button style="float: right;" onclick="$(this).fadeOut(0);$('#nbtDeleteFormElement<?php echo $element['id']; ?>').fadeIn();">Delete</button>
		<button class="nbtHidden" id="nbtDeleteFormElement<?php echo $element['id']; ?>" style="float: right;" onclick="nbtDeleteFormElement(<?php echo $element['id']; ?>);">For real</button>
		<?php
	
		switch ( $element['type'] ) {
			
			case "section_heading":
			
				?><h4>Section heading</h4>
				<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on extraction form. This form element is purely aesthetic, and will not appear on the exported spreadsheet.</p><?php
				
			break;
			
			case "open_text":
				
				?><h4>Open text field</h4>
				<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on exported spreadsheet</p><?php
				
			break;
			
			case "single_select":
				
				?><h4>Single select</h4>
				<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>);"></p>
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
				<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Column prefix: <input type="text" id="nbtElementColumnPrefix<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeMultiSelectColumnPrefix(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
				<p>Options</p>
				<p class="nbtFinePrint">Display name will appear on extraction form; DB name will appear on exported spreadsheet; Other form elements marked with this toggle class will appear only if this element is selected</p>
				<div id="nbtMultiSelectOptionsTable<?php echo $element['id']; ?>"><?php
				
				$tableelementid = $element['id'];
				
				include ('./multiselectoptionstable.php');
				
				?></div><?php
				
			break;
			
		}
	
		?>
		<p>Codebook</p>
		<p class="nbtFinePrint">Will appear on extraction sheet when (?) is clicked</p>
		<textarea style="width: 100%; height: 80px;" id="nbtElementCodebook<?php echo $element['id']; ?>" onblur="nbtChangeElementCodebook(<?php echo $element['id']; ?>);"><?php echo $element['codebook']; ?></textarea>
		<p>Toggle: <input type="text" id="nbtElementToggle<?php echo $element['id']; ?>" value="<?php echo $element['toggle']; ?>" onblur="nbtChangeElementToggle(<?php echo $element['id']; ?>);"></p>
		<p class="nbtFinePrint">If the toggle field is filled in, this form element will be hidden until an extractor clicks a selector marked with the same toggle class</p>
		<button onclick="nbtMoveFeedElement(<?php echo $element['formid']; ?>, <?php echo $element['id']; ?>, 1);">Move up</button>
		<button onclick="nbtMoveFeedElement(<?php echo $element['formid']; ?>, <?php echo $element['id']; ?>, -1);">Move down</button>
	<p id="nbtFormElementFeedback<?php echo $element['id']; ?>" class="nbtHidden nbtFinePrint">&nbsp;</p>
	</div><?php
	
}

?>
<button onclick="$(this).fadeOut(0);$('#nbtNewElementSelector').fadeIn();">Add new element</button>

<div id="nbtNewElementSelector" class="nbtHidden">
	<h3>Add new form element</h3>
	<button onclick="nbtAddNewSectionHeading(<?php echo $_GET['id']; ?>);">Section heading</button>
	<button onclick="nbtAddNewOpenText(<?php echo $_GET['id']; ?>);">Open text</button>
	<button onclick="nbtAddNewSingleSelect(<?php echo $_GET['id']; ?>);">Single select</button>
	<button onclick="nbtAddNewMultiSelect(<?php echo $_GET['id']; ?>);">Multi select</button>
	<button>Citation</button>
	<button>Date</button>
	<button>Tabled data</button>
	<button>Arms / outcomes / efficacy</button>
</div>