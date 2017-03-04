<?php 
session_start();
header("Content-type: application/javascript"); ?>
$(function() {
	$(".component tbody").sortable({ handle: '.handle', opacity: 0.6, cursor: 'move', update: function() {
			var order = $(this).sortable("serialize") + '&action=updateRecordsListings'; 
			$.post('<?=$_SESSION['_EQROOT_'];?>cms_plugins/sort_plugin', order, function(theResponse){
				$('#result').html(theResponse);
			}); 															 
		}								  
	});
});