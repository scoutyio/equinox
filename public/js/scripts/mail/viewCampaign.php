<?php 
session_start();
header("Content-type: application/javascript"); ?>

function tinymceinit(){
    tinymce.init({
        selector:'textarea.tinymce',
        theme: "modern",
        width: "100%",
        height: 200,
        resize: false,
        relative_urls: false,
        remove_script_host: false,
        formats: {
            bold : {inline : 'b' },  
            italic : {inline : 'i' },
            underline : {inline : 'u'},
            article : {inline : 'article'}
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
       theme_advanced_buttons3_add : "pastetext,pasteword,selectall",
       toolbar: "insertfile| styleselect | bold italic forecolor backcolor | fontsizeselect fontselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | media | emoticons responsivefilemanager | code fullpage", 
       image_advtab: true ,
       tools: "inserttable",
       external_filemanager_path: "<?=$_SESSION['_EQROOT_'];?>resources/filemanager/",
       filemanager_title:"File Manager"  
    }); 
}

tinymceinit();

$(document).ready(function() { 

    var cats = jQuery.parseJSON($("#cats").val()),
        pickedcats = $("#contacts").val().split(','),
        options;

    $.each(cats, function(i,a){
        options += '<option value="'+ a.id +'">' + a.name + '</option>';
    });

    $(".chzn-select").append(options);

    $.each(pickedcats,function(i,a){
        $("#contact option[value=" + a + "]").attr("selected","selected");
    })

    

    jQuery("#valid").validationEngine();

    function showformSerialize(){ tinymce.triggerSave(false,true); }
    
    $('.mainForm').ajaxForm({    
        dataType:   'json',
        beforeSerialize: showformSerialize, 
        beforeSubmit: function(arr, $form, options) { 

            $('.mainForm').find('input[type=submit]').addClass('is-waiting');
            $('.loader').html('<i class="fa fa-spinner fa-spin fa-1x"></i>');
        },
        success:    function(d) {

            if(d.error == "1"){
                $('.mainForm').find('input[type=submit]').removeClass('is-waiting');
                console.log(d.message);
            } else {
                if(d.attach == 1){
                    $('.attachmentfile').attr("href","<?=$_SESSION['_SITEROOT_'].'uploads/mail/';?>" + d.file).html(d.file);
                }
            }
            $('.mainForm').find('input[type=submit]').removeClass('is-waiting');
            $('.loader').html('');
        }
    });

    $("#sendCampaign").click(function(){
       
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: "<?=$_SESSION['_EQROOT_'];?>mail/send/"+$("[name=id]").val(),
            success: function(d) {    
                redirect('<?=$_SESSION['_EQROOT_'];?>'+d.redirect);
            }
        });
        return false;
    });
}); 

