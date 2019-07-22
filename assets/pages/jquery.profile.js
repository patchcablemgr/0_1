/**
 * Admin
 * This page allows administrators to manage users
 */

$( document ).ready(function() {

	$('#buttonPasswordChangeSubmit').on('click', function(){
		var newPassword = $('#inputNewPassword').val();
		var newPasswordConfirm = $('#inputNewPasswordConfirm').val();
		
		$.post('backend/process_password-change.php', {new_password:newPassword,new_password_confirm:newPasswordConfirm}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("https://otterm8.com/app/login.php");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				displaySuccess('Password has been changed.');
			}
		});
	});
	
	$('#checkboxMFA').on('change', function(){
		if($(this).is(':checked')) {
			var mfaState = true;
		} else {
			var mfaState = false;
		}
		
		//Collect object data
		var data = {
			mfaState:mfaState
		};
		data = JSON.stringify(data);
		
		//Retrieve object details
		$.post('backend/process_profile-mfa.php', {data:data}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("https://otterm8.com/app/login.php");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				displaySuccess('2FA has been updated.');
				$('#QRCodeContainer').html(responseJSON.success.html);
			}
		});
	});

	$('.buttonAcceptInvitation').on('click', function(){
		var invitationTableRow = $(this).closest('tr');
		var invitationCode = $(invitationTableRow).attr('data-invitationcode');
		
		//Collect object data
		var data = {
			invitationCode: invitationCode,
			action: 'accept'
		};
		data = JSON.stringify(data);
		
		//Retrieve object details
		$.post('backend/process_accept-invitation.php', {data:data}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("https://otterm8.com/app/login.php");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				window.location.replace('/app/logout.php');
			}
		});
	});

	$('.buttonDeclineInvitation').on('click', function(){
		var invitationTableRow = $(this).closest('tr');
		var invitationCode = $(invitationTableRow).attr('data-invitationcode');
		
		//Collect object data
		var data = {
			invitationCode: invitationCode,
			action: 'decline'
		};
		data = JSON.stringify(data);
		
		//Retrieve object details
		$.post("backend/process_invitation.php", {data:data}, function(response){
			var alertMsg = '';
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("https://otterm8.com/app/login.php");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				$(invitationTableRow).remove();
			}
		});
	});

	$('#buttonRevertInvitation').on('click', function(){
		
		//Collect object data
		var data = {
			action: 'revert'
		};
		data = JSON.stringify(data);
		
		//Retrieve object details
		$.post("backend/process_invitation.php", {data:data}, function(response){
			var alertMsg = '';
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("https://otterm8.com/app/login.php");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				window.location.replace('/app/logout.php');
			}
		});
	});
});
