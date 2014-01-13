<div class="nbtContentPanel nbtGreyGradient">
	<h2>Export database</h2>
	
	<div id="nbtListOfDumpFiles"><?php
	
		include ('./dumpfiles.php');
	
	?></div>
	
	<button onclick="nbtNewDumpFile();">Generate new MySQL dump file</button>
	
</div>