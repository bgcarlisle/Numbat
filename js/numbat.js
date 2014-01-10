var numbaturl = 'http://127.0.0.1/~carlisle/Numbat/';

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
		var emailregex = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/
		
		if ( ! emailregex.test (id.value) ) { // If it's not a well-formed email
			
			document.getElementById("nbtSignupEmailFeedback").innerHTML = 'Enter a valid email address';
			$("#sigSignupEmailFeedback").removeClass('sigFeedbackGood');
			$("#sigSignupEmailFeedback").addClass('sigFeedbackBad');
			$("#sigSignupEmailFeedback").fadeIn(100);
		
		} else { // Email is well-formed
					
			// Check that the email isn't already in use on another account
			
			var xmlhttp;
			
			if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
		
				xmlhttp = new XMLHttpRequest();
				
			} else { // Code for IE5, IE6
			
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			
			}
			
			xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					document.getElementById("sigSignupEmailFeedback").innerHTML=xmlhttp.responseText;
					
					if (xmlhttp.responseText == 'Email is already in use :(') {
						$("#sigSignupEmailFeedback").removeClass('sigFeedbackGood');
						$("#sigSignupEmailFeedback").addClass('sigFeedbackBad');
						$("#sigSignupEmailFeedback").fadeIn(100);
					} else {
						$("#sigSignupEmailFeedback").removeClass('sigFeedbackBad');
						$("#sigSignupEmailFeedback").addClass('sigFeedbackGood');
						$("#sigSignupEmailFeedback").fadeIn(100);
					}
					
				} else if (xmlhttp.readyState > 0 && xmlhttp.readyState < 4) {
					document.getElementById("sigSignupEmailFeedback").innerHTML='think think think ...';
				}
			}
			
			xmlhttp.open ("POST","http://www.bgcarlisle.com/signals/signup/checkemail.php",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			
			xmlhttp.send ("email=" + id.value);
		
		}
	
	} else {
		
		document.getElementById("sigSignupEmailFeedback").innerHTML='';
			$("#sigSignupEmailFeedback").removeClass('sigFeedbackGood');
			$("#sigSignupEmailFeedback").removeClass('sigFeedbackBad');
			$("#sigSignupEmailFeedback").fadeOut(100);
		
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

function nbtNewExtractionForm () {
	
	$.ajax ({
		url: numbaturl + 'forms/newform.php',
		type: 'post',
		data: {
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormsTable').html(html);
		
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
	}).done ( function (html) {
		
		$('#nbtFormsTable').html(html);
		
	});
	
}

function nbtSaveFormName ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/changeformname.php',
		type: 'post',
		data: {
			formid: fid,
			newname: $('#nbtFormName').val()
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormNameFeedback').html(html);
		
		$('#nbtFormNameFeedback').fadeIn(500, function () {
			
			$('#nbtFormNameFeedback').fadeOut(500);
			
		})
		
	});
	
}

function nbtSaveFormDescription ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/changeformdescription.php',
		type: 'post',
		data: {
			formid: fid,
			newname: $('#nbtFormDescription').val()
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormDescriptionFeedback').html(html);
		
		$('#nbtFormDescriptionFeedback').fadeIn(500, function () {
			
			$('#nbtFormDescriptionFeedback').fadeOut(500);
			
		})
		
	});
	
}

function nbtAddNewOpenText ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/addopentext.php',
		type: 'post',
		data: {
			formid: fid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormElements').html(html);
		
	});
	
}

function nbtAddNewSingleSelect ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/addsingleselect.php',
		type: 'post',
		data: {
			formid: fid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormElements').html(html);
		
	});
	
}

function nbtAddNewSectionHeading ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/addsectionheading.php',
		type: 'post',
		data: {
			formid: fid
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

function nbtChangeColumnName ( eid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/changecolumnname.php',
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

function nbtMoveFeedElement ( fid, eid, dir ) {
	
	$.ajax ({
		url: numbaturl + 'forms/moveelement.php',
		type: 'post',
		data: {
			formid: fid,
			element: eid,
			direction: dir
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormElements').html(html);
		
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

function nbtAddNewMultiSelect ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/addmultiselect.php',
		type: 'post',
		data: {
			formid: fid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormElements').html(html);
		
	});
	
}

function nbtAddNewTableData ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/addtabledata.php',
		type: 'post',
		data: {
			formid: fid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormElements').html(html);
		
	});
	
}

function nbtUpdateMultiSelectOptionColumn ( eid, sid, oldcolumn ) {
	
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

