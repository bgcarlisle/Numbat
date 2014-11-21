<?php

include_once ("../../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) == 4 ) {

      $all_forms = nbt_get_all_extraction_forms ();

      $citation_form_elements = nbt_get_all_citation_form_elements ();

      foreach ( $all_forms as $form ) {

            nbt_move_extractions_for_form_db_refset_fromref_toref ( $form['id'], $_POST['refset'], $_POST['from_rid'], $_POST['to_rid'] );

      }

}

?>
