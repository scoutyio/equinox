<?php 
session_start();
header("Content-type: application/javascript"); ?>

$(function() {
	$(".sorting ul").sortable({ 
		handle: '.handle', 
		opacity: 0.6, 
		cursor: 'move', 
		placeholder: "ui-state-highlight",
		update: function() {
			var order = $(this).sortable("serialize") + '&action=updateRecordsListings'; 
			console.log(order);
			$.post("<?=$_SESSION['_EQROOT_'];?>pages/sortpages", order, function(theResponse){
			
			}); 															 
		}								  
	});
});