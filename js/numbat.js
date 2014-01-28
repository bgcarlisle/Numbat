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

function nbtChangeDateColumnName ( eid ) {
	
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
		url: numbaturl + 'master/movesubextraction.php',
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

function nbtUpdateSubMultiSelectOptionColumn ( seid, sid, oldcolumn ) {
	
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

function nbtAddTableDataColumn ( eid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/addtabledatacolumn.php',
		type: 'post',
		data: {
			element: eid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtTableDataColumnsTable' + eid).html(html);
		
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

function nbtUpdateTableDataColumnDB ( eid, cid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/updatetabledatacolumndb.php',
		type: 'post',
		data: {
			column: cid,
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

function nbtAddNewCountrySelector ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/addcountryselector.php',
		type: 'post',
		data: {
			formid: fid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormElements').html(html);
		
	});
	
}

function nbtAddNewDateSelector ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/adddateselector.php',
		type: 'post',
		data: {
			formid: fid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormElements').html(html);
		
	});
	
}

function nbtNewDumpFile () {
	
	$.ajax ({
		url: numbaturl + 'backup/newdumpfile.php',
		type: 'post',
		data: {
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtListOfDumpFiles').html(html);
		
	});
	
}

function nbtAddNewCitationSelector ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/addcitationselector.php',
		type: 'post',
		data: {
			formid: fid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormElements').html(html);
		
	});
	
}

function nbtChangeCitationSelectorSuffix ( eid ) {
	
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

function nbtSaveTextField (formid, extractionid, questionid, textfieldid, feedbackid) {
	
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
		
		$('#' + feedbackid).html(html);
		
		$('#' + feedbackid).fadeIn(50, function () {
			
			setTimeout ( function () {
				
				$('#' + feedbackid).fadeOut(1000);
				
			}, 2000);
			
		});
		
	});
	
}

function nbtSaveSubExtractionTextField (elementid, extractionid, questionid, textfieldid, feedbackid) {
	
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

function nbtSaveCitationTextField (sectionid, citationid, questionid, textfieldid, feedbackid) {
	
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
		
		$('#' + feedbackid).html(html);
		
		$('#' + feedbackid).fadeIn(50, function () {
			
			setTimeout ( function () {
				
				$('#' + feedbackid).fadeOut(1000);
				
			}, 2000);
			
		});
		
	});
	
}

function nbtSaveDateField (formid, extractionid, questionid, textfieldid, feedbackid) {
	
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
			url: numbaturl + 'extract/updateextraction.php',
			type: 'post',
			data: {
				fid: formid,
				id: extractionid,
				question: questionid,
				answer: html + '-01'
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
		
		$.ajax ({
			url: numbaturl + 'extract/updatesubextraction.php',
			type: 'post',
			data: {
				eid: elementid,
				id: subextractionid,
				question: questionid,
				answer: html + '-01'
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
	
	if ( $('#' + buttonid).hasClass('nbtTextOptionChosen') ) { // IF it's already selected
		
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
			
			nbtUpdateConditionalDisplays ();
			
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
			
			nbtUpdateConditionalDisplays ();
			
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
			
			nbtUpdateConditionalDisplays ();
			
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
			
			nbtUpdateConditionalDisplays ();
			
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
		
		nbtUpdateConditionalDisplays ();
		
	});
	
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
		
		nbtUpdateConditionalDisplays ();
		
	});
	
}

function nbtUpdateExtractionTableData ( tableid, rowid, columnid, inputid) {
	
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
		
		$('#nbtTable' + tableid + 'Feedback').html(html);
		
		$('#nbtTable' + tableid + 'Feedback').slideDown(50, function () {
			
			setTimeout ( function () {
				
				$('#nbtTable' + tableid + 'Feedback').slideUp(1000);
				
			}, 2000);
			
		});
		
	});
	
}

function nbtAddExtractionTableDataRow (tableid, refsetid, refid) {
	
	$.ajax ({
		url: numbaturl + 'extract/addtabledatarow.php',
		type: 'post',
		data: {
			tid: tableid,
			refset: refsetid,
			ref: refid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtTableExtraction' + tableid).html(html);
		
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
		
		$('#nbtTDRowID' + rowid).fadeOut(200).removeClass('nbtTDRow' + tableid);
		
	});
	
}

function nbtFindCitation (eid, suffix, targetid, cid, rsid, refid) {
	
	if ( $('#nbtCitationFinder' + eid).val() == '' ) {
		
		$('#' + targetid).fadeOut(100);
		
		$('#' + targetid).html('');
		
	} else {
		
		$('#nbtCitationSuggestions' + eid).fadeIn(100);
		
		$.ajax ({
			url: numbaturl + 'extract/citationfinder.php',
			type: 'post',
			data: {
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
		
		$('#nbtCitation' + sectionid + '-' + citationid).slideUp();
		
	});
	
}

function nbtRemoveMasterCitation ( sectionid, citationid ) {
	
	$.ajax ({
		url: numbaturl + 'master/removecitation.php',
		type: 'post',
		data: {
			section: sectionid,
			citation: citationid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtCitation' + sectionid + '-' + citationid).slideUp();
		
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
		
		$('#nbtCiteNoFeedback' + section + '-' + citid).html(html);
		
		$('#nbtCiteNoFeedback' + section + '-' + citid).fadeIn(50, function () {
			
			setTimeout ( function () {
				
				$('#nbtCiteNoFeedback' + section + '-' + citid).fadeOut(1000);
				
			}, 2000);
			
		});
		
	});
	
}

function nbtUpdateConditionalDisplays () {
	
	$('a.nbtTextOptionSelect').each( function () {
		
		if ( $(this).hasClass('nbtTextOptionChosen') ) {
			
			if ( $(this).attr('conditionalid') != '') {
				
				$('.' + $(this).attr('conditionalid')).slideDown(50);
				
			}
			
		} else {
			
			if ( $(this).attr('conditionalid') != '') {
			
				$('.' + $(this).attr('conditionalid')).slideUp(50);
			
			}
			
		}
		
	});
	
}

function nbtFindReferenceToAssign () {
	
	if ( $('#nbtAssignUser').val() == 'NULL' ) {
		
		$('#nbtFoundReferencesForAssigment').addClass('nbtFeedback').addClass('nbtFeedbackBad').addClass('nbtFinePrint').removeClass('nbtCitationSuggestions').html('Choose a user to be assigned');
		
	} else {
		
		if ( $('#nbtAssignForm').val() == 'NULL' ) {
			
			$('#nbtFoundReferencesForAssigment').addClass('nbtFeedback').addClass('nbtFeedbackBad').addClass('nbtFinePrint').removeClass('nbtCitationSuggestions').html('Choose a form');
			
		} else {
			
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
						url: numbaturl + 'assignments/referencefinder.php',
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
		
	}
	
}

function nbtAddAssignment ( uid, fid, rsid, rid ) {
	
	$.ajax ({
		url: numbaturl + 'assignments/addassignment.php',
		type: 'post',
		data: {
			userid: uid,
			formid: fid,
			refset: rsid,
			ref: rid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		if ( html == 'Assignment added' ) {
			
			$('#nbtAddAssignmentFeedback' + rid).addClass('nbtFeedback').addClass('nbtFeedbackGood').addClass('nbtFinePrint');
			
		} else {
			
			$('#nbtAddAssignmentFeedback' + rid).addClass('nbtFeedback').addClass('nbtFeedbackBad').addClass('nbtFinePrint');
			
		}
		
		$('#nbtAddAssignmentFeedback' + rid).html(html);
		
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

function nbtAddNewSubExtraction ( fid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/addsubextraction.php',
		type: 'post',
		data: {
			formid: fid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtFormElements').html(html);
		
	});
	
}

function nbtChangeSubExtractionSuffix ( eid ) {
	
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

function nbtDeleteSubElement ( seid ) {
	
	$.ajax ({
		url: numbaturl + 'forms/deletesubelement.php',
		type: 'post',
		data: {
			element: seid
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

function nbtMoveSubElement ( eid, seid, dir ) {
	
	$.ajax ({
		url: numbaturl + 'forms/movesubelement.php',
		type: 'post',
		data: {
			element: eid,
			subelement: seid,
			direction: dir
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtSubExtractionElements' + eid).html(html);
		
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

function nbtChangeSubMultiSelectColumnPrefix ( seid ) {
	
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

function nbtAddAdvancedAssignment () {
	
	$.ajax ({
		url: numbaturl + 'assignments/addadvassignment.php',
		type: 'post',
		data: {
			userid: $('#nbtAssignUser').val(),
			formid: $('#nbtAssignForm').val(),
			refset: $('#nbtAssignRefSet').val(),
			query: $('#nbtAdvancedAssignInput').val()
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtAdvancedAssignmentFeedback').html(html);
		
		$('#nbtAdvancedAssignmentFeedback').fadeIn(500, function () {
			
			$('#nbtAdvancedAssignmentFeedback').fadeOut(1500);
			
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

function nbtUpdateManualReference ( rsid, refid, columnid, textfieldid, feedbackid ) {
	
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
		
		$('#' + feedbackid).html(html);
		
		$('#' + feedbackid).fadeIn(50, function () {
			
			setTimeout ( function () {
				
				$('#' + feedbackid).fadeOut(1000);
				
			}, 2000);
			
		});
		
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

function nbtCopyTableDataRow ( eid, rsid, refid, rid ) {
	
	$.ajax ({
		url: numbaturl + 'master/copytabledatarow.php',
		type: 'post',
		data: {
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

function nbtCopySubExtraction ( eid, rsid, refid, oid ) {
	
	$.ajax ({
		url: numbaturl + 'master/copysubextraction.php',
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
		url: numbaturl + 'master/deletemastertabledatarow.php',
		type: 'post',
		data: {
			elementid: eid,
			row: rid
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtMasterTD' + eid + 'RowID' + rid).slideUp();
		
	});
	
}

function nbtDeleteMasterSubExtraction ( eid, oid ) {
	
	$.ajax ({
		url: numbaturl + 'master/deletemastersubextraction.php',
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
		url: numbaturl + 'master/copytomaster.php',
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
		
	});
	
}

function nbtCopyMultiSelectToMaster ( fid, rsid, refid, exid, eid, uid ) {
	
	$.ajax ({
		url: numbaturl + 'master/copymstomaster.php',
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
		
	});
	
}

function nbtCopyCitationToMaster ( eid, cid, rsid, ref ) {
	
	$.ajax ({
		url: numbaturl + 'master/copycitetomaster.php',
		type: 'post',
		data: {
			element: eid,
			citationid: cid,
			refset: rsid,
			refid: ref
		},
		dataType: 'html'
	}).done ( function (html) {
		
		$('#nbtMasterCitations' + eid).html(html);
		
	});
	
}

function nbtCopySEPreviousSingleSelect ( eid, previousid, currentid, dbname ) {
	
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