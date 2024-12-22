<?php

$form = nbt_get_form_for_id ($_GET['id']);

?><div class="nbtContentPanel nbtGreyGradient">

	<div class="nbtFinePrint" style="float: right;"><a href="<?php echo SITE_URL; ?>extract/?action=preview&form=<?php echo $_GET['id']; ?>" target="_blank">View preview</a></div>

	<h2>Form metadata</h2>

	<?php
	switch ($form['formtype']) {

		case "extraction":
		case "":
			echo "<p>Form type: <span style=\"font-weight: 800;\">extraction</span></p>";
			break;
		case "screening":
			echo "<p>Form type: <span style=\"font-weight: 800;\">screening</span></p>";
			break;

	}
	?>

	<p>Name of form</p>
	<input type="text" id="nbtFormMetadata-name" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'name');" value="<?php echo $form['name']; ?>">

	<p>Version</p>
	<input type="text" id="nbtFormMetadata-version" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'version');" value="<?php echo $form['version']; ?>">

	<p>Author(s)</p>
	<input type="text" id="nbtFormMetadata-author" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'author');" value="<?php echo $form['author']; ?>">

	<p>Affiliation</p>
	<input type="text" id="nbtFormMetadata-affiliation" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'affiliation');" value="<?php echo $form['affiliation']; ?>">

	<p>Name of project for which this form was developed</p>
	<input type="text" id="nbtFormMetadata-project" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'project');" value="<?php echo $form['project']; ?>">

	<p>Link to protocol or registration for project associated with this form</p>
	<input type="text" id="nbtFormMetadata-protocol" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'protocol');" value="<?php echo $form['protocol']; ?>">

	<p>Date when this project was designed and carried out</p>
	<input type="text" id="nbtFormMetadata-projectdate" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'projectdate');" value="<?php echo $form['projectdate']; ?>">

	<p>Description</p>
	<textarea id="nbtFormMetadata-description" onblur="nbtSaveFormMetadata(<?php echo $form['id']; ?>, 'description');" style="width: 100%; height: 100px;"><?php echo $form['description']; ?></textarea>

	<hr>

		<?php
		switch ($form['formtype']) {

			case "extraction":
			case "":
				?><h2>Form elements</h2>
				<button onclick="collapseAllFormElements();">Collapse all form elements</button>
				<button onclick="expandAllFormElements();">Expand all form elements</button>
				<p>Drag elements to re-order.</p>
				<div id="nbtFormElements"><?php
				include ('./elements.php');
				echo "</div>";
				break;
			case "screening":
				?><h2>Reference data and reasons for exclusion</h2>
				<?php
				include ('./screening.php');
				break;

			}
		?>

	</div>

</div>
