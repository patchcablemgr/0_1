/**
 * Admin
 * This page allows administrators to manage users
 */

$( document ).ready(function() {
	
	var updateHandler = StripeCheckout.configure({
		key: 'pk_live_QDUGHVuUzQwKXmMMswYRIRpT',
		image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
		locale: 'auto',
		token: function(token) {
			
			var data = {
				action:'update',
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
				}
			});
		}
	});
	
	$('#buttonUpdateCustomer').on('click', function(){
		
		// Open Checkout with further options:
		updateHandler.open({
			name: 'Otterm8.com',
			description: 'Update Payment',
			panelLabel: 'Update'
		});
	});

	// Close Checkout on page navigation:
	window.addEventListener('popstate', function() {
		handler.close();
	});

});
