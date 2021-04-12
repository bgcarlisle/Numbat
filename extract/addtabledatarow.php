<?php

include_once ('../config.php');

if ( nbt_get_privileges_for_userid ( $_SESSION[INSTALL_HASH . '_nbt_userid'] ) >= 2 ) {

    $newid = nbt_add_new_extraction_table_data_row ( $_POST['tid'], $_POST['refset'], $_POST['ref'], $_SESSION[INSTALL_HASH . '_nbt_userid'] );

    if ($newid) {

	$nbtExtractTableDataID = $_POST['tid'];
	$nbtExtractRefSet = $_POST['refset'];
	$nbtExtractRefID = $_POST['ref'];

	$tableformat = $_POST['tableformat'];

	// include ('./tabledata.php');

	$columns = nbt_get_all_columns_for_table_data ( $nbtExtractTableDataID );

	$no_of_columns = count ( $columns );

?>
    <tr class="nbtTDRow<?php echo $nbtExtractTableDataID; ?>" id="nbtTDRowID<?php echo $nbtExtractTableDataID; ?>-<?php echo $newid; ?>">
	<?php foreach ( $columns as $column ) { ?>
	    <td>
		<?php if ( $tableformat == "table_data" ) { ?>
		    <input type="text" id="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $newid; ?>" class="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>" value="<?php echo $row[$column['dbname']]; ?>" onkeyup="nbtExtractionTableDataKeyHandle(event, this, <?php echo $nbtExtractTableDataID; ?>, <?php echo $column['id']; ?>, <?php echo $nbtExtractRefSet; ?>, <?php echo $nbtExtractRefID; ?>);" onblur="nbtUpdateExtractionTableData(<?php echo $nbtExtractTableDataID; ?>, <?php echo $newid; ?>, '<?php echo $column['dbname']; ?>', 'nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $newid; ?>');">
		<?php } else { ?>
		    <textarea id="nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $newid; ?>" onblur="nbtUpdateExtractionTableData(<?php echo $nbtExtractTableDataID; ?>, <?php echo $newid; ?>, '<?php echo $column['dbname']; ?>', 'nbtTable<?php echo $nbtExtractTableDataID; ?>-<?php echo $column['id']; ?>-<?php echo $newid; ?>');"><?php echo $row[$column['dbname']]; ?></textarea>
		<?php }?>
	    </td>
	    
	<?php } ?>
	<td><button onclick="$(this).fadeOut(0);$('button#nbtTableExtractionRowDelete<?php echo $nbtExtractTableDataID; ?>-<?php echo $newid; ?>').fadeIn();">Delete</button>
	    <button id="nbtTableExtractionRowDelete<?php echo $nbtExtractTableDataID; ?>-<?php echo $newid; ?>" class="nbtHidden" onclick="nbtRemoveExtractionTableDataRow(<?php echo $nbtExtractTableDataID; ?>, <?php echo $newid; ?>);">For real</button></td>
    </tr>
    <?php

    } else {

	echo "Something went wrong";

    }

}

?>
