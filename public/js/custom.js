$(document).ready(function () {
	
	//====== Component Tabs ======//
	
	$(".tabclick").click(function() {
	  $('<form action="'+$(this).data("loc")+'" method="POST">' + 
		'<input type="hidden" name="pluginTabulator" value="1">' +
		'<input type="hidden" name="pluginStructure" value="'+$(this).data("value")+'">' +
		'</form>').submit();
	});

	//===== Multiple select with dropdown =====//

	$("#dialog3").dialog({
		autoOpen: false,
		width: '400px',
		modal: true,
		buttons:[{
					text: "Add",
					"class": 'modalSaveClass',
					click: function() {
						save();
					}
				},{
					text: "Close",
					"class": 'modalCancelClass',
					click: function() {
						$( this ).dialog( "close" );
						$( this ).html('');
					}
				}],
		open: function() {
			
		}
	});
	
	$("#mass-email-msg").dialog({
		autoOpen: false,
		width: 'auto',
		modal: true,
		buttons:[{
					text: "Close",
					"class": 'modalCancelClass',
					click: function() {
						$( this ).dialog( "close" );
						$( this ).html('');
					}
				}],
		open: function() {
			
		}
	});

	//===== Dual select boxes =====//
	
	$.configureBoxes();
		
	//===== Time picker =====//
	
	$('.timepicker').timeEntry({
		show24Hours: true, // 24 hours format
		showSeconds: true, // Show seconds?
		spinnerImage: '/clinic/equinox/public/images/ui/spinnerUpDown.png', // Arrows image
		spinnerSize: [17, 26, 0], // Image size
		spinnerIncDecOnly: true // Only up and down arrows
	});
	//===== Alert windows =====//

	$(".bAlert").click( function() {
		jAlert('This is a custom alert box. Title and this text can be easily editted', 'Alert Dialog Sample');
	});
	
	$(".bConfirm").click( function() {
		jConfirm('Can you confirm this?', 'Confirmation Dialog', function(r) {
			jAlert('Confirmed: ' + r, 'Confirmation Results');
		});
	});
	
	$(".bPromt").click( function() {
		jPrompt('Type something:', 'Prefilled value', 'Prompt Dialog', function(r) {
			if( r ) alert('You entered ' + r);
		});
	});
	
	$(".bHtml").click( function() {
		jAlert('You can use HTML, such as <strong>bold</strong>, <em>italics</em>, and <u>underline</u>!');
	});


	//===== Accordion =====//		
	
	$('div.menu_body:eq(0)').show();
	$('.acc .head:eq(0)').show().css({
		color:"#2B6893"
	});
	
	$(".acc .head").click(function() {	
		$(this).css({color:"#2B6893"}).next("div.menu_body").slideToggle(300).siblings("div.menu_body").slideUp("slow");
		$(this).siblings().css({color:"#404040"});
	});
	
	//===== ToTop =====//

	$().UItoTop({ easingType: 'easeOutQuart' });	
	
	
	//===== Spinner options =====//
	
	var itemList = [
		{url: "http://ejohn.org", title: "John Resig"},
		{url: "http://bassistance.de/", title: "J&ouml;rn Zaefferer"},
		{url: "http://snook.ca/jonathan/", title: "Jonathan Snook"},
		{url: "http://rdworth.org/", title: "Richard Worth"},
		{url: "http://www.paulbakaus.com/", title: "Paul Bakaus"},
		{url: "http://www.yehudakatz.com/", title: "Yehuda Katz"},
		{url: "http://www.azarask.in/", title: "Aza Raskin"},
		{url: "http://www.karlswedberg.com/", title: "Karl Swedberg"},
		{url: "http://scottjehl.com/", title: "Scott Jehl"},
		{url: "http://jdsharp.us/", title: "Jonathan Sharp"},
		{url: "http://www.kevinhoyt.org/", title: "Kevin Hoyt"},
		{url: "http://www.codylindley.com/", title: "Cody Lindley"},
		{url: "http://malsup.com/jquery/", title: "Mike Alsup"}
	];

	var opts = {
		's1': {decimals:2},
		's2': {stepping: 0.25},
		's3': {currency: '$'},
		's4': {},
		's5': {
			//
			// Two methods of adding external items to the spinner
			//
			// method 1: on initalisation call the add method directly and format html manually
			init: function(e, ui) {
				for (var i=0; i<itemList.length; i++) {
					ui.add('<a href="'+ itemList[i].url +'" target="_blank">'+ itemList[i].title +'</a>');
				}
			},

			// method 2: use the format and items options in combination
			format: '<a href="%(url)" target="_blank">%(title)</a>',
			items: itemList
		}
	};

	for (var n in opts)
		$( "#" + n ).spinner(opts[n]);

	$( "button" ).click(function(e){
		var ns = $(this).attr('id').match(/(s\d)\-(\w+)$/);
		if (ns != null)
			$( '#'+ns[1] ).spinner( (ns[2] == 'create') ? opts[ns[1]] : ns[2]);
	});


	//===== Contacts list =====//
	
	$('#myList').listnav({ 
		initLetter: 'all', 
		includeAll: true, 
		includeOther: true, 
		flagDisabled: true, 
		noMatchText: '', 
		prefixes: ['the','a']
	});
	//===== Dynamic data table =====//

	oTable = $('#example').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"sDom": '<""f>t<"F"lp>'
	});
	
	
	//===== Form elements styling =====//
	
	$("input:checkbox, input:radio").uniform();
	
	
	//===== Form validation engine =====//
	

	//===== Datepickers =====//

	$( ".datepicker" ).datepicker({ 
		defaultDate: +7,
		autoSize: true,
		appendText: '(yyyy-mm-dd)',
		dateFormat: 'yy-mm-dd'
	});	

	$( ".datepickerInline" ).datepicker({ 
		defaultDate: +7,
		autoSize: true,
		appendText: '(dd-mm-yyyy)',
		dateFormat: 'dd-mm-yy',
		numberOfMonths: 1
	});		
		
	//===== Tooltip =====//
		
	$('.leftDir').tipsy({fade: true, gravity: 'e'});
	$('.rightDir').tipsy({fade: true, gravity: 'w'});
	$('.topDir').tipsy({fade: true, gravity: 's'});
	$('.botDir').tipsy({fade: true, gravity: 'n'});

		
	//===== Information boxes =====//
		
	$( ".hideit" ).click(function() {
		$(this).fadeTo(200, 0.00, function(){ //fade
			$(this).slideUp(300, function() { //slide up
				$(this).remove(); //then remove from the DOM
			});
		});
	});	
	

	//=====Resizable table columns =====//
	
	var onSampleResized = function(e){
		var columns = $(e.currentTarget).find("th");
		var msg = "columns widths: ";
		columns.each(function(){ msg += $(this).width() + "px; "; })
	};	

	$( ".resize" ).colResizable({
		liveDrag:true, 
		gripInnerHtml:"<div class='grip'></div>", 
		draggingClass:"dragging", 
		onResize:onSampleResized});


	//===== Image gallery control buttons =====//

	 $( ".pics ul li" ).hover(
		  function() { $(this).children(".actions").show("fade", 200); },
		  function() { $(this).children(".actions").hide("fade", 200); }
	 );
	

	//===== Color picker =====//

	$('.colorpick').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	}).bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});	
	
	
	//===== jQuery UI sliders =====//	
	
	$( ".uiSlider" ).slider();
	
	$( ".uiSliderInc" ).slider({
		value:100,
		min: 0,
		max: 500,
		step: 50,
		slide: function( event, ui ) {
			$( "#amount" ).val( "$" + ui.value );
		}
	});

	$( "#amount" ).val( "$" + $( ".uiSliderInc" ).slider( "value" ) );
		
	$( ".uiRangeSlider" ).slider({
		range: true,
		min: 0,
		max: 500,
		values: [ 75, 300 ],
		slide: function( event, ui ) {
			$( "#rangeAmount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
		}
	});
	$( "#rangeAmount" ).val( "$" + $( ".uiRangeSlider" ).slider( "values", 0 ) +" - $" + $( ".uiRangeSlider" ).slider( "values", 1 ));
			
	$( ".uiMinRange" ).slider({
		range: "min",
		value: 37,
		min: 1,
		max: 700,
		slide: function( event, ui ) {
			$( "#minRangeAmount" ).val( "$" + ui.value );
		}
	});
	$( "#minRangeAmount" ).val( "$" + $( ".uiMinRange" ).slider( "value" ) );
	
	$( ".uiMaxRange" ).slider({
		range: "max",
		min: 1,
		max: 100,
		value: 20,
		slide: function( event, ui ) {
			$( "#maxRangeAmount" ).val( ui.value );
		}
	});
	$( "#maxRangeAmount" ).val( $( ".uiMaxRange" ).slider( "value" ) );	
	
	
	
	$( "#eq > span" ).each(function() {
		// read initial values from markup and remove that
		var value = parseInt( $( this ).text(), 10 );
		$( this ).empty().slider({
			value: value,
			range: "min",
			animate: true,
			orientation: "vertical"
		});
	});
	
	//===== Autofocus =====//	
	
	$( ".autoF" ).focus();

	//===== User nav dropdown =====//		

	$( ".dd" ).click(function () {
		$('ul.menu_body').fadeIn(200);
	});
	
	$(document).bind('click', function(e) {
		var $clicked = $(e.target);
		if (! $clicked.parents().hasClass("dd"))
			$("ul.menu_body").fadeOut(200);
	});
	
	$( ".exp" ).click(function(){
		$sub = $(this).next(".sub");
		if($sub.hasClass("active")){
			$sub.slideUp("fast",function(){
				$sub.removeClass("active");
			});
		}else{
			$sub.slideDown("fast").addClass("active");
		}
	});
	
	$('.acts').click(function () {
		$('ul.actsBody').slideToggle(100);
	});
	
});

function showhide(id){
	if(!$('#'+id).is(':visible')) {
		$('#'+id).show();
	} else {
		$('#'+id).hide();
	}
}

function redirect(loc, conf){
	if(conf) {
		var a = confirm(conf);
		if (a) {
			window.location = loc;
		}
	} else {
		window.location = loc;
	}
}

function url_prefix(uri, key, value) {
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  var separator = uri.indexOf('?') !== -1 ? "&" : "?";
  if (uri.match(re)) {
    return uri.replace(re, '$1' + key + "=" + value + '$2');
  }
  else {
    return uri + separator + key + "=" + value;
  }
}

function goBack() {
    window.history.go(-2);
}