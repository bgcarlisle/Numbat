<div class="nbtContentPanel nbtGreyGradient">
    <h2>
	<img src="<?php echo SITE_URL; ?>images/useradmin.png" class="nbtTitleImage">
	User admininstration
    </h2>
    <table class="nbtTabledData">
	<tr class="nbtTableHeaders">
	    <td>Username</td>
	    <td>Email</td>
	    <td>Email verification</td>
	    <td>Password</td>
	    <td>Privileges</td>
	</tr>
	<?php

	$allusers = nbt_get_all_users ();

	foreach ( $allusers as $user ) {

	?><tr>
	    <td><?php echo $user['username']; ?></td>
	    <td><?php echo $user['email']; ?></td>
	    <td>
		<select id="nbtUserEmailVerified<?php echo $user['id']; ?>" onchange="nbtChangeUserEmailVerify(<?php echo $user['id']; ?>);"">
		    <option value="1"<?php if ($user['emailverify'] == "0") { echo " selected"; } ?>>Email verified</option>
		    <option value="0"<?php if ($user['emailverify'] != "0") { echo " selected"; } ?>>Email not verified</option>
		</select>
	    </td>
	    <td>
		<button onclick="nbtAdminChangePassword(<?php echo $user['id'] ?>);">Change password</button>
	    </td>
	    <td><?php

		if ( $user['id'] == $_SESSION[INSTALL_HASH . '_nbt_userid']) {

		?>Admin<?php

		       } else {

		       ?><select id="nbtUserPrivileges<?php echo $user['id']; ?>" onchange="nbtChangeUserPrivileges(<?php echo $user['id']; ?>);">
		<option value="0"<?php

				 if ( $user['privileges'] == 0 ) {

				     echo " selected";

				 }

				 ?>>None</option>
		<option value="2"<?php

				 if ( $user['privileges'] == 2 ) {

				     echo " selected";

				 }

				 ?>>User</option>
		<option value="4"<?php

				 if ( $user['privileges'] == 4 ) {

				     echo " selected";

				 }

				 ?>>Admin</option>
		       </select><?php

				}

				?>
	    </td>
	</tr><?php

	     }

	     ?>
    </table>

    <div class="nbtHidden nbtFeedbackGood nbtFeedback nbtFinePrint" id="nbtPrivilegeFeedback">&nbsp;</div>

    <div class="nbtHidden" id="nbtPasswordChangeFeedback" style="border: 1px solid #333; border-radius: 5px; padding: 10px;">&nbsp;</div>

    <p>Admin: can assign extractions, edit reference sets and forms, grant user privileges, do extractions and export data</p>
    <p>User: can do extractions and reconcile extractions with other users only</p>

    <p>All users must have verified email addresses in order to sign in. In the case that the user's email account is blocking emails from Numbat and they are not in the user's spam folder, you may mark a user's email address as verified manually and use the "change password" button above to generate a link to a form that will allow them to choose a password.</p>

</div>
