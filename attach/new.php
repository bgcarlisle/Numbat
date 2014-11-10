<div class="nbtContentPanel nbtGreyGradient">
      <h2>Manage attachments</h2>

      <h3>Add a new attachment</h3>

      <?php

      if ( isset ( $_GET['refset'] ) && isset ( $_GET['ref'] ) ) {

            ?><p class="nbtFinePrint">Choose a file to attach to the following reference</p><?php

            $ref = nbt_get_reference_for_refsetid_and_refid ( $_GET['refset'], $_GET['ref'] );

            ?><h4><?php echo $ref['title']; ?>.</h4>
            <p class="nbtFinePrint"><?php echo $ref['authors']; ?>.
            <?php

            if ( $ref['journal'] != "" && $ref['year'] != "" ) {

                  ?><span class="nbtJournalName"><?php echo $ref['journal']; ?></span>: <?php echo $ref['year']; ?>.<?php

            }

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
