<?php 
session_start();
header("Content-type: application/javascript"); ?>
$(function() {
	
    $("#valid").validationEngine();
    
	$('.mainForm').ajaxForm({ 
		target:     '#divToUpdate',
		beforeSubmit: function(arr, $form, options) { 
			$.getJSON('<?=$_SESSION['_EQROOT_'];?>login/check_status', function(check) {
		        if(check.status == 'loggedout'){
		            window.location = '<?=$_SESSION['_EQROOT_'];?>';
		            return false;
		        } else {
					$('.loader').html('<i class="fa fa-spinner fa-spin fa-1x"></i>');
				}
			});	
		},
		success:    function(data) {
			setTimeout(function() {
				  $('.loader').html('');
			}, 1000);	
		}
	}); 
});