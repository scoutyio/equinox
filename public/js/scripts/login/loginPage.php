<?php 
session_start();
header("Content-type: application/javascript"); ?>
$(function() {

	var a = function(){

		$("#valid").validationEngine();
		$('.mainForm').ajaxForm({ 
			target:     '#divToUpdate',
	    	dataType:   'json',
			beforeSubmit: function(arr, $form, options) {
				$('.nNote').addClass('displaynone').html('');
				$('.submitForm').addClass('is-waiting');
			},
			success:    function(data) {
				if(data.message=="success") {
					if(data.visitedpage) {
						var red = data.visitedpage;
					} else {
						var red = "<?=$_SESSION['_EQROOT_'];?>";
					}
					setTimeout(function() {
						$('.rememberMe').html('');
						redirect("<?=$_SESSION['_EQROOT_'];?>",'');
					}, 2000);
				} else {
					setTimeout(function() {
						$('.submitForm').removeClass('is-waiting');
						$('.nNote').removeClass('displaynone').html(data.message);
					}, 2000);
				}
			}
		});  
	};

	$.when(
		a()
	).then(function(){
		
	})


});