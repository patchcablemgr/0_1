/**
 * Admin
 * This page allows administrators to manage users
 */
 
$( document ).ready(function() {

	
	$('#buttonSubmit').on('click', function(event){
		var input = $('#textInput').val();
		//Collect object data
		var data = {
			input: input
			};
		data = JSON.stringify(data);
		
		//Retrieve object details
		$.post("backend/process_glbp.php", {data:data}, function(response){
			$('#results').html(response);
		});
	});

});
