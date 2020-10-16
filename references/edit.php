<?php

$refset = nbt_get_refset_for_id ( $_GET['refset'] );

?><div class="nbtContentPanel nbtGreyGradient">
    <h2>Edit reference set</h2>
    <p>Name</p>
    <input id="nbtNewRefSetName" type="text" value="<?php echo $refset['name']; ?>" onblur="nbtChangeRefSetName(<?php echo $refset['id']; ?>);">
    <span class="nbtFinePrint nbtHidden nbtFeedback" id="nbtNewRefSetNameFeedback">&nbsp;</span>
</div>
