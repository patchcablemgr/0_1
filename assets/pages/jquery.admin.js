/**
 * Admin
 * This page allows administrators to manage users
 */
 
$( document ).ready(function() {
	
	//modify buttons style
    $.fn.editableform.buttons = 
    '<button type="submit" class="btn btn-primary editable-submit waves-effect waves-light"><i class="zmdi zmdi-check"></i></button>' +
    '<button type="button" class="btn editable-cancel btn-secondary waves-effect"><i class="zmdi zmdi-close"></i></button>';

	
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
				window.location.replace("https://otterm8.com/app/login.php");
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
