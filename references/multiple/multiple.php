<?php

include_once ('../../config.php');

$refset = nbt_get_refset_for_id ( $_GET['refset'] );

?><div class="nbtContentPanel">

      <h2>Manage multiple references</h2>

      <p>Reference set: <?php echo $refset['name']; ?></p>

      <p>Search for multiple identical references:</p>

      <input type="text" id="nbtSearchMultiples">

      <button onclick="nbtSearchForMultiples(<?php echo $_GET['refset']; ?>);">Search</button>

      <button onclick="$('#nbtMultipleSearchResponse').html('');$('#nbtSearchMultiples').val('');">Clear</button>

      <div id="nbtMultipleSearchResponse" class="nbtCitationSuggestions">&nbsp;</div>

</div>
