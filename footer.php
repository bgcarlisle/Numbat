<script>

 var numbaturl = '<?php echo SITE_URL; ?>';

 $(document).ready(function () {

     $('#nbtFormElements').sortable();

     $('.nbtSubExtractionEditor').sortable();

     nbtCheckLogin();

     // Here comes the conditional display logic
     <?php

     if (isset($formelements) && count($formelements) > 0) {
	 foreach ($formelements as $ele) {
	     if ($ele['startup_visible'] != 1) {
		 // We're only interested in elements that are hidden at startup
		 $cd_events = nbt_get_conditional_events ($ele['id']);

		 // Make a CSS selector for all the things that should
		 // trigger this

		 $trigger_selectors = [];
		 foreach ($cd_events as $event) {
		     $trigger_element = nbt_get_form_element_for_elementid ($event['trigger_element']);
		     if ( ! in_array (".nbt" . $trigger_element['columnname'], $trigger_selectors) ) {
			 array_push($trigger_selectors, ".nbt" . $trigger_element['columnname']);
		     }
		 }
		 $trigger_selector = implode(", ", $trigger_selectors);

		 if (count (trigger_selectors) > 0) {

		     // Make jQuery expressions for each of the conditions
		     $event_expressions = [];
		     foreach ($cd_events as $event) {
			 $trigger_element = nbt_get_form_element_for_elementid ($event['trigger_element']);
			 $trigger_options = nbt_get_all_select_options_for_element ($event['trigger_element']);

			 switch ($trigger_element['type']) {
			     case "single_select":
				 switch ($event['type']) {
				     case "is":
					 foreach ($trigger_options as $opt) {
					     if ($opt['id'] == $event['trigger_option']) {
						 array_push($event_expressions, "$('#nbtQ" . $trigger_element['columnname'] . "A" . $opt['dbname'] . "').hasClass('nbtTextOptionChosen')");
					     }
					 }
					 break;
				     case "is-not":
					 foreach ($trigger_options as $opt) {
					     if ($opt['id'] == $event['trigger_option']) {
						 array_push($event_expressions, "! $('#nbtQ" . $trigger_element['columnname'] . "A" . $opt['dbname'] . "').hasClass('nbtTextOptionChosen')");
					     }
					 }
					 break;
				     case "has-response":
					 array_push($event_expressions, "$('.nbt" . $trigger_element['columnname'] . "').hasClass('nbtTextOptionChosen')");
					 break;
				     case "no-response":
					 array_push($event_expressions, "! $('.nbt" . $trigger_element['columnname'] . "').hasClass('nbtTextOptionChosen')");
					 break;
				 }
				 break;
			     case "multi_select":
				 switch ($event['type']) {
				     case "is":
					 foreach ($trigger_options as $opt) {
					     if ($opt['id'] == $event['trigger_option']) {
						 array_push($event_expressions, "$('#nbtMS" . $trigger_element['columnname'] . "_" . $opt['dbname'] . "').hasClass('nbtTextOptionChosen')");
					     }
					 }
					 break;
				     case "is-not":
					 foreach ($trigger_options as $opt) {
					     if ($opt['id'] == $event['trigger_option']) {
						 array_push($event_expressions, "! $('#nbtMS" . $trigger_element['columnname'] . "_" . $opt['dbname'] . "').hasClass('nbtTextOptionChosen')");
					     }
					 }
					 break;
				     case "has-response":
					 array_push($event_expressions, "$('.nbt" . $trigger_element['columnname'] . "').hasClass('nbtTextOptionChosen')");
					 break;
				     case "no-response":
					 array_push($event_expressions, "! $('.nbt" . $trigger_element['columnname'] . "').hasClass('nbtTextOptionChosen')");
					 break;
				 }
				 break;
			 }

		     }

		     switch ($ele['conditional_logical_operator']) {
			 case "any":
			     // Combine jQuery expressions with "OR"
			     $combined_expression = implode(" | ", $event_expressions);
			     break;
			 case "all":
			     // Combine jQuery expressions with "AND"
			     $combined_expression = implode(" & ", $event_expressions);
			     break;
		     }

		     if ($combined_expression == "") {
			 $combined_expression = "false";
		     }

		     echo "\n\n$('" . $trigger_selector . "').on('answerChange', function () {\n";

		     echo "  if (" . $combined_expression . ") {\n";

		     echo "    $('#nbtElementContainer" . $ele['id'] . "').slideDown();\n";

		     echo "  } else {\n";

		     echo "    $('#nbtElementContainer" . $ele['id'] . "').slideUp();\n";

		     if ($ele['destructive_hiding'] == 1) {
			 // Clear the element when hidden

			 switch ($ele['type']) {
			     case "open_text":
				 echo "    $('#nbtTextField" . $ele['columnname'] . "').val('');\n";
				 break;
			     case "text_area":
				 echo "    $('#nbtTextAreaField" . $ele['columnname'] . "').val('');\n";
				 break;
			     case "date_selector":
				 echo "    $('#nbtDateField" . $ele['columnname'] . "').val('');\n";
				 break;
			     case "single_select":
				 echo "    $('#nbtElementContainer" . $ele['id'] . " .nbtTextOptionChosen').click();\n";
				 break;
			     case "multi_select":
				 $element_options = nbt_get_all_select_options_for_element ($ele['id']);
				 foreach ($element_options as $ele_opt) {
				     echo "    nbtClearMultiSelect(" . $ele['formid'] . ", " . $extraction['id'] . ", '" . $ele['columnname'] . "_" . $ele_opt['dbname'] . "', 'nbtMS" . $ele['columnname'] . "_" . $ele_opt['dbname'] . "');\n";
				 }
				 break;
			     case "country_selector":
				 echo "    $('#nbtCountrySelect" . $ele['columnname'] . "').val('');\n";
				 break;
			     case "prev_select":
				 echo "    $('#nbtTextField" . $ele['columnname'] . "').val('');\n";
				 break;
			 }
		     }

		     echo "  }\n";

		     echo "});\n\n";


		 }

	     }

	     // Add conditional display logic for each sub-extraction

	     if ($ele['type'] == "sub_extraction") {

		 // Get all the subelements

		 $subelements = nbt_get_sub_extraction_elements_for_elementid ( $ele['id'] );

		 foreach ($subelements as $sele) {
		     if ($sele['startup_visible'] != 1) {
			 // We're only interested in subelements that are hidden at startup
			 $sub_cd_events = nbt_get_sub_conditional_events ($sele['id']);

			 // Make a CSS selector for all the things that should
			 // trigger this

			 $sub_trigger_selectors = [];
			 foreach ($sub_cd_events as $event) {
			     $trigger_element = nbt_get_sub_element_for_subelementid ($event['trigger_element']);

			     if (! in_array (".nbtCDSubelement" . $trigger_element['id'], $sub_trigger_selectors) ) {
				 array_push($sub_trigger_selectors, ".nbtCDSubelement" . $trigger_element['id']);
			     }

			 }
			 $sub_trigger_selector = implode(", ", $sub_trigger_selectors);

			 if (count ($sub_trigger_selectors) > 0) {

			     // Make jQuery expressions for each of the conditions
			     $sub_event_expressions = [];
			     foreach ($sub_cd_events as $event) {
				 $trigger_element = nbt_get_sub_element_for_subelementid ($event['trigger_element']);
				 $trigger_options = nbt_get_all_select_options_for_sub_element ($event['trigger_element']);

				 switch ($trigger_element['type']) {
				     case "single_select":
					 switch ($event['type']) {
					     case "is":
						 foreach ($trigger_options as $opt) {
						     if ($opt['id'] == $event['trigger_option']) {
							 array_push($sub_event_expressions, "$('#nbtSub" . $trigger_element['id'] . "-' + subexid + 'Q" . $trigger_element['dbname'] . "A" . $opt['dbname'] . "').hasClass('nbtTextOptionChosen')");
						     }
						 }
						 break;
					     case "is-not":
						 foreach ($trigger_options as $opt) {
						     if ($opt['id'] == $event['trigger_option']) {
							 array_push($sub_event_expressions, "! $('#nbtSub" . $trigger_element['id'] . "-' + subexid + 'Q" . $trigger_element['dbname'] . "A" . $opt['dbname'] . "').hasClass('nbtTextOptionChosen')");
						     }
						 }
						 break;
					     case "has-response":
						 array_push($sub_event_expressions, "$('.nbtSubCDSubextraction' + subexid + '.nbtCDSubelement" . $trigger_element['id'] . "').hasClass('nbtTextOptionChosen')");
						 break;
					     case "no-response":
						 array_push($sub_event_expressions, "! $('.nbtSubCDSubextraction' + subexid + '.nbtCDSubelement" . $trigger_element['id'] . "').hasClass('nbtTextOptionChosen')");
						 break;
					 }
					 break;
				     case "multi_select":
					 switch ($event['type']) {
					     case "is":
						 foreach ($trigger_options as $opt) {
						     if ($opt['id'] == $event['trigger_option']) {
							 array_push($sub_event_expressions, "$('#nbtSub" . $trigger_element['elementid'] . "-' + subexid + 'MS" . $opt['dbname'] . "').hasClass('nbtTextOptionChosen')");
						     }
						 }
						 break;
					     case "is-not":
						 foreach ($trigger_options as $opt) {
						     if ($opt['id'] == $event['trigger_option']) {
							 array_push($sub_event_expressions, "! $('#nbtSub" . $trigger_element['elementid'] . "-' + subexid + 'MS" . $opt['dbname'] . "').hasClass('nbtTextOptionChosen')");
						     }
						 }
						 break;
					     case "has-response":
						 array_push($sub_event_expressions, "$('.nbtSubCDSubextraction' + subexid + '.nbtCDSubelement" . $trigger_element['id'] . "').hasClass('nbtTextOptionChosen')");
						 break;
					     case "no-response":
						 array_push($sub_event_expressions, "! $('.nbtSubCDSubextraction' + subexid + '.nbtCDSubelement" . $trigger_element['id'] . "').hasClass('nbtTextOptionChosen')");
						 break;
					 }
					 break;
				 }
			     }

			     switch ($sele['conditional_logical_operator']) {
				 case "any":
				     // Combine jQuery expressions with "OR"
				     $sub_combined_expression = implode(" | ", $sub_event_expressions);
				     break;
				 case "all":
				     // Combine jQuery expressions with "AND"
				     $sub_combined_expression = implode(" & ", $sub_event_expressions);
				     break;
			     }

			     if ($sub_combined_expression == "") {
				 $sub_combined_expression = "false";
			     }

			     echo "\n\n$('.nbtSubExtraction').on('answerChange', '" . $sub_trigger_selector . "', function () {\n";

			     echo "  var subexid = $(this).attr('subextractionid');\n";

			     echo "  if (" . $sub_combined_expression . ") {\n";

			     echo "    $('#nbtSubelementContainer" . $sele['id'] . "-' + subexid).slideDown();\n";

			     echo "  } else {\n";

			     echo "    $('#nbtSubelementContainer" . $sele['id'] . "-' + subexid).slideUp();\n";

			     // Add destructive hiding part here

			     if ($sele['destructive_hiding'] == 1) {
				 switch ($sele['type']) {
				     case "open_text":
					 echo "    if ($('#nbtSub' + subexid + 'TextField" . $sele['dbname'] . "').val() != '') {\n";
					 echo "      $('#nbtSub' + subexid + 'TextField" . $sele['dbname'] . "').val('').blur();\n";
					 echo "    }";
					 break;
				     case "date_selector":
					 echo "    if ($('#nbtSub' + subexid + 'DateField" . $sele['dbname'] . "').val() != '') {\n";
					 echo "      $('#nbtSub' + subexid + 'DateField" . $sele['dbname'] . "').val('').blur();\n";
					 echo "    }";
					 break;
				     case "single_select":
					 echo "    $('.nbtCDSubelement" . $sele['id'] . ".nbtSubCDSubextraction' + subexid + '.nbtTextOptionChosen').click();\n";
					 break;
				     case "multi_select":
					 $sub_element_options = nbt_get_all_select_options_for_sub_element ($sele['id']);
					 foreach ($sub_element_options as $sele_opt) {
					     echo "    nbtClearSubextractionMultiSelect (" . $sele['elementid'] . ", subexid, '" . $sele['dbname'] . "_" . $sele_opt['dbname'] . "', 'nbtSub" . $sele['elementid'] . "-' + subexid + 'MS" . $sele_opt['dbname'] . "');\n";
					 }
					 break;

				 }

			     }

			     echo "  }\n";

			     echo "});\n\n";

			 }

		     }

		 }

	     }
	 }
     }

     echo "$('.nbtTextOptionSelect').trigger('answerChange');\n\n";

     ?>

     // End of conditional display logic

     $('.nbtAssignerDropdown').on('change', function () {

    	 eid = $(this).data('element');

    	 if ($('#nbtAssignUser' + eid).val() != "NULL" && $('#nbtAssignForm' + eid).val() != "NULL") {
    	     $('#nbtAssignerButton' + eid).prop('disabled', false);
    	 } else {
    	     $('#nbtAssignerButton' + eid).prop('disabled', true);
    	 }

     });

     // Screening
     if ($('#nbtScreeningGrid').length) { // If there's the screening table

       formid = $('#nbtScreeningGrid').data('formid');
       rsid = $('#nbtScreeningGrid').data('refsetid');

       // Focus goes where clicked
       $('#nbtScreeningGrid tr.nbtFocusableScreeningRow').on('click', function () {
         if (! $(this).hasClass('nbtScreeningFocus')) {
           $('#nbtScreeningGrid tr').removeClass('nbtScreeningFocus');
           $(this).addClass('nbtScreeningFocus');
         } else {
           $('#nbtScreeningGrid tr').removeClass('nbtScreeningFocus');
         }

       });

       // Add keyboard shortcuts
       document.addEventListener('keyup', function (event) {
         if (! $('input.nbtScreeningNotes:focus').length) { // These don't work when a notes is in focus

           if (! (event.getModifierState("Alt") | event.getModifierState("Control") | event.getModifierState("Meta"))) {
             // These don't work if Control, Alt or Meta are being pressed
             if (event.keyCode == 74) { // Letter j
               if ($('.nbtScreeningFocus').length) {
                 if ($('.nbtScreeningFocus').next('.nbtFocusableScreeningRow:visible').length) {
                   $('.nbtScreeningFocus').removeClass('nbtScreeningFocus').next('.nbtFocusableScreeningRow:visible').addClass('nbtScreeningFocus');
                   $('html, body').animate({
                     scrollTop: $('.nbtScreeningFocus').offset().top - 50
                   }, 250);
                 }
               } else {
                 $('.nbtFocusableScreeningRow:visible:first').addClass('nbtScreeningFocus');
               }

             }

             if (event.keyCode == 75) { // Letter k
               if ($('.nbtScreeningFocus').length) {
                 if ($('.nbtScreeningFocus').prev('.nbtFocusableScreeningRow:visible').length) {
                   $('.nbtScreeningFocus').removeClass('nbtScreeningFocus').prev('.nbtFocusableScreeningRow:visible').addClass('nbtScreeningFocus');
                   $('html, body').animate({
                     scrollTop: $('.nbtScreeningFocus').offset().top - 50
                   }, 250);
                 }
               } else {
                 $('.nbtFocusableScreeningRow:visible:last').addClass('nbtScreeningFocus');
               }
             }

             if (event.keyCode == 27) { // Esc key
               // Removes focus
               $('#nbtScreeningGrid tr').removeClass('nbtScreeningFocus');
             }

             if (event.keyCode == 49 || event.keyCode == 97) { // 1 or numpad 1
               // Mark as include
               $('.nbtScreeningFocus .nbtScreeningIncludeBox').click();
             }

             if ((event.keyCode >= 50 && event.keyCode <= 57) || (event.keyCode >= 98 && event.keyCode <= 105)) {
               if (event.keyCode <= 57) {
                 num = event.keyCode - 48;
               } else {
                 num = event.keyCode - 96;
               }

               boxnum = num - 2;

               // Mark exclusion reason
               $('.nbtScreeningFocus .nbtScreeningExcludeBox' + boxnum).click();
             }

             if (event.keyCode == 48 || event.keyCode == 96) { // 0 or numpad 0
               $('.nbtScreeningFocus input.nbtScreeningNotes').focus();
             }

             if (event.keyCode == 72) {
               $('.nbtScreeningDone').parent().fadeToggle(0);
             }

           }

         } else { // A notes input is focused
           if (event.keyCode == 27) { // Esc key
             // Removes focus
             $('.nbtScreeningFocus input.nbtScreeningNotes').blur();
           }
         }



       });

       // When the include box is clicked
       $('.nbtScreeningIncludeBox').on('click', function (event) {
         event.stopPropagation();

         includebox = $(this);
         $.ajax ({
           url: numbaturl + 'extract/updatescreening.php',
           type: 'post',
           data: {
            action: 'include',
            fid: formid,
            refset: rsid,
            rid: $(this).data('referenceid')
           },
           dataType: 'html'
         }).done ( function (response) {

          switch (response) {
            case "1":
              includebox.css("background-color","#ccffcc");
              includebox.html('Include');
              includebox.addClass('nbtScreeningDone');
              includebox.parent().children('.nbtScreeningExcludeBox').css("background-color", "");
              break;
            case "0":
              includebox.css("background-color","#ffcccc");
              includebox.addClass('nbtScreeningDone');
              includebox.html('Exclude');
              break;
            case "null":
              includebox.css("background-color","");
              includebox.html('Include?');
              includebox.removeClass('nbtScreeningDone');
              includebox.parent().children('.nbtScreeningExcludeBox').css("background-color", "");
              break;
            case "Error":
              alert('Error saving');
              break;
          }

         });

       });

       // When an exclude box is clicked
       $('.nbtScreeningExcludeBox').on('click', function (event) {
         event.stopPropagation();

        excludebox = $(this);
        $.ajax ({
          url: numbaturl + 'extract/updatescreening.php',
          type: 'post',
          data: {
           action: 'exclude',
           reason: $(this).data('excludereason'),
           fid: formid,
           refset: rsid,
           rid: $(this).data('referenceid')
          },
          dataType: 'html'
        }).done ( function (response) {

          if (response == "Clear all") {
            excludebox.parent().children('.nbtScreeningExcludeBox').css("background-color", "");
          } else {
            if ( response != "Error") {
              excludebox.parent().children('.nbtScreeningIncludeBox').css("background-color", "#ffcccc").html('Exclude');
              excludebox.parent().children('.nbtScreeningExcludeBox').css("background-color", "");
              excludebox.css("background-color","#ffcccc");
            } else {
              alert('Error saving');
            }

          }

        });

       });

       // When the notes field is clicked
       $('input.nbtScreeningNotes').on('click', function (event) {
         event.stopPropagation(); // Don't focus/unfocus the row
       });

      // When the notes field is unfocused
       $('input.nbtScreeningNotes').on('blur', function () {

        notesfield = $(this);
        $.ajax ({
          url: numbaturl + 'extract/updatescreening.php',
          type: 'post',
          data: {
           action: 'notes',
           notes: $(this).val(),
           fid: formid,
           refset: rsid,
           rid: $(this).data('referenceid')
          },
          dataType: 'html'
        }).done ( function (response) {

          if (response == "Saved") {
            notesfield.css("background-color","#ccffcc");
            setTimeout( function () {
         	     notesfield.css("background-color","");
         	 }, 1000);
          } else {
            alert("Error saving")
          }

        });
       });


     } // End of screening


 });

 function nbtClearText (id, originalText) { // This clears the "user name" field when it's clicked on.

     if (id.value == originalText) {
	 id.value='';

     }

 }

 function nbtRestoreText (id, originalText) { // This restores "user name" to the "user name" field when it's unclicked, if the field is empty.

     if (id.value.length == 0) {

	 id.value=originalText;

     }

 }

 function nbtSignupCheckEmail (id) {

     if (id.value.length > 0) {

	 // var emailregex=/[A-Za-z0-9._%+\-]\.[A-Za-z0-9.\-]\.[A-Za-z]{2,4}+$/; //^[a-zA-z]+$/
	 var emailregex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/

	 if ( ! emailregex.test (id.value) ) { // If it's not a well-formed email

	     $('#nbtSignupEmailFeedback').html('Enter a valid email address');
	     $("#nbtSignupEmailFeedback").removeClass('nbtFeedbackGood');
	     $("#nbtSignupEmailFeedback").addClass('nbtFeedbackBad');
	     $("#nbtSignupEmailFeedback").fadeIn(100);

	 } else { // Email is well-formed

	     // Check that the email isn't already in use on another account

	     $.ajax ({
		 url: numbaturl + 'signup/checkemail.php',
		 type: 'post',
		 data: {
		     email: $(id).val()
		 },
		 dataType: 'html'
	     }).done( function (response) {

		 $('#nbtSignupEmailFeedback').html(response);

		 if (response == 'Email is already in use :(') {
		     $("#nbtSignupEmailFeedback").removeClass('nbtFeedbackGood');
		     $("#nbtSignupEmailFeedback").addClass('nbtFeedbackBad');
		     $("#nbtSignupEmailFeedback").fadeIn(100);
		 } else {
		     $("#nbtSignupEmailFeedback").removeClass('nbtFeedbackBad');
		     $("#nbtSignupEmailFeedback").addClass('nbtFeedbackGood');
		     $("#nbtSignupEmailFeedback").fadeIn(100);
		 }
	     });

	 }

     } else {
	 $('#nbtSignupEmailFeedback').html('');
	 $("#nbtSignupEmailFeedback").removeClass('nbtFeedbackGood');
	 $("#nbtSignupEmailFeedback").removeClass('nbtFeedbackBad');
	 $("#nbtSignupEmailFeedback").fadeOut(100);

     }

 }

 function nbtChangeUserPrivileges ( userid ) {

     $.ajax ({
    	 url: numbaturl + 'users/changeprivileges.php',
    	 type: 'post',
    	 data: {
  	     user: userid,
  	     privileges: $('#nbtUserPrivileges' + userid).val()
    	 },
	     dataType: 'html'
     }).done ( function (html) {

    	 $('#nbtPrivilegeFeedback').html(html);

    	 $('#nbtPrivilegeFeedback').fadeIn(500, function () {
    	     $('#nbtPrivilegeFeedback').fadeOut(3000);
    	 });

     });

 }

 function nbtChangeUserEmailVerify ( userid ) {

     $.ajax ({
	 url: numbaturl + 'users/changeemailverify.php',
	 type: 'post',
	 data: {
	     user: userid,
	     verify: $('#nbtUserEmailVerified' + userid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtPrivilegeFeedback').html(html);

	 $('#nbtPrivilegeFeedback').fadeIn(500, function () {

	     $('#nbtPrivilegeFeedback').fadeOut(3000);

	 });

     });

 }

 function nbtAdminChangePassword ( userid ) {

     $.ajax ({
	 url: numbaturl + 'users/generatepasswordlink.php',
	 type: 'post',
	 data: {
	     user: userid
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 $('#nbtPasswordChangeFeedback').slideUp(500, function () {
	     $('#nbtPasswordChangeFeedback').html(response);
	     $('#nbtPasswordChangeFeedback').slideDown();
	 });

     });

 }

 function nbtNewExtractionForm (type) {

     $.ajax ({
    	 url: numbaturl + 'forms/newform.php',
    	 type: 'post',
    	 data: {
         formtype: type
    	 },
    	 dataType: 'html'
     }).done ( function (html) {

	     $('#nbtFormsTable').html(html);

     });

 }

 function nbtDeleteExtraction ( fid, eid, rsid ) {

     $.ajax ({
	 url: numbaturl + 'references/multiple/delete_extraction.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     extractionid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 nbtSearchForMultiples ( rsid );

     });

 }

 function nbtDeleteForm ( fid ) {

     $.ajax ({
	 url: numbaturl + 'forms/deleteform.php',
	 type: 'post',
	 data: {
	     formid: fid
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 if ( response == "Deleted" ) {
	     $('#nbtFormTableRow' + fid).fadeOut(100, function() {
		 $('#nbtFormTableRow' + fid).remove();
	     });
	 }

     });

 }

 function nbtSaveFormMetadata ( fid, col ) {

     $.ajax ({
	 url: numbaturl + 'forms/changeformmetadata.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     column: col,
	     newval: $('#nbtFormMetadata-' + col).val()
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 if (response == "Changes saved") {

	     $('#nbtFormMetadata-' + col).addClass('nbtBackgroundFeedbackGood');
	     
             setTimeout( function () {
         	 $('#nbtFormMetadata-' + col).removeClass('nbtBackgroundFeedbackGood');
             }, 1000);
	     
	 } else {

	     $('#nbtFormMetadata-' + col).addClass('nbtBackgroundFeedbackBad');
	     
             setTimeout( function () {
         	 $('#nbtFormMetadata-' + col).removeClass('nbtBackgroundFeedbackBad');
             }, 1000);
	     
	 }

     });

 }

 function nbtAddNewOpenText ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addopentext.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtAddNewPrevSelect ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addprevselect.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtAddNewTagsElement ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addtagselement.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtAddNewTextArea ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addtextarea.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtAddNewSingleSelect ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsingleselect.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtAddNewSectionHeading ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsectionheading.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtAddNewAssignmentEditor ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addassignmenteditor.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtAddNewRefdata ( fid, eid ) {
     $.ajax ({
	 url: numbaturl + 'forms/addrefdata.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });
 }

 function nbtAddNewExtractionTimer ( fid, eid ) {
     $.ajax ({
	 url: numbaturl + 'forms/addextractiontimer.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });
 }

 function nbtDeleteFormElement ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/deleteformelement.php',
	 type: 'post',
	 data: {
	     element: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElement' + eid).slideUp(1000);

     });

 }

 function nbtRemoveSpecialCharactersFromField ( id ) {

     // get rid of special characters

     column_name = $( id ).val();

     column_name = column_name.replace(/[^A-Za-z0-9_]+/g, '_');
     column_name = column_name.replace(/__/g, '_');
     column_name = column_name.replace(/^_/, '');
     column_name = column_name.replace(/_$/, '');

     $( id ).val(column_name);

 }

 function nbtChangeColumnName ( eid ) {

     nbtRemoveSpecialCharactersFromField ('#nbtElementColumnName' + eid);

     $.ajax ({
	 url: numbaturl + 'forms/changecolumnname.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newcolumnname: $('#nbtElementColumnName' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 $('#nbtFormElementFeedback' + eid).html(response);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeRefdataColumnName ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/changerefdatacolumnname.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newcolumnname: $('#nbtElementColumnName' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeSubRefdataFormat ( seid ) {

     $.ajax ({
	 url: numbaturl + 'forms/changesubrefdataformat.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     newcolumnname: $('#nbtSubRefdataFormat' + seid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeRegex ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/changeregex.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newregex: $('#nbtElementRegex' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });
 }

 function nbtChangeTagsPrompts ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/changetagsprompts.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newtagsprompts: $('#nbtElementTagsPrompts' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });
 }

 function nbtChangeSubTagsPrompts ( seid ) {

     $.ajax ({
	 url: numbaturl + 'forms/changesubtagsprompts.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     newtagsprompts: $('#nbtSubElementTagsPrompts' + seid).val()
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 $('#nbtSubElementFeedback' + seid).html(response);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });
 }

 function nbtSearchTagsPrompts ( eid ) {

     query = $('#TagSearch' + eid).val();

     if ( query == '' ) {
	 $('.TagPrompts' + eid).fadeOut(0);
     } else {
	 $('.TagPrompts' + eid).fadeOut(0);

	 $('.TagPrompts' + eid + ' td.TagPromptCell').each( function (index) {
	     cell_value = $(this).html();

	     if (cell_value.toLowerCase().search(query.toLowerCase()) != -1) {
		 $(this).parent().fadeIn(0);
	     }
	 });
     }

 }

 function nbtSearchSubTagsPrompts ( inputelement ) {

     query = inputelement.val();

     if ( query == '' ) {
	 inputelement.parent().parent().parent().find('.TagPromptRow').fadeOut(0);
     } else {
	 inputelement.parent().parent().parent().find('.TagPromptRow').fadeOut(0);


	 inputelement.parent().parent().parent().find('.TagPromptRow td.TagPromptCell').each( function (index) {
	     cell_value = $(this).html();

	     if (cell_value.toLowerCase().search(query.toLowerCase()) != -1) {
		 $(this).parent().fadeIn(0);
	     }
	 });

     }

 }

 function nbtUpdateSelectedTagsTable ( eid, exid, form, columnname ) {

     selectedtagstext = $('#SelectedTagsText' + eid).val();
     selectedtags = selectedtagstext.split(";");
     selectedtags = selectedtags.map(s => s.trim());
     selectedtags = selectedtags.sort();

     tagpromptstext = $('#nbtElementTagsPrompts' + eid).val();
     tagprompts = tagpromptstext.split(";");
     tagprompts = tagprompts.map(s => s.trim());
     tagprompts = tagprompts.sort();

     $('#SelectedTagsTable' + eid + ' tr:not(.nbtTableHeaders)').remove();

     for (var key in selectedtags) {

	 if (selectedtags[key] != '') {

	     if ( tagprompts.includes(selectedtags[key])) {
		 addtopromptsbutton = '';
	     } else {
		 addtopromptsbutton = '<button onclick="nbtAddTagToPrompts(' + eid + ', $(this));">Add to prompts</button> ';
	     }

	     $('#SelectedTagsTable' + eid).append('<tr><td><input type="text" value="' + selectedtags[key] + '" onblur="nbtRemoveTagFromSelected(' + eid + ', \'' + selectedtags[key].replace(/\'/g, '\\\'') + '\');nbtAddTagToSelected(' + eid + ', $(this).val(), ' + exid + ', ' + form + ', \'' + columnname + '\');"></td><td style="text-align: right;">' + addtopromptsbutton + '<button onclick="nbtRemoveTagFromSelected(' + eid + ', \'' + selectedtags[key].replace(/\'/g, '\\\'') + '\', ' + exid + ', ' + form + ', \'' + columnname + '\');">Remove</button></td></tr>');

	 }

     }

     $('#SelectedTagsTable' + eid).append('<tr><td><input type="text" placeholder="Add new tag" value="" onblur="nbtAddTagToSelected(' + eid + ', $(this).val(), ' + exid + ', ' + form + ', \'' + columnname + '\');" onkeyup="if (event.keyCode == 13) {nbtAddTagToSelected(' + eid + ', $(this).val(), ' + exid + ', ' + form + ', \'' + columnname + '\');}"></td><td>&nbsp;</td></tr>');

     // Update the database
     $.ajax ({
	 url: numbaturl + 'extract/updateextraction.php',
	 type: 'post',
	 data: {
	     fid: form,
	     id: exid,
	     question: columnname,
	     answer: selectedtags.join(";")
	 },
	 dataType: 'html'
     }).done( function (response) {

	 $('#TagFeedback' + eid).html(response);
	 $('#TagFeedback' + eid).slideDown(400);

	 setTimeout( function () {
	     $('#TagFeedback' + eid).slideUp(400);
	 }, 2000);

     });

 }

 function nbtUpdateSelectedSubTagsTable ( eid, seid, subexid, columnname ) {

     selectedtagstext = $('#SelectedSubTagsText' + seid + '-' + subexid).val();
     selectedtags = selectedtagstext.split(";");
     selectedtags = selectedtags.map(s => s.trim());
     selectedtags = selectedtags.sort();

     tagpromptstext = $('.nbtSubElementTagsPrompts' + seid).val();
     tagprompts = tagpromptstext.split(";");
     tagprompts = tagprompts.map(s => s.trim());
     tagprompts = tagprompts.sort();

     $('#SelectedSubTagsTable' + seid + '-' + subexid + ' tr:not(.nbtTableHeaders)').remove();

     for (var key in selectedtags) {

	 if (selectedtags[key] != '') {

	     if ( tagprompts.includes(selectedtags[key])) {
		 addtopromptsbutton = '';
	     } else {
		 addtopromptsbutton = '<button onclick="nbtAddSubTagToPrompts(' + seid + ', $(this));">Add to prompts</button> ';
	     }

	     $('#SelectedSubTagsTable' + seid + '-' + subexid).append('<tr><td><input type="text" value="' + selectedtags[key] + '" onblur="nbtRemoveSubTagFromSelected(' + seid + ', ' + subexid + ', \'' + selectedtags[key].replace(/\'/g, '\\\'') + '\');nbtAddSubTagToSelected(' + eid + ', ' + seid + ', $(this).val(), ' + subexid + ', \'' + columnname + '\');"></td><td style="text-align: right;">' + addtopromptsbutton + '<button onclick="nbtRemoveSubTagFromSelected(' + eid + ', ' + seid + ', \'' + selectedtags[key].replace(/\'/g, '\\\'') + '\', ' + subexid + ', \'' + columnname + '\');">Remove</button></td></tr>');

	 }

     }

     $('#SelectedSubTagsTable' + seid + '-' + subexid).append('<tr><td><input type="text" placeholder="Add new tag" value="" onblur="nbtAddSubTagToSelected(' + eid + ', ' + seid + ', $(this).val(), ' + subexid + ', \'' + columnname + '\');" onkeyup="if (event.keyCode == 13) {nbtAddSubTagToSelected(' + eid + ', ' + seid + ', $(this).val(), ' + subexid + ', \'' + columnname + '\');}"></td><td>&nbsp;</td></tr>');

     // Update the database
     $.ajax ({
	 url: numbaturl + 'extract/updatesubextraction.php',
	 type: 'post',
	 data: {
	     eid: eid,
	     id: subexid,
	     question: columnname,
	     answer: selectedtags.join(";")
	 },
	 dataType: 'html'
     }).done( function (response) {

	 $('#TagFeedback' + eid).html(response);
	 $('#TagFeedback' + eid).slideDown(400);

	 setTimeout( function () {
	     $('#TagFeedback' + eid).slideUp(400);
	 }, 2000);

     });

 }

 function nbtUpdateSelectedFinalSubTagsTable ( eid, seid, subexid, columnname ) {

     selectedtagstext = $('#SelectedSubTagsText' + seid + '-' + subexid).val();
     selectedtags = selectedtagstext.split(";");
     selectedtags = selectedtags.map(s => s.trim());
     selectedtags = selectedtags.sort();

     $('#SelectedSubTagsTable' + seid + '-' + subexid + ' tr:not(.nbtTableHeaders)').remove();

     for (var key in selectedtags) {

	 if (selectedtags[key] != '') {

	     $('#SelectedSubTagsTable' + seid + '-' + subexid).append('<tr><td><input type="text" value="' + selectedtags[key] + '" onblur="nbtRemoveSubTagFromSelectedFinal(' + seid + ', ' + subexid + ', \'' + selectedtags[key].replace(/\'/g, '\\\'') + '\');nbtAddSubTagToSelectedFinal(' + eid + ', ' + seid + ', $(this).val(), ' + subexid + ', \'' + columnname + '\');"></td><td style="text-align: right;"><button onclick="nbtRemoveSubTagFromSelectedFinal(' + eid + ', ' + seid + ', \'' + selectedtags[key].replace(/\'/g, '\\\'') + '\', ' + subexid + ', \'' + columnname + '\');">Remove</button></td></tr>');

	 }

     }

     $('#SelectedSubTagsTable' + seid + '-' + subexid).append('<tr><td><input type="text" placeholder="Add new tag" value="" onblur="nbtAddSubTagToSelectedFinal(' + eid + ', ' + seid + ', $(this).val(), ' + subexid + ', \'' + columnname + '\');" onkeyup="if (event.keyCode == 13) {nbtAddSubTagToSelectedFinal(' + eid + ', ' + seid + ', $(this).val(), ' + subexid + ', \'' + columnname + '\');}"></td><td>&nbsp;</td></tr>');

     // Update the database
     $.ajax ({
	 url: numbaturl + 'final/updatesubextraction.php',
	 type: 'post',
	 data: {
	     eid: eid,
	     id: subexid,
	     question: columnname,
	     answer: selectedtags.join(";")
	 },
	 dataType: 'html'
     }).done( function (response) {

	 $('#TagFeedback' + seid + '-' + subexid).html(response);
	 $('#TagFeedback' + seid + '-' + subexid).slideDown(400);

	 setTimeout( function () {
	     $('#TagFeedback' + seid + '-' + subexid).slideUp(400);
	 }, 2000);

     });

 }

 function nbtAddTagToSelected ( eid, tagval, exid, form, columnname ) {

     tagval = tagval.replace(";", "_");

     selectedtagstext = $('#SelectedTagsText' + eid).val();

     selectedtags = selectedtagstext.split(";");

     found = 0;

     for (var key in selectedtags) {

	 if (selectedtags[key].toLowerCase().trim() == "") {
	     selectedtags.splice(key, 1);
	 } else {

	     if (selectedtags[key].toLowerCase().trim() == tagval.toLowerCase().trim()) {
		 found = 1;
	     }

	 }

     }

     if (found == 0) {
	 if (tagval != '') {
	     selectedtags.push(tagval.trim());
	 }
	 selectedtagstext = selectedtags.sort().join(";");
	 $('#SelectedTagsText' + eid).val(selectedtagstext);
     }

     nbtUpdateSelectedTagsTable ( eid, exid, form, columnname );

 }

function nbtAddSubTagToSelected ( eid, seid, tagval, subexid, columnname ) {

     tagval = tagval.replace(";", "_");

     selectedtagstext = $('#SelectedSubTagsText' + seid + '-' + subexid).val();

     selectedtags = selectedtagstext.split(";");

     found = 0;

     for (var key in selectedtags) {

	 if (selectedtags[key].toLowerCase().trim() == "") {
	     selectedtags.splice(key, 1);
	 } else {

	     if (selectedtags[key].toLowerCase().trim() == tagval.toLowerCase().trim()) {
		 found = 1;
	     }

	 }

     }

     if (found == 0) {
	 if (tagval != '') {
	     selectedtags.push(tagval.trim());
	 }
	 selectedtagstext = selectedtags.sort().join(";");
	 $('#SelectedSubTagsText' + seid + '-' + subexid).val(selectedtagstext);
     }

     nbtUpdateSelectedSubTagsTable ( eid, seid, subexid, columnname );

 }

function nbtAddSubTagToSelectedFinal ( eid, seid, tagval, subexid, columnname ) {

     tagval = tagval.replace(";", "_");

     selectedtagstext = $('#SelectedSubTagsText' + seid + '-' + subexid).val();

     selectedtags = selectedtagstext.split(";");

     found = 0;

     for (var key in selectedtags) {

	 if (selectedtags[key].toLowerCase().trim() == "") {
	     selectedtags.splice(key, 1);
	 } else {

	     if (selectedtags[key].toLowerCase().trim() == tagval.toLowerCase().trim()) {
		 found = 1;
	     }

	 }

     }

     if (found == 0) {
	 if (tagval != '') {
	     selectedtags.push(tagval.trim());
	 }
	 selectedtagstext = selectedtags.sort().join(";");
	 $('#SelectedSubTagsText' + seid + '-' + subexid).val(selectedtagstext);
     }

     nbtUpdateSelectedFinalSubTagsTable ( eid, seid, subexid, columnname );

 }

 function nbtRemoveTagFromSelected ( eid, tagval, exid, form, columnname ) {

     selectedtagstext = $('#SelectedTagsText' + eid).val();

     selectedtags = selectedtagstext.split(";");

     found = 0;

     for (var key in selectedtags) {

	 if (selectedtags[key].toLowerCase().trim() == "") {
	     selectedtags.splice(key, 1);
	 } else {

	     if (selectedtags[key].toLowerCase().trim() == tagval.toLowerCase().trim()) {
		 selectedtags.splice(key, 1);
	     }

	 }

     }

     selectedtagstext = selectedtags.sort().join(";");
     $('#SelectedTagsText' + eid).val(selectedtagstext);

     nbtUpdateSelectedTagsTable ( eid, exid, form, columnname );

 }

 function nbtRemoveSubTagFromSelected ( eid, seid, tagval, subexid, columnname ) {

     selectedtagstext = $('#SelectedSubTagsText' + seid + '-' + subexid).val();

     selectedtags = selectedtagstext.split(";");

     found = 0;

     for (var key in selectedtags) {

	 if (selectedtags[key].toLowerCase().trim() == "") {
	     selectedtags.splice(key, 1);
	 } else {

	     if (selectedtags[key].toLowerCase().trim() == tagval.toLowerCase().trim()) {
		 selectedtags.splice(key, 1);
	     }

	 }

     }

     selectedtagstext = selectedtags.sort().join(";");
     $('#SelectedSubTagsText' + seid + '-' + subexid).val(selectedtagstext);

     nbtUpdateSelectedSubTagsTable ( eid, seid, subexid, columnname );

 }

 function nbtRemoveSubTagFromSelectedFinal ( eid, seid, tagval, subexid, columnname ) {

     selectedtagstext = $('#SelectedSubTagsText' + seid + '-' + subexid).val();

     selectedtags = selectedtagstext.split(";");

     found = 0;

     for (var key in selectedtags) {

	 if (selectedtags[key].toLowerCase().trim() == "") {
	     selectedtags.splice(key, 1);
	 } else {

	     if (selectedtags[key].toLowerCase().trim() == tagval.toLowerCase().trim()) {
		 selectedtags.splice(key, 1);
	     }

	 }

     }

     selectedtagstext = selectedtags.sort().join(";");
     $('#SelectedSubTagsText' + seid + '-' + subexid).val(selectedtagstext);

     nbtUpdateSelectedFinalSubTagsTable ( eid, seid, subexid, columnname );

 }

 function nbtAddTagToPrompts ( eid, button ) {

     tagpromptstext = $('#nbtElementTagsPrompts' + eid).val();
     tagprompts = tagpromptstext.split(";");
     tagprompts = tagprompts.map(s => s.trim());
     newtag = button.parent().parent().children().find('input').val().trim();
     tagprompts.push(newtag);
     tagprompts = tagprompts.sort();
     $('#nbtElementTagsPrompts' + eid).val(tagprompts.join(";"));

     $.ajax ({
	 url: numbaturl + 'extract/changetagsprompts.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newtagsprompts: $('#nbtElementTagsPrompts' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {
	 button.replaceWith('<span>Added to tag prompts for future extractions</span>');
     });
 }

 function nbtAddSubTagToPrompts ( seid, button ) {

     tagpromptstext = $('.nbtSubElementTagsPrompts' + seid).val();
     tagprompts = tagpromptstext.split(";");
     tagprompts = tagprompts.map(s => s.trim());
     newtag = button.parent().parent().children().find('input').val().trim();
     tagprompts.push(newtag);
     tagprompts = tagprompts.sort();
     $('.nbtSubElementTagsPrompts' + seid).val(tagprompts.join(";"));

     $.ajax ({
	 url: numbaturl + 'extract/changesubtagsprompts.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     newtagsprompts: $('.nbtSubElementTagsPrompts' + seid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {
	 button.replaceWith('<span>Added to tag prompts for future extractions</span>');
     });
 }

 function nbtChangeSubElementRegex ( seid ) {

     $.ajax ({
	 url: numbaturl + 'forms/changesubelementregex.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     newregex: $('#nbtSubElementRegex' + seid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });
 }

 function nbtChangeDateColumnName ( eid ) {

     nbtRemoveSpecialCharactersFromField ('#nbtElementColumnName' + eid);

     $.ajax ({
	 url: numbaturl + 'forms/changedatecolumnname.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newcolumnname: $('#nbtElementColumnName' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeMultiSelectColumnPrefix ( eid ) {

     nbtRemoveSpecialCharactersFromField ('#nbtElementColumnPrefix' + eid);

     $.ajax ({
	 url: numbaturl + 'forms/changemultiselectcolumnprefix.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newcolumnname: $('#nbtElementColumnPrefix' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeDisplayName ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/changedisplayname.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newdisplayname: $('#nbtElementDisplayName' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeElementCodebook ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/changeelementcodebook.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newcodebook: $('#nbtElementCodebook' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtMoveSelectOption ( eid, sid, dir ) {

     $.ajax ({
	 url: numbaturl + 'forms/moveselectoption.php',
	 type: 'post',
	 data: {
	     selectid: sid,
	     element: eid,
	     direction: dir
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSingleSelectOptionsTable' + eid).html(html);

     });

 }

 function nbtMoveMultiSelectOption ( eid, sid, dir ) {

     $.ajax ({
	 url: numbaturl + 'forms/movemultiselectoption.php',
	 type: 'post',
	 data: {
	     selectid: sid,
	     element: eid,
	     direction: dir
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMultiSelectOptionsTable' + eid).html(html);

     });

 }

 function nbtMoveSubExtraction ( eid, seid, dir, rsid, rid, uid ) {

     $.ajax ({
	 url: numbaturl + 'extract/movesubextraction.php',
	 type: 'post',
	 data: {
	     element: eid,
	     subextraction: seid,
	     direction: dir,
	     refset: rsid,
	     ref: rid,
	     userid: uid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtraction' + eid + '-' + uid).html(html);

     });

 }

 function nbtMasterMoveSubExtraction ( eid, seid, dir, rsid, rid, uid ) {

     $.ajax ({
	 url: numbaturl + 'final/movesubextraction.php',
	 type: 'post',
	 data: {
	     element: eid,
	     subextraction: seid,
	     direction: dir,
	     refset: rsid,
	     ref: rid,
	     userid: uid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtraction' + eid + '-' + uid).html(html);

     });

 }

 function nbtAddSingleSelectOption ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsingleselectoption.php',
	 type: 'post',
	 data: {
	     element: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSingleSelectOptionsTable' + eid).html(html);

     });

 }

 function nbtAddMultiSelectOption ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addmultiselectoption.php',
	 type: 'post',
	 data: {
	     element: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMultiSelectOptionsTable' + eid).html(html);

     });

 }

 function nbtRemoveSingleSelectOption ( eid, sid ) {

     $.ajax ({
	 url: numbaturl + 'forms/removesingleselectoption.php',
	 type: 'post',
	 data: {
	     selectid: sid,
	     element: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSingleSelectOptionsTable' + eid).html(html);

     });

 }

 function nbtRemoveSubSingleSelectOption ( eid, sid ) {

     $.ajax ({
	 url: numbaturl + 'forms/removesubsingleselectoption.php',
	 type: 'post',
	 data: {
	     selectid: sid,
	     element: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubSingleSelectOptionsTable' + eid).html(html);

     });

 }

 function nbtRemoveSubMultiSelectOption ( seid, sid ) {

     $.ajax ({
	 url: numbaturl + 'forms/removesubmultiselectoption.php',
	 type: 'post',
	 data: {
	     selectid: sid,
	     subelement: seid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubMultiSelectOptionsTable' + seid).html(html);

     });

 }

 function nbtRemoveMultiSelectOption ( eid, sid ) {

     $.ajax ({
	 url: numbaturl + 'forms/removemultiselectoption.php',
	 type: 'post',
	 data: {
	     selectid: sid,
	     element: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMultiSelectOptionsTable' + eid).html(html);

     });

 }

 function nbtChangeElementToggle ( eid ) {

     if ( $('#nbtElementToggle' + eid).val() != '' ) {

	 var regex=/^[0-9A-Za-z.\-_]+$/; //^[a-zA-z]+$/

	 if ( ! regex.test ( $('#nbtElementToggle' + eid).val() ) ) {

	     $('#nbtElementToggle' + eid).addClass('nbtBadField');

	 } else {

	     $('#nbtElementToggle' + eid).removeClass('nbtBadField');

	 }

     } else {

	 $('#nbtElementToggle' + eid).removeClass('nbtBadField');

     }

     $.ajax ({
	 url: numbaturl + 'forms/changeelementtoggle.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newtoggle: $('#nbtElementToggle' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtUpdateSingleSelect ( eid, sid, dbcolumn ) {

     if ( dbcolumn == 'dbname' ) {
	 nbtRemoveSpecialCharactersFromField ('#nbtSingleSelect' + sid + dbcolumn);
     }

     $.ajax ({
	 url: numbaturl + 'forms/updatesingleselect.php',
	 type: 'post',
	 data: {
	     selectid: sid,
	     column: dbcolumn,
	     newvalue: $('#nbtSingleSelect' + sid + dbcolumn).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtUpdateSubSelect ( seid, sid, dbcolumn ) {

     $.ajax ({
	 url: numbaturl + 'forms/updatesingleselect.php',
	 type: 'post',
	 data: {
	     selectid: sid,
	     column: dbcolumn,
	     newvalue: $('#nbtSingleSelect' + sid + dbcolumn).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtAddNewMultiSelect ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addmultiselect.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtAddNewTableData ( fid, eid, tform ) {

     $.ajax ({
	 url: numbaturl + 'forms/addtabledata.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid,
	     tableformat: tform
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtUpdateMultiSelectOptionColumn ( eid, sid, oldcolumn ) {

     nbtRemoveSpecialCharactersFromField ('#nbtMultiSelectColumn' + sid);

     $.ajax ({
	 url: numbaturl + 'forms/updatemultiselectoptioncolumn.php',
	 type: 'post',
	 data: {
	     element: eid,
	     selectid: sid,
	     oldcolumn: oldcolumn,
	     newcolumn: $('#nbtMultiSelectColumn' + sid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMultiSelectOptionsTable' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).html('Changes saved');

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtUpdateSubMultiSelectOptionColumn ( seid, sid, oldcolumn ) {

     nbtRemoveSpecialCharactersFromField ('#nbtMultiSelectColumn' + sid);

     $.ajax ({
	 url: numbaturl + 'forms/updatesubmultiselectoptioncolumn.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     selectid: sid,
	     oldcolumn: oldcolumn,
	     newcolumn: $('#nbtMultiSelectColumn' + sid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubMultiSelectOptionsTable' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).html('Changes saved');

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeTableSuffix ( eid ) {

     nbtRemoveSpecialCharactersFromField ('#nbtTableSuffix' + eid);

     $.ajax ({
	 url: numbaturl + 'forms/changetablesuffix.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newsuffix: $('#nbtTableSuffix' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeSubTableSuffix ( seid ) {

     nbtRemoveSpecialCharactersFromField ('#nbtSubTableSuffix' + seid);

     $.ajax ({
	 url: numbaturl + 'forms/changesubtablesuffix.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     newsuffix: $('#nbtSubTableSuffix' + seid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtAddTableDataColumn ( eid, tform ) {

     $.ajax ({
	 url: numbaturl + 'forms/addtabledatacolumn.php',
	 type: 'post',
	 data: {
	     element: eid,
	     tableformat: tform
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtTableDataColumnsTable' + eid).html(html);

     });

 }

 function nbtAddSubTableDataColumn ( seid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubtabledatacolumn.php',
	 type: 'post',
	 data: {
	     subelement: seid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubTableDataColumnsTable' + seid).html(html);

     });

 }

 function nbtRemoveTableDataColumn ( eid, cid ) {

     $.ajax ({
	 url: numbaturl + 'forms/removetabledatacolumn.php',
	 type: 'post',
	 data: {
	     element: eid,
	     column: cid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtTableDataColumnsTable' + eid).html(html);

     });

 }

 function nbtRemoveSubTableDataColumn ( seid, cid ) {

     $.ajax ({
	 url: numbaturl + 'forms/removesubtabledatacolumn.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     column: cid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubTableDataColumnsTable' + seid).html(html);

     });

 }

 function nbtUpdateTableDataColumnDisplay ( eid, cid ) {

     $.ajax ({
	 url: numbaturl + 'forms/updatetabledatacolumndisplay.php',
	 type: 'post',
	 data: {
	     column: cid,
	     newvalue: $('#nbtTableDataColumnDisplay' + cid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtUpdateSubTableDataColumnDisplay ( seid, cid ) {

     $.ajax ({
	 url: numbaturl + 'forms/updatetabledatacolumndisplay.php',
	 type: 'post',
	 data: {
	     column: cid,
	     newvalue: $('#nbtTableDataColumnDisplay' + cid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtUpdateTableDataColumnDB ( eid, tform, cid ) {

     $.ajax ({
	 url: numbaturl + 'forms/updatetabledatacolumndb.php',
	 type: 'post',
	 data: {
	     column: cid,
	     tableformat: tform,
	     newvalue: $('#nbtTableDataColumnDB' + cid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtUpdateSubTableDataColumnDB ( seid, cid ) {

     nbtRemoveSpecialCharactersFromField ('#nbtTableDataColumnDB' + cid);

     $.ajax ({
	 url: numbaturl + 'forms/updatesubtabledatacolumndb.php',
	 type: 'post',
	 data: {
	     column: cid,
	     newvalue: $('#nbtTableDataColumnDB' + cid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtMoveTableDataColumn ( eid, cid, dir ) {

     $.ajax ({
	 url: numbaturl + 'forms/movetabledatacolumn.php',
	 type: 'post',
	 data: {
	     column: cid,
	     element: eid,
	     direction: dir
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtTableDataColumnsTable' + eid).html(html);

     });

 }

 function nbtMoveSubTableDataColumn ( seid, cid, dir ) {

     $.ajax ({
	 url: numbaturl + 'forms/movesubtabledatacolumn.php',
	 type: 'post',
	 data: {
	     column: cid,
	     subelement: seid,
	     direction: dir
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubTableDataColumnsTable' + seid).html(html);

     });

 }

 function nbtAddNewCountrySelector ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addcountryselector.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtAddNewDateSelector ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/adddateselector.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtNewDumpFile () {

     $('#nbtCoverup').fadeIn();
     $('#nbtThinky').fadeIn();

     $.ajax ({
	 url: numbaturl + 'backup/newdumpfile.php',
	 type: 'post',
	 data: {
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtCoverup').fadeOut();
	 $('#nbtThinky').fadeOut();

	 $('#nbtListOfDumpFiles').html(html);

     });

 }

 function nbtAddNewCitationSelector ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addcitationselector.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);

     });

 }

 function nbtChangeCitationSelectorSuffix ( eid ) {

     nbtRemoveSpecialCharactersFromField ('#nbtCitationSelectorSuffix' + eid);

     $.ajax ({
	 url: numbaturl + 'forms/changecitationselectorsuffix.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newsuffix: $('#nbtCitationSelectorSuffix' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtAddCitationProperty ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addcitationproperty.php',
	 type: 'post',
	 data: {
	     element: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtCitationSelectorTable' + eid).html(html);

     });

 }

 function nbtUpdateCitationPropertyDisplay ( eid, cid ) {

     $.ajax ({
	 url: numbaturl + 'forms/updatecitationpropertydisplay.php',
	 type: 'post',
	 data: {
	     column: cid,
	     newvalue: $('#nbtCitationPropertyDisplay' + cid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtUpdateCitationPropertyDB ( eid, cid ) {

     $.ajax ({
	 url: numbaturl + 'forms/updatecitationpropertydb.php',
	 type: 'post',
	 data: {
	     column: cid,
	     newvalue: $('#nbtCitationPropertyDB' + cid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtCitationPropertyRemindToggle ( cid ) {

     $.ajax ({
	 url: numbaturl + 'forms/togglecitepropertyremind.php',
	 type: 'post',
	 data: {
	     column: cid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if ( html.substring(0,1) == '0' ) {

	     $('#nbtCitationPropertyRemind' + cid).removeClass('nbtTextOptionChosen');

	 } else {

	     $('#nbtCitationPropertyRemind' + cid).addClass('nbtTextOptionChosen');

	 }

     });

 }

 function nbtCitationPropertyForceCapsToggle ( cid ) {

     $.ajax ({
	 url: numbaturl + 'forms/togglecitepropertyforcecaps.php',
	 type: 'post',
	 data: {
	     column: cid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if ( html.substring(0,1) == '0' ) {

	     $('#nbtCitationPropertyForceCaps' + cid).removeClass('nbtTextOptionChosen');

	 } else {

	     $('#nbtCitationPropertyForceCaps' + cid).addClass('nbtTextOptionChosen');

	 }

     });

 }

 function nbtSubElementPromptCopyFromPrevToggle ( seid ) {

     $.ajax ({
	 url: numbaturl + 'forms/togglesubelementcopyfromprev.php',
	 type: 'post',
	 data: {
	     subelement: seid
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 if ( response == 'Changes saved' ) {

	     $('#nbtSubElementPromptCopyFromPrev' + seid).toggleClass('nbtTextOptionChosen');

	 }

     });

 }

 function nbtMoveCitationProperty ( eid, cid, dir ) {

     $.ajax ({
	 url: numbaturl + 'forms/movecitationproperty.php',
	 type: 'post',
	 data: {
	     column: cid,
	     element: eid,
	     direction: dir
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtCitationSelectorTable' + eid).html(html);

     });

 }

 function nbtSaveTextField (formid, extractionid, questionid, textfieldid, feedbackid, regex = null) {

     if ( ! ($('#' + textfieldid).val() == "" | regex == null | RegExp(regex).test ($('#' + textfieldid).val()) ) ) {

	 $('#' + textfieldid).addClass('nbtBackgroundFeedbackBad');

	 $('#' + feedbackid).html('Not saved: regex does not match ' + regex).slideDown();

     } else {

	 $('#' + feedbackid).slideUp();

	 $.ajax ({
	     url: numbaturl + 'extract/updateextraction.php',
	     type: 'post',
	     data: {
		 fid: formid,
		 id: extractionid,
		 question: questionid,
		 answer: $('#' + textfieldid).val()
	     },
	     dataType: 'html'
	 }).done ( function (html) {

	     if (html == 'Changes saved') {

		 $('#' + textfieldid).addClass('nbtBackgroundFeedbackGood');
		 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackBad');

		 setTimeout ( function () {

		     $('#' + textfieldid).removeClass('nbtBackgroundFeedbackGood');

		 }, 500);

	     } else {

		 $('#' + textfieldid).addClass('nbtBackgroundFeedbackBad');

		 setTimeout ( function () {

		     $('#' + textfieldid).removeClass('nbtBackgroundFeedbackBad');

		 }, 500);

	     }

	 });

     }



 }

 function nbtSaveSubExtractionTextField (elementid, extractionid, questionid, textfieldid, feedbackid, regex = null) {

     if ( ! ($('#' + textfieldid).val() == "" | regex == null | RegExp(regex).test ($('#' + textfieldid).val()) ) ) {

	 $('#' + textfieldid).addClass('nbtBackgroundFeedbackBad');

	 $('#' + feedbackid).html('Not saved: regex does not match ' + regex).slideDown();

     } else {

	 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackBad');

	 $.ajax ({
	     url: numbaturl + 'extract/updatesubextraction.php',
	     type: 'post',
	     data: {
		 eid: elementid,
		 id: extractionid,
		 question: questionid,
		 answer: $('#' + textfieldid).val()
	     },
	     dataType: 'html'
	 }).done ( function (html) {

	     $('#' + feedbackid).html(html);

	     $('#' + feedbackid).fadeIn(50, function () {

		 setTimeout ( function () {

		     $('#' + feedbackid).fadeOut(1000);

		 }, 2000);

	     });

	 });

     }

 }

 function nbtSaveMasterSubExtractionTextField (elementid, extractionid, questionid, textfieldid, feedbackid) {

     $.ajax ({
	 url: numbaturl + 'final/updatesubextraction.php',
	 type: 'post',
	 data: {
	     eid: elementid,
	     id: extractionid,
	     question: questionid,
	     answer: $('#' + textfieldid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#' + feedbackid).html(html);

	 $('#' + feedbackid).fadeIn(50, function () {

	     setTimeout ( function () {

		 $('#' + feedbackid).fadeOut(1000);

	     }, 2000);

	 });

     });

 }

 function nbtSaveCitationTextField (sectionid, citationid, questionid, textfieldid) {

     $.ajax ({
	 url: numbaturl + 'extract/updatecitationproperty.php',
	 type: 'post',
	 data: {
	     section: sectionid,
	     cid: citationid,
	     question: questionid,
	     answer: $('#' + textfieldid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if (html == 'Changes saved') {

	     $('#' + textfieldid).addClass('nbtBackgroundFeedbackGood');

	     setTimeout ( function () {

		 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackGood');

	     }, 500);

	 } else {

	     $('#' + textfieldid).addClass('nbtBackgroundFeedbackBad');

	     setTimeout ( function () {

		 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackBad');

	     }, 500);

	 }

     });

 }

 function nbtSaveMasterCitationTextField (sectionid, citationid, questionid, textfieldid) {

     $.ajax ({
	 url: numbaturl + 'final/updatecitationproperty.php',
	 type: 'post',
	 data: {
	     section: sectionid,
	     cid: citationid,
	     question: questionid,
	     answer: $('#' + textfieldid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if (html == 'Changes saved') {

	     $('#' + textfieldid).addClass('nbtBackgroundFeedbackGood');

	     setTimeout ( function () {

		 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackGood');

	     }, 500);

	 } else {

	     $('#' + textfieldid).addClass('nbtBackgroundFeedbackBad');

	     setTimeout ( function () {

		 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackBad');

	     }, 500);

	 }

     });

 }

 function nbtSaveDateField (formid, extractionid, questionid, textfieldid) {

     $.ajax ({
	 url: numbaturl + 'extract/formatdate.php',
	 type: 'post',
	 data: {
	     datestring: $('#' + textfieldid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#' + textfieldid).val(html);

	 if ( html != 'Bad date format' ) {

	     $.ajax ({
		 url: numbaturl + 'extract/updateextraction.php',
		 type: 'post',
		 data: {
		     fid: formid,
		     id: extractionid,
		     question: questionid,
		     answer: html
		 },
		 dataType: 'html'
	     }).done ( function (html2) {

		 if (html2 == 'Changes saved') {

		     $('#' + textfieldid).addClass('nbtBackgroundFeedbackGood');

		     setTimeout ( function () {

			 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackGood');

		     }, 500);

		 } else {

		     $('#' + textfieldid).addClass('nbtBackgroundFeedbackBad');

		     setTimeout ( function () {

			 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackBad');

		     }, 500);

		 }

	     });

	 } else {

	     $('#' + textfieldid).addClass('nbtBackgroundFeedbackBad');

	     setTimeout ( function () {

		 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackBad');
		 $('#' + textfieldid).val('');

	     }, 1500);

	 }

     });

 }

 function nbtSaveSubExtractionDateField (elementid, subextractionid, questionid, textfieldid, feedbackid) {

     $.ajax ({
	 url: numbaturl + 'extract/formatdate.php',
	 type: 'post',
	 data: {
	     datestring: $('#' + textfieldid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#' + textfieldid).val(html);

	 if ( html != 'Bad date format' ) {

	     $.ajax ({
		 url: numbaturl + 'extract/updatesubextraction.php',
		 type: 'post',
		 data: {
		     eid: elementid,
		     id: subextractionid,
		     question: questionid,
		     answer: html
		 },
		 dataType: 'html'
	     }).done ( function (html2) {

		 $('#' + feedbackid).html(html2);

		 $('#' + feedbackid).fadeIn(50, function () {

		     setTimeout ( function () {

			 $('#' + feedbackid).fadeOut(1000);

		     }, 2000);

		 });

	     });

	 } else {

	     $('#' + textfieldid).addClass('nbtBackgroundFeedbackBad');

	     setTimeout ( function () {

		 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackBad');
		 $('#' + textfieldid).val('');

	     }, 1500);

	 }

     });

 }

 function nbtSaveMasterSubExtractionDateField (elementid, subextractionid, questionid, textfieldid, feedbackid) {

     $.ajax ({
	 url: numbaturl + 'extract/formatdate.php',
	 type: 'post',
	 data: {
	     datestring: $('#' + textfieldid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#' + textfieldid).val(html);

	 $.ajax ({
	     url: numbaturl + 'final/updatesubextraction.php',
	     type: 'post',
	     data: {
		 eid: elementid,
		 id: subextractionid,
		 question: questionid,
		 answer: html
	     },
	     dataType: 'html'
	 }).done ( function (html2) {

	     $('#' + feedbackid).html(html2);

	     $('#' + feedbackid).fadeIn(50, function () {

		 setTimeout ( function () {

		     $('#' + feedbackid).fadeOut(1000);

		 }, 2000);

	     })

	 });

     });

 }

 function nbtSaveSingleSelect (formid, extractionid, questionlabel, response, buttonid, classid) {

     if ( $('#' + buttonid).hasClass('nbtTextOptionChosen') ) { // If it's already selected

	     $.ajax ({
		 url: numbaturl + 'extract/updateextraction.php',
		 type: 'post',
		 data: {
		 fid: formid,
		 id: extractionid,
		 question: questionlabel,
		 answer: 'NULL'
	     },
	     dataType: 'html'
	 }).done ( function (html) {

	     $('.' + classid).removeClass('nbtTextOptionChosen');
	     $('.' + classid).trigger('answerChange');

	 });

     } else { // It's not already selected

	 $.ajax ({
	     url: numbaturl + 'extract/updateextraction.php',
	     type: 'post',
	     data: {
		 fid: formid,
		 id: extractionid,
		 question: questionlabel,
		 answer: response
	     },
	     dataType: 'html'
	 }).done ( function (html) {

	     $('.' + classid).removeClass('nbtTextOptionChosen');
	     $('#' + buttonid).addClass('nbtTextOptionChosen');
	     $('.' + classid).trigger('answerChange');

	     if ( questionlabel == "status" && response == "2" && $('#time_finished').val() == "NaN" ) {
		 // If the extraction is being marked as complete
		 $('#time_finished').val('0');
	     }

	 });

     }

 }

 function nbtSaveSubExtractionSingleSelect (elementid, subextractionid, questionlabel, response, buttonid, classid) {

     if ( $('#' + buttonid).hasClass('nbtTextOptionChosen') ) { // IF it's already selected

	 $.ajax ({
	     url: numbaturl + 'extract/updatesubextraction.php',
	     type: 'post',
	     data: {
		 eid: elementid,
		 id: subextractionid,
		 question: questionlabel,
		 answer: 'NULL'
	     },
	     dataType: 'html'
	 }).done ( function (html) {

	     $('.' + classid).removeClass('nbtTextOptionChosen');
	     $('.' + classid).trigger('answerChange');

	 });

     } else { // It's not already selected

	 $.ajax ({
	     url: numbaturl + 'extract/updatesubextraction.php',
	     type: 'post',
	     data: {
		 eid: elementid,
		 id: subextractionid,
		 question: questionlabel,
		 answer: response
	     },
	     dataType: 'html'
	 }).done ( function (html) {

	     $('.' + classid).removeClass('nbtTextOptionChosen');
	     $('#' + buttonid).addClass('nbtTextOptionChosen');
	     $('.' + classid).trigger('answerChange');

	 });

     }

 }

 function nbtSaveMasterSubExtractionSingleSelect (elementid, subextractionid, questionlabel, response, buttonid, classid) {

     if ( $('#' + buttonid).hasClass('nbtTextOptionChosen') ) { // IF it's already selected

	 $.ajax ({
	     url: numbaturl + 'final/updatesubextraction.php',
	     type: 'post',
	     data: {
		 eid: elementid,
		 id: subextractionid,
		 question: questionlabel,
		 answer: 'NULL'
	     },
	     dataType: 'html'
	 }).done ( function (html) {

	     $('.' + classid).removeClass('nbtTextOptionChosen');

	 });

     } else { // It's not already selected

	 $.ajax ({
	     url: numbaturl + 'final/updatesubextraction.php',
	     type: 'post',
	     data: {
		 eid: elementid,
		 id: subextractionid,
		 question: questionlabel,
		 answer: response
	     },
	     dataType: 'html'
	 }).done ( function (html) {

	     $('.' + classid).removeClass('nbtTextOptionChosen');
	     $('#' + buttonid).addClass('nbtTextOptionChosen');

	 });

     }

 }

 function nbtSaveMultiSelect (formid, extractionid, questionlabel, buttonid) {

     $.ajax ({
	 url: numbaturl + 'extract/togglefield.php',
	 type: 'post',
	 data: {
	     fid: formid,
	     id: extractionid,
	     question: questionlabel
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#' + buttonid).toggleClass('nbtTextOptionChosen');
	 $('#' + buttonid).trigger('answerChange');

     });

 }

 function nbtClearMultiSelect (formid, extractionid, questionlabel, buttonid) {

     $.ajax ({
	 url: numbaturl + 'extract/clearfield.php',
	 type: 'post',
	 data: {
	     fid: formid,
	     id: extractionid,
	     question: questionlabel
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#' + buttonid).removeClass('nbtTextOptionChosen');
	 $('#' + buttonid).trigger('answerChange');

     });

 }

 function nbtClearSubextractionMultiSelect (elementid, subextractionid, column, buttonid) {

     if ($('#' + buttonid + '.nbtTextOptionChosen').length) {
	 // Only contact the server if there is one selected,
	 // otherwise do nothing

	 $.ajax ({
	     url: numbaturl + 'extract/subclearfield.php',
	     type: 'post',
	     data: {
		 eid: elementid,
		 seid: subextractionid,
		 col: column
	     },
	     dataType: 'html'
	 }).done ( function (html) {

	     $('#' + buttonid).removeClass('nbtTextOptionChosen');
	     $('#' + buttonid).trigger('answerChange');

	 });
     }

 }

 function nbtSaveSubExtractionMultiSelect (elementid, subextractionid, questionlabel, buttonid) {

     $.ajax ({
	 url: numbaturl + 'extract/subtogglefield.php',
	 type: 'post',
	 data: {
	     eid: elementid,
	     id: subextractionid,
	     question: questionlabel
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#' + buttonid).toggleClass('nbtTextOptionChosen');
	 $('#' + buttonid).trigger('answerChange');

     });

 }

 function nbtSaveMasterSubExtractionMultiSelect (elementid, subextractionid, questionlabel, buttonid) {

     $.ajax ({
	 url: numbaturl + 'final/subtogglefield.php',
	 type: 'post',
	 data: {
	     eid: elementid,
	     id: subextractionid,
	     question: questionlabel
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#' + buttonid).toggleClass('nbtTextOptionChosen');

     });

 }

 function nbtUpdateExtractionTableData ( tableid, rowid, columnid, inputid ) {

     $.ajax ({
	 url: numbaturl + 'extract/updatetabledata.php',
	 type: 'post',
	 data: {
	     tid: tableid,
	     row: rowid,
	     column: columnid,
	     newvalue: $('#' + inputid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if (html == 'Changes saved') {

	     $('#' + inputid).addClass('nbtBackgroundFeedbackGood');

	     setTimeout ( function () {

		 $('#' + inputid).removeClass('nbtBackgroundFeedbackGood');

	     }, 500);

	 } else {

	     $('#' + inputid).addClass('nbtBackgroundFeedbackBad');

	     setTimeout ( function () {

		 $('#' + inputid).removeClass('nbtBackgroundFeedbackBad');

	     }, 500);

	 }

     });

 }

 function nbtUpdateSubTableData ( tableid, rowid, columnid, inputid ) {

     $.ajax ({
	 url: numbaturl + 'extract/updatesubtabledata.php',
	 type: 'post',
	 data: {
	     tid: tableid,
	     row: rowid,
	     column: columnid,
	     newvalue: $('#' + inputid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if (html == 'Changes saved') {

	     $('#' + inputid).addClass('nbtBackgroundFeedbackGood');

	     setTimeout ( function () {

		 $('#' + inputid).removeClass('nbtBackgroundFeedbackGood');

	     }, 500);

	 } else {

	     $('#' + inputid).addClass('nbtBackgroundFeedbackBad');

	     setTimeout ( function () {

		 $('#' + inputid).removeClass('nbtBackgroundFeedbackBad');

	     }, 500);

	 }

     });

 }

 function nbtUpdateFinalColumn ( form, refset, ref, col, inputid, element_type, eid ) {

     $.ajax ({
	 url: numbaturl + 'final/updatecolumn.php',
	 type: 'post',
	 data: {
	     fid: form,
	     rsid: refset,
	     rid: ref,
	     column: col,
	     newvalue: $('#' + inputid).val(),
	     elementtype: element_type
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 if (response == 'Changes saved') {

	     $('#nbtExtractedElement' + eid).removeClass('nbtFeedbackBad').addClass('nbtFeedbackGood');

	     $('.nbtElement' + eid + 'Check').fadeOut();

	     $('#' + inputid).addClass('nbtBackgroundFeedbackGood');

	     setTimeout ( function () {

		 $('#' + inputid).removeClass('nbtBackgroundFeedbackGood');

	     }, 500);

	 } else {

	     $('#' + inputid).addClass('nbtBackgroundFeedbackBad');

	     setTimeout ( function () {

		 $('#' + inputid).removeClass('nbtBackgroundFeedbackBad');

	     }, 500);

	 }

     });

 }

 function nbtUpdateFinalSelector (form, refset, ref, col, newval, eid, element_type) {

     if ( element_type == 'single_select' ) {
	 if ($('#nbtElement' + eid + '-' + newval).hasClass('nbtTextOptionChosen')) {
	     setnull = 'TRUE';
	 } else {
	     setnull = 'FALSE';
	 }
     }

     if ( element_type == 'multi_select' ) {
	 if ($('#nbtElement' + eid + '-' + col).hasClass('nbtTextOptionChosen')) {
	     newval = 0;
	 } else {
	     newval = 1;
	 }
	 setnull = 'FALSE';
     }

     $.ajax ({
	 url: numbaturl + 'final/updateselector.php',
	 type: 'post',
	 data: {
	     fid: form,
	     rsid: refset,
	     rid: ref,
	     column: col,
	     newvalue: newval,
	     elementtype: element_type,
	     setnull: setnull
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 if (response == 'Changes saved') {

	     $('#nbtExtractedElement' + eid).removeClass('nbtFeedbackBad').addClass('nbtFeedbackGood');

	     $('.nbtElement' + eid + 'Check').fadeOut();

	     if ( element_type == 'single_select' ) {

		 $('.nbtElement' + eid).removeClass('nbtTextOptionChosen');

		 if (setnull == 'FALSE') {
		     $('#nbtElement' + eid + '-' + newval).addClass('nbtTextOptionChosen');
		 }

	     }

	     if ( element_type == 'multi_select' ) {
		 $('#nbtElement' + eid + '-' + col).toggleClass('nbtTextOptionChosen');
	     }
	 }

     });

 }

 function nbtUpdateMasterExtractionTableData ( tableid, rowid, columnid, inputid) {

     $.ajax ({
	 url: numbaturl + 'final/updatetabledata.php',
	 type: 'post',
	 data: {
	     tid: tableid,
	     row: rowid,
	     column: columnid,
	     newvalue: $('#' + inputid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if (html == 'Changes saved') {

	     $('#' + inputid).addClass('nbtBackgroundFeedbackGood');

	     setTimeout ( function () {

		 $('#' + inputid).removeClass('nbtBackgroundFeedbackGood');

	     }, 500);

	 } else {

	     $('#' + inputid).addClass('nbtBackgroundFeedbackBad');

	     setTimeout ( function () {

		 $('#' + inputid).removeClass('nbtBackgroundFeedbackBad');

	     }, 500);

	 }

     });

 }

 function nbtUpdateMasterSubTableData ( tableid, rowid, columnid, inputid) {

     $.ajax ({
	 url: numbaturl + 'final/updatesubtabledata.php',
	 type: 'post',
	 data: {
	     tid: tableid,
	     row: rowid,
	     column: columnid,
	     newvalue: $('#' + inputid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if (html == 'Changes saved') {

	     $('#' + inputid).addClass('nbtBackgroundFeedbackGood');

	     setTimeout ( function () {

		 $('#' + inputid).removeClass('nbtBackgroundFeedbackGood');

	     }, 500);

	 } else {

	     $('#' + inputid).addClass('nbtBackgroundFeedbackBad');

	     setTimeout ( function () {

		 $('#' + inputid).removeClass('nbtBackgroundFeedbackBad');

	     }, 500);

	 }

     });

 }

 function nbtAddExtractionTableDataRow (tform, tableid, refsetid, refid) {

     $.ajax ({
	 url: numbaturl + 'extract/addtabledatarow.php',
	 type: 'post',
	 data: {
	     tableformat: tform,
	     tid: tableid,
	     refset: refsetid,
	     ref: refid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 //$('#nbtTableExtraction' + tableid).html(html);
	 $('#nbtTableExtraction' + tableid + ' table.nbtTabledData').append(html);

	 setTimeout ( function () {

	     $('#nbtTableExtraction' + tableid + ' table.nbtTabledData tr.nbtTDRow' + tableid + ':last td:first input').focus();

	 }, 50);

     });

 }

 function nbtAddSubTableDataRow (tableid, refsetid, refid, seid) {

     $.ajax ({
	 url: numbaturl + 'extract/addsubtabledatarow.php',
	 type: 'post',
	 data: {
	     tid: tableid,
	     refset: refsetid,
	     ref: refid,
	     subextraction: seid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubTableExtraction' + tableid + '-' + seid).html(html);

	 setTimeout ( function () {

	     $('#nbtTableExtraction' + tableid + ' table.nbtTabledData tr.nbtTDRow' + tableid + ':last td:first input').focus();

	 }, 50);

     });

 }

 function nbtRemoveExtractionTableDataRow ( tableid, rowid ) {

     $.ajax ({
	 url: numbaturl + 'extract/removetabledatarow.php',
	 type: 'post',
	 data: {
	     tid: tableid,
	     row: rowid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtTDRowID' + tableid + '-' + rowid).fadeOut(200).remove();

     });

 }

 function nbtRemoveSubTableDataRow ( tableid, rowid ) {

     $.ajax ({
	 url: numbaturl + 'extract/removesubtabledatarow.php',
	 type: 'post',
	 data: {
	     tid: tableid,
	     row: rowid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubTDRowID' + tableid + '-' + rowid).fadeOut(200).remove();

     });

 }

 function nbtFindCitation (event, eid, suffix, targetid, cid, rsid, refid) {

     if (event.keyCode == 13){ // Enter key pressed and only one result

	 $('div#nbtCitationSuggestions' + eid + ' div button').first().click();

     } else {

	 if ( $('#nbtCitationFinder' + eid).val() == '' ) {

	     $('#' + targetid).fadeOut(100);

	     $('#' + targetid).html('');

	 } else {

	     $('#nbtCitationSuggestions' + eid).fadeIn(100);

	     $.ajax ({
		 url: numbaturl + 'extract/citationfinder.php',
		 type: 'post',
		 data: {
		     elementid: eid,
		     citationsid: cid,
		     citationsuffix: suffix,
		     refset: rsid,
		     query: $('#nbtCitationFinder' + eid).val(),
		     reference: refid
		 },
		 dataType: 'html'
	     }).done ( function (html) {

		 $('#' + targetid).html(html);

	     });

	 }

     }

 }

 function nbtAddCitation (cid, csuffix, rsid, origrefid, citid, user) {

     // citation section id
     // rsid => refset id
     // origrefid => id of the reference being extracted
     // citid => id of the citation in the paper being extracted
     // user => user id

     if ( $('.nbtCitOrigRef' + cid + '-' + citid).length ) {

	 $('.nbtCitOrigRef' + cid + '-' + citid).removeClass('nbtGreyGradient').addClass('nbtAlreadyAdded');

	 $('#nbtDoubleCitationFeedback' + cid).fadeIn();

	 setTimeout ( function () {

	     $('.nbtCitOrigRef' + cid + '-' + citid).addClass('nbtGreyGradient').removeClass('nbtAlreadyAdded');

	     $('#nbtDoubleCitationFeedback' + cid).fadeOut();


	 }, 1700);

     } else {

	 $.ajax ({
	     url: numbaturl + 'extract/addcitation.php',
	     type: 'post',
	     data: {
		 citationsid: cid,
		 citationsuffix: csuffix,
		 refset: rsid,
		 reference: origrefid,
		 citation: citid,
		 userid: user
	     },
	     dataType: 'html'
	 }).done ( function (html) {

	     $('#nbtCitationList' + cid).html(html);
	     $('div#nbtCitationList' + cid + ' input').first().focus();

	 });

     }

 }

 function nbtRemoveCitation ( sectionid, citationid ) {

     $.ajax ({
	 url: numbaturl + 'extract/removecitation.php',
	 type: 'post',
	 data: {
	     section: sectionid,
	     citation: citationid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtCitation' + sectionid + '-' + citationid).slideUp(500, function() {

	     $('#nbtCitation' + sectionid + '-' + citationid).remove();

	 });

     });

 }

 function nbtRemoveMasterCitation ( sectionid, citationid, cid ) {

     $.ajax ({
	 url: numbaturl + 'final/removecitation.php',
	 type: 'post',
	 data: {
	     section: sectionid,
	     citation: citationid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtCitation' + sectionid + '-' + citationid).slideUp(500, function() {

	     $('#nbtCitation' + sectionid + '-' + citationid).remove();

	 });

	 $('.nbtMasterCite' + sectionid + '-' + cid).fadeOut(500, function() {

	     $('.nbtMasterCite' + sectionid + '-' + cid).html('');

	 });

     });

 }

 function nbtUpdateCiteNo ( section, citid ) {

     $.ajax ({
	 url: numbaturl + 'extract/updateciteno.php',
	 type: 'post',
	 data: {
	     sectionid: section,
	     id: citid,
	     newvalue: $('#nbtCiteNo' + section + '-' + citid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if (html == '&#10003;') {

	     $('#nbtCiteNo' + section + '-' + citid).addClass('nbtBackgroundFeedbackGood');

	     setTimeout ( function () {

		 $('#nbtCiteNo' + section + '-' + citid).removeClass('nbtBackgroundFeedbackGood');

	     }, 500);

	 } else {

	     $('#nbtCiteNo' + section + '-' + citid).addClass('nbtBackgroundFeedbackBad');

	     setTimeout ( function () {

		 $('#nbtCiteNo' + section + '-' + citid).removeClass('nbtBackgroundFeedbackBad');

	     }, 500);

	 }

     });

 }

 function nbtFindReferenceToAttach () {

     if ( $('#nbtAssignRefSet').val() == 'NULL' ) {

	 $('#nbtFoundReferencesForAssigment').addClass('nbtFeedback').addClass('nbtFeedbackBad').addClass('nbtFinePrint').removeClass('nbtCitationSuggestions').html('Choose a reference set');


     } else {

	 $('#nbtFoundReferencesForAssigment').removeClass('nbtFeedback').removeClass('nbtFeedbackBad').removeClass('nbtFinePrint').addClass('nbtCitationSuggestions');

	 if ( $('#nbtReferenceFinder').val() == '' ) {

	     $('#nbtFoundReferencesForAssigment').fadeOut(100);

	     $('#nbtFoundReferencesForAssigment').html('');

	 } else {

	     $('#nbtFoundReferencesForAssigment').fadeIn(100);

	     $.ajax ({
		 url: numbaturl + 'attach/referencefinder.php',
		 type: 'post',
		 data: {
		     userid: $('#nbtAssignUser').val(),
		     formid: $('#nbtAssignForm').val(),
		     refset: $('#nbtAssignRefSet').val(),
		     query: $('#nbtReferenceFinder').val()
		 },
		 dataType: 'html'
	     }).done ( function (html) {

		 $('#nbtFoundReferencesForAssigment').html(html);

	     });

	 }

     }

 }

 function nbtToggleAssignment ( uid, fid, rsid, rid ) {

     $.ajax ({
	 url: numbaturl + 'assignments/toggleassignment.php',
	 type: 'post',
	 data: {
	     userid: uid,
	     formid: fid,
	     refset: rsid,
	     ref: rid
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 $('#nbtAssignment-' + rid + '-' + fid + '-' + uid).toggleClass('nbtAssigned').toggleClass('nbtNotAssigned');

	 nbtUpdateCompletedAssignmentsCount ( fid, rsid );

     })
 }

 function nbtAssignerChooseColumn ( refset ) {

     $('#nbtRefsetColumnSelectValues').empty();
     $('#nbtRefsetColumnSelectValues').append('<option value="na">...</option>');

     $.ajax ({
	 url: numbaturl + 'assignments/selectcolumn.php',
	 type: 'post',
	 data: {
	     rsid: refset,
	     col: $('#nbtRefsetColumnSelect').val()
	 },
	 dataType: 'html'
     }).done(function (response) {

	 $('#nbtRefsetColumnSelectValues').empty();
	 $('#nbtRefsetColumnSelectValues').append('<option value="na">Choose a value</option>');

	 values = JSON.parse(response);

	 for (var key in values) {

	     $('#nbtRefsetColumnSelectValues').append('<option value="' + values[key][0].replace(/"/g, '&quot;') + '">' + values[key][0] + '</option>');

	 }

     });
 }

 function nbtAssignerSelectByColumn ( refset ) {

     $.ajax ({
	 url: numbaturl + 'assignments/selectcolumnvalue.php',
	 type: 'post',
	 data: {
	     rsid: refset,
	     col: $('#nbtRefsetColumnSelect').val(),
	     val: $('#nbtRefsetColumnSelectValues').val()
	 },
	 dataType: 'html'
     }).done(function (response) {

	 // First unselect everything
	 $('input.nbtAssignSelect').prop('checked', false);

	 rids = JSON.parse(response);

	 for (var key in rids) {

	     $('#nbtAssignSelectRefID' + rids[key][0]).prop('checked', true);

	 }

     });

 }

 function nbtAssignerSelectKRandom ( refset, crit ) {

     kval = $('#nbtRandomK' + crit).val();

     if ( crit != '' ) {
	 nval = $('#nbtRandomN' + crit).val();
	 comptype = $('#nbtRandomComp' + crit).val();
	 formname = $('#nbtRandomForm' + crit).val();
     } else {
	 nval = '';
	 comptype = '';
	 formname = '';
     }

     $.ajax ({
	 url: numbaturl + 'assignments/selectkrandom.php',
	 type: 'post',
	 data: {
	     rsid: refset,
	     k: kval,
	     n: nval,
	     crit: crit,
	     comp: comptype,
	     form: formname
	 },
	 dataType: 'html'
     }).done(function (response) {

	 // First unselect everything
	 $('input.nbtAssignSelect').prop('checked', false);

	 rids = JSON.parse(response);

	 for (var key in rids) {

	     $('#nbtAssignSelectRefID' + rids[key][0]).prop('checked', true);

	 }

     });

 }

 function nbtAssignerSelectKRandomByUser ( refset ) {

     $.ajax ({
	 url: numbaturl + 'assignments/selectkrandombyuser.php',
	 type: 'post',
	 data: {
	     rsid: refset,
	     k: $('#nbtRandomKByUserK').val(),
	     form: $('#nbtRandomKByUserForm').val(),
	     yn: $('#nbtRandomKByUserYN').val(),
	     user: $('#nbtRandomKByUserUser').val()
	 },
	 dataType: 'html'
     }).done(function (response) {

	 // First unselect everything
	 $('input.nbtAssignSelect').prop('checked', false);

	 rids = JSON.parse(response);

	 for (var key in rids) {

	     $('#nbtAssignSelectRefID' + rids[key][0]).prop('checked', true);

	 }

     });

 }

 function nbtAssignerSelectKRandomByUserAndUsers ( refset ) {

     $.ajax ({
	 url: numbaturl + 'assignments/selectkrandombyuserandusers.php',
	 type: 'post',
	 data: {
	     rsid: refset,
	     k: $('#nbtRandomKByUserAndUsersK').val(),
	     form: $('#nbtRandomKByUserAndUsersForm').val(),
	     yn: $('#nbtRandomKByUserAndUsersFormYN').val(),
	     user: $('#nbtRandomKByUserAndUsersUser').val(),
	     comp: $('#nbtRandomKByUserAndUsersComp').val(),
	     n: $('#nbtRandomKByUserAndUsersUserN').val()
	 },
	 dataType: 'html'
     }).done(function (response) {

	 // First unselect everything
	 $('input.nbtAssignSelect').prop('checked', false);

	 rids = JSON.parse(response);

	 for (var key in rids) {

	     $('#nbtAssignSelectRefID' + rids[key][0]).prop('checked', true);

	 }

     });

 }

 function nbtAssign () {

     if (
	 $('input.nbtAssignSelect:checked').length > 0 &&
	 $('#nbtAssignFormChooser').val() != 'ns' &&
	 $('#nbtAssignUserChooser').val() != 'ns'
     ) {
	 rsid = $('#nbtRefSetID').val();

	 refs = [];
	 $('input.nbtAssignSelect:checked').each(function() {
	     refs.push($(this).val());
	 });
	 rids = refs.join(',');

	 fid = $('#nbtAssignFormChooser').val();

	 uid = $('#nbtAssignUserChooser').val();

	 $.ajax ({
	     url: numbaturl + 'assignments/assign.php',
	     type: 'post',
	     data: {
		 userid: uid,
		 formid: fid,
		 refset: rsid,
		 refids: rids
	     },
	     dataType: 'html'
	 }).done(function (response) {

	     if ( response == "SUCCESS" ) {

		 nbtUpdateCompletedAssignmentsCount ( fid, rsid );

		 if ( fid == "all" && uid == "all" ) {

		     forms = $('#nbtAllFormIDs').val().split(',');
		     users = $('#nbtAllUserIDs').val().split(',');

		     forms.forEach(function (form) {
			 users.forEach(function (user) {
			     refs.forEach(function (ref) {
				 $('#nbtAssignment-' + ref + '-' + form + '-' + user).removeClass('nbtNotAssigned').addClass('nbtAssigned');
			     });
			 });
		     });

		 } else {
		     if ( fid == "all" ) {

			 forms = $('#nbtAllFormIDs').val().split(',');

			 forms.forEach(function (form) {
			     refs.forEach(function (ref) {
				 $('#nbtAssignment-' + ref + '-' + form + '-' + uid).removeClass('nbtNotAssigned').addClass('nbtAssigned');
			     });
			 });

		     } else {
			 if ( uid == "all") {

			     users = $('#nbtAllUserIDs').val().split(',');

			     users.forEach(function (user) {
				 refs.forEach(function (ref) {
				     $('#nbtAssignment-' + ref + '-' + fid + '-' + user).removeClass('nbtNotAssigned').addClass('nbtAssigned');
				 });
			     });

			 } else {

			     refs.forEach(function (ref) {
				 $('#nbtAssignment-' + ref + '-' + fid + '-' + uid).removeClass('nbtNotAssigned').addClass('nbtAssigned');
			     });

			 }
		     }
		 }

	     }

	 });

     } else {

	 alert('Please make sure you have selected a form, a user and at least one reference.');

     }

 }

 function nbtRemoveAssign () {

     if (
	 $('input.nbtAssignSelect:checked').length > 0 &&
	 $('#nbtAssignFormChooser').val() != 'ns' &&
	 $('#nbtAssignUserChooser').val() != 'ns'
     ) {
	 rsid = $('#nbtRefSetID').val();

	 refs = [];
	 $('input.nbtAssignSelect:checked').each(function() {
	     refs.push($(this).val());
	 });
	 rids = refs.join(',');

	 fid = $('#nbtAssignFormChooser').val();

	 uid = $('#nbtAssignUserChooser').val();

	 $.ajax ({
	     url: numbaturl + 'assignments/unassign.php',
	     type: 'post',
	     data: {
		 userid: uid,
		 formid: fid,
		 refset: rsid,
		 refids: rids
	     },
	     dataType: 'html'
	 }).done(function (response) {

	     if (response == "SUCCESS") {

		 nbtUpdateCompletedAssignmentsCount ( fid, rsid );

		 if (fid == "all" && uid == "all") {
		     forms = $('#nbtAllFormIDs').val().split(',');
		     users = $('#nbtAllUserIDs').val().split(',');

		     forms.forEach(function (form) {
			 users.forEach(function (user) {
			     refs.forEach(function (ref) {
				 $('#nbtAssignment-' + ref + '-' + form + '-' + user).addClass('nbtNotAssigned').removeClass('nbtAssigned');
			     });
			 });
		     });

		 } else {
		     if (fid == "all") {
			 forms = $('#nbtAllFormIDs').val().split(',');

			 forms.forEach(function (form) {
			     refs.forEach(function (ref) {
				 $('#nbtAssignment-' + ref + '-' + form + '-' + uid).addClass('nbtNotAssigned').removeClass('nbtAssigned');
			     });
			 });
		     } else {
			 if (uid == "all") {
			     users = $('#nbtAllUserIDs').val().split(',');

			     users.forEach(function (user) {
				 refs.forEach(function (ref) {
				     $('#nbtAssignment-' + ref + '-' + fid + '-' + user).addClass('nbtNotAssigned').removeClass('nbtAssigned');
				 });

			     });

			 } else {

			     refs.forEach(function (ref) {
				 $('#nbtAssignment-' + ref + '-' + fid + '-' + uid).addClass('nbtNotAssigned').removeClass('nbtAssigned');
			     });

			 }
		     }
		 }
	     }

	 });

     } else {

	 alert ('Please make sure you have selected a form, a user and at least one reference.');

     }

 }

 function nbtAddAssignmentInExtraction ( rsid, rid, eid ) {

     $.ajax ({
	 url: numbaturl + 'assignments/addassignment.php',
	 type: 'post',
	 data: {
	     userid: $('#nbtAssignUser' + eid).val(),
	     formid: $('#nbtAssignForm' + eid).val(),
	     refset: rsid,
	     ref: rid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if ( html == 'Assignment added' ) {

	     $('#nbtAddAssignmentFeedback' + eid).removeClass('nbtFeedbackBad').addClass('nbtFeedback').addClass('nbtFeedbackGood').addClass('nbtFinePrint');

	 } else {

	     $('#nbtAddAssignmentFeedback' + eid).removeClass('nbtFeedbackGood').addClass('nbtFeedback').addClass('nbtFeedbackBad').addClass('nbtFinePrint');

	 }

	 $('#nbtAddAssignmentFeedback' + eid).html(html).slideDown(500, function () {

	     setTimeout ( function () {

		 $('#nbtAddAssignmentFeedback' + eid).slideUp();

	     }, 3000);

	 });

     });

 }

 function nbtDeleteAssignment ( aid ) {

     $.ajax ({
	 url: numbaturl + 'assignments/deleteassignment.php',
	 type: 'post',
	 data: {
	     assignment: aid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if ( html == 'deleted' ) {

	     $('#nbtAssignmentTableRow' + aid).fadeOut();

	 }

     });

 }

 function nbtAddNewSubExtraction ( fid, eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubextraction.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElements').html(html);
	 $('.nbtSubExtractionEditor').sortable();

     });

 }

 function nbtChangeSubExtractionSuffix ( eid ) {

     nbtRemoveSpecialCharactersFromField ('#nbtTableSuffix' + eid);

     $.ajax ({
	 url: numbaturl + 'forms/changesubextractionsuffix.php',
	 type: 'post',
	 data: {
	     element: eid,
	     newsuffix: $('#nbtTableSuffix' + eid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtFormElementFeedback' + eid).html(html);

	 $('#nbtFormElementFeedback' + eid).fadeIn(500, function () {

	     $('#nbtFormElementFeedback' + eid).fadeOut(1500);

	 });

     });

 }

 function nbtAddNewSubOpenText ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubopentext.php',
	 type: 'post',
	 data: {
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtractionElements' + eid).html(html);

     });

 }

 function nbtAddNewSubTextArea ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubtextarea.php',
	 type: 'post',
	 data: {
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtractionElements' + eid).html(html);

     });

 }

 function nbtAddNewSubRefData ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubrefdata.php',
	 type: 'post',
	 data: {
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtractionElements' + eid).html(html);

     });

 }

 function nbtDeleteSubElement ( seid ) {

     $.ajax ({
	 url: numbaturl + 'forms/deletesubelement.php',
	 type: 'post',
	 data: {
	     subelement: seid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElement' + seid).slideUp(1000);

     });

 }

 function nbtChangeSubDisplayName ( seid ) {

     $.ajax ({
	 url: numbaturl + 'forms/changesubdisplayname.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     newdisplayname: $('#nbtSubElementDisplayName' + seid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeSubColumnName ( seid ) {

     nbtRemoveSpecialCharactersFromField ('#nbtSubElementColumnName' + seid);

     $.ajax ({
	 url: numbaturl + 'forms/changesubcolumnname.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     newcolumnname: $('#nbtSubElementColumnName' + seid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeSubElementCodebook ( seid ) {

     $.ajax ({
	 url: numbaturl + 'forms/changesubelementcodebook.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     newcodebook: $('#nbtSubElementCodebook' + seid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtChangeSubElementToggle ( seid ) {

     if ( $('#nbtSubElementToggle' + seid).val() != '' ) {

	 var regex=/^[0-9A-Za-z.\-_]+$/; //^[a-zA-z]+$/

	 if ( ! regex.test ( $('#nbtSubElementToggle' + seid).val() ) ) {

	     $('#nbtSubElementToggle' + seid).addClass('nbtBadField');

	 } else {

	     $('#nbtSubElementToggle' + seid).removeClass('nbtBadField');

	 }

     } else {

	 $('#nbtSubElementToggle' + seid).removeClass('nbtBadField');

     }

     $.ajax ({
	 url: numbaturl + 'forms/changesubelementtoggle.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     newtoggle: $('#nbtSubElementToggle' + seid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtAddNewSubSingleSelect ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubsingleselect.php',
	 type: 'post',
	 data: {
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtractionElements' + eid).html(html);

     });

 }

 function nbtAddSubSingleSelectOption ( seid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubsingleselectoption.php',
	 type: 'post',
	 data: {
	     subelement: seid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubSingleSelectOptionsTable' + seid).html(html);

     });

 }

 function nbtMoveSubSelectOption ( seid, sid, dir ) {

     $.ajax ({
	 url: numbaturl + 'forms/movesubselectoption.php',
	 type: 'post',
	 data: {
	     selectid: sid,
	     subelement: seid,
	     direction: dir
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubSingleSelectOptionsTable' + seid).html(html);

     });

 }

 function nbtAddNewSubMultiSelect ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubmultiselect.php',
	 type: 'post',
	 data: {
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtractionElements' + eid).html(html);

     });

 }

 function nbtAddNewSubTable ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubtable.php',
	 type: 'post',
	 data: {
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtractionElements' + eid).html(html);

     });

 }

 function nbtChangeSubMultiSelectColumnPrefix ( seid ) {

     nbtRemoveSpecialCharactersFromField ('#nbtSubElementColumnPrefix' + seid);

     $.ajax ({
	 url: numbaturl + 'forms/changesubmultiselectcolumnprefix.php',
	 type: 'post',
	 data: {
	     subelement: seid,
	     newcolumnname: $('#nbtSubElementColumnPrefix' + seid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubElementFeedback' + seid).html(html);

	 $('#nbtSubElementFeedback' + seid).fadeIn(500, function () {

	     $('#nbtSubElementFeedback' + seid).fadeOut(1500);

	 });

     });

 }

 function nbtAddSubMultiSelectOption ( seid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubmultiselectoption.php',
	 type: 'post',
	 data: {
	     subelement: seid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubMultiSelectOptionsTable' + seid).html(html);

     });

 }

 function nbtMoveSubMultiSelectOption ( seid, sid, dir ) {

     $.ajax ({
	 url: numbaturl + 'forms/movesubmultiselectoption.php',
	 type: 'post',
	 data: {
	     selectid: sid,
	     subelement: seid,
	     direction: dir
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubMultiSelectOptionsTable' + seid).html(html);

     });

 }

 function nbtAddNewSubDateSelector ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubdateselector.php',
	 type: 'post',
	 data: {
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtractionElements' + eid).html(html);

     });

 }

 function nbtAddNewSubTagsElement ( eid ) {

     $.ajax ({
	 url: numbaturl + 'forms/addsubtagselement.php',
	 type: 'post',
	 data: {
	     elementid: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtractionElements' + eid).html(html);

     });

 }

 function nbtNewSubExtraction (eid, rsid, rid, uid) {

     $.ajax ({
	 url: numbaturl + 'extract/addsubextraction.php',
	 type: 'post',
	 data: {
	     elementid: eid,
	     refset: rsid,
	     ref: rid,
	     userid: uid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtraction' + eid + '-' + uid).html(html);

	 $('.nbtTextOptionSelect').trigger('answerChange');

     });

 }

 function nbtDeleteSubExtraction ( eid, seid ) {

     $.ajax ({
	 url: numbaturl + 'extract/removesubextraction.php',
	 type: 'post',
	 data: {
	     elementid: eid,
	     subextractionid: seid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtSubExtractionInstance' + eid + '-' + seid).slideUp();

     });

 }

 function nbtChangeRefSetName ( rsid ) {

     $.ajax ({
	 url: numbaturl + 'references/changename.php',
	 type: 'post',
	 data: {
	     refset: rsid,
	     newname: $('#nbtNewRefSetName').val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtNewRefSetNameFeedback').html(html);

	 $('#nbtNewRefSetNameFeedback').fadeIn(500, function () {

	     $('#nbtNewRefSetNameFeedback').fadeOut(1500);

	 });

     });

 }

 function nbtDeleteRefSet ( rsid ) {

     $.ajax ({
	 url: numbaturl + 'references/deleterefset.php',
	 type: 'post',
	 data: {
	     refset: rsid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtRefSetRow' + rsid).fadeOut();

     });

 }

 function nbtDeleteRef ( rsid, rid ) {

     $.ajax ({
	 url: numbaturl + 'references/deleteref.php',
	 type: 'post',
	 data: {
	     refset: rsid,
	     ref: rid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtRefRow' + rid).fadeOut(400, function () {
	     $('#nbtRefRow' + rid).remove();
	 });

     });

 }

 function nbtAddNewReferenceToRefSet ( rsid ) {

     $.ajax ({
	 url: numbaturl + 'extract/addnewreference.php',
	 type: 'post',
	 data: {
	     refset: rsid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtManualRefs').html(html);

     });

     $('#nbtManualRefs').fadeIn(500);
     $('#nbtManualRefsCoverup').fadeIn(500);

 }

 function nbtUpdateManualReference ( rsid, refid, columnid, textfieldid ) {

     $.ajax ({
	 url: numbaturl + 'extract/updatemanref.php',
	 type: 'post',
	 data: {
	     refset: rsid,
	     ref: refid,
	     column: columnid,
	     newvalue: $('#' + textfieldid).val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if ( html == "Changes saved" ) {

	     $('#' + textfieldid).addClass('nbtBackgroundFeedbackGood');

	     setTimeout ( function () {

		 $('#' + textfieldid).removeClass('nbtBackgroundFeedbackGood');

	     }, 500);

	 }

     });

 }

 function nbtRemoveManualReference ( rsid, refid ) {

     $.ajax ({
	 url: numbaturl + 'extract/removemanual.php',
	 type: 'post',
	 data: {
	     refset: rsid,
	     ref: refid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtManRef' + rsid + '-' + refid).slideUp(200);

	 $('#nbtManualRefsCoverup').fadeOut(500);

	 $('#nbtManualRefs').fadeOut(500);

     });

 }

 function nbtCopyTableDataRow ( tform, eid, rsid, refid, rid ) {

     $.ajax ({
	 url: numbaturl + 'final/copytabledatarow.php',
	 type: 'post',
	 data: {
	     tableformat: tform,
	     elementid: eid,
	     refset: rsid,
	     ref: refid,
	     row: rid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMasterTable' + eid).html(html);

     });

 }

 function nbtCopyTableDataAllRows ( tform, eid, rsid, refid, uid ) {

     $.ajax ({
	 url: numbaturl + 'final/copytabledataallrows.php',
	 type: 'post',
	 data: {
	     tableformat: tform,
	     elementid: eid,
	     refset: rsid,
	     ref: refid,
	     user: uid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMasterTable' + eid).html(html);

     });

 }

 function nbtAddTableDataRowToMaster ( tform, eid, rsid, refid ) {

     $.ajax ({
	 url: numbaturl + 'final/addtabledatarowtofinal.php',
	 type: 'post',
	 data: {
	     tableformat: tform,
	     elementid: eid,
	     refset: rsid,
	     ref: refid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMasterTable' + eid).html(html);

     });

 }

 function nbtAddSubTableRowToMaster ( eid, rsid, refid, seid ) {

     $.ajax ({
	 url: numbaturl + 'final/addsubtablerowtofinal.php',
	 type: 'post',
	 data: {
	     elementid: eid,
	     refset: rsid,
	     ref: refid,
	     subextractionid: seid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMasterSubTable' + eid + '-' + seid).html(html);

     });

 }

 function nbtCopySubExtraction ( eid, rsid, refid, oid ) {

     $.ajax ({
	 url: numbaturl + 'final/copysubextraction.php',
	 type: 'post',
	 data: {
	     elementid: eid,
	     refset: rsid,
	     ref: refid,
	     original: oid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMasterSubExtraction' + eid).html(html);

     });

 }

 function nbtDeleteMasterTableRow ( eid, rid ) {

     $.ajax ({
	 url: numbaturl + 'final/deletefinaltabledatarow.php',
	 type: 'post',
	 data: {
	     elementid: eid,
	     row: rid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMasterTD' + eid + 'RowID' + rid).slideUp().remove();

     });

 }

 function nbtDeleteMasterSubTableRow ( eid, rid ) {

     $.ajax ({
	 url: numbaturl + 'final/deletefinalsubtablerow.php',
	 type: 'post',
	 data: {
	     elementid: eid,
	     row: rid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMasterTD' + eid + 'RowID' + rid).slideUp().remove();

     });

 }

 function nbtDeleteMasterSubExtraction ( eid, oid ) {

     $.ajax ({
	 url: numbaturl + 'final/deletefinalsubextraction.php',
	 type: 'post',
	 data: {
	     elementid: eid,
	     original: oid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtMasterSubExtractionInstance' + eid + '-' + oid).slideUp();

     });

 }

 function nbtCopyToMaster ( fid, rsid, refid, rowid, exid, eid, uid ) {

     $.ajax ({
	 url: numbaturl + 'final/copytofinal.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     refset: rsid,
	     ref: refid,
	     row: rowid,
	     extrid: exid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtExtractedElement' + eid).removeClass('nbtFeedbackBad').addClass('nbtFeedbackGood');

	 $('.nbtElement' + eid + 'Check').fadeOut(0);

	 $('#nbtExtractedElement' + eid + '-' + uid).fadeIn();

	 // for open text, textarea and dates

	 $('#nbtFinalOverride' + eid).val($('#nbtExtractedValue' + eid + '-' + uid).html());

	 // for single select

	 $('.nbtElement' + eid).removeClass('nbtTextOptionChosen');

	 $('#nbtElement' + eid + '-' + $('.nbtSingleSelectExtraction' + eid + '-' + exid + '.nbtTextOptionChosen').attr('dbname')).addClass('nbtTextOptionChosen');

     });

 }

 function nbtCopyTagToFinal ( newtag, eid, formid, refset, refid, col ) {

     finaltagstext = $('#nbtFinalSelectedTags' + eid ).val();
     finaltags = finaltagstext.split(";");
     finaltags = finaltags.map(s => s.trim());
     finaltags.push(newtag.trim());
     finaltags = finaltags.sort();

     function onlyUnique(value, index, self) {
	 return self.indexOf(value) === index;
     }
     finaltags = finaltags.filter(onlyUnique);

     for (var key in finaltags) {

	 if (finaltags[key].toLowerCase().trim() == "") {
	     finaltags.splice(key, 1);
	 }

     }

     $.ajax ({
	 url: numbaturl + 'final/updatecolumn.php',
	 type: 'post',
	 data: {
	     newvalue: finaltags.join(";"),
	     fid: formid,
	     rsid: refset,
	     rid: refid,
	     column: col
	 },
	 dataType: 'html'
     }).done ( function (html) {
	 $('#nbtFinalSelectedTags' + eid).val(finaltags.join(";"));
	 nbtUpdateFinalTagsTable (eid, formid, refset, refid, col);
     });

 }

 function nbtRemoveTagFromFinal ( tagval, eid, formid, refset, refid, col ) {

     finaltagstext = $('#nbtFinalSelectedTags' + eid ).val();

     if (finaltagstext != "") {

	 finaltags = finaltagstext.split(";");
	 finaltags = finaltags.map(s => s.trim());
	 finaltags = finaltags.sort();

	 function onlyUnique(value, index, self) {
	     return self.indexOf(value) === index;
	 }
	 finaltags = finaltags.filter(onlyUnique);

	 for (var key in finaltags) {

	     if (finaltags[key].toLowerCase().trim() == "") {
		 finaltags.splice(key, 1);
	     }

	     if (finaltags[key].toLowerCase().trim() == tagval.toLowerCase().trim()) {
		 finaltags.splice(key, 1);
	     }
	 }

     } else {
	 finaltags = [];
     }

     $.ajax ({
	 url: numbaturl + 'final/updatecolumn.php',
	 type: 'post',
	 data: {
	     newvalue: finaltags.join(";"),
	     fid: formid,
	     rsid: refset,
	     rid: refid,
	     column: col
	 },
	 dataType: 'html'
     }).done ( function (html) {
	 $('#nbtFinalSelectedTags' + eid).val(finaltags.join(";"));
	 nbtUpdateFinalTagsTable (eid, formid, refset, refid, col);
     });

 }

 function nbtUpdateFinalTagsTable (eid, formid, refset, refid, col) {

     $('#nbtTagsContainer' + eid).removeClass('nbtFeedbackBad').addClass('nbtFeedbackGood');

     finaltagstext = $('#nbtFinalSelectedTags' + eid ).val();
     finaltags = finaltagstext.split(";");
     finaltags = finaltags.map(s => s.trim());

     $('.nbtFinalTagRow' + eid).remove();

     if (finaltagstext != "") {
	 for (var key in finaltags) {
	     $('#nbtFinalTagsTable' + eid).append('<tr class="nbtFinalTagRow' + eid + '"><td>' + finaltags[key] + '</td><td style="text-align: right;"><button onclick="nbtRemoveTagFromFinal(\'' + finaltags[key].replace(/\'/g, '\\\'') + '\', ' + eid + ', ' + formid + ', ' + refset + ', ' + refid + ', \'' + col + '\');">Remove</button></td></tr>');
	 }
     }

     $('#nbtFinalTagsTable' + eid).append('<tr class="nbtFinalTagRow' + eid + '"><td colspan="2"><input type="text" placeholder="Add new tag" onblur="nbtCopyTagToFinal($(this).val(), ' + eid + ', ' + formid + ', ' + refset +', ' + refid + ', \'' + col + '\');" onkeyup="if (event.keyCode == 13) { nbtCopyTagToFinal($(this).val(), ' + eid + ', ' + formid + ', ' + refset +', ' + refid + ', \'' + col + '\'); }"></td></tr>');

 }

 function nbtCopyMultiSelectToMaster ( fid, rsid, refid, exid, eid, uid ) {

     $.ajax ({
	 url: numbaturl + 'final/copymstofinal.php',
	 type: 'post',
	 data: {
	     formid: fid,
	     refset: rsid,
	     ref: refid,
	     extrid: exid,
	     element: eid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtExtractedElement' + eid).removeClass('nbtFeedbackBad').addClass('nbtFeedbackGood');

	 $('.nbtElement' + eid + 'Check').fadeOut(0);

	 $('#nbtExtractedElement' + eid + '-' + uid).fadeIn();

	 // update the final copy

	 $('.nbtElement' + eid).removeClass('nbtTextOptionChosen');

	 $('.nbtExtractedOption' + eid + '-' + uid + '.nbtTextOptionChosen').each(function () {
	     $('#nbtElement' + eid + '-' + $(this).attr('dbname')).addClass('nbtTextOptionChosen');
	 });

     });

 }

 function nbtCopyCitationToMaster ( eid, cid, rsid, ref, citationid ) {

     // alert ($('.nbtCitOrigRef' + eid + '-' + citationid).length);

     $.ajax ({
	 url: numbaturl + 'final/copycitetofinal.php',
	 type: 'post',
	 data: {
	     element: eid,
	     citationid: cid,
	     refset: rsid,
	     refid: ref
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 if ( ! $('.nbtCitOrigRef' + eid + '-' + citationid).length ) {

	     // alert ('not already cited');

	     $('#nbtMasterCiteCopyFeedback' + eid + '-' + cid).html('Copied').removeClass('nbtFeedbackGood').removeClass('nbtFeedbackBad').fadeIn(500, function () {

		 $('#nbtMasterCitations' + eid).html(html);
		 $('#nbtMasterCiteCopyFeedback' + eid + '-' + cid).addClass('nbtFeedbackGood');

	     });

	 } else {

	     $('#nbtMasterCiteCopyFeedback' + eid + '-' + cid).html('Not copied').fadeIn();
	     $('#nbtMasterCiteCopyFeedback' + eid + '-' + cid).addClass('nbtFeedbackBad');

	 }

     });

 }

 function nbtCopySEPreviousSingleSelect ( eid, previousid, currentid, dbname ) {

     $('.nbtSub' + currentid + '-' + dbname + '.nbtTextOptionChosen').click();

     $('.nbtSub' + previousid + '-' + dbname + '.nbtTextOptionChosen').each ( function () {

	 var previousbuttonid = 'nbtSub' + eid + '-' + previousid + 'Q' + dbname + 'A';

	 var prev = $(this).attr('id');

	 var previousanswer = prev.substring(previousbuttonid.length);

	 var currentbuttonid = 'nbtSub' + eid + '-' + currentid + 'Q' + dbname + 'A' + previousanswer;

	 $('#' + currentbuttonid).click();

     });

 }

 function nbtCopySEPreviousMultiSelect ( eid, previousid, currentid, dbname ) {

     $('.nbtSub' + currentid + '-' + dbname + '.nbtTextOptionChosen').each ( function () {

	 $(this).click();

     });

     $('.nbtSub' + previousid + '-' + dbname + '.nbtTextOptionChosen').each ( function () {

	 var previousbuttonid = 'nbtSub' + eid + '-' + previousid + 'MS';

	 var prev = $(this).attr('id');

	 var previousanswer = prev.substring(previousbuttonid.length);

	 var currentbuttonid = 'nbtSub' + eid + '-' + currentid + 'MS' + previousanswer;

	 $('#' + currentbuttonid).click();

     });

 }

 function nbtPasswordCheck () {
     if ( $('#nbtSignUpPassword1').val() == "" ) {
	 $('#nbtChangePassButton').attr('disabled', true);
     } else {
	 if ($('#nbtSignUpPassword1').val() == $('#nbtSignUpPassword2').val()) {
	     $('#nbtChangePassButton').removeAttr('disabled');
	 } else {
	     $('#nbtChangePassButton').attr('disabled', true);
	 }
     }
 }

 function nbtChangePassword ( user, code ) {

     $.ajax ({
	 url: numbaturl + 'forgot/changepass.php',
	 type: 'post',
	 data: {
	     username: user,
	     changecode: code,
	     newpassword: $('#nbtSignUpPassword1').val()
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('div#nbtChangePass').html('<h2>Password change ' + html + '</h2><p>You may <a href="' + numbaturl + '">log in</a> normally.</p>');

     });

 }

 function nbtHideAssignment ( aid ) {

     $.ajax ({
	 url: numbaturl + 'assignments/hide.php',
	 type: 'post',
	 data: {
	     assignmentid: aid
	 },
	 dataType: 'html'
     }).done ( function (html) {

	 $('#nbtAssignment' + aid).fadeOut();

     });

 }

 function nbtSetMasterStatus ( fid, mid, newstatus, buttonid, classid ) {

     if ( $('#' + buttonid).hasClass('nbtTextOptionChosen') ) { // IF it's already selected

	 $.ajax ({
	     url: numbaturl + 'final/changefinalstatus.php',
	     type: 'post',
	     data: {
		 formid: fid,
		 masterid: mid,
		 masterstatus: newstatus
	     },
	     dataType: 'html'
	 }).done ( function (html) {
	     $('.' + classid).removeClass('nbtTextOptionChosen');
	 });

     } else { // It's not already selected

	 $.ajax ({
	     url: numbaturl + 'final/changefinalstatus.php',
	     type: 'post',
	     data: {
		 formid: fid,
		 masterid: mid,
		 masterstatus: newstatus
	     },
	     dataType: 'html'
	 }).done ( function (html) {
	     $('.' + classid).removeClass('nbtTextOptionChosen');
	     $('#' + buttonid).addClass('nbtTextOptionChosen');
	 });

     }

 }

 function nbtCheckLogin () {

     if ($('.nbtSigninPanel').length == 0) {

         $.ajax ({
	     url: numbaturl + 'timeout.php',
	     type: 'post',
	     dataType: 'html'
	 }).done ( function (response) {

	     if ( response == 1 ) {

		 setTimeout ( function () {

		     nbtCheckLogin ();

		 }, 30000);

	     } else {

		 alert ('Your session timed out. Log in again to continue working.');
		 window.location.replace(numbaturl);

	     }

	 });

     }

 }

 function nbtCheckTextAreaCharacters ( textareaid, maxlength ) {

     if ( $('#' + textareaid).val().length > maxlength ) {

	 $('#' + textareaid).val() = $('#' + textareaid).val().substring ( 0, maxlimit );

     }

 }

 function nbtDeleteAttachment ( rsid, rid, ext ) {

     $.ajax ({
	 url: numbaturl + 'attach/delete.php',
	 type: 'post',
	 data: {
	     refsetid: rsid,
	     refid: rid,
	     filetype: ext
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 if ( response == 'Deleted' ) {

	     $('#nbtAttachmentRow' + rsid + '-' + rid + '-' + ext).css('text-decoration', 'line-through');
	     $('#nbtDeleteAttachment' + rsid + '-' + rid + '-' + ext).fadeOut();

	 } else {

	     alert ('There was an error deleting this file.');

	 }

     });

 }

 function nbtSearchForMultiples ( rsid ) {

     $.ajax ({
	 url: numbaturl + 'references/multiple/search.php',
	 type: 'post',
	 data: {
	     refset: rsid,
	     query: $('#nbtSearchMultiples').val()
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 $('#nbtMultipleSearchResponse').html(response);

     });

 }

 function nbtDeleteMultipleRef ( rsid, rid ) {

     $.ajax ({
	 url: numbaturl + 'references/multiple/delete.php',
	 type: 'post',
	 data: {
	     refset: rsid,
	     ref: rid
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 $('#nbtMultiple' + rid).fadeOut();

     });

 }

 function nbtMultipleMoveAssignments ( rsid, from_ref ) {

     to_ref = $('#nbtMultiMoveAssignChooser' + from_ref).val();

     $.ajax ({
	 url: numbaturl + 'references/multiple/move_assignments.php',
	 type: 'post',
	 data: {
	     refset: rsid,
	     from_rid: from_ref,
	     to_rid: to_ref
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 nbtSearchForMultiples ( rsid );

     });

 }

 function nbtMultipleMoveCitations ( rsid, from_ref ) {

     to_ref = $('#nbtMultiMoveCiteChooser' + from_ref).val();

     $.ajax ({
	 url: numbaturl + 'references/multiple/move_citations.php',
	 type: 'post',
	 data: {
	     refset: rsid,
	     from_rid: from_ref,
	     to_rid: to_ref
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 nbtSearchForMultiples ( rsid );

     });

 }

 function nbtMultipleMoveExtractions ( rsid, from_ref ) {

     to_ref = $('#nbtMultiMoveExtractChooser' + from_ref).val();

     $.ajax ({
	 url: numbaturl + 'references/multiple/move_extractions.php',
	 type: 'post',
	 data: {
	     refset: rsid,
	     from_rid: from_ref,
	     to_rid: to_ref
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 nbtSearchForMultiples ( rsid );

     });

 }

 function nbtMultipleMoveMaster ( rsid, from_ref ) {

     to_ref = $('#nbtMultiMoveMasterChooser' + from_ref).val();

     $.ajax ({
	 url: numbaturl + 'references/multiple/move_final.php',
	 type: 'post',
	 data: {
	     refset: rsid,
	     from_rid: from_ref,
	     to_rid: to_ref
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 nbtSearchForMultiples ( rsid );

     });

 }

 function nbtCiteClearField ( eid ) {

     $('#nbtCitationSuggestions' + eid).slideUp(500, function () {

	 $('#nbtCitationSuggestions' + eid).html('&nbsp;');
	 $('#nbtCitationSuggestions' + eid).slideDown();

     });
     $('#nbtCitationFinder' + eid).val('');

 }

 jQuery.fn.reverse = function() {

     return this.pushStack(this.get().reverse(), arguments);

 }

 function nbtExtractionTableDataKeyHandle ( event, input_element, eid, cid, rsid, rid ) {

     if (event.keyCode == 40) { // Down arrow key pressed

	 found_focus = 0;

	 row_counter = 0;

	 num_of_rows = $('.nbtTable' + eid + '-' + cid).length;

	 $('.nbtTable' + eid + '-' + cid).each( function () {

	     row_counter ++;

	     if ( found_focus == 1 ) {

		 $(this).focus();

	     }

	     if ( $(this).is(':focus') ) {

		 found_focus ++;

		 if ( row_counter == num_of_rows && found_focus == 1 ) {

		     $(input_element).blur();

		     setTimeout ( function () {

			 nbtAddExtractionTableDataRow ('table_data', eid, rsid, rid);

		     }, 50);

		 }

	     }

	 });

     }

     if (event.keyCode == 38) { // Up arrow key pressed

	 found_focus = 0;

	 $('.nbtTable' + eid + '-' + cid).reverse().each( function () {

	     if ( found_focus == 1 ) {

		 $(this).focus();

	     }

	     if ( $(this).is(':focus') ) {

		 found_focus ++;

	     }

	 });

     }

 }

 function nbtSubTableDataKeyHandle ( event, input_element, eid, cid, rsid, rid, seid ) {

     if (event.keyCode == 40) { // Down arrow key pressed

	 found_focus = 0;

	 row_counter = 0;

	 num_of_rows = $('.nbtSubTable' + eid + '-' + cid).length;

	 $('.nbtSubTable' + eid + '-' + cid).each( function () {

	     row_counter ++;

	     if ( found_focus == 1 ) {

		 $(this).focus();

	     }

	     if ( $(this).is(':focus') ) {

		 found_focus ++;

		 if ( row_counter == num_of_rows && found_focus == 1 ) {

		     $(input_element).blur();

		     setTimeout ( function () {

			 nbtAddSubTableDataRow (eid, rsid, rid, seid);

		     }, 50);

		 }

	     }

	 });

     }

     if (event.keyCode == 38) { // Up arrow key pressed

	 found_focus = 0;

	 $('.nbtTable' + eid + '-' + cid).reverse().each( function () {

	     if ( found_focus == 1 ) {

		 $(this).focus();

	     }

	     if ( $(this).is(':focus') ) {

		 found_focus ++;

	     }

	 });

     }

 }

 function nbtMasterTableDataKeyHandle ( event, input_element, eid, cid ) {

     if (event.keyCode == 40) { // Down arrow key pressed

	 found_focus = 0;

	 row_counter = 0;

	 num_of_rows = $('.nbtTable' + eid + '-' + cid).length;

	 $('.nbtTable' + eid + '-' + cid).each( function () {

	     row_counter ++;

	     if ( found_focus == 1 ) {

		 $(this).focus();

	     }

	     if ( $(this).is(':focus') ) {

		 found_focus ++;

	     }

	 });

     }

     if (event.keyCode == 38) { // Up arrow key pressed

	 found_focus = 0;

	 $('.nbtTable' + eid + '-' + cid).reverse().each( function () {

	     if ( found_focus == 1 ) {

		 $(this).focus();

	     }

	     if ( $(this).is(':focus') ) {

		 found_focus ++;

	     }

	 });

     }

 }

 function nbtExportData ( etype, rsid, fid, f_or_e ) {

     $.ajax ({
	 url: numbaturl + 'export/save_dataset.php',
	 type: 'post',
	 data: {
	     export_type: etype,
	     refsetid: rsid,
	     formid: fid,
	     final: f_or_e
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 $('#nbtThinkyLinky').attr('href', numbaturl + 'export/' + response + '.tsv');

	 $('#nbtCoverup').fadeIn();
	 $('#nbtThinky').fadeIn();


     });

 }

 function nbtExportIRR (fid, rid) {

     if ($('#nbtIRR' + fid + '-' + rid).val() != 'ns') {

	 window.location.href = numbaturl + 'export/save_irr.php?formid=' + fid + '&refset=' + rid + '&elementid=' + $('#nbtIRR' + fid + '-' + rid).val();

     }

 }

 function nbtExportRefset ( rsid ) {

     if ( rsid != "ns" ) {

	 $.ajax ({
	     url: numbaturl + 'references/save_refset.php',
	     type: 'post',
	     data: {
		 refsetid: rsid
	     },
	     dataType: 'html'
	 }).done ( function (response) {

	     $('#nbtThinkyLinky').attr('href', numbaturl + 'references/referenceset_' + response + '.csv')

	     $('#nbtCoverup').fadeIn();
	     $('#nbtThinky').fadeIn();


	 });

     }

 }

 function nbtChoosePrevSelect ( colname, newval ) {

     $('#nbtTextField' + colname).val(newval).blur();

 }

 function nbtUpdateRefsetMetadata ( col, rsid ) {

     $.ajax ({
	 url: numbaturl + 'references/update_metadata.php',
	 type: 'post',
	 data: {
	     refsetid: rsid,
	     column: col,
	     newcolumn: $('#nbtMetadata-' + col).val()
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 $('#nbtMetadataResponse-' + col).html(response).fadeIn(500, function () {
	     setTimeout(function () {
		 $('#nbtMetadataResponse-' + col).fadeOut();
	     }, 1000);
	 });

     });

 }

 function nbtUpdateCompletedAssignmentsCount ( formid, refsetid ) {

     denominator = $('.nbtAssigned.nbtAssignmentNameForForm' + formid).length;

     if ( denominator > 0 ) {

	 $.ajax ({
	     url: numbaturl + 'assignments/incomplete_assignments_count.php',
	     type: 'post',
	     data: {
		 fid: formid,
		 rsid: refsetid
	     },
	     dataType: 'html'
	 }).done ( function (response) {

	     denominator = $('.nbtAssigned.nbtAssignmentNameForForm' + formid).length;

	     numerator = denominator - response;

	     $('#nbtAssignmentsNotCompletedCountForForm-' + formid).html(numerator + '/' + denominator + ' assignments complete');

	 });

     } else {

	 $('#nbtAssignmentsNotCompletedCountForForm-' + formid).html(denominator + ' assignments');

     }

 }

 function nbtUpdateExtractionDisplay () {

     time_started = $('#time_started').val();
     time_finished = $('#time_finished').val();

     switch (true) {

	 case time_started < 60:

	     $('#time_started_display').html('Less than a minute ago');

	     break;

	 case time_started < (60*60):

	     // console.log('less than an hour');

	     display_time_started = Math.floor(time_started / 60);

	     $('#time_started_display').html( display_time_started + ' minute(s) ago');

	     break;

	 case time_started < (24*60*60):

	     // console.log('less than a day');

	     display_time_hours = Math.floor(time_started / (60*60));

	     display_time_minutes = Math.floor ( (time_started - (display_time_hours*60*60)) / 60);

	     display_time_minutes = ('0' + display_time_minutes).slice(-2);

	     display_time_combined = display_time_hours + ":" + display_time_minutes;

	     $('#time_started_display').html( display_time_combined + ' ago');

	     break;

	 case time_started < (7*24*60*60):

	     // console.log('less than a week');

	     display_time_days = Math.floor( time_started / 86400 );

	     $('#time_started_display').html( display_time_days + ' day(s) ago');

	     break;

	 case time_started < (365*24*60*60):

	     // console.log('less than a year');

	     display_time_weeks = Math.floor( time_started / (86400*7) );

	     $('#time_started_display').html( display_time_weeks + ' week(s) ago');

	     break;

	 default:

	     // console.log('more than a year');

	     display_time_years = Math.floor( time_started / (365*24*60*60) );

	     $('#time_started_display').html( display_time_years + ' year(s) ago');

	     break;
     }

     if (time_finished == "NaN") {

	 $('#time_finished_display').html('(Timer is still running; mark the extraction as Completed to stop the timer)');

	 $('#nbtFinishedTimeClearButton').slideUp();

     } else {

	 $('#nbtFinishedTimeClearButton').slideDown();

	 time_elapsed =  time_started - time_finished;

	 // console.log('finished ' + time_finished);
	 // console.log('elapsed ' + time_elapsed);

	 switch (true) {

	     case time_finished < 60:

		 finished_display = 'Less than a minute ago';

		 break;

	     case time_finished < (60*60):

		 display_time_finished = Math.floor(time_finished / 60);

		 finished_display = display_time_finished + ' minute(s) ago';

		 break;

	     case time_finished < (24*60*60):

		 display_time_hours = Math.floor(time_finished / (60*60));

		 display_time_minutes = Math.floor ( (time_finished - (display_time_hours*60*60)) / 60);

		 display_time_minutes = ('0' + display_time_minutes).slice(-2);

		 display_time_combined = display_time_hours + ":" + display_time_minutes;

		 finished_display = display_time_combined + ' ago';

		 break;

	     case time_finished < (7*24*60*60):

		 display_time_days = Math.floor( time_finished / 86400 );

		 finished_display = display_time_days + ' day(s) ago';

		 break;

	     case time_finished < (365*24*60*60):

		 display_time_weeks = Math.floor( time_finished / (86400*7) );

		 finished_display = display_time_weeks + ' week(s) ago';

		 break;

	     default:

		 display_time_years = Math.floor( time_finished / (365*24*60*60) );

		 finished_display = display_time_years + ' years(s) ago';

		 break;

	 }

	 switch (true) {

	     case time_elapsed < (24*60*60):

		 elapsed_hours = Math.floor(time_elapsed / (60*60))

		 elapsed_minutes = Math.floor ( (time_elapsed - (elapsed_hours*60*60)) / 60 );

		 elapsed_display_minutes = ('0' + elapsed_minutes).slice(-2);

		 elapsed_display = elapsed_hours + ":" + elapsed_display_minutes;

		 break;

	     default:

		 elapsed_days = Math.floor( time_elapsed / 86400 );

		 elapsed_display = elapsed_days + ' days';

		 break;
	 }

	 $('#time_finished_display').html(finished_display + ' (extraction time ' + elapsed_display + ')');

     }

 }

 function nbtUpdateExtractionTimer (seconds) {

     time_started = parseInt($('#time_started').val());
     time_finished = parseInt($('#time_finished').val());

     $('#time_started').val(time_started + seconds);
     $('#time_finished').val(time_finished + seconds);

     nbtUpdateExtractionDisplay();

     setTimeout( function () {

	 nbtUpdateExtractionTimer (seconds);

     }, seconds * 1000);

 }

 function nbtRestartExtractionTimer ( form, refset, ref ) {

     $.ajax ({
	 url: numbaturl + 'extract/restart-extraction-timer.php',
	 type: 'post',
	 data: {
	     fid: form,
	     rsid: refset,
	     rid: ref
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 if (response == 'Timer reset') {

	     $('#time_started').val('0');
	     $('#time_finished').val('NaN');
	     $('#nbtQstatusA1').click();

	 } else {

	     alert ('Error resetting timer');

	 }

     });

 }

 function nbtClearFinishedTime ( form, refset, ref ) {

     $.ajax ({
	 url: numbaturl + 'extract/clear-finished-extraction-timer.php',
	 type: 'post',
	 data: {
	     fid: form,
	     rsid: refset,
	     rid: ref
	 },
	 dataType: 'html'
     }).done ( function (response) {

	 if (response == 'Timer reset') {

	     $('#time_finished').val('NaN');
	     $('#nbtQstatusA1').click();

	 } else {

	     alert ('Error resetting timer');

	 }

     });

 }

 $('a.nbtstatus').click(function () {
     $('#nbtStatusAtBottomOfExtraction').html($(this).html());
     $('#nbtStatusAtBottomOfReconciliation').html($(this).html());
 });

 $('#nbtFormElements').on('sortupdate', function (event, ui) {
     // Make an array of the elements in order
     elements = [];
     $(this).children('.nbtFormEditorElement').each(function () {
	 if ($(this).attr('elementid') != '') {
	     elements.push($(this).attr('elementid'));
	 }
     });
     elements = JSON.stringify(elements);

     // Send that to the server
     $.ajax ({
	 url: numbaturl + 'forms/saveformelementssortorder.php',
	 type: 'post',
	 data: {
	     elementorder: elements
	 },
	 dataType: 'html'
     }).done( function (response) {
	 res = JSON.parse(response);

	 if (!res) {
	     alert ('Error saving order of form elements');
	 }

     });

 });

 $('.nbtSubExtractionEditor').on('sortupdate', function (event, ui) {
     // Make an array of the subelements in order
     subelements = [];
     $(this).children('.nbtSubElementEditor').each(function () {
	 if ($(this).attr('subelementid') != '') {
	     subelements.push($(this).attr('subelementid'));
	 }
     });
     subelements = JSON.stringify(subelements);

     // Send that to the server
     $.ajax ({
	 url: numbaturl + 'forms/savesubelementssortorder.php',
	 type: 'post',
	 data: {
	     subelementorder: subelements
	 },
	 dataType: 'html'
     }).done( function (response) {
	 res = JSON.parse(response);

	 if (!res) {
	     alert ('Error saving order of sub-extraction elements');
	 }

     });
 });

 $('div#nbtFormElements').on('click', '.nbtFormEditorCollapse', function() {
     $(this).parent().children().not('h4').not('button').not('.nbtFormElementDeleterContainer').slideToggle();
     if ($(this).parent().children().find('.nbtDisplayName').val() != '') {
	 $(this).parent().children().children('.nbtDisplayNameHidden').html('(' + $(this).parent().children().find('.nbtDisplayName').val() + ')');
     } else {
	 $(this).parent().children().children('.nbtDisplayNameHidden').html('');
     }
     $(this).parent().children().children('.nbtDisplayNameHidden').fadeToggle();
 });

 function collapseAllFormElements () {
     $('div#nbtFormElements .nbtFormEditorCollapse').parent().children().children('.nbtDisplayNameHidden').not(':visible').parent().parent().children('.nbtFormEditorCollapse').click();
 }

 function expandAllFormElements () {
     $('div#nbtFormElements .nbtFormEditorCollapse').parent().children().children('.nbtDisplayNameHidden:visible').parent().parent().children('.nbtFormEditorCollapse').click();
 }

 function nbtFormElementToggleStartupVisible (elementid) {

     $.ajax ({
	 url: numbaturl + 'forms/elementtogglestartupvisible.php',
	 type: 'post',
	 data: {
	     element: elementid
	 },
	 dataType: 'html'
     }).done( function (response) {

	 if ( response == 1 ) {
	     $('#nbtCondDispStartStatusVisible' + elementid).addClass('nbtTextOptionChosen');
	     $('#nbtCondDispStartStatusHidden' + elementid).removeClass('nbtTextOptionChosen');

	     $('#nbtCondDispEventsContainer' + elementid).slideUp();
	     $('#nbtAddConditionalDisplayEvent' + elementid).slideUp();
	     $('#nbtConditionLogicDescription' + elementid).slideUp();
	     $('#nbtDestructiveHidingDescription' + elementid).slideUp();
	 } else {
	     $('#nbtCondDispStartStatusVisible' + elementid).removeClass('nbtTextOptionChosen');
	     $('#nbtCondDispStartStatusHidden' + elementid).addClass('nbtTextOptionChosen');

	     $('#nbtCondDispEventsContainer' + elementid).slideDown();
	     $('#nbtAddConditionalDisplayEvent' + elementid).slideDown();
	     $('#nbtConditionLogicDescription' + elementid).slideDown();
	     $('#nbtDestructiveHidingDescription' + elementid).slideDown();
	 }

     });

 }

 function nbtSubElementToggleStartupVisible (subelementid) {

     $.ajax ({
	 url: numbaturl + 'forms/subelementtogglestartupvisible.php',
	 type: 'post',
	 data: {
	     subelement: subelementid
	 },
	 dataType: 'html'
     }).done( function (response) {

	 if ( response == 1 ) {
	     $('#nbtCondDispStartStatusVisibleSub' + subelementid).addClass('nbtTextOptionChosen');
	     $('#nbtCondDispStartStatusHiddenSub' + subelementid).removeClass('nbtTextOptionChosen');

	     $('#nbtCondDispEventsContainerSub' + subelementid).slideUp();
	     $('#nbtAddConditionalDisplayEventSub' + subelementid).slideUp();
	     $('#nbtConditionLogicDescriptionSub' + subelementid).slideUp();
	     $('#nbtDestructiveHidingDescriptionSub' + subelementid).slideUp();
	 } else {
	     $('#nbtCondDispStartStatusVisibleSub' + subelementid).removeClass('nbtTextOptionChosen');
	     $('#nbtCondDispStartStatusHiddenSub' + subelementid).addClass('nbtTextOptionChosen');

	     $('#nbtCondDispEventsContainerSub' + subelementid).slideDown();
	     $('#nbtAddConditionalDisplayEventSub' + subelementid).slideDown();
	     $('#nbtConditionLogicDescriptionSub' + subelementid).slideDown();
	     $('#nbtDestructiveHidingDescriptionSub' + subelementid).slideDown();
	 }

     });

 }

 function nbtAddCondDispEvent (elementid) {

     $.ajax ({
	 url: numbaturl + 'forms/addconddispevent.php',
	 type: 'post',
	 data: {
	     element: elementid
	 },
	 dataType: 'html'
     }).done( function (response) {

	 $('#nbtCondDispEventsContainer' + elementid).html(response);

     });

 }

 function nbtAddCondDispEventSub (subelementid) {

     $.ajax ({
	 url: numbaturl + 'forms/addconddispevent.php',
	 type: 'post',
	 data: {
	     subelement: subelementid
	 },
	 dataType: 'html'
     }).done( function (response) {

	 $('#nbtCondDispEventsContainerSub' + subelementid).html(response);

     });

 }

 function nbtRemoveCondDispEvent (eventid) {

     $.ajax ({
	 url: numbaturl + 'forms/removeconddispevent.php',
	 type: 'post',
	 data: {
	     event: eventid
	 },
	 dataType: 'html'
     }).done( function (response) {

	 if (response == "Success") {
	     $('#nbtCondDispEvent' + eventid).slideUp(400, function () {
		 $('#nbtCondDispEvent' + eventid).remove();
	     });
	 } else {
	     alert (response);
	 }

     });

 }

 function nbtUpdateCondDispTriggerElement (eventid) {

     $.ajax ({
	 url: numbaturl + 'forms/updateconddisptriggerelement.php',
	 type: 'post',
	 data: {
	     event: eventid,
	     trigger_element: $('#nbtCondDispTriggerElement' + eventid).val()
	 },
	 dataType: 'html'
     }).done( function (response) {
	 res = JSON.parse(response);

	 if (res) {
	     $('#nbtCondDispTriggerOption' + eventid).html('');
	     $('#nbtCondDispTriggerOption' + eventid).append('<option value="ns" selected>Choose an option</option>');
	     for (var i = 0; i < res.length; i++) {
		 $('#nbtCondDispTriggerOption' + eventid).append('<option value="' + res[i]['id'] + '">' + res[i]['displayname'] + '</option>');
	     }
	     nbtUpdateCondDispTriggerOption (eventid);
	 } else {
	     alert (response);
	 }
     });

 }

 function nbtUpdateSubCondDispTriggerElement (eventid) {

     $.ajax ({
	 url: numbaturl + 'forms/updatesubconddisptriggerelement.php',
	 type: 'post',
	 data: {
	     event: eventid,
	     trigger_element: $('#nbtSubCondDispTriggerElement' + eventid).val()
	 },
	 dataType: 'html'
     }).done( function (response) {
	 res = JSON.parse(response);

	 if (res) {
	     $('#nbtSubCondDispTriggerOption' + eventid).html('');
	     $('#nbtSubCondDispTriggerOption' + eventid).append('<option value="ns" selected>Choose an option</option>');
	     for (var i = 0; i < res.length; i++) {
		 $('#nbtSubCondDispTriggerOption' + eventid).append('<option value="' + res[i]['id'] + '">' + res[i]['displayname'] + '</option>');
	     }
	     nbtUpdateCondDispTriggerOption (eventid);
	 } else {
	     alert (response);
	 }
     });

 }

 function nbtUpdateCondDispTriggerOption (eventid) {

     $.ajax ({
	 url: numbaturl + 'forms/updateconddisptriggeroption.php',
	 type: 'post',
	 data: {
	     event: eventid,
	     trigger_option: $('#nbtCondDispTriggerOption' + eventid).val()
	 },
	 dataType: 'html'
     }).done( function (response) {
	 if (response != 'Success') {
	     alert(response);
	 }
     });

 }

 function nbtUpdateSubCondDispTriggerOption (eventid) {

     $.ajax ({
	 url: numbaturl + 'forms/updateconddisptriggeroption.php',
	 type: 'post',
	 data: {
	     event: eventid,
	     trigger_option: $('#nbtSubCondDispTriggerOption' + eventid).val()
	 },
	 dataType: 'html'
     }).done( function (response) {
	 if (response != 'Success') {
	     alert(response);
	 }
     });

 }

 function nbtUpdateCondDispType (eventid) {

     $.ajax ({
	 url: numbaturl + 'forms/updateconddisptype.php',
	 type: 'post',
	 data: {
	     event: eventid,
	     cd_type: $('#nbtCondDispType' + eventid).val()
	 },
	 dataType: 'html'
     }).done( function (response) {
	 if (response == 'Success') {
	     switch ($('#nbtCondDispType' + eventid).val()) {
		 case "is":
		 case "is-not":
		     $('#nbtCondDispTriggerOption' + eventid).slideDown();
		     $('#nbtSubCondDispTriggerOption' + eventid).slideDown();
		     break;
		 case "has-response":
		 case "no-response":
		     $('#nbtCondDispTriggerOption' + eventid).slideUp();
		     $('#nbtSubCondDispTriggerOption' + eventid).slideUp();
		     break;
	     }
	 } else {
	     alert(response);
	 }
     });

 }

 function nbtUpdateCondDispLogic (elementid) {

     $.ajax ({
	 url: numbaturl + 'forms/updateconddisplogic.php',
	 type: 'post',
	 data: {
	     element: elementid,
	     operator: $('#nbtCondDispLogic' + elementid).val()
	 },
	 dataType: 'html'
     }).done( function (response) {
	 if (response != 'Success') {
	     alert(response);
	 }
     });

 }

 function nbtUpdateSubCondDispLogic (subelementid) {

     $.ajax ({
	 url: numbaturl + 'forms/updatesubconddisplogic.php',
	 type: 'post',
	 data: {
	     subelement: subelementid,
	     operator: $('#nbtSubCondDispLogic' + subelementid).val()
	 },
	 dataType: 'html'
     }).done( function (response) {
	 if (response != 'Success') {
	     alert(response);
	 }
     });

 }

 function nbtUpdateCondDispHideAction (elementid) {

     $.ajax ({
	 url: numbaturl + 'forms/updateconddisphideaction.php',
	 type: 'post',
	 data: {
	     element: elementid,
	     action: $('#nbtCondDispHideAction' + elementid).val()
	 },
	 dataType: 'html'
     }).done( function (response) {
	 if (response != 'Success') {
	     alert(response);
	 }
     });

 }

 function nbtUpdateSubCondDispHideAction (subelementid) {

     $.ajax ({
	 url: numbaturl + 'forms/updatesubconddisphideaction.php',
	 type: 'post',
	 data: {
	     subelement: subelementid,
	     action: $('#nbtSubCondDispHideAction' + subelementid).val()
	 },
	 dataType: 'html'
     }).done( function (response) {
	 if (response != 'Success') {
	     alert(response);
	 }
     });

 }

 function nbtDeleteUploadedFile (fileid) {

     $.ajax ({
	 url: numbaturl + 'uploads/delete.php',
	 type: 'post',
	 data: {
	     fid: fileid
	 },
	 dataType: 'html'
     }).done( function (response) {
	 if (response != 'dbfile') {
	     alert('Error deleting file');
	 } else {
	     $('#nbtUploadRow' + fileid + ' td').addClass('nbtBadField');
	      setTimeout( function () {
		  $('#nbtUploadRow' + fileid).slideUp();
	      }, 1000);

	 }
     });

 }

</script>
</body>
</html>
