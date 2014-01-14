<?php

$ref = nbt_get_reference_for_refsetid_and_refid ( $_GET['refset'], $_GET['ref'] );

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );

$extraction = nbt_get_extraction ( $_GET['form'], $_GET['refset'], $_GET['ref'], $_SESSION['nbt_userid'] );

?><button onclick="$('.nbtSidebar').fadeIn(200);$(this).fadeOut(0);" id="nbtUnhideSidebar" style="display: none; position: fixed; left: 20px; top: 100px;">Unhide notes</button>
<div class="nbtSidebar">
	<h3>Extraction notes</h3>
	<p class="nbtFinePrint">These notes are for your own reference. These will not be included in the final report, and they are not visible to other extractors. <a href="#" onclick="event.preventDefault();$(this).parent().parent().fadeOut(200);$('button#nbtUnhideSidebar').fadeIn(200);">[Hide]</a></p>
	<textarea id="nbtExtractionNotes" onblur="nbtSaveTextField(<?php echo $_GET['form']; ?>, <?php echo $extraction['id']; ?>, 'notes', 'nbtExtractionNotes', 'nbtNotesFeedback');"><?php
	
	echo $extraction['notes'];
	
	?></textarea>
	<p class="nbtInputFeedback" id="nbtNotesFeedback">&nbsp;</p>
	
	<h3>Manual citations</h3>
	<button onclick="nbtInlineAddNewReferenceToDrug(event, <?php echo $extraction['drugid']; ?>);">Add a new reference</button>
	<button onclick="window.open('<?php echo SITE_URL . "drug/" . $_GET['drug'] . "/" . "new/"; ?>','_blank');">View manually added references</button>
</div>
<div class="nbtCoverup" id="nbtManualRefsCoverup">&nbsp;</div>
<div id="nbtManualRefs" class="nbtInlineManualNewRef">&nbsp;</div>
<div class="nbtNonsidebar">
	<div class="nbtContentPanel">
		<h2><?php echo $ref['title']; ?></h2>
		<p><?php echo $ref['authors']; ?></p>
		<p><span class="nbtJournalName"><?php echo $ref['journal']; ?></span>: <?php echo $ref['year']; ?></p>
	</div>
	<div class="nbtContentPanel">
		<h3>Abstract</h3>
		<p class="nbtFinePrint"><a href="#" onclick="event.preventDefault();$('#nbtAbstract').slideToggle(200);">Show / hide abstract</a></p>
		<p id="nbtAbstract"><?php
		
		if ( $ref['abstract'] != NULL) {
			
			echo $ref['abstract'];
			
		} else {
			
			echo "[No abstract]";
			
		}
		
		?></p>
	</div>
	<div class="nbtContentPanel">
		<h3>Status of extraction</h3>
		<?php nbt_echo_single_select ($_GET['form'], $extraction, "status", array (
			0 => "Not yet started",
			1 => "In progress",
			2 => "Completed"
		) ); ?>
	</div>
	<?php
	
	if ( $formelements[0]['type'] != "section_heading" ) {
		
		?><div class="nbtContentPanel"><?php
		
	}
	
		foreach ( $formelements as $element ) {
			
			switch ( $element['type'] ) {
				
				case "section_heading":
					
					if ( $element['id'] != $formelements[0]['id'] ) {
						
						?></div><?php
						
					}
					
					?><div class="nbtContentPanel">
						<h3><?php echo $element['displayname']; ?><?php
					
						if ( $element['codebook'] != "" ) {
							
							?> <a href="#" onclick="event.preventDefault();$(this).parent().next('.nbtCodebook').slideToggle(100);">(?)</a></h3>
							<div class="nbtCodebook"><?php echo $element['codebook']; ?></div><?php
							
						} else {
							
							?></h3><?php
							
						}
					
				break;
				
				case "open_text":
					
					?><p><?php echo $element['displayname']; ?><?php
					
					if ( $element['codebook'] != "" ) {
						
						?> <a href="#" onclick="event.preventDefault();$(this).parent().next('.nbtCodebook').slideToggle(100);">(?)</a></p>
						<div class="nbtCodebook"><?php echo $element['codebook']; ?></div><?php
						
					} else {
						
						?></p><?php
						
					}
					
					nbt_echo_text_field ($_GET['form'], $extraction, $element['columnname'], 500, FALSE);
					
				break;
				
				case "date_selector":
					
					?><p><?php echo $element['displayname']; ?><?php
					
					if ( $element['codebook'] != "" ) {
						
						?> <a href="#" onclick="event.preventDefault();$(this).parent().next('.nbtCodebook').slideToggle(100);">(?)</a></p>
						<div class="nbtCodebook"><?php echo $element['codebook']; ?></div><?php
						
					} else {
						
						?></p><?php
						
					}
					
					nbt_echo_date_selector ($_GET['form'], $extraction, $element['columnname']);
					
				break;
				
				case "single_select":
					
					?><p><?php echo $element['displayname']; ?><?php
					
					if ( $element['codebook'] != "" ) {
						
						?> <a href="#" onclick="event.preventDefault();$(this).parent().next('.nbtCodebook').slideToggle(100);">(?)</a></p>
						<div class="nbtCodebook"><?php echo $element['codebook']; ?></div><?php
						
					} else {
						
						?></p><?php
						
					}
					
					$answers = array ();
					
					$selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );
					
					foreach ( $selectoptions as $option ) {
						
						$answers[$option['dbname']] = $option['displayname'];
						
					}
					
					nbt_echo_single_select ( $_GET['form'], $extraction, $element['columnname'], $answers );
					
				break;
				
				case "multi_select":
					
					?><p><?php echo $element['displayname']; ?><?php
					
					if ( $element['codebook'] != "" ) {
						
						?> <a href="#" onclick="event.preventDefault();$(this).parent().next('.nbtCodebook').slideToggle(100);">(?)</a></p>
						<div class="nbtCodebook"><?php echo $element['codebook']; ?></div><?php
						
					} else {
						
						?></p><?php
						
					}
					
					$answers = array ();
					
					$selectoptions = nbt_get_all_select_options_for_element ( $element['id'] );
					
					foreach ( $selectoptions as $option ) {
						
						$answers[$option['dbname']] = $option['displayname'];
						
					}
					
					nbt_echo_multi_select ($_GET['form'], $extraction, $element['columnname'], $answers );
					
				break;
				
				case "country_selector":
					
					?><p><?php echo $element['displayname']; ?><?php
					
					if ( $element['codebook'] != "" ) {
						
						?> <a href="#" onclick="event.preventDefault();$(this).parent().next('.nbtCodebook').slideToggle(100);">(?)</a></p>
						<div class="nbtCodebook"><?php echo $element['codebook']; ?></div><?php
						
					} else {
						
						?></p><?php
						
					}
					
					$countries = nbt_return_country_array (); ?>
					
					<select id="nbtCountrySelect<?php echo $element['columnname']; ?>" onblur="nbtSaveTextField(<?php echo $_GET['form']; ?>, <?php echo $extraction['id']; ?>, '<?php echo $element['columnname']; ?>', 'nbtCountrySelect<?php echo $element['columnname']; ?>', 'sigCountrySelect<?php echo $element['columnname']; ?>Feedback');">
						<?php
						
						foreach ( $countries as $country ) {
							
							?><option value="<?php echo $country; ?>"<?php
							
								if ( $extraction[$element['columnname']] == $country ) {
									
									?> selected<?php
									
								}
							
							?>><?php echo $country; ?></option><?php
							
						}
						
						?>
					</select>
					<span class="nbtInputFeedback" id="sigCountrySelect<?php echo $element['columnname']; ?>Feedback">&nbsp;</span><?php
					
				break;
				
				case "table_data":
					
					?><p><?php echo $element['displayname']; ?><?php
					
					if ( $element['codebook'] != "" ) {
						
						?> <a href="#" onclick="event.preventDefault();$(this).parent().next('.nbtCodebook').slideToggle(100);">(?)</a></p>
						<div class="nbtCodebook"><?php echo $element['codebook']; ?></div><?php
						
					} else {
						
						?></p><?php
						
					}
					
					$nbtExtractTableDataID = $element['id'];
					$nbtExtractRefSet = $_GET['refset'];
					$nbtExtractRefID = $_GET['ref'];
					
					include ('./tabledata.php');
					
				break;
				
			}
			
		}
	
		?></div>
	</div>
	
	<div style="height: 200px;">&nbsp;</div>
</div>
<script type="text/javascript">
sigUpdateConditionalDisplays ();
sigUpdateProgress ();
</script>