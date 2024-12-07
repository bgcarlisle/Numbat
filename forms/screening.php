<?php

$elements = nbt_get_elements_for_formid ($_GET['id']);

foreach ($elements as $element) {

    if ($element['type'] == "reference_data") {
	echo "<p>Extractors will be presented with the following data pulled from the reference set. Indicate which column of data to present by entering the name of the column from the reference set. E.g. if your column name in your reference set is `screening_notes`, type `\$screening_notes` below. If you specify a column that does not exist in that reference set, nothing will be displayed for that variable. You can use HTML to format your reference data.</p>";
	echo "<p>By default, extractors will be provided with the title, authors, year, journal and abstract items as specified on the reference set editor page. You can leave this blank if you do not wish to provide other reference data.</p>";
?><textarea style="width: 100%; height: 80px;" id="nbtElementColumnName<?php echo $element['id']; ?>" onblur="nbtChangeRefdataColumnName(<?php echo $element['id']; ?>);" maxlength="2500"><?php echo $element['columnname']; ?></textarea>
<?php
																																																					       
}

if ($element['columnname'] == "exclusion_reason") {
?><p>A user will be presented with two questions: 1. Include or exclude the reference in question?, 2. If it is to be excluded, choose one of the reasons why.</p>
    <p>You can enter reasons for exclusion below and change their order.</p>
    <p>If you need more flexibility, make an "extraction" type form.</p>
    <?php
    echo "<div id=\"nbtSingleSelectOptionsTable" . $element['id'] . "\">";
    
    $tableelementid = $element['id'];
    include ('./singleselectoptionstable.php');

    echo "</div>";
    }

    }

    ?>
