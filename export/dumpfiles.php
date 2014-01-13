<?php

$files = scandir ( ABS_PATH . "export/dumpfiles/");

?><ul><?php

foreach ( $files as $file ) {
	
	if ( substr ($file, 0, 1) != "." ) {
		
		?><li><a href="<?php echo SITE_URL; ?>export/dumpfiles/<?php echo $file; ?>"><?php echo $file; ?></a></li><?php
		
	}
	
}

?></ul>