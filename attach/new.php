<div class="nbtContentPanel nbtGreyGradient">
      <h2>Manage attachments</h2>

      <h3>Add a new attachment</h3>

      <p>Select a reference set and search for a reference</p>

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

      </div>

</div>
