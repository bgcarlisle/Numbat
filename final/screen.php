<?php

$formelements = nbt_get_elements_for_formid ( $_GET['form'] );
$refset = nbt_get_refset_for_id ( $_GET['refset'] );
$form = nbt_get_form_for_id ( $_GET['form'] );

if (! isset($_GET['screeningpage'])) {
  $_GET['screeningpage'] = 1;
}

$assignments = nbt_get_assignments_for_refset_form_paginated (
  $_SESSION[INSTALL_HASH . '_nbt_userid'],
  $_GET['refset'], "referenceid",
  "DESC",
  "",
  FALSE,
  $_GET['screeningpage'],
  $_GET['form']
);

?>
