<?php

include_once ("../config.php");

$distinctcites = nbt_get_distinct_citations_for_element_refset_and_ref ( $nbtListCitationsCitationID, $nbtListCitationsRefSetID, $nbtListCitationsReference );

$refset = nbt_get_refset_for_id ($nbtListCitationsRefSetID);

?><table class="nbtTabledData">

	<tr><?php

	foreach ( $extractions as $extraction ) {

		?><td><span class="nbtExtractionName"><?php echo $extraction['username']; ?></span></td><?php

	}

	foreach ( $distinctcites as $dcite ) {

		?><tr><?php

			foreach ( $extractions as $extraction ) {

				$pcite = nbt_get_particular_citation ( $nbtListCitationsCitationID, $nbtListCitationsRefSetID, $nbtListCitationsReference, $extraction['userid'], $dcite['citationid']);

				echo $pcite['id'];

				if ( $pcite ) {

					?><td><?php

						foreach ( $pcite as $cite ) {

							if ( $cite['cite_no'] != NULL ) {

								?><h4>#<?php echo $cite['cite_no']; ?></h4><?php

							} else {

								?><p><h4><?php echo $cite[$refset['title']]; ?></h4></p>
								<p><?php echo $cite[$refset['authors']]; ?>, <em><?php echo $cite[$refset['journal']]; ?></em>: <?php echo $cite[$refset['year']]; ?></p><?php

							}

							$columns = nbt_get_all_columns_for_citation_selector ( $nbtListCitationsCitationID );

							foreach ( $columns as $column ) {

								if ( $cite[$column['dbname']] == "" ) {

									$cite[$column['dbname']] = "[Left blank]";

								}

								?><p><?php echo $column['displayname']; ?><span class="nbtFeedback"><?php echo $cite[$column['dbname']]; ?></span></p><?php

							}

							?><button style="margin: 5px 0 10px 0;" onclick="nbtCopyCitationToMaster(<?php echo $nbtListCitationsCitationID; ?>, <?php echo $cite['id']; ?>, <?php echo $nbtListCitationsRefSetID; ?>, <?php echo $nbtListCitationsReference; ?>, <?php echo $cite['citationid']; ?>);">Copy to final</button>
							<span class="nbtHidden nbtMasterCite<?php echo $nbtListCitationsCitationID; ?>-<?php echo $cite['citationid']; ?>" id="nbtMasterCiteCopyFeedback<?php echo $nbtListCitationsCitationID; ?>-<?php echo $cite['id']; ?>" style="padding: 1px 4px;"></span><?php

						}

					?></td><?php

				} else {

					?><td><p>[Not cited]</p></td><?php

				}

			}

		?></tr><?php

	}

	?></tr>

</table>
