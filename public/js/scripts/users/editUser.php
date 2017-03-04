<?php 
session_start();
header("Content-type: application/javascript"); ?>
$(function() {
	$("#valid").validationEngine();
	
	$('.mainForm').ajaxForm({ 
		target:     '#divToUpdate',
    	dataType:  'json', 
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
				  if(data.message=="fail"){
						alert("Sorry that email already exists in the system");
					}
			}, 1000);
		}
	}); 
});