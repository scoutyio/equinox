<?php 
session_start();
header("Content-type: application/javascript"); ?>

$('#searchCont').bind('keypress', function(e) {
	if(e.keyCode==13){
		window.location=url_prefix(location.href,"search",$("#searchCont").val());
	}
});