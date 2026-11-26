<div class="nbtContentPanel nbtGreyGradient">
      <h2>Manage attachments</h2>

      <h3>Add a new attachment</h3>

      <?php

      if ( isset ( $_GET['refset'] ) && isset ( $_GET['ref'] ) ) {

	  echo '<p class="nbtFinePrint">Choose a file to attach to the following reference</p>';
	  
	  $ref = nbt_get_reference_for_refsetid_and_refid ( $_GET['refset'], $_GET['ref'] );

	  $refset = nbt_get_refset_for_id ( $_GET['refset'] );

	  $refmeta = $refset['refmeta'];

	  preg_match_all(
	      '/\$([A-Za-z0-9_-]+)/',
	      $refset['refmeta'],
	      $cols_to_replace
	  );

	  
	  foreach ( $cols_to_replace[0] as $col_to_replace ) {

	      $refmeta = preg_replace (
		  "/\\" . $col_to_replace . "\b/",
		  $ref[substr($col_to_replace, 1)],
		  $refmeta
	      );
	  }

	  echo "<div>" . $refmeta . "</div>";	  

      ?></p>
      <form action="<?php echo SITE_URL; ?>attach/upload.php" method="post" enctype="multipart/form-data">
          <input type="file" name="file" id="file">
          <input type="hidden" name="refsetid" value="<?php echo $_GET['refset']; ?>">
          <input type="hidden" name="refid" value="<?php echo $_GET['ref']; ?>">
          <button>Attach file</button>
      </form><?php

	     } else {

             ?><p>Select a reference set and search for a reference</p>

          <select id="nbtAssignRefSet" onchange="$('#nbtSelectedRefSet').html('referenceset_' + $(this).val())">
              <option value="NULL">Choose a reference set</option>
              <?php

              $refsets = nbt_get_all_ref_sets ();

              foreach ( $refsets as $refset ) {

              ?><option value="<?php echo $refset['id']; ?>"><?php echo $refset['name']; ?></option><?php

												    }

												    ?>
          </select>

          <div id="nbtAssignReferenceSearch">
              <p>Search for a reference to assign</p>
              <input type="text" class="nbtCitationFinder" id="nbtReferenceFinder" onkeyup="nbtFindReferenceToAttach();">
              <div class="nbtCitationSuggestions" id="nbtFoundReferencesForAssigment">&nbsp;</div>

          </div><?php

		}

		?>

</div>
