<div class="nbtContentPanel nbtGreyGradient">
	<h2><img src="<?php echo SITE_URL; ?>images/backup.png" class="nbtTitleImage">Backup data</h2>
	
	<div id="nbtListOfDumpFiles"><?php
	
		include ('./dumpfiles.php');
	
	?></div>
	
	<button onclick="nbtNewDumpFile();">Generate new MySQL dump file</button>
	
</div>