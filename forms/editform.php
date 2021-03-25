<?php

$form = nbt_get_form_for_id ($_GET['id']);

?><div class="nbtContentPanel nbtGreyGradient">

	<div class="nbtFinePrint" style="float: right;"><a href="<?php echo SITE_URL; ?>extract/?action=preview&form=<?php echo $_GET['id']; ?>" target="_blank">View preview</a></div>

	<h2>Form metadata</h2>
	
	<p>Name of form</p>
	<input type="text" id="nbtFormMetadata-name" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'name');" value="<?php echo $form['name']; ?>">
	<p class="nbtFeedback nbtFeedbackGood nbtHidden nbtFinePrint" id="nbtFormMetadataFeedback-name">&nbsp;</p>
	
	<p>Version</p>
	<input type="text" id="nbtFormMetadata-version" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'version');" value="<?php echo $form['version']; ?>">
	<p class="nbtFeedback nbtFeedbackGood nbtHidden nbtFinePrint" id="nbtFormMetadataFeedback-version">&nbsp;</p>
	
	<p>Author(s)</p>
	<input type="text" id="nbtFormMetadata-author" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'author');" value="<?php echo $form['author']; ?>">
	<p class="nbtFeedback nbtFeedbackGood nbtHidden nbtFinePrint" id="nbtFormMetadataFeedback-author">&nbsp;</p>
	
	<p>Affiliation</p>
	<input type="text" id="nbtFormMetadata-affiliation" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'affiliation');" value="<?php echo $form['affiliation']; ?>">
	<p class="nbtFeedback nbtFeedbackGood nbtHidden nbtFinePrint" id="nbtFormMetadataFeedback-affiliation">&nbsp;</p>
	
	<p>Name of project for which this form was developed</p>
	<input type="text" id="nbtFormMetadata-project" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'project');" value="<?php echo $form['project']; ?>">
	<p class="nbtFeedback nbtFeedbackGood nbtHidden nbtFinePrint" id="nbtFormMetadataFeedback-project">&nbsp;</p>
	
	<p>Link to protocol or registration for project associated with this form</p>
	<input type="text" id="nbtFormMetadata-protocol" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'protocol');" value="<?php echo $form['protocol']; ?>">
	<p class="nbtFeedback nbtFeedbackGood nbtHidden nbtFinePrint" id="nbtFormMetadataFeedback-protocol">&nbsp;</p>
	
	<p>Date when this project was designed and carried out</p>
	<input type="text" id="nbtFormMetadata-protocol" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'projectdate');" value="<?php echo $form['projectdate']; ?>">
	<p class="nbtFeedback nbtFeedbackGood nbtHidden nbtFinePrint" id="nbtFormMetadataFeedback-projectdate">&nbsp;</p>

	<p>Description</p>
	<textarea id="nbtFormMetadata-description" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'description');" style="width: 100%; height: 100px;"><?php echo $form['description']; ?></textarea>
	<p class="nbtFeedback nbtFeedbackGood nbtHidden nbtFinePrint" id="nbtFormMetadataFeedback-description">&nbsp;</p>

	<hr>
	
	<h2>Form elements</h2>

	<div id="nbtFormElements">

		<?php

		include ('./elements.php');

		?>

	</div>

</div>

<script type="text/javascript">

	nbtCheckLogin();

</script>
