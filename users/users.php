<div class="nbtContentPanel nbtGreyGradient">
	<h2>User admininstration</h2>
	<table class="nbtTabledData">
		<tr class="nbtTableHeaders">
			<td>Username</td>
			<td>Email</td>
			<td>Privileges</td>
		</tr>
		<?php
		
		$allusers = nbt_get_all_users ();
		
		foreach ( $allusers as $user ) {
			
			?><tr>
				<td><?php echo $user['username']; ?></td>
				<td><?php echo $user['email']; ?></td>
				<td><?php
					
					if ( $user['id'] == $_SESSION['nbt_userid']) {
						
						?>Admin<?php
						
					} else {
						
						?><select id="nbtUserPrivileges<?php echo $user['id']; ?>" onchange="nbtChangeUserPrivileges(<?php echo $user['id']; ?>);">
							<option value="0"<?php
								
								if ( $user['privileges'] == 0 ) {
									
									?> selected<?php
									
								}
								
							?>>None</option>
							<option value="2"<?php
								
								if ( $user['privileges'] == 2 ) {
									
									?> selected<?php
									
								}
								
							?>>User</option>
							<option value="4"<?php
								
								if ( $user['privileges'] == 4 ) {
									
									?> selected<?php
									
								}
								
							?>>Admin</option>
						</select><?php
						
					}
					
				?></td>
			</tr><?php
			
		}
		
		?>
	</table>
	
	<div class="nbtHidden nbtFeedbackGood nbtFeedback nbtFinePrint" id="nbtPrivilegeFeedback">&nbsp;</div>
	
	<p>Admin: can assign extractions, edit reference sets and forms, grant user privileges, do extractions and export data</p>
	<p>User: can do extractions and reconcile extractions with other users only</p>
	
</div>