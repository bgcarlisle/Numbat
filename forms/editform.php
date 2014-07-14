<?php

$form = nbt_get_form_for_id ($_GET['id']);

?><div class="nbtContentPanel nbtGreyGradient">

	<div class="nbtFinePrint" style="float: right;"><a href="<?php echo SITE_URL; ?>extract/?action=preview&form=<?php echo $_GET['id']; ?>" target="_blank">View preview</a></div>

	<h2>Name of form</h2>
	<input type="text" id="nbtFormName" onblur="nbtSaveFormName(<?php echo $form['id']; ?>);" value="<?php echo $form['name']; ?>">
	<p class="nbtFeedback nbtFeedbackGood nbtHidden nbtFinePrint" id="nbtFormNameFeedback">&nbsp;</p>

	<h3>Description</h3>
	<textarea id="nbtFormDescription" onblur="nbtSaveFormDescription(<?php echo $form['id']; ?>);" style="width: 100%; height: 100px;"><?php echo $form['description']; ?></textarea>
	<p class="nbtFeedback nbtFeedbackGood nbtHidden nbtFinePrint" id="nbtFormDescriptionFeedback">&nbsp;</p>

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
