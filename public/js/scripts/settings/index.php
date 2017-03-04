<?php 
session_start();
header("Content-type: application/javascript"); ?>
$(function() {
// wait for the DOM to be loaded 
	$(document).ready(function() { 
		
		$("#valid2").validationEngine();
		
		$('.mainForm1').ajaxForm({ 
			beforeSubmit: function(arr, $form, options) { 
				$('.loader1').html('<i class="fa fa-spinner fa-spin fa-1x"></i>');
			},
			success:    function(data) {
				setTimeout(function() {
					  $('.loader1').html('');
				}, 1000);
				
			}
		}); 
		
		$('.mainForm2').ajaxForm({ 
			target:     '#logoImage',
			beforeSubmit: function(arr, $form, options) { 
				$('.loader2').html('<i class="fa fa-spinner fa-spin fa-1x"></i>');
			},
			success:    function(data) {
				$logoImg = $("#logoImage").html();
				$("#logoImage").html('');
				setTimeout(function() {
					  $('.loader2').html('');
					  $("#logoImage").html($logoImg);
				}, 1000);
				
			}
		}); 
	}); 
});