<?php 
session_start();
header("Content-type: application/javascript"); ?>
$(function() {
	
	$("#valid").validationEngine();

	$("#catid").change(function(){
		$.ajax({
			type: 'POST',
			url: "<?=$_SESSION['_EQROOT_'];?>pages/getparent",
			data: { 
				'id': $(this).val(),
				'currpage' : $("#id").val()
			},
			success: function(msg){
				$("#parentid").html(msg);
			}
		});
	});

	$('.mainForm').ajaxForm({
		target: '#pageurl',
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
		success: function(data) {
			setTimeout(function() {
				  $('.loader').html('');
				  $("#page_url").val($("#pageurl").html());
			}, 1000);	
		}
	}); 
		
	$('input[type=radio][name=contenttype]').change(function() {
		if (this.value == 'newcontent') {
			$("#newComponents").show();
			$("#existingComponents").hide();
		}
		else if (this.value == 'exitstingcontent') {
			$("#newComponents").hide();
			$("#existingComponents").show();
		}
	});

	$("#componentContent").sortable({ handle: '.handle', opacity: 0.6, cursor: 'move', update: function() {
		var order = $(this).sortable("serialize") + '&action=updateRecordsListings'; 
		$.post("<?=$_SESSION['_EQROOT_'];?>pages/sortcomp", order, function(theResponse){
			
		}); 															 
	  }								  
	});
});

function modalRun(cid,pageid){
	var arr = cid.split(',');
	if(arr[1]=="single"){
		
		$.ajax({
			type: 'POST',
			url: "<?=$_SESSION['_EQROOT_'];?>pages/add_component_to_page",
			beforeSend : function() {
				$.getJSON('<?=$_SESSION['_EQROOT_'];?>login/check_status', function(check) {
			        if(check.status == 'loggedout'){
			            window.location = '<?=$_SESSION['_EQROOT_'];?>';
			            return false;
			        }
				});
			},
			data: {
				'cid': arr[0],
				'pageid': pageid
			},success: function (data) {
				//$('#componentContent').html(data);
				$('#componentContent').load('<?=$_SESSION['_EQROOT_'];?>pages/show_pages_contents?pageid='+pageid);
				$('#existingComponents').load('<?=$_SESSION['_EQROOT_'];?>pages/existing_content_select?pageid='+pageid);
			}
		});
		
	}else{
		if(arr[0]!=0){
			$("#dialog3").dialog("open");
			$(".ui-dialog-title").html("Add Existing Component");
			$.ajax({
				type: 'POST',
				url: "<?=$_SESSION['_EQROOT_'];?>pages/add_component",
				beforeSend : function() {
					$.getJSON('<?=$_SESSION['_EQROOT_'];?>login/check_status', function(check) {
				        if(check.status == 'loggedout'){
				            window.location = '<?=$_SESSION['_EQROOT_'];?>';
				            return false;
				        }
					});
				},
				data: { 
					'cid': arr[0],
					'pageid': pageid
				},
				success: function(msg){
					$("#dialog3").html(msg);
				}
			});
		}
	}
}

function removepagecontent(pcid,pageid){
	$.getJSON('<?=$_SESSION['_EQROOT_'];?>login/check_status', function(check) {
        if(check.status == 'loggedout'){
            window.location = '<?=$_SESSION['_EQROOT_'];?>';
            return false;
        } else {
        	var a = confirm("Do you really want to do this?");
			if(a){
				$.ajax({
					type: 'POST',
					url: "<?=$_SESSION['_EQROOT_'];?>pages/deletecontent",
					data: { 
						'pcid': pcid,
					},
					success: function(msg){
						$('#componentContent').load('<?=$_SESSION['_EQROOT_'];?>pages/show_pages_contents?pageid='+pageid);
						$('#existingComponents').load('<?=$_SESSION['_EQROOT_'];?>pages/existing_content_select?pageid='+pageid);
					}
				});
			}
        }
	});
}