<?php

class PluginFormBuilder {

    public function renderPluginForm($pluginId, $plugStrucId, $id) {

        global $con;

        $pluginFormBuilder = new PluginFormBuilder();
        $pluginListBuilder = new PluginListBuilder();

        $pstruc_q = 'SELECT * FROM cms_plugin_structure
    				 WHERE
    				 id = "' . $plugStrucId . '"';
        $pstruc_r = mysqli_query($con,$pstruc_q);
        $pstruc   = mysqli_fetch_array($pstruc_r,MYSQLI_ASSOC);

        $fieldset_q = 'SELECT * FROM cms_fieldsets
					   WHERE
					   com_struc_id = "' . $plugStrucId . '"';
        $fieldset_r = mysqli_query($con,$fieldset_q);

        $pluginForm = '';

        //Loop through each fieldset. Each form is each fieldset basically
        while ($fieldset = mysqli_fetch_array($fieldset_r,MYSQLI_ASSOC)) {

            $pluginForm .= "<div class=\"widget first\">\n";
            $pluginForm .= "<div class=\"head\"><h5 class=\"iInfo\">" . $fieldset['name'] . "</h5><div class=\"loader\"></div></div>\n";
            $pluginForm .= "<form method=\"post\" id=\"valid\" class=\"mainForm\" action=\"" . _EQROOT_ . "plugins/" . (!empty($id) ? 'save' : 'addNew') . "\" enctype=\"multipart/form-data\">\n";
            $pluginForm .= "<input type=\"hidden\" name=\"pluginid\" value=\"" . $pluginId . "\" />";
            if(!empty($_GET['pageid'])){
                $pluginForm .= "<input type=\"hidden\" name=\"pageid\" value=\"" . $_GET['pageid'] . "\" />\n";
            }
            $pluginForm .= "<input type=\"hidden\" name=\"plugstrucid\" value=\"" . $plugStrucId . "\" />\n";
            $pluginForm .= "<input type=\"hidden\" name=\"recordset\" value=\"" . $pstruc['db_name'] . "\" />\n";
            $pluginForm .= "<input type=\"hidden\" name=\"recordid\" value=\"" . (!empty($_GET['recordid']) ? $_GET['recordid'] : '0') . "\" />\n";
            $pluginForm .= "<input type=\"hidden\" name=\"fieldsetid\" value=\"" . $fieldset['id'] . "\" />\n";
            if(!empty($id)){
                $pluginForm .= "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />\n";
            }
            $pluginForm .= "<div id=\"formBody\">\n";
            if(!empty($id)){
                $pluginForm .= $pluginFormBuilder->formBody($pstruc['id'],$fieldset['id'],$id);
            }else{
                $pluginForm .= $pluginFormBuilder->formBody($pstruc['id'],$fieldset['id'],'');
            }
            $pluginForm .= "</div>\n";
            $pluginForm .= "<div class=\"fix\"></div>\n";
            $pluginForm .= "<div class=\"rowElem formaction noborder\">\n";
            if(!empty($id)){
                $pluginForm .= "<input type=\"button\" value=\"Delete\" class=\"redBtn floatright\" onclick=\"redirect('" . _EQROOT_ . "plugins/deleterecord/" . $id . "?pid=" . $pluginId . "&psid=" . $plugStrucId . "','Delete this record? Are you sure?')\">\n";
            }
            $pluginForm .= "<input type=\"button\" value=\"" . (!empty($id)?'Back':'Cancel') . "\" class=\"blueBtn floatright\" onclick=\"redirect('" . _EQROOT_ . (empty($_GET['pageid'])?'plugins/view/' . $pluginId : 'pages/edit/' . $_GET['pageid']) . "')\">\n";
            $pluginForm .= "<input type=\"submit\" value=\"" . (!empty($id)?'Save':'Add') . "\" id=\"addEdit\" class=\"greyishBtn floatright\">\n";
            $pluginForm .= "<div class=\"fix\"></div>\n";
            $pluginForm .= "</div>\n";
            $pluginForm .= "</div>\n";

            $childArray = $pluginListBuilder->buildPluginStrucArray($pluginId,$plugStrucId);

            $x   = 0;
            if(!empty($id)){
                foreach ($childArray as $c) {

                    $structureInfo = $pluginListBuilder->singleStrucArray($c['id']);

                    $recordArray = $pluginListBuilder->strucRecordArray($c['id']);

                    $sortorder = false;
                    //check if it is a sortorder listing
                    if(strpos($structureInfo['listorder'],'sortorder') !== false) {
                        $sortorder = true;
                    }

                    $pluginForm .= "<div class=\"widget first\"><ul class=\"tabs\">\n<li class=\"activeTab\"><a>" . $c['name'] . "</a></li>\n</ul>\n";

                    $className = '';
                    $pluginForm .= "<div style=\"" . $className . "\" id=\"" . $c['db_name'] . "-tab\" class=\"tab_content\">\n";
                    $pluginForm .= "<a href=\"" . _EQROOT_ . "plugins/add/" . $pluginId . "?psid=" . $c['id'] . "&recordid=" . $id .
                                    "\" class=\"addComponentStructure modal\"><i class=\"fa fa-plus-square fa-1x\"></i> New " . $c['recordname'] . "</a>\n";
                    $pluginForm .= "<div class=\"floatright searchPluginDiv\"><label class=\"floatleft\">Search:&nbsp;</label><form method=\"GET\" action=\"" . $_SERVER['REQUEST_URI'] . "\" class=\"floatleft\"><input type=\"text\" name=\"search" . $c['db_name'] . "\" value=\"" . (isset($_GET['searchChild' . $pstruc['db_name']])?$_GET['searchChild' . $pstruc['db_name']]:"") . "\" class=\"searchPlugin\" onkeydown=\"if (event.keyCode == 13) { this.form.submit(); return false; }\"></form></div>\n";
                    $pluginForm .= "<div class=\"widget first\">";
                    $pluginForm .= '<table cellpadding="0" cellspacing="0" width="100%" class="tableStatic" style="border-top:1px solid #ccc;" data-pluginid="' . $pluginId . '" data-structureid="' . $c['id'] . '" data-recordid="' . $_GET['id'] . '">';
                    $pluginForm .= '<thead>';
                    $pluginForm .= $pluginListBuilder->generateTableHead($plugStrucId,$c['id']) . "</div>\n";
                    $pluginForm .= '</thead>';
                    $pluginForm .= '<tbody class="pluginList"></tbody>';
                    $pluginForm .= '</table>';
                    $pluginForm .= '</div>';
                    if(!$sortorder) {
                        $pluginForm .= '<div class="pagination pluginspaging floatright" ><ul class="pages" data-next=""><li class="prev"><a>Prev</a></li><li class="next" data-next=""><a>Next</a></li></ul></div>';
                        $pluginForm .= '<label class="pluginsorderlabel floatleft">Items per page</label><select class="pluginscount floatleft"><option>5</option><option>10</option><option>25</option><option>50</option></select>';
                    }
                    $pluginForm .= '</div>';
                    $pluginForm .= "<div class=\"fix\"></div></div>\n";

                    $x++;
                }
            }

            $pluginForm .= "</div><div class=\"fix\"></div></div>";
        }
        return $pluginForm;
    }

    public function formBody($plugstrucid,$fieldsetId,$id=NULL) {

        global $con;

        if(isset($id)){
            $current_q = 'SELECT * FROM cms_content
                          WHERE
                          id = "' . $id . '"';
            $current_r = mysqli_query($con,$current_q);
            $current   = mysqli_fetch_array($current_r,MYSQLI_ASSOC);
        }
        $pstruc_q = 'SELECT * FROM cms_plugin_structure
                     WHERE
                     id = "' . $plugstrucid . '"';
        $pstruc_r = mysqli_query($con,$pstruc_q);
        $pstruc   = mysqli_fetch_array($pstruc_r,MYSQLI_ASSOC);

        $records_q = 'SELECT * FROM cms_records
              			  WHERE
              			  fieldsetid = "' . $fieldsetId . '"
              			  ORDER BY sortorder ASC';
        $records_r = mysqli_query($con,$records_q);

        $x         = 0;
        $znum      = 0;
        $formBody  = '';



        while ($records = mysqli_fetch_array($records_r,MYSQLI_ASSOC)) {

            $x++;
            $znum++;
            $eqApp = new Apps();
            $pluginListBuilder = new PluginListBuilder();
            $recordOptions = $eqApp->get_field_options($records['options'], $znum);
            $elementNameId = $pstruc['db_name'] . '_' . $records['db_name'];
            $formBody .= "<div class=\"rowElem" . ($x == 1 ? ' noborder' : '') . "\">\n";
            $formBody .= "<label>" . $records['name'] . ($recordOptions['record_required'] == 'true' ? ' <span style="color:red">*</span> ' : '') . ": " .  (!empty($records['helper'])?'<a onclick="$.jGrowl(\''.$records['helper'].'\');">&nbsp;&nbsp;<i class="fa fa-question-circle fa-1x"></i></a>':'') . "</label>\n";
            $formBody .= "<div class=\"formRight\">\n";

            switch ($records['type']) {

                case "text":
                    $formBody .= "<input value=\"".$current[$records['db_name']]."\" ".
                                    "type=\"text\" ".
                                    "name=\"" . $elementNameId ."\" ".
                                    ($recordOptions['record_disabled'] == 'true' ? 'readonly="readonly"' : '') .
                                    "id=\"" . $elementNameId ."\" ".
                                    "class=\"validate[" . ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') . ($recordOptions['record_email'] == 'true' ? ',custom[email]' : '') . "]\" ".
                                    "style=\"" . $recordOptions['record_styles'] . "\"/>\n";

                break;

                case "number":

                    $formBody .= "<input value=\"" . $current[$records['db_name']] . "\" ".
                                 "type=\"text\" name=\"" . $elementNameId . "\" " .
                                 "id=\"" . $elementNameId . "\" " .
                                 "style=\"" . $recordOptions['record_styles'] . "\" ".
                                 "class=\"validate[" . ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') . ",custom[number]]\" " .
                                 "style=\"" . $recordOptions['record_styles'] . "\"/>";

                break;

                case "email":

                    $formBody .= "<input value=\"" . $current[$records['db_name']] . "\" " .
                                 "type=\"text\" name=\"" . $elementNameId . "\" " .
                                 "id=\"" . $elementNameId . "\" " .
                                 ($recordOptions['record_disabled'] == 'true' ? 'readonly="readonly"' : '') .
                                 "style=\"" . $recordOptions['record_styles'] . "\" ".
                                 "class=\"validate[" . ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') . ",custom[email]]\" " .
                                 "style=\"" . $recordOptions['record_styles'] . "\"/>\n";

                break;

                case "html":

                    $formBody .= "<textarea ".
                                 "class=\"tinymce validate[" . ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') . "]\" ".
                                 "name=\"" . $elementNameId . "\" ".
                                 "id=\"" . $elementNameId . "\">" .
                                 stripslashes($current[$records['db_name']]) .
                                 "</textarea>\n";

                break;

                case "textarea":

                    $formBody .= "<textarea " .
                                 "name=\"" . $elementNameId . "\" " .
                                 "id=\"" . $elementNameId . "\" " .
                                 "rows=\"10\" " .
                                 ($recordOptions['record_disabled'] == 'true' ? 'readonly="readonly"' : '') .
                                 "class=\"validate[" . ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') . "]\" " .
                                 "style=\"" . $recordOptions['record_styles'] . "\">" .
                                 $current[$records['db_name']] .
                                 "</textarea>\n";

                break;

                case "select":
                case "multiselect":

                    $formBody .= "<select name=\"" . $elementNameId . ($records['type'] == 'multiselect' ? '[]' : '') . "\" " .
                                 ($recordOptions['record_disabled'] == 'true' ? 'disabled="disabled" ' : '') .
                                 "id=\"" . $elementNameId . ($records['type'] == 'multiselect' ? '[]' : '') . "\" " .
                                 "class=\"validate[" . ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') . "]\" ";

                                switch($records['type']){
                                    case 'multiselect':
                                            $formBody .= "multiple=\"multiple\" ";
                                        break;

                                    }
                    $formBody .= "style=\"" . $recordOptions['record_styles'] . "\">";
                    for ($d = 0; $d < count($recordOptions['record_select_vals']); $d++) {
                                        // echo $recordOptions['record_select_vals'][$d] . '\b/',$current[$records['db_name']];
                        $formBody .= "\n<option value=\"" . $recordOptions['record_select_vals'][$d] . "\"" .
                                        ($recordOptions['record_select_vals'][$d]==$current[$records['db_name']]?' selected':'') . ">" . $recordOptions['record_select_vals'][$d] . "</option>";
                    }
                    $formBody .= "\n</select>\n";

                break;

                case "yesno":

                    $formBody .= "<input type=\"radio\" id=\"" . $elementNameId . "1\" name=\"" . $elementNameId . "\" ".($current[$records['db_name']] == "yes" ?' checked=\"checked\"':'') ." value=\"yes\" ".
                                 ($recordOptions['record_disabled'] == 'true' ? 'readonly="readonly"' : '') ."/>\n" .
                                 "<label for=\"" . $elementNameId . "1\">Yes</label>\n";
                    $formBody .= "<input type=\"radio\" id=\"" . $elementNameId . "2\" name=\"" . $elementNameId . "\" ".($current[$records['db_name']] == "no" ?' checked=\"checked\"':'') ." value=\"no\" " .
                                 ($recordOptions['record_disabled'] == 'true' ? 'readonly="readonly"' : '') . "/>\n" .
                                 "<label for=\"" . $elementNameId . "2\">No</label>\n";
                break;

                case "checkboxes":

                    $checkArr = explode(',',$current[$records['db_name']]);

                    for ($d = 0; $d < count($recordOptions['record_select_vals']); $d++) {

                        $formBody .= "<input type=\"checkbox\" " .
                                     "class=\"validate[" . ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') . "]\" " .
                                     "id=\"" . $elementNameId . $d . "\" name=\"" . $elementNameId . "[]\" " .
                                     (preg_match('/\b' . $recordOptions['record_select_vals'][$d] . '\b/',$current[$records['db_name']])?'checked ':'') .
                                     "value=\"" . $recordOptions['record_select_vals'][$d] . "\" />\n" .
                                     "<label for=\"" . $elementNameId . $d . "\">" . $recordOptions['record_select_vals'][$d] . "</label>\n";
                    }

                break;

                case "radio":

                    for ($d = 0; $d < count($recordOptions['record_select_vals']); $d++) {

                        $formBody .= "<input type=\"radio\" class=\"validate[" . ($recordOptions['record_select_vals'][$d] == 'true' ? 'required' : 'optional') .
                                     "]\" id=\"" . $elementNameId . $d . "\" name=\"" . $elementNameId . "\" " .
                                     (preg_match('/\b' . $recordOptions['record_select_vals'][$d] . '\b/',$current[$records['db_name']])?'checked':'') .
                                     " value=\"" . $recordOptions['record_select_vals'][$d] . "\" />\n".
                                     "<label for=\"" . $elementNameId . $d . "\">" . $recordOptions['record_select_vals'][$d] . "</label>\n";
                    }

                break;

                case "photo":
                case "multiphoto":

                    $formBody .= (!empty($current[$records['db_name']])?
                                    "<div class=\"recordImg\">\n" .
                                        "<a title=\"Delete '" . $current[$records['db_name']] . "'\" data-id=\"" . $id . "\" data-recordname=\"" . $records['db_name'] . "\" data-filename=\"" . strtolower($current[$records['db_name']]) . "\"><i class=\"fa fa-trash fa-1x\"></i></a>\n".
                                        "<img src=\"" . _SITEROOT_ . "uploads/" . $id . "/" . $records['db_name'] . "/300xauto_" . strtolower($current[$records['db_name']]) . "\" data-orig=\"" . _SITEROOT_ . "uploads/" . $id . "/" . $records['db_name'] . "/300xauto_" . strtolower($current[$records['db_name']]) . "\" />\n".
                                    "</div>\n"
                                 :'');
                    $formBody .= "<input type=\"file\" class=\"plugin-photo validate[" . ($recordOptions['record_required'] == 'true' && empty($current[$records['db_name']]) ? 'required' : 'optional') . ",checkFileType[jpg|jpeg|gif|JPG|png|PNG|ico|bmp]] fileInput\" name=\"" . $elementNameId .($records['db_name']=="multiphoto"?"[]":""). "\" ".($records['db_name']=="multiphoto"?" mulitple=\"multiple\" ":"")." id=\"" . $elementNameId . "[]\" />\n";

                break;

                case "file":

                    $ext  = strtolower(pathinfo($current[$records['db_name']], PATHINFO_EXTENSION));

                    $formBody .= (!empty($current[$records['db_name']])?
                                    "<div class=\"recordFile\">\n".
                                        "<a class=\"deleteRecordFile\" title=\"Delete '" . $current[$records['db_name']] . "'\" data-id=\"" . $id . "\" data-recordname=\"" . $records['db_name'] . "\" data-filename=\"" . $current[$records['db_name']] . "\"></a>\n" .
                                        "<a href=\"" . _EQROOT_ . "resources/uploads/" . $id . "/" . $records['db_name'] . "/" . $current[$records['db_name']] . "\" target=\"_blank\" class=\"documentIcon " . $ext . "\"></a>\n" .
                                    "</div>\n"
                                :'');

                    $formBody .= "<input value=\"" . $current[$records['db_name']] .
                                 "\" type=\"file\" class=\"validate[" . ($recordOptions['record_required'] == 'true' && empty($current[$records['db_name']]) ? 'required' : 'optional') . ",checkFileType[pdf|doc|docx|xls|csv|rar|txt|avi|mov|flv|mp3|zip|wav|php|html|css|js|jar|bak|tar|sys|exe|iso]] " .
                                 "fileInput\" name=\"" . $elementNameId . "\" id=\"" . $elementNameId . "\" />\n";
                break;

                case "date":

                    $formBody .= "<input value=\"".$current[$records['db_name']] . "\" type=\"text\" ".
                                 ($recordOptions['record_disabled'] == 'true' ? 'readonly="readonly"' : '') .
                                    "class=\"datepicker validate[" .
                                    ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') . "]\" name=\"" .
                                    $elementNameId . "\" id=\"" . $elementNameId . "\" />\n";
                break;

                case "color":

                    $formBody .= "<input value=\"" . $current[$records['db_name']] ."\" type=\"text\" " .
                                 ($recordOptions['record_disabled'] == 'true' ? 'readonly="readonly"' : '') .
                                 "class=\"colorpick validate[" .
                                 ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') .
                                 "]\" id=\"" . $elementNameId . "\" name=\"" .
                                 $elementNameId . "\" id=\"" . $elementNameId . "\"/>\n
                                 <label for=\"" . $elementNameId . "\" class=\"pick\"></label>\n";
                break;

                case "time":

                    $formBody .= "<input value=\"" . $current[$records['db_name']] . "\" " .
                                 ($recordOptions['record_disabled'] == 'true' ? 'readonly="readonly"' : '') .
                                  "type=\"text\" class=\"timepicker validate[" .
                                 ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') .
                                 "]\" name=\"" . $elementNameId . "\" id=\"" . $elementNameId . "\" size=\"10\">\n" .
                                 "<span class=\"ml10\">use your mousewheel and keyboard</span>\n";

                break;

                case "foreignkey":

                    $fcomStruc_q = 'SELECT * FROM cms_plugin_structure
                    				WHERE
                    				id = "' . $records['fkey'] . '"';
                    $fcomStruc_r = mysqli_query($con,$fcomStruc_q);
                    $fcomStruc   = mysqli_fetch_array($fcomStruc_r,MYSQLI_ASSOC);

                    $fFieldset_q   = 'SELECT * FROM cms_fieldsets
                                    WHERE
                                    com_struc_id = "' . $records['fkey'] . '"';
                    $fFieldset_r   = mysqli_query($con,$fFieldset_q);
                    $fkeyFieldset  = mysqli_fetch_array($fFieldset_r,MYSQLI_ASSOC);


                    $fetchKeys_q = 'SELECT * FROM cms_content
                    				WHERE
                    				pluginid = "' . $fcomStruc['pluginid'] . '"
                    				AND recordset = "' . $fcomStruc['db_name'] . '"';
                    $fetchKeys_r = mysqli_query($con,$fetchKeys_q);


                    $fkeyOptions = explode(',', $recordOptions['record_fkeyoptions']);
                    if(count($fkeyOptions)) {
                        $fkeyOptions = $pluginListBuilder->validateRecordViaFieldset($fkeyOptions,$fkeyFieldset['id']);
                    } else {
                        $fkeyOptions = array('id');
                    }

                    $fkeyValues = explode(',', $recordOptions['record_fkeyvalue']);
                    if(count($fkeyOptions)) {
                        $fkeyValues = $pluginListBuilder->validateRecordViaFieldset($fkeyValues,$fkeyFieldset['id']);
                    } else {
                        $fkeyValues = array('id');
                    }

                    $formBody .= "<select " . ($recordOptions['record_disabled'] == 'true' ? 'disabled="disabled" ' : '') .
                                 " class=\"validate[" . ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') . "]\" " .
                                 "name=\"" . $elementNameId . ($recordOptions['record_fkeytype'] == 'multiselect' ? '[]' : '') . "\" " .
                                 "id=\"" . $elementNameId . ($recordOptions['record_fkeytype'] == 'multiselect' ? '[]' : '') . "\" " .
                                 ($recordOptions['record_fkeytype'] == 'multiselect' ? 'multiple="multiple"' : '') .
                                 "style=\"" . $recordOptions['record_styles'] . "\">\n";

                    while($fetchKey = mysqli_fetch_array($con,$fetchKeys_r)) {

                        $fkeyValue = (!empty($fkeyValues)?$fetchKey[$fkeyValues[0]]:$fetchKey['id']);
                        $formBody .= "\n<option " .
                                        (preg_match('/\b' . $fkeyValue . '\b/', $current[$records['db_name']])?' selected':'') .
                                          ' value="' . $fkeyValue . '">';
                                          $gg = 0;
                                        foreach($fkeyOptions as $option) :
                                            $formBody .= ($gg>0?' ':'').$fetchKey[$option];
                                            $gg++;
                                        endforeach;
                        $formBody .= "</option>";
                    }

                    $formBody .= "\n</select>\n";



                break;

                case "custom_url":

                $formBody .= "<input type=\"text\" disabled=\"disabled\" value=\"" . $current[$records['db_name']] . "\"/ class=\"custom_url\">\n<br/><br/>".
                             "<strong class=\"pluginmeta\">Meta Information:</strong>\n".
                                "<a class=\"pluginmetalink\" onclick=\"showhide('metaforms'); if (document.getElementById('pagemetaimg').src.indexOf('plus.gif') &gt; 0){ document.getElementById('pagemetaimg').src = '" . EQ_PUB . "images/minus.gif'; } else { document.getElementById('pagemetaimg').src = '" . EQ_PUB . "images/plus.gif'; } return false;\"><img src=\"" . EQ_PUB . "images/plus.gif\" id=\"pagemetaimg\" width=\"9\" height=\"9\" border=\"0\" align=\"absmiddle\" alt=\"Modify Meta Information\"></a>\n".
                                "<div id=\"metaforms\" class=\"pluginmetaform\"><table width=\"100%\">\n".
                                    "<tr><td width=\"150px\" style=\"vertical-align:top\"><label>Search Index</label></td><td style=\"vertical-align:top\">\n".
                                        "<input type=\"radio\" id=\"allow\" name=\"search_index\"  " . ($current['search_index']=="yes"?"checked=\"checked\"":"") . "  value=\"yes\"/><label for=\"allow\">Allow</label>\n".
                                        "<input type=\"radio\" id=\"dont\" name=\"search_index\"  " . ($current['search_index']=="no"?"checked=\"checked\"":"") . " value=\"no\"/><label for=\"dont\">Don't Allow</label>\n".
                                    "</td></tr>\n".
                                    "<tr><td width=\"100px\" style=\"vertical-align:top\"><label>Title</label></td><td style=\"vertical-align:top\"><input type=\"text\" name=\"meta_title\" value=\"" . $current['meta_title'] . "\" style=\"width:80%\"/></td></tr>\n".
                                    "<tr><td style=\"vertical-align:top\"><label>Description</label></td><td style=\"vertical-align:top\"><input type=\"text\" name=\"meta_description\" value=\"" . $current['meta_description'] . "\" /></td></tr>\n".
                                    "<tr><td style=\"vertical-align:top\"><label>Keywords</label></td><td style=\"vertical-align:top\"><input type=\"text\" name=\"meta_keywords\" value=\"" . $current['meta_keywords'] . "\" /></td></tr>\n".
                                "</table></div>";

                break;

                case "ukpostcode":

                    $formBody .= "<input value=\"" . $current[$records['db_name']] . "\" " .
                                 "type=\"text\" name=\"" . $elementNameId . "\" " .
                                 "id=\"" . $elementNameId . "\" " .
                                 "style=\"" . $recordOptions['record_styles'] . "\" ".
                                 ($recordOptions['record_disabled'] == 'true' ? 'readonly="readonly"' : '') .
                                 "class=\"validate[" . ($recordOptions['record_required'] == 'true' ? 'required' : 'optional') . ",custom[ukpostcode]]\" " .
                                 "style=\"" . $recordOptions['record_styles'] . "\"/>";

                break;
            }

            $formBody .= "</div>\n";
            $formBody .= "<div class=\"fix\"></div>\n";
            $formBody .= "</div>\n";

        }
        return $formBody;
    }
}
