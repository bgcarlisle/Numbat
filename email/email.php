<div class="nbtContentPanel nbtGreyGradient">
    <h2>
	<img src="<?php echo SITE_URL; ?>images/useradmin.png" class="nbtTitleImage">
	Outgoing email settings
    </h2>
    <p>The following are settings for email sent by Numbat either to register an account or to reset a password.</p>
    <?php

    $outgoing_email_activated = nbt_get_setting ("outgoing_email_activated");

    ?>
    <p>Outgoing mail enabled</p>
    <select id="nbtOutgoingEmailToggle">
	<option value="1"<?php if ($outgoing_email_activated == "1") { echo " selected"; } ?>>Outgoing email enabled</option>
	<option value ="0"<?php if ($outgoing_email_activated == "0") { echo " selected"; } ?>>Outgoing email disabled</option>
    </select>

    <p>SMTP hostname (e.g. "smtp.emailcompany.com")</p>

    <input id="nbtOutgoingEmailHost" type="text" value="<?php echo nbt_get_setting("smtp_host"); ?>">

    <span id=""></span>

    <p>SMTP username (e.g. "youremail@emailcompany.com")</p>

    <input id="nbtOutgoingEmailUser" type="text" value="<?php echo nbt_get_setting("smtp_user"); ?>">

    <p>SMTP password</p>
    <p class="nbtFinePrint">Previously entered password not displayed; if you leave this field blank and click "Save changes" below, it will not update the password in the database. To clear the password from the database, enter a dummy password or a single space or something.</p>

    <input id="nbtOutgoingEmailPassword" type="text" value="">

    <div>
	<button onclick="nbtSaveOutgoingEmailSettings();">Save changes</button>
	<span id="nbtEmailUpdateFeedback" style="margin-top: 5px; padding: 2px 5px;">&nbsp;</span>
    </div>

</div>
