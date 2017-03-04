<?php 
session_start();
header("Content-type: application/javascript"); ?>
(function() {
	var _tbody = $(".pluginList"),
		_columns = $("thead td").length,
		_pluginid = _tbody.parent().attr('data-pluginid'),
		_strucid = _tbody.parent().attr('data-structureid'),
		_events = {};

	function _init() {

		_events.loadList($('.pluginscount').val());

		$(".pluginList").sortable({ 
			handle: '.handle', 
			opacity: 0.6, 
			cursor: 'move',
			placeholder: "ui-state-highlight", 
			update: function() {
				var order = $(this).sortable("serialize") + '&action=updateRecordsListings'; 
				$.post("<?=$_SESSION['_EQROOT_'];?>plugins/sortplugin", order, function(theResponse){

				}); 															 
		  }								  
		});

		$('.pluginscount').on('change',function(){
			var perpage = $(this).val();
			_events.loadList(perpage);
		});

		$('.pluginspaging li').on('click',function(){
			var perpage = $('.pluginscount').val(),
				page = $(this).attr("data-next");

			if($(this).hasClass('prev')) {
				_events.loadList(perpage,page);
			}
			if($(this).hasClass('next')) {
				_events.loadList(perpage,page);
			}
		});
	}

	_events = {

		loadList: function (perpage, page, recordid) {

			perpage = perpage || 5;
			page = page || 1;
			recordid = recordid || '';

			$.ajax({
		        url: '<?=$_SESSION['_EQROOT_'];?>plugins/pluginListTableBody',
		    	type: "POST",
		        data: {
		        	pluginid: _pluginid,
		        	strucid: _strucid,
		        	perpage: perpage,
		        	page: page,
		        	recordid: recordid
		        },
		        beforeSend: function() {
		        	_tbody.html('<tr><td colspan="' + _columns + '"  align="center"><i class="fa fa-spinner fa-spin fa-3x"></i></td></tr>');
		        },
		        success: function(d) {
		        	_tbody.html(d);

		        	console.log(page);
					var prev = parseInt(page) - 1 <= 0 ? 1 : parseInt(page)-1,
						next = parseInt(page) + 1 >= parseInt($("tbody tr").attr("data-numpages")) ? parseInt($("tbody tr").attr("data-numpages")) : parseInt(page)+1;


					$('.prev').attr("data-next",prev);
					$('.next').attr("data-next",next);
		        },
		        error: function(xhr, status, errorThrown) {
		        	console.log('error');
		        }
		    });
		}
	}

	return {
		init: _init()
	}
})();