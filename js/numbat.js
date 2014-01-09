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