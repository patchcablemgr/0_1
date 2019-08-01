/**
 * Admin
 * This page allows administrators to manage users
 */
 
 function toggleSMTPFields(){
	var mailMethod = $('input[name="mailMethod"]:checked').val();
	if(mailMethod == 'smtp') {
		$('#fieldsSMTP').show();
	} else {
		$('#fieldsSMTP').hide();
	}
 }
 
 function toggleSMTPAuthFields() {
	if($('#smtpAuthentication').is(':checked')) {
		$('#fieldsSMTPAuth').show();
	} else {
		$('#fieldsSMTPAuth').hide();
	}
 }
 
$( document ).ready(function() {
	
	//modify buttons style
    $.fn.editableform.buttons = 
    '<button type="submit" class="btn btn-primary editable-submit waves-effect waves-light"><i class="zmdi zmdi-check"></i></button>' +
    '<button type="button" class="btn editable-cancel btn-secondary waves-effect"><i class="zmdi zmdi-close"></i></button>';

	toggleSMTPFields();
	toggleSMTPAuthFields();
	
	$('.mailMethod').on('change', function(){
		toggleSMTPFields();
	});
	
	$('#smtpAuthentication').on('change', function(){
		toggleSMTPAuthFields();
	});
	
	$('#smtpSubmit').on('click', function(event){
		event.preventDefault();
		
		var mailMethod = $('input[name="mailMethod"]:checked').val();
		var data = {};
		
		data['mailMethod'] = mailMethod;
		data['fromEmail'] = $('#smtpFromEmail').val();
		data['fromName'] = $('#smtpFromName').val();
		
		if(mailMethod == 'smtp') {
			data['smtpServer'] = $('#smtpServer').val();
			data['smtpPort'] = $('#smtpPort').val();
			if($('#smtpAuthentication').is(':checked')) {
				data['smtpAuth'] = 'yes';
				data['smtpUsername'] = $('#smtpUsername').val();
				data['smtpPassword'] = $('#smtpPassword').val();
			} else {
				data['smtpAuth'] = 'no';
			}
		}
		
		data = JSON.stringify(data);
		
		// Process mail settings
		$.post("backend/process_mail_settings.php", {data:data}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("/");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				alert(responseJSON.success);
			}
		});
	});
	
	$('#invitationSubmit').on('click', function(event){
		event.preventDefault();
		var email = $('#invitationEmail').val();

		//Collect object data
		var data = {
			email: email,
			action: 'create'
			};
		data = JSON.stringify(data);
		
		//Retrieve object details
		$.post("backend/process_invitation.php", {data:data}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("/");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				alert(responseJSON.success);
			}
		});
	});

	$('#inline-orgName').editable({
		showbuttons: false,
		//type: 'text',
		//pk: 1,
		//name: 'orgName',
		//title: 'Enter username',
		mode: 'inline',
		params: function(params){
			var data = {
				'value':params.value
			};
			params.data = JSON.stringify(data);
			return params;
		},
		url: 'backend/process_organization-name.php',
		success: function(response){
			var alertMsg = '';
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("https://otterm8.com/app/login.php");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				$('#orgName').html(responseJSON.success);
			}
		}
	});

	$('.buttonRemoveUser').on('click', function(){
		var userID = $(this).attr('data-userID');
		var userType = $(this).attr('data-userType');
		//Collect object data
		var data = {
			userID: userID,
			userType: userType,
			action: 'delete'
		};
		data = JSON.stringify(data);
		
		//Retrieve object details
		$.post('backend/process_admin_edit-user.php', {data:data}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace('/app/login.php');
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				$(this).closest('tr').remove();
			}
		});
	});

	var roleData = [
		{'value':3,'text':'Administrator'},
		{'value':4,'text':'Operator'},
		{'value':5,'text':'User'}
	];

	$('.editableUserRole').editable({
		showbuttons: false,
		mode: 'inline',
		source: roleData,
		params: function(params){
			var data = {
				action: 'role',
				userID: params.pk,
				groupID: params.value,
				userType: $(this).attr('data-userType'),
				action: 'role'
			};
			params.data = JSON.stringify(data);
			return params;
		},
		url: 'backend/process_admin_edit-user.php'
	});
});
