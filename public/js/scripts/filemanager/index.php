<?php 
session_start();
header("Content-type: application/javascript"); ?>
$(function() {
	
	$('#fileManager').elfinder({
		url : '<?=$_SESSION['_EQROOT_'];?>resources/php/connector.php'
	});
});
	