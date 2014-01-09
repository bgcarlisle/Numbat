<?php

$elements = nbt_get_elements_for_formid ($_GET['id']);

foreach ( $elements as $element ) {
	
	switch ( $element['type'] ) {
		
		case "single_select":
			
			?><p>Type: single select</p><?php
			
		break;
		
		case "multi_select":
			
		break;
		
	}
	
}

?><button onclick="$(this).fadeOut(0);$('#nbtNewElementSelector').fadeIn();">Add new element</button>

<div id="nbtNewElementSelector" class="nbtHidden">
	<h3>Add new form element</h3>
	<button>Open text</button>
	<button>Single select</button>
	<button>Multi select</button>
	<button>Citation</button>
	<button>Date</button>
	<button>Section heading</button>
	<button>Tabled data</button>
	<button>Arms / outcomes / efficacy</button>
</div>