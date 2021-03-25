<?php

$form = nbt_get_form_for_id ($_GET['id']);

$elements = nbt_get_elements_for_formid ($_GET['id']);

if ( ! is_dir ( ABS_PATH . "forms/tmp/" ) ) {
    
    mkdir( ABS_PATH . "forms/tmp/", 0777 );
    
} else {

    chmod ( ABS_PATH . "forms/tmp/", 0777 );
}

$formdata = array(
    "name"          => $form['name'],
    "description"   => $form['description'],
    "version"       => $form['version'],
    "author"        => $form['author'],
    "affiliation"   => $form['affiliation'],
    "project"       => $form['project'],
    "protocol"      => $form['protocol'],
    "projectdate"   => $form['projectdate'],
    "numbatversion" => "2.12",
    "elements"      => json_encode($elements)
);

file_put_contents( ABS_PATH . "forms/tmp/form.json", json_encode($formdata));

$formfile = json_decode(file_get_contents(ABS_PATH . "forms/tmp/form.json"));

echo $formfile['name'];

?>
