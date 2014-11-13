<?php

include_once ("../../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {

      $suggestions = nbt_search_multiples ( $_POST['refset'], $_POST['query'] );

      $counter = 0;

      foreach ( $suggestions as $suggestion ) {

            if ( $counter < 5 ) {

                  ?><div id="nbtMultiple<?php echo $suggestion['id']; ?>">
                        <h4>[<?php echo $suggestion['id']; ?>] <?php echo $suggestion['title']; ?>.</h4>
                        <p><?php echo $suggestion['authors']; ?>. <span class="nbtJournalName"><?php echo $suggestion['journal']; ?></span>: <?php echo $suggestion['year']; ?>.</p>

                        <?php

                        if ( is_dir ( ABS_PATH . "attach/files/" . $_POST['refset'] . "/" ) ) {

                              $files = scandir ( ABS_PATH . "attach/files/" . $_POST['refset'] . "/" );

                              foreach ( $files as $file ) {

                                    if ( substr ($file, 0, 1) != "." ) {

                                          // Get the ref id

                                          $file_ref = explode(".", $file);

                                          $refid = $file_ref[0];

                                          if ( $file_ref[0] == $suggestion['id'] ) {

                                                ?><span class="nbtAttachment">Attached <?php echo $file_ref[1]; ?></span><?php

                                          }

                                    }

                              }

                        }

                        ?>

                        <h4>Reference details</h4>

                        <table class="nbtTabledData">
                              <tr class="nbtTableHeaders">
                                    <td>Assignments</td>
                                    <td>Extractions</td>
                                    <td>Master copies</td>
                                    <td>Citations</td>
                              </tr>
                              <tr>
                                    <td>
                                          <ul><?php

                                          $assignments = nbt_get_all_assignments_for_refset_and_ref ( $_POST['refset'], $suggestion['id'] );

                                          foreach ( $assignments as $assignment ) {

                                                $assigned_form = nbt_get_form_for_id ( $assignment['formid'] );

                                                $assigned_username = nbt_get_username_for_userid ( $assignment['userid'] );

                                                ?><li>
                                                      <?php echo $assigned_form['name']; ?>
                                                      <span class="nbtExtractionName"><?php echo $assigned_username; ?></span>
                                                </li><?php

                                          }

                                          ?></ul>
                                    </td>
                                    <td>
                                          <ul><?php

                                          $all_forms = nbt_get_all_extraction_forms ();

                                          foreach ( $all_forms as $form ) {

                                                $form_extractions = nbt_get_extractions_for_refset_ref_and_form ( $_POST['refset'], $suggestion['id'], $form['id'], 1 );

                                                foreach ( $form_extractions as $extraction ) {

                                                      ?><li>
                                                            <?php echo $form['name']; ?>
                                                            <span class="nbtExtractionName"><?php echo $extraction['username'] ?></span>
                                                      </li><?php

                                                }

                                          }

                                          ?></ul>
                                    </td>
                                    <td>
                                          <ul><?php

                                          foreach ( $all_forms as $form ) {

                                                $form_masters = nbt_get_master_extractions_for_refset_ref_and_form ( $_POST['refset'], $suggestion['id'], $form['id'] );

                                                foreach ( $form_masters as $master ) {

                                                      ?><li>
                                                            <?php echo $form['name']; ?>
                                                      </li><?php

                                                }

                                          }

                                          ?></ul>
                                    </td>
                                    <td>
                                          <ul><?php

                                          $citation_form_elements = nbt_get_all_citation_form_elements ();

                                          foreach ( $citation_form_elements as $citation_form_element ) {

                                                $citations = nbt_get_all_citations_for_element_and_citationid ( $citation_form_element['columnname'], $_POST['refset'], $suggestion['id'] );

                                                foreach ($citations as $citation ) {

                                                      ?><li>
                                                            <?php echo $citation['referenceid'];

                                                            if ( ! is_null ($citation['cite_no']) ) {
                                                                  ?> [#<?php echo $citation['cite_no']; ?>]<?php
                                                            }

                                                            ?> &rarr; <?php echo $citation['citationid']; ?>
                                                            <span class="nbtExtractionName"><?php echo $citation['username']; ?></span>
                                                      </li><?php

                                                }

                                          }

                                          ?></ul>
                                    </td>
                              </tr>
                              <tr>
                                    <td>
                                          <p>Move assignments to:</p>
                                          <select id="nbtMultiMoveAssignChooser<?php echo $suggestion['id']; ?>">
                                                <option>Choose a reference id</option>
                                                <?php

                                                foreach ( $suggestions as $assign_suggest ) {

                                                      if ( $assign_suggest['id'] != $suggestion['id'] ) {

                                                            ?><option value="<?php echo $assign_suggest['id']; ?>">[<?php echo $assign_suggest['id']; ?>]</option><?php

                                                      }

                                                }

                                                ?>
                                          </select>
                                          <button onclick="nbtMultipleMoveAssignments(<?php echo $_POST['refset']; ?>, <?php echo $suggestion['id']; ?>);">Move</button>
                                    </td>
                                    <td>
                                          <p>Move extractions to:</p>
                                          <select id="nbtMultiMoveExtractChooser<?php echo $suggestion['id']; ?>">
                                                <option>Choose a reference id</option>
                                                <?php

                                                foreach ( $suggestions as $assign_suggest ) {

                                                      if ( $assign_suggest['id'] != $suggestion['id'] ) {

                                                            ?><option value="<?php echo $assign_suggest['id']; ?>">[<?php echo $assign_suggest['id']; ?>]</option><?php

                                                      }

                                                }

                                                ?>
                                          </select>
                                          <button onclick="nbtMultipleMoveExtractions(<?php echo $_POST['refset']; ?>, <?php echo $suggestion['id']; ?>);">Move</button>
                                    </td>
                                    <td>
                                          <p>Move master copies to:</p>
                                          <select id="nbtMultiMoveMasterChooser<?php echo $suggestion['id']; ?>">
                                                <option>Choose a reference id</option>
                                                <?php

                                                foreach ( $suggestions as $assign_suggest ) {

                                                      if ( $assign_suggest['id'] != $suggestion['id'] ) {

                                                            ?><option value="<?php echo $assign_suggest['id']; ?>">[<?php echo $assign_suggest['id']; ?>]</option><?php

                                                      }

                                                }

                                                ?>
                                          </select>
                                          <button onclick="nbtMultipleMoveMaster(<?php echo $_POST['refset']; ?>, <?php echo $suggestion['id']; ?>);">Move</button>
                                    </td>
                                    <td>
                                          <p>Move citations to:</p>
                                          <select id="nbtMultiMoveCiteChooser<?php echo $suggestion['id']; ?>">
                                                <option>Choose a reference id</option>
                                                <?php

                                                foreach ( $suggestions as $assign_suggest ) {

                                                      if ( $assign_suggest['id'] != $suggestion['id'] ) {

                                                            ?><option value="<?php echo $assign_suggest['id']; ?>">[<?php echo $assign_suggest['id']; ?>]</option><?php

                                                      }

                                                }

                                                ?>
                                          </select>
                                          <button onclick="nbtMultipleMoveCitations(<?php echo $_POST['refset']; ?>, <?php echo $suggestion['id']; ?>);">Move</button>
                                    </td>
                              </tr>
                        </table>

                        <button id="nbtDeleteMultipleRefDelete<?php echo $suggestion['id']; ?>" onclick="$(this).fadeOut(0);$('#nbtDeleteMultipleRefWarning<?php echo $suggestion['id']; ?>').fadeIn();$('#nbtDeleteMultipleRefConfirm<?php echo $suggestion['id']; ?>').fadeIn();$('#nbtDeleteMultipleRefCancel<?php echo $suggestion['id']; ?>').fadeIn();">Delete this reference</button>
                        <p class="nbtHidden" id="nbtDeleteMultipleRefWarning<?php echo $suggestion['id']; ?>">WARNING: You can't undo this. Make sure that you've moved all the assignments, extractions, master copies and citations to another equivalent reference.</p>
                        <button class="nbtHidden" id="nbtDeleteMultipleRefConfirm<?php echo $suggestion['id']; ?>" onclick="nbtDeleteMultipleRef(<?php echo $_POST['refset']; ?>, <?php echo $suggestion['id']; ?>);">Delete for real</button>
                        <button class="nbtHidden" id="nbtDeleteMultipleRefCancel<?php echo $suggestion['id']; ?>" onclick="$(this).fadeOut(0);$('#nbtDeleteMultipleRefWarning<?php echo $suggestion['id']; ?>').fadeOut(0);$('#nbtDeleteMultipleRefConfirm<?php echo $suggestion['id']; ?>').fadeOut(0);$('#nbtDeleteMultipleRefDelete<?php echo $suggestion['id']; ?>').fadeIn();">Cancel</button>
                  </div><?php

                  $counter ++;

            } else {

                  ?><div>
                        <p>There are more than 5 results for the query you entered. You may need to refine your search.</p>
                  </div><?php
            }

      }

} else {

      echo "You do not have sufficient privileges";

}

?>
