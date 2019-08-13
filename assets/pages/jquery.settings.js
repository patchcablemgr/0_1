/**
 * Admin
 * This page allows administrators to manage users
 */

$( document ).ready(function() {

	$('#selectTimezone').on('change', function(){
		var timezone = $(this).val();
		
		var data = {
			property: 'timezone',
			value: timezone
		};
		data = JSON.stringify(data);
		
		$.post('backend/process_settings.php', {data:data}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("/");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				displaySuccess(responseJSON.success);
			}
		});
	});
	
	$('.radioScanMethod').on('change', function(){
		var value = $('.radioScanMethod:checked').val();
		
		var data = {
			property: 'scanMethod',
			value: value
		};
		data = JSON.stringify(data);
		
		$.post('backend/process_settings.php', {data:data}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("/");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else {
				displaySuccess(responseJSON.success);
			}
		});
	});
});
