/**
 * Order
 * This page takes in order items, shipping address, and payment info
 */

$( document ).ready(function() {
	$('.viewButton').on('click', function(){
		var invoiceID = $(this).attr('data-invoiceID');
		$('#invoiceContainer').load('includes/content-invoices.php', {invoiceID:invoiceID});
	});
});
