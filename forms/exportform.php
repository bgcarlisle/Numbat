<?php

$form = nbt_get_form_for_id ($_GET['id']);

$elements = nbt_get_elements_for_formid ($_GET['id']);

if ( ! file_exists ( ABS_PATH . "forms/tmp/" ) ) {

    mkdir( ABS_PATH . "forms/tmp/" )
    
}

$readme .= "# Numbat form ";

echo yaml_emit($form);

?>
