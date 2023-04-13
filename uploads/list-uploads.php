<div class="nbtContentPanel nbtGreyGradient">
    <h2>
        Upload files
    </h2>
    <p>Files uploaded using this tool will be available in the <code>uploads/</code> folder in this Numbat instance.</p>
    <form action="<?php echo SITE_URL; ?>upload/" method="post" enctype="multipart/form-data">
        <input type="file" name="file" id="file">
        <button>Upload file</button>
    </form>
</div>
