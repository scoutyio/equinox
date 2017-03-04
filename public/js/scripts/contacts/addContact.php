<?php 
session_start();
header("Content-type: application/javascript"); ?>
$(document).ready(function() { 
	
    $("#valid").validationEngine();
    
	$('.mainForm').ajaxForm({ 
		target:     '#divToUpdate',
    	dataType:   'json', 
		beforeSubmit: function(arr, $form, options) { 
			
		},
		success:    function(data) {
		}
	}); 
});