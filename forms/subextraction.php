<?php

$subelements = nbt_get_sub_extraction_elements_for_elementid ( $subelementid );

foreach ( $subelements as $subelement ) {
	
	?><div style="border: 1px solid #999; border-radius: 3px; padding: 10px; margin: 5px 0 20px 0; background: #ddd;" id="nbtSubElement<?php echo $subelement['id']; ?>">
		<button style="float: right;" onclick="$(this).fadeOut(0);$('#nbtDeleteSubElement<?php echo $subelement['id']; ?>').fadeIn();">Delete</button>
		<button class="nbtHidden" id="nbtDeleteSubElement<?php echo $subelement['id']; ?>" style="float: right;" onclick="nbtDeleteSubElement(<?php echo $subelement['id']; ?>);">For real</button>
		<?php
	
		switch ( $subelement['type'] ) {
			
			case "open_text":
				
				?><h4>Open text field</h4>
				<p>Display name: <input type="text" id="nbtSubElementDisplayName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['displayname']; ?>" onblur="nbtChangeSubDisplayName(<?php echo $subelement['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Column name: <input type="text" id="nbtSubElementColumnName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['dbname']; ?>" onblur="nbtChangeSubColumnName(<?php echo $subelement['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on exported spreadsheet</p><?php
				
			break;
			
			case "single_select":
				
				?><h4>Single select</h4>
				<p>Display name: <input type="text" id="nbtSubElementDisplayName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['displayname']; ?>" onblur="nbtChangeSubDisplayName(<?php echo $subelement['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Column name: <input type="text" id="nbtSubElementColumnName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['dbname']; ?>" onblur="nbtChangeSubColumnName(<?php echo $subelement['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
				<p>Options</p>
				<p class="nbtFinePrint">Display name will appear on extraction form; DB name will appear on exported spreadsheet; Other form elements marked with this toggle class will appear only if this element is selected</p>
				<div id="nbtSubSingleSelectOptionsTable<?php echo $subelement['id']; ?>"><?php
				
				$tablesubelementid = $subelement['id'];
				
				include ('./subsingleselectoptionstable.php');
				
				?></div><?php
				
			break;
			
			case "multi_select":
			
				?><h4>Multi select</h4>
				<p>Display name: <input type="text" id="nbtSubElementDisplayName<?php echo $subelement['id']; ?>" value="<?php echo $subelement['displayname']; ?>" onblur="nbtChangeSubDisplayName(<?php echo $subelement['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Column prefix: <input type="text" id="nbtSubElementColumnPrefix<?php echo $subelement['id']; ?>" value="<?php echo $subelement['dbname']; ?>" onblur="nbtChangeSubMultiSelectColumnPrefix(<?php echo $subelement['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on exported spreadsheet</p>
				<p>Options</p>
				<p class="nbtFinePrint">Display name will appear on extraction form; DB name will appear on exported spreadsheet; Other form elements marked with this toggle class will appear only if this element is selected</p>
				<div id="nbtSubMultiSelectOptionsTable<?php echo $subelement['id']; ?>"><?php
				
				$tablesubelementid = $subelement['id'];
				
				include ('./submultiselectoptionstable.php');
				
				?></div><?php
				
			break;
			
			case "country_selector":
				
				?><h4>Country selector</h4>
				<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeColumnName(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on exported spreadsheet</p><?php
			
			break;
			
			case "date_selector":
				
				?><h4>Date selector</h4>
				<p>Display name: <input type="text" id="nbtElementDisplayName<?php echo $element['id']; ?>" value="<?php echo $element['displayname']; ?>" onblur="nbtChangeDisplayName(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on extraction form</p>
				<p>Column name: <input type="text" id="nbtElementColumnName<?php echo $element['id']; ?>" value="<?php echo $element['columnname']; ?>" onblur="nbtChangeDateColumnName(<?php echo $element['id']; ?>);"></p>
				<p class="nbtFinePrint">Will appear on exported spreadsheet</p><?php
			
			break;
			
		}
	
		?>
		<p>Codebook</p>
		<p class="nbtFinePrint">Will appear on extraction sheet when (?) is clicked</p>
		<textarea style="width: 100%; height: 80px;" id="nbtSubElementCodebook<?php echo $subelement['id']; ?>" onblur="nbtChangeSubElementCodebook(<?php echo $subelement['id']; ?>);"><?php echo $subelement['codebook']; ?></textarea>
		<p>Toggle: <input type="text" id="nbtSubElementToggle<?php echo $subelement['id']; ?>" value="<?php echo $subelement['toggle']; ?>" onblur="nbtChangeSubElementToggle(<?php echo $subelement['id']; ?>);"></p>
		<p class="nbtFinePrint">If the toggle field is filled in, this form element will be hidden until an extractor clicks a selector marked with the same toggle class. Do not put spaces, #'s or periods here.</p>
		<button onclick="nbtMoveSubElement(<?php echo $subelementid; ?>, <?php echo $subelement['id']; ?>, 1);">Move up</button>
		<button onclick="nbtMoveSubElement(<?php echo $subelementid; ?>, <?php echo $subelement['id']; ?>, -1);">Move down</button>
	<p id="nbtSubElementFeedback<?php echo $subelement['id']; ?>" class="nbtHidden nbtFinePrint">&nbsp;</p>
	</div><?php
	
}

?>
<button onclick="$(this).fadeOut(0);$('#nbtNewElementSelector<?php echo $subelementid; ?>').fadeIn();">Add new sub-extraction element</button>

<div id="nbtNewElementSelector<?php echo $subelementid; ?>" class="nbtHidden">
	<h3>Add new sub-extraction element</h3>
	<button onclick="nbtAddNewSubOpenText(<?php echo $subelementid; ?>);">Open text</button>
	<button onclick="nbtAddNewSubSingleSelect(<?php echo $subelementid; ?>);">Single select</button>
	<button onclick="nbtAddNewSubMultiSelect(<?php echo $subelementid; ?>);">Multi select</button>
	<button onclick="nbtAddNewCountrySelector(<?php echo $_GET['id']; ?>);">Country selector</button>
	<button onclick="nbtAddNewDateSelector(<?php echo $_GET['id']; ?>);">Date selector</button>
</div>