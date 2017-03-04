<?php 
session_start();
header("Content-type: application/javascript"); ?>
$(function() {
	
	$("#valid").validationEngine();

	$("#catid").change(function(){
		
		$.ajax({
			type: 'POST',
			url: "<?=$_SESSION['_EQROOT_'];?>pages/getparent",
			beforeSend : function() {
				$.getJSON('<?=$_SESSION['_EQROOT_'];?>login/check_status', function(check) {
			        if(check.status == 'loggedout'){
			            window.location = '<?=$_SESSION['_EQROOT_'];?>';
			            return false;
			        }
				});
			},
			data: { 
				'id': $(this).val(),
			},
			success: function(msg){
				$("#parentid").html(msg);
			}
		});
	});
});