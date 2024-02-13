<?php

$elements = nbt_get_elements_for_formid ($_GET['id']);

foreach ($elements as $element) {

  if ($element['columnname'] == "exclusion_reason") {
    ?><div id="nbtSingleSelectOptionsTable<?php echo $element['id']; ?>">
        <?php

        $tableelementid = $element['id'];

        include ('./singleselectoptionstable.php');

        ?>
    </div><?php
  }

}

?>
