<?php

include_once ("../../config.php");

if ( nbt_get_privileges_for_userid ( $_SESSION['nbt_userid'] ) == 4 ) {

      $citation_form_elements = nbt_get_all_citation_form_elements ();

      foreach ( $citation_form_elements as $citation_form_element ) {

            nbt_move_citations_for_element_db_fromref_toref ( $citation_form_element['columnname'], $_POST['refset'], $_POST['from_rid'], $_POST['to_rid'] );

      }

}

?>
