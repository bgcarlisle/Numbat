<?php

$form = nbt_get_form_for_id ($_GET['id']);

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . date("Y-m-d") . '-' . $form['name'] . '.json"');

$elements = nbt_get_elements_for_formid ($_GET['id']);
$selectoptions = nbt_get_all_select_options_for_formid ($_GET['id']);
$tabledatacols = nbt_get_all_table_data_cols_for_formid ($_GET['id']);
$subelements = nbt_get_all_subelements_for_formid ($_GET['id']);
$citationscols = nbt_get_all_citations_cols_for_formid ($_GET['id']);

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
    "elements"      => json_encode($elements),
    "selectoptions" => json_encode($selectoptions),
    "tabledatacols" => json_encode($tabledatacols),
    "subelements"   => json_encode($subelements),
    "citationscols" => json_encode($citationscols)
);

echo json_encode($formdata);

?>
