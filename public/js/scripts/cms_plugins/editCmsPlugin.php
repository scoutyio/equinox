<?php
session_start();
header("Content-type: application/javascript"); ?> 

$(function() {
    
    $("#valid").validationEngine();

    $(".export_plugin").click(function(){
        $.ajax({
            type: 'POST',
            url: "<?=$_SESSION['_EQROOT_'];?>cms_plugins/export/" + $(this).attr("data-id"),
            success: function(msg) {
                $(".importDiv").slideUp(function() {
                    $(".exportDiv").slideDown(function() {
                        $("#export").val(msg);
                    });
                });
            }
        });
    });

    $(".import_plugin").click(function(){
        $(".exportDiv").slideUp(function(){
            $(".importDiv").slideDown();
        });
    });

    $(".importDiv .close, .exportDiv .close").click(function(){
        $(this).parent().slideUp();
    })

    $(".importButton").click(function(){
        $.ajax({
            type: 'POST',
            url: "<?=$_SESSION['_EQROOT_'];?>cms_plugins/import/" + $(this).attr("data-id"),
            data: {
                'importstring': $("#import").val(),
            },
            success: function(msg) {
                <!-- console.log(msg); -->
                $("#comStrucDiv").html(msg)
            }
        });
    });

    $("#comStrucDiv").sortable({
        handle: '.handle',
        opacity: 0.6,
        cursor: 'move',
        update: function() {
            var order = $(this).sortable("serialize") +
                '&action=updateRecordsListings';
            $.post(
                "<?=$_SESSION['_EQROOT_'];?>cms_plugins/sort_plugin_structure",
                order, function(theResponse) {});
        }
    });
    $(".holder").sortable({
        handle: '.handle',
        opacity: 0.6,
        cursor: 'move',
        update: function() {
            var order = $(this).sortable("serialize") +
                '&action=updateRecordsListings';
            $.post(
                "<?=$_SESSION['_EQROOT_'];?>cms_plugins/sort_record",
                order, function(theResponse) {});
        }
    });
    $("#dialog-message").dialog({
        autoOpen: false,
        modal: true,
        dialogClass: "fixed-dialog",
        position: "center",
        buttons: [{
            text: "Save",
            "class": 'modalSaveClass',
            click: function() {
                save();
            }
        }, {
            text: "Cancel",
            "class": "modalCancelClass",
            click: function() {
                $(this).dialog('close');
                $(this).html('');
            }
        }, {
            text: "Delete",
            "class": 'modalDeleteClass',
            click: function() {
                deletethis();
            }
        }],
        open: function() {

            $dialog = $(this);
            $(document).keyup(function (e) {
		        if (e.keyCode == 27) {
		        	$dialog.html('');
		        	$dialog.dialog('close');
		        }
		    });
			$('.ui-dialog-titlebar-close').click(function(){
			    $dialog.html('');
			});
        }
    });
    $("#dialog-message2").dialog({
        autoOpen: false,
        modal: true,
        dialogClass: "fixed-dialog",
        position: "center",
        buttons: [{
            text: "Add",
            "class": "modalSaveClass",
            click: function() {
                save();
            }
        }, {
            text: "Cancel",
            "class": 'modalCancelClass',
            click: function() {
                $(this).dialog('close');
                $(this).html('');
            }
        }],
        open: function() {
            $dialog = $(this);
            $(document).keyup(function (e) {
		        if (e.keyCode == 27) {
		        	$dialog.html('');
		        	$dialog.dialog('close');
		        }
		    });
			$('.ui-dialog-titlebar-close').click(function(){
			    $dialog.html('');
			});
        }
    });
    
});

function modalRun(type, pluginid, plugstruid, fid, rid) {
    
    $.getJSON('<?=$_SESSION['_EQROOT_'];?>login/check_status', function(check) {
        if(check.status == 'loggedout'){
            window.location = '<?=$_SESSION['_EQROOT_'];?>';
        } else {
            $("#dialog-message2,#dialog-message").css({'background-image':'url("<?=$_SESSION['_EQROOT_'];?>public/images/loaders/ajax-loader-large.gif")','background-size':'100%','background-repeat':'no-repeat'});
            $type = type;
            $(".ui-dialog-title").html($type);
            if ($type == "Add Component Structure") {
                $("#dialog-message2").dialog("open");
                $.ajax({
                    type: 'POST',
                    url: "<?=$_SESSION['_EQROOT_'];?>cms_plugins/add_plugin_structure",
                    data: {
                        'pluginid': pluginid
                    },
                    success: function(msg) {
                       $("#dialog-message2").css({'background-image':'none'}).html(msg);
                    }
                });
            }
            if ($type == "Edit Plugin Structure") {
                $("#dialog-message").dialog("open");
                $.ajax({
                    type: 'POST',
                    url: "<?=$_SESSION['_EQROOT_'];?>cms_plugins/edit_plugin_structure",
                    data: {
                        'pluginid': pluginid,
                        'plugstruid': plugstruid,
                    },
                    success: function(msg) {
                        $("#dialog-message").css({'background-image':'none'}).html(msg);
                    }
                });
            }
            if ($type == "Add Fieldset") {
                $("#dialog-message2").dialog("open");
                $("#dialog-message2").css({'background-image':'url("<?=$_SESSION['_EQROOT_'];?>public/images/loader_big.gif") no-repeat'});
                $.ajax({
                    type: 'POST',
                    url: "<?=$_SESSION['_EQROOT_'];?>cms_plugins/add_fieldset_structure",
                    data: {
                        'pluginid': pluginid,
                        'plugstruid': plugstruid,
                    },
                    success: function(msg) {
                        $("#dialog-message2").css({'background-image':'none'}).html(msg);
                    }
                });
            }
            if ($type == "Edit Field Structure") {
                $("#dialog-message").dialog("open");
                $.ajax({
                    type: 'POST',
                    url: "<?=$_SESSION['_EQROOT_'];?>cms_plugins/edit_fieldset_structure",
                    data: {
                        'fid': fid,
                        'pluginid': pluginid,
                        'plugstrcuid': plugstruid,
                    },
                    success: function(msg) {
                        $("#dialog-message").css({'background-image':'none'}).html(msg);
                    }
                });
            }
            if ($type == "Add Record Structure") {
                $("#dialog-message2").dialog("open");
                $.ajax({
                    type: 'POST',
                    url: "<?=$_SESSION['_EQROOT_'];?>cms_plugins/add_record_structure",
                    data: {
                        'fid': fid,
                        'pluginid': pluginid,
                        'plugstrcuid': plugstruid,
                    },
                    success: function(msg) {
                        $("#dialog-message2").css({'background-image':'none'}).html(msg);
                    }
                });
            }
            if ($type == "Edit Record Structure") {
                $("#dialog-message").dialog("open");
                $.ajax({
                    type: 'POST',
                    url: "<?=$_SESSION['_EQROOT_'];?>cms_plugins/edit_record_structure",
                    data: {
                        'rid': rid,
                        'fid': fid,
                        'pluginid': pluginid,
                        'plugstrcuid': plugstruid,
                    },
                    success: function(msg) {
                        $("#dialog-message").css({'background-image':'none'}).html(msg);
                    }
                });
            }
        }
    });

    
}