<div class="nbtContentPanel nbtGreyGradient">
	<h2>Manage extraction assignments</h2>
	
	<h3>Add a new assignment</h3>
	
	<p>Select a user, form and reference set</p>
	
	<select id="nbtAssignUser">
		<option value="NULL">Choose a user to assign</option>
		<?php
		
		$users = nbt_get_all_users ();
		
		foreach ( $users as $user ) {
			
			?><option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option><?php
			
		}
		
		?>
	</select>
	
	<select id="nbtAssignForm">
		<option value="NULL">Choose a form to use</option>
		<?php
		
		$forms = nbt_get_all_extraction_forms ();
		
		foreach ( $forms as $form ) {
			
			?><option value="<?php echo $form['id']; ?>"><?php echo $form['name']; ?></option><?php
			
		}
		
		?>
	</select>
	
	<select id="nbtAssignRefSet" onchange="$('#nbtSelectedRefSet').html('referenceset_' + $(this).val())">
		<option value="NULL">Choose a reference set</option>
		<?php
		
		$refsets = nbt_get_all_ref_sets ();
		
		foreach ( $refsets as $refset ) {
			
			?><option value="<?php echo $refset['id']; ?>"><?php echo $refset['name']; ?></option><?php
			
		}
		
		?>
	</select>
	
	<div id="nbtAssignReferenceSearch">
		<p>Search for a reference to assign</p>
		<input type="text" class="nbtCitationFinder" id="nbtReferenceFinder" onkeyup="nbtFindReferenceToAssign();">
		<div class="nbtCitationSuggestions" id="nbtFoundReferencesForAssigment">&nbsp;</div>
	
	</div>
	
	<p class="nbtFinePrint" id="nbtShowAdvancedAssignments"><a href="#" onclick="event.preventDefault();$('#nbtAdvancedAssignmentsWarning').slideToggle();">Advanced options</a></p>
	
	<div class="nbtHidden" id="nbtAdvancedAssignmentsWarning">
		<p>WARNING: By using this tool, you may delete all your assignments, extractions and forms. Only use this if you are reasonably competent in constructing MySQL queries.</p>
		<p>You are strongly advised to <a href="<?php echo SITE_URL; ?>backup/">back up your files</a> before every use of the advanced assignments tool.</p>
		<button onclick="$('#nbtAssignReferenceSearch').fadeOut(50);$('#nbtAdvancedAssignmentsWarning').fadeOut(50);$('#nbtShowAdvancedAssignments').fadeOut(50);$('#nbtAdvancedAssignments').slideDown();">I understand</button>
	</div>
	
	<div class="nbtHidden" id="nbtAdvancedAssignments">
		<p>Type a MySQL "WHERE" clause to be queried against the reference set table selected above (<span id="nbtSelectedRefSet">none chosen yet</span>).</p>
		<p>E.g. If your reference set has an "include" column that indicates whether the paper is to be extracted, you could write "`include` = 1" and that would 
		
		Or if you wanted to choose 50 random ones, write, "ORDER BY RAND() LIMIT 50". To assign 50 more that haven't been assigned yet, write, "`id` NOT IN (SELECT `referenceid` FROM `assignments` WHERE `refsetid` = 1) ORDER BY RAND() LIMIT 50"</p>
		<input type="text" class="nbtCitationFinder" id="nbtAdvancedAssignInput">
		<button onclick="$(this).fadeOut(0);$('#nbtExecuteQuery').fadeIn();">Execute query</button>
		<div class="nbtHidden" id="nbtExecuteQuery">
			<p>Are you sure? This can't be undone, and you could seriously ruin everything. <a href="<?php echo SITE_URL; ?>backup/">Did you make a backup yet? You should probably make a backup now.</a></p>
			<button onclick="nbtAddAdvancedAssignment();">Yes, God help me</button>
			<p class="nbtFinePrint nbtHidden" id="nbtAdvancedAssignmentFeedback">&nbsp;</p>
		</div>
	</div>
	
</div>