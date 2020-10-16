<?php

$refset = nbt_get_refset_for_id ( $_GET['refset'] );
$refsetcols = nbt_get_columns_for_refset ( $_GET['refset'] );

?><div class="nbtContentPanel nbtGreyGradient">
    <h2>Edit reference set</h2>
    <h3>Name</h3>
    <input id="nbtNewRefSetName" type="text" value="<?php echo $refset['name']; ?>" onblur="nbtChangeRefSetName(<?php echo $refset['id']; ?>);">
    <span class="nbtFinePrint nbtHidden nbtFeedback" id="nbtNewRefSetNameFeedback">&nbsp;</span>
    <h3>Reference metadata</h3>
    <p>Title</p>
    <select id="nbtMetadata-title" onchange="nbtUpdateRefsetMetadata('title', <?php echo $refset['id']; ?>);">
	<?php

	$colcount = 0;

	foreach ( $refsetcols as $col ) {

	    if ( $colcount == $refset['title'] ) {

		echo "<option value=\"" . $colcount . "\" selected>" . $col[0] . "</option>";
	    } else {

		echo "<option value=\"" . $colcount . "\">" . $col[0] . "</option>";
	    }

	    $colcount++;
	    
	}

	?>
    </select>
    <span id="nbtMetadataResponse-title">&nbsp;</span>
    
    <p>Authors</p>
    <select id="nbtMetadata-authors" onchange="nbtUpdateRefsetMetadata('authors', <?php echo $refset['id']; ?>);">
	<?php

	$colcount = 0;

	foreach ( $refsetcols as $col ) {

	    if ( $colcount == $refset['authors'] ) {

		echo "<option value=\"" . $colcount . "\" selected>" . $col[0] . "</option>";
	    } else {

		echo "<option value=\"" . $colcount . "\">" . $col[0] . "</option>";
	    }

	    $colcount++;
	    
	}

	?>
    </select>
    <span id="nbtMetadataResponse-authors">&nbsp;</span>
    
    
    <p>Year</p>
    <select id="nbtMetadata-year" onchange="nbtUpdateRefsetMetadata('year', <?php echo $refset['id']; ?>);">
	<?php

	$colcount = 0;

	foreach ( $refsetcols as $col ) {

	    if ( $colcount == $refset['year'] ) {

		echo "<option value=\"" . $colcount . "\" selected>" . $col[0] . "</option>";
	    } else {

		echo "<option value=\"" . $colcount . "\">" . $col[0] . "</option>";
	    }

	    $colcount++;
	    
	}

	?>
    </select>
    <span id="nbtMetadataResponse-year">&nbsp;</span>
    
    
    <p>Journal</p>
    <select id="nbtMetadata-journal" onchange="nbtUpdateRefsetMetadata('journal', <?php echo $refset['id']; ?>);">
	<?php

	$colcount = 0;

	foreach ( $refsetcols as $col ) {

	    if ( $colcount == $refset['journal'] ) {

		echo "<option value=\"" . $colcount . "\" selected>" . $col[0] . "</option>";
	    } else {

		echo "<option value=\"" . $colcount . "\">" . $col[0] . "</option>";
	    }

	    $colcount++;
	    
	}

	?>
    </select>
    <span id="nbtMetadataResponse-journal">&nbsp;</span>
    
    <p>Abstract</p>
    <select id="nbtMetadata-abstract" onchange="nbtUpdateRefsetMetadata('abstract', <?php echo $refset['id']; ?>);">
	<?php

	$colcount = 0;

	foreach ( $refsetcols as $col ) {

	    if ( $colcount == $refset['abstract'] ) {

		echo "<option value=\"" . $colcount . "\" selected>" . $col[0] . "</option>";
	    } else {

		echo "<option value=\"" . $colcount . "\">" . $col[0] . "</option>";
	    }

	    $colcount++;
	    
	}

	?>
    </select>
    <span id="nbtMetadataResponse-abstract">&nbsp;</span>
</div>
