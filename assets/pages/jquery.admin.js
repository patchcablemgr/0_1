/**
 * Admin
 * This page allows administrators to manage users
 */
 
$( document ).ready(function() {
	
	//modify buttons style
    $.fn.editableform.buttons = 
    '<button type="submit" class="btn btn-primary editable-submit waves-effect waves-light"><i class="zmdi zmdi-check"></i></button>' +
    '<button type="button" class="btn editable-cancel btn-secondary waves-effect"><i class="zmdi zmdi-close"></i></button>';
	
	var cardInfoHandler = StripeCheckout.configure({
		key: 'pk_live_QDUGHVuUzQwKXmMMswYRIRpT',
		image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
		locale: 'auto',
		token: function(token) {
			
			var data = {
				action:'cardInfo',
				token:token.id
			};
			
			data = JSON.stringify(data);
			
			$.post('backend/process_subscription.php', {data:data}, function(response){
				var responseJSON = JSON.parse(response);
				if (responseJSON.active == 'inactive'){
					window.location.replace('/app/login.php');
				} else if ($(responseJSON.error).size() > 0){
					displayError(responseJSON.error);
				} else {
					var last4 = responseJSON.success.last4;
					var expiration = responseJSON.success.expiration;
					$('#paymentInfoLast4').html('**** '+last4);
					$('#paymentInfoExpiration').html(expiration);
					$('#selectPlan').prop('disabled', false);
				}
			});
		}
	});
	
	$('#buttonCardInfo').on('click', function(){
		
		// Open Checkout with further options:
		cardInfoHandler.open({
			name: 'Otterm8.com',
			description: 'Update Payment',
			panelLabel: 'Update'
		});
	});

	// Close Checkout on page navigation:
	window.addEventListener('popstate', function() {
		cardInfoHandler.close();
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
	
	$('#selectPlan').on('change', function(){
		var plan = $('select option:selected').val();
		
		//Collect object data
		var data = {
			action:'update',
			plan: plan
		};
		data = JSON.stringify(data);
		
		//Retrieve object details
		$.post('backend/process_subscription.php', {data:data}, function(responseJSON){
			var response = JSON.parse(responseJSON);
			if (response.active == 'inactive'){
				window.location.replace('/app/login.php');
			} else if($(response.error).size() > 0){
				displayError(response.error);
			} else {
				$('#confirmCurrentPlan').html(response.success.currentPlan);
				$('#confirmNewPlan').html(response.success.newPlan);
				$('#confirmAmount').html(response.success.amount);
				$('#updatePlanModal').modal('show');
			}
		});
	});
	
	$('#buttonUpdateYes').on('click', function(){
		var plan = $('#selectPlan option:selected').val();
		
		//Collect object data
		var data = {
			action:'updateConfirmed',
			plan: plan
		};
		data = JSON.stringify(data);
		
		//Retrieve object details
		$.post('backend/process_subscription.php', {data:data}, function(responseJSON){
			var response = JSON.parse(responseJSON);
			if (response.active == 'inactive'){
				window.location.replace('/app/login.php');
			} else if($(response.error).size() > 0){
				displayError(response.error);
			} else {
				$('#accountBalance').html(response.success.accountBalance);
				$('#subscriptionRenewal').html(response.success.subscriptionRenewal);
			}
		});
		$('#updatePlanModal').modal('hide');
	});
	
	$('#buttonUpdateNo').on('click', function(){
		//Collect object data
		var data = {
			action:'updateCancelled'
		};
		data = JSON.stringify(data);
		
		//Retrieve object details
		$.post('backend/process_subscription.php', {data:data}, function(responseJSON){
			var response = JSON.parse(responseJSON);
			if (response.active == 'inactive'){
				window.location.replace('/app/login.php');
			} else if($(response.error).size() > 0){
				displayError(response.error);
			} else {
				var plan = response.success.plan;
				$('#selectPlan option[value="'+plan+'"]').prop('selected', true);
				$('#updatePlanModal').modal('hide');
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
