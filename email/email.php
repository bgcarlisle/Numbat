<div class="nbtContentPanel nbtGreyGradient">
    <h2>
	<img src="<?php echo SITE_URL; ?>images/useradmin.png" class="nbtTitleImage">
	Outgoing email settings
    </h2>
    <p>The following are settings for email sent by Numbat either to register an account or to reset a password.</p>
    <?php

    $outgoing_email_activated = nbt_get_setting ("outgoing_email_activated");

    ?>
    <p>Outgoing mail activated</p>
    <select id="nbtOutgoingEmailToggle" onchange="nbtChangeOutgoingEmail();">
	<option value="1"<?php if ($outgoing_email_activated == "1") { echo " selected"; } ?>>Outgoing email activated</option>
	<option value ="0"<?php if ($outgoing_email_activated == "0") { echo " selected"; } ?>>Outgoing email not activated</option>
    </select>

    <p>SMTP hostname (e.g. "smtp.emailcompany.com")</p>

    <input id="nbtOutgoingEmailHost" type="text" onblur="nbtSaveOutgoingEmailSetting(this, 'host');" value="<?php echo nbt_get_setting("smtp_host"); ?>">

    <span id=""></span>

    <p>SMTP username (e.g. "youremail@emailcompany.com")</p>

    <input id="nbtOutgoingEmailUser" type="text" onblur="nbtSaveOutgoingEmailSetting(this, 'user');" value="<?php echo nbt_get_setting("smtp_user"); ?>">

    <p>SMTP password</p>

    <input id="nbtOutgoingEmailPassword" type="text" onblur="nbtSaveOutgoingEmailSetting(this, 'password');" value="<?php echo nbt_get_setting("smtp_password"); ?>">

</div>
