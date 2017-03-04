
(function() {

	var _events = {},
		_root = $('body').data('siteroot'),
		_hasTiny = false,
		_pluginid = $('input[name="pluginid"]').val(),
		_id = $('input[name="id"]').val();

	function _init() {

		__tinymceinit();

		$(".pluginList").each(function() {

			var _tbody    = $(this),
				_table	  = $(this).parent(),
				_columns  = _table.find("thead td").length,
				_pluginid = _table.attr('data-pluginid'),
				_strucid  = _table.attr('data-structureid'),
				_recordid = _table.attr('data-recordid'),
				_plugcount = _table.parent().siblings(".pluginscount").val();

				console.log(_strucid + " columns: " + _columns);

				_events.loadList(_pluginid,_strucid,_tbody,_columns,$('.pluginscount').val());
		});

		$(".pluginList").sortable({
			handle: '.handle',
			opacity: 0.6,
			cursor: 'move',
			placeholder: "ui-state-highlight",
			update: function() {
				var order = $(this).sortable("serialize") + '&action=updateRecordsListings';
				console.log(order);
				$.post( _root + "plugins/sortplugin", order, function(theResponse){

				});
		 	}
		});

		$('.pluginscount').on('change',function(){
			var perpage = $(this).val();
			_events.loadList(perpage);
		});

		$('.pluginspaging li').on('click',function(){
			var perpage = $('.pluginscount').val(),
				page = $(this).attr("data-next"),
				_tbody    = $(this).parent().parent().siblings(".widget.first").find("tbody"),
				_table	  = $(this).parent().parent().siblings(".widget.first").find("table")
				_columns  = _table.find("thead td").length,
				_pluginid = _table.attr('data-pluginid'),
				_strucid  = _table.attr('data-structureid'),
				_recordid = _table.attr('data-recordid'),
				_plugcount = _table.parent().siblings(".pluginscount").val();

			if($(this).hasClass('prev')) {
				_events.loadList(_pluginid,_strucid,_tbody,_columns,perpage,page);
			}
			if($(this).hasClass('next')) {
				_events.loadList(_pluginid,_strucid,_tbody,_columns,perpage,page);
			}
		});

		$('.recordImg > a,.recordFile > .deleteRecordFile').click(function(){
			$filename   = $(this).attr('data-filename');
			$recordid   = $(this).attr('data-id');
			$recordname = $(this).attr('data-recordname');
			$.getJSON(_root + 'login/check_status', function(check) {
		        if(check.status == 'loggedout'){
		            window.location = _root;
		            return false;
		        } else {
		        	a = confirm('Are you sure you want to delete : ' + $filename + '?');
					if(a) {
						$("a[data-id='" + $recordid + "']").parent().remove();
						$(this).parent().remove();
						$.post( _root + "plugins/removerecordfile/" + $recordid,{
								recordname : $recordname,
								filename   : $filename
							},function(result){
		    					$("#divToUpdate").html(result);
		    				}
						);
					}
				}
			});
		});

		$("#valid").validationEngine();



		var showformSerialize = function(){
			tinymce.triggerSave(false,true);
		}

		$('.mainForm').ajaxForm({
			// target:     '#divToUpdate',
			dataType: 'json',
			beforeSerialize: function(){
				if(_hasTiny){
					tinymce.triggerSave(false,true);
				}
			},
			beforeSubmit: function(arr, $form, options) {

				$('.mainForm').css({'opacity':0.3}).find('input[type="submit"]').addClass("is-waiting");

				$.getJSON(_root + 'login/check_status', function(check) {
			        if(check.status == 'loggedout') {
			            window.location = _root;
			            return false;
			        } else {
						// $('.loader').html('<i class="fa fa-spinner fa-spin fa-1x"></i>');

					}
				});
			},
			success:    function(a) {
				$('.mainForm').css({'opacity': 1}).find('input[type="submit"]').removeClass('is-waiting');
				function scrollTo() {
		        return;
		    }
			  $('.loader').html('');
				if(a.hasOwnProperty("custom_url")) {
					$('.custom_url').val(a['custom_url']);
				}

			}
		});

		$(document).on('change','.plugin-photo',function(event){
	    	var val = $(this).val();
	    	if(val){

			    switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
			        case 'gif': case 'jpg': case 'png': case 'jpeg':
			            var getImagePath = URL.createObjectURL(event.target.files[0]),
			            	imgDiv = $(this).parent().find('.recordImg');

			            if(imgDiv.length){
			            	imgDiv.find('img').attr('src',getImagePath).css({'width':'300px'});
			            } else {
			            	$(this).parent().append('<div class="recordImg"><img src="'+getImagePath+'" width="300"></div>')
			            }
			            break;
			        default:
			            $(this).val('');
			            alert('Not an Image!!')
			            break;
			    }
	    	} else {
	    		$('.property-featured-image-inner').html('');
	    	}
	    });
	}

	_events = {

		loadList: function (pluginid, strucid, tbody, columns, perpage, page) {
		console.log("columns " + columns);

			perpage = perpage || 5;
			page = page || 1;

			$.ajax({
		        url: _root + 'plugins/pluginListTableBody',
		    	type: "POST",
		        data: {
		        	pluginid: pluginid,
		        	strucid: strucid,
		        	perpage: perpage,
		        	page: page,
		        	recordid: _id
		        },
		        beforeSend: function() {
		        	tbody.html('<tr><td colspan="' + columns + '"  align="center"><i class="fa fa-spinner fa-spin fa-3x"></i></td></tr>');
		        },
		        success: function(d) {
		        	tbody.html(d);
					var prev = parseInt(page) - 1 <= 0 ? 1 : parseInt(page)-1,
						next = parseInt(page) + 1 >= parseInt(tbody.attr("data-numpages")) ? parseInt(tbody.attr("data-numpages")) : parseInt(page)+1;


					$('.prev').attr("data-next",prev);
					$('.next').attr("data-next",next);
		        },
		        error: function(xhr, status, errorThrown) {
		        	console.log('error');
		        }
		    });
		}
	}

	function __tinymceinit(){
	    tinymce.init({
	        selector:'textarea.tinymce',
	        theme: "modern",
	        width: "100%",
	        height: 200,
	        resize: false,
	        relative_urls: false,
	        formats: {
	            bold : {inline : 'b' },
	            italic : {inline : 'i' },
	            underline : {inline : 'u'},
	            article : {inline : 'article'}
	        },
			setup: function(editor) {
				editor.on('init', function() {
					_hasTiny = true;
				  //all your after init logics here.
				});
			},
	        browser_spellcheck: true,
	        menubar : false,
	        valid_elements : "@[id|class|style|title|lang|xml::lang|onclick|ondblclick|"
	            + "onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|"
	            + "onkeydown|onkeyup],a[rel|ref|rev|charset|hreflang|tabindex|accesskey|type|"
	            + "name|href|target|title|class|onfocus|onblur],strong/b,em,i,strike,u,"
	            + "#p,-ol[type|compact],-ul[type|compact],-li,br,img[longdesc|usemap|"
	            + "src|border|alt=|title|hspace|vspace|width|height|align],-sub,-sup,"
	            + "-blockquote,-table[border=0|cellspacing|cellpadding|width|frame|rules|"
	            + "height|align|summary|bgcolor|background|bordercolor],-tr[rowspan|width|"
	            + "height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot,"
	            + "#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor"
	            + "|scope],#th[colspan|rowspan|width|height|align|valign|scope],caption,-div,"
	            + "-span,-code,-pre,address,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],-font[face"
	            + "|size|color],dd,dl,dt,cite,abbr,acronym,del[datetime|cite],ins[datetime|cite],"
	            + "object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width"
	            + "|height|src|*],script[src|type],map[name],area[shape|coords|href|alt|target],bdo,"
	            + "button,col[align|char|charoff|span|valign|width],colgroup[align|char|charoff|span|"
	            + "valign|width],dfn,fieldset,form[action|accept|accept-charset|enctype|method],"
	            + "input[accept|alt|checked|disabled|maxlength|name|readonly|size|src|type|value],"
	            + "kbd,label[for],legend,noscript,optgroup[label|disabled],option[disabled|label|selected|value],"
	            + "q[cite],samp,select[disabled|multiple|name|size],small,"
	            + "textarea[cols|rows|disabled|name|readonly],tt,var,big",
	        extended_valid_elements : "iframe[src|width|height|name|align]",
	        font_formats: "Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;AkrutiKndPadmini=Akpdmi-n",
	        plugins: [
	             "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
	             "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
	             "save table contextmenu directionality emoticons template textcolor responsivefilemanager code"
	       ],
	       paste_as_text: true,
	       toolbar: "insertfile| styleselect | bold italic forecolor backcolor | fontsizeselect fontselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | media | emoticons responsivefilemanager | code fullpage",
	       image_advtab: true ,
	       tools: "inserttable",
	       external_filemanager_path: _root + "resources/filemanager/",
	       filemanager_title:"File Manager"
	    });
	}
	return {
		init: _init()
	}
})();
