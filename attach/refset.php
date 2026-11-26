<div class="nbtContentPanel nbtGreyGradient">
      <h2>View attachments</h2>

      <h3><?php

	  $refset = nbt_get_refset_for_id ( $_GET['refset'] );

	  echo $refset['name'];

	  
	  $refmeta = $refset['refmeta'];

	  preg_match_all(
	      '/\$([A-Za-z0-9_-]+)/',
	      $refset['refmeta'],
	      $cols_to_replace
	  );

      ?></h3>

      <table class="nbtTabledData">

            <tr class="nbtTableHeaders">
                  <td>Reference</td>
                  <td>Link to attachment</td>
                  <td style="min-width: 60px;">&nbsp;</td>
            </tr>

	    <?php

	    if ( is_dir ( ABS_PATH . "attach/files/" . $_GET['refset'] . "/" ) ) {

		$files = scandir ( ABS_PATH . "attach/files/" . $_GET['refset'] . "/" );

		foreach ( $files as $file ) {

                    if ( substr ($file, 0, 1) != "." ) {

                        // Get the ref id

                        $file_ref = explode(".", $file);

                        $refid = $file_ref[0];

                        switch ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) ) {

                            case 4:

				echo '<tr id="nbtAttachmentRow' . $_GET['refset'] . '-' . $refid . '-' . $file_ref[1] . '">';

				echo "<td>";
				
				
				$ref = nbt_get_reference_for_refsetid_and_refid( $_GET['refset'], $refid );
	      
	      foreach ( $cols_to_replace[0] as $col_to_replace ) {

		  $refmeta = preg_replace (
		      "/\\" . $col_to_replace . "\b/",
		      $ref[substr($col_to_replace, 1)],
		      $refmeta
		  );
	      }

	      echo "<div>" . $refmeta . "</div>";

              ?>
          </td>
          <td><a href="<?php echo SITE_URL; ?>attach/files/<?php echo $_GET['refset']; ?>/<?php echo $file; ?>">Attached file</a></td>
                                          <td>
                                                <button onclick="$(this).fadeOut(0);$('#nbtDeleteAttachment<?php echo $_GET['refset']; ?>-<?php echo $refid; ?>-<?php echo $file_ref[1]; ?>').fadeIn(500);">Delete</button>
                                                <button class="nbtHidden" id="nbtDeleteAttachment<?php echo $_GET['refset']; ?>-<?php echo $refid; ?>-<?php echo $file_ref[1]; ?>" onclick="nbtDeleteAttachment(<?php echo $_GET['refset']; ?>, <?php echo $refid; ?>, '<?php echo $file_ref[1]; ?>');">For real</button>
                                          </td>
                                    </tr><?php

                              break;

                              case 2:

                                    ?><tr>
                                          <td>
                                                <?php

                                                $ref = nbt_get_reference_for_refsetid_and_refid( $_GET['refset'], $refid );
						foreach ( $cols_to_replace[0] as $col_to_replace ) {

						    $refmeta = preg_replace (
							"/\\" . $col_to_replace . "\b/",
							$ref[substr($col_to_replace, 1)],
							$refmeta
						    );
						}

						echo "<div>" . $refmeta . "</div>";

                                                ?>
                                          </td>
                                          <td><?php echo $file; ?></td>
                                          <td>&nbsp;</td>
                                    </tr><?php

                              break;

                        }

                  }

            }

      }

      ?>

      </table>

</div>
