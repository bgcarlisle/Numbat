<div class="nbtContentPanel nbtGreyGradient">
    <h2>
        Upload files
    </h2>
    <p>Files uploaded using this tool will be available in the <code>uploads/files/</code> folder in this Numbat instance.</p>
    <h3>New file</h3>
    <form action="<?php echo SITE_URL; ?>uploads/" method="post" enctype="multipart/form-data">
        <input type="file" name="file" id="file">
	<input type="hidden" name="action" value="upload">
        <button>Upload file</button>
    </form>

    <h3>Files already uploaded</h3>

    <?php $files_in_db = nbt_get_all_uploads(); ?>
    <?php $files_on_disk = nbt_all_files_in_uploads_dir(); ?>

    <?php foreach ($files_on_disk as $file) { ?>
	<?php if (! in_array($file, array_column($files_in_db, "filename"))) { ?>
	    <div class="nbtBadField" style="margin: 5px 0 20px 0;"><strong>Warning: File <a href="<?php echo SITE_URL; ?>uploads/files/<?php echo $file; ?>" target="_blank"><?php echo $file; ?></a> is in the <code>upload/files/</code> directory, but not indexed as an upload</strong></div>
	<?php } ?>
    <?php } ?>
    
    <table class="nbtTabledData">
	<tr class="nbtTableHeaders">
	    <td>When uploaded</td>
	    <td>File name</td>
	    <td>Uploaded by</td>
	    <td>Delete</td>
	</tr>
	<?php foreach ($files_in_db as $file) { ?>
	    <tr id="nbtUploadRow<?php echo $file[0]; ?>">
		<td><?php echo $file['when_uploaded']; ?></td>
		<td>
		    <a href="<?php echo SITE_URL; ?>uploads/files/<?php echo $file['filename']; ?>" target="_blank"><?php echo $file['filename']; ?></a>
		    <?php if ( ! in_array ($file['filename'], $files_on_disk) ) { ?>
			<p><strong>Warning: File missing</strong></p>
		    <?php } ?>
		</td>
		<td><?php echo $file['username']; ?></td>
		<td>
		    <button id="nbtDeleteUploadButton<?php echo $file[0]; ?>" onclick="$('#nbtConfirmDeleteUpload<?php echo $file[0]; ?>').slideDown();$(this).slideUp(0);">Delete</button>
		    <div id="nbtConfirmDeleteUpload<?php echo $file[0]; ?>" class="nbtHidden">
			<button onclick="nbtDeleteUploadedFile(<?php echo $file[0]; ?>);">For real</button>
			<button onclick="$('#nbtConfirmDeleteUpload<?php echo $file[0]; ?>').slideUp(0);$('#nbtDeleteUploadButton<?php echo $file[0]; ?>').slideDown();">Cancel</button>
		    </div>
		</td>
	    </tr>
	<?php } ?>
    </table>
</div>
