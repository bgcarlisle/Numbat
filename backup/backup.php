<div class="nbtContentPanel nbtGreyGradient">
	<h2><img src="<?php echo SITE_URL; ?>images/backup.png" class="nbtTitleImage">Backup data</h2>

	<div id="nbtListOfDumpFiles"><?php

		include ('./dumpfiles.php');

	?></div>

	<button onclick="nbtNewDumpFile();">Generate new MySQL dump file</button>

</div>
<div id="nbtCoverup" style="display: none; background: #ccc; opacity: 0.5; z-index: 1; width: 100%; height: 100%; position: fixed; top: 0; left: 0;">&nbsp;</div>
<div id="nbtThinky" style="display: none; z-index: 2; position: fixed; top: 100px; width: 100%; text-align: center;">Backin' up ...</div>
