<?php

class PluginListBuilder {

    public $tabsHtml;
    public $containerHtml;


    function buildPluginStrucArray($pluginId,$parentId) {

        global $con;

        $structureArray   = array();
        $c_q              = 'SELECT * FROM cms_plugin_structure
              							 WHERE
              							 pluginid = "' . $pluginId . '"
              							 AND parentid = "' . $parentId . '"
              							 ORDER BY sortorder ASC';
        $c_r              = mysqli_query($con,$c_q);
        while ($c = mysqli_fetch_array($c_r,MYSQLI_ASSOC)) {

            $pluginStrucArray 				= array();
            $pluginStrucArray['id']         = $c['id'];
            $pluginStrucArray['pluginid']   = $c['pluginid'];
            $pluginStrucArray['parentid']   = $c['parentid'];
            $pluginStrucArray['name']       = $c['name'];
            $pluginStrucArray['recordname'] = $c['recordname'];
            $pluginStrucArray['db_name']    = $c['db_name'];
            $pluginStrucArray['listorder']  = $c['listorder'];
            $pluginStrucArray['listsearch'] = $c['listsearch'];
            $pluginStrucArray['listfields'] = $c['listfields'];
            array_push($structureArray,$pluginStrucArray);
        }
        return $structureArray;
    }

    function singleStrucArray($id) {

        global $con;

        $structureArray   = array();
        $c_q              = 'SELECT * FROM cms_plugin_structure
							 WHERE
							 id = "' . $id . '"';
        $c_r              = mysqli_query($con,$c_q);
        while ($c = mysqli_fetch_array($c_r,MYSQLI_ASSOC)) {
            $structureArray['id']         = $c['id'];
            $structureArray['pluginid']   = $c['pluginid'];
            $structureArray['parentid']   = $c['parentid'];
            $structureArray['name']       = $c['name'];
            $structureArray['recordname'] = $c['recordname'];
            $structureArray['db_name']    = $c['db_name'];
            $structureArray['listorder']  = $c['listorder'];
            $structureArray['listsearch'] = $c['listsearch'];
            $structureArray['listfields'] = $c['listfields'];
        }
        return $structureArray;
    }


    function strucRecordArray($structureId) {
        global $con;

        $recordArray      = array();
        $f_q              = 'SELECT * FROM cms_fieldsets
              							 WHERE
              							 com_struc_id = "' . $structureId . '"';
        $f_r = mysqli_query($con,$f_q);
        $f 	 = mysqli_fetch_array($f_r,MYSQLI_ASSOC);

		    $r_q = 'SELECT * FROM cms_records
							 WHERE
							 fieldsetid = "' . $f['id'] . '"
							 ORDER BY sortorder ASC';
        $r_r = mysqli_query($con,$r_q);

        $recordArray['fieldsetId'] = $f['id'];
        while($r = mysqli_fetch_assoc($r_r)){

        	$setArr 			  = array();
        	$setArr['strucId']	  = $structureId;
            $setArr['id']         = $r['id'];
            $setArr['name']   = $r['name'];
            $setArr['type']   = $r['type'];
            $setArr['db_name']       = $r['db_name'];
            $setArr['helper'] = $r['helper'];
            $setArr['options']    = $r['options'];
            $setArr['fkey']  = $r['fkey'];
            $setArr['sortorder'] = $r['sortorder'];
            $recordArray[$r['db_name']]= $setArr;
        }

        return $recordArray;
    }

    function buildPluginStrucTabs($pluginId) {

        $array = $this->buildPluginStrucArray($pluginId,0);
        $str   = "eq_tabultor_" . $_SESSION['x_equi'] . "_" . $pluginId;

        if (isset($_POST['pluginTabulator'])) {

            $_SESSION[$str] = $_POST['pluginStructure'];
            header('location: ' . $_SERVER['REQUEST_URI']);
        }

        $x    = 0;
        $html = '<div class="widget first">';
        $html .= '<ul class="tabs">';
        foreach ($array as $c) {

            $className = '';

            if (isset($_SESSION[$str])) {
                if ($_SESSION[$str] == $c['db_name']) {
                    $className = "activeTab";
                }
            } else {
                if ($x == 0) {
                    $className = "activeTab";
                }
            }
            $html .= '<li ' . (!empty($className) ? 'class="' . $className . '"' : '') . '>';
            $html .= '<a class="tabclick " ';
            $html .= 'data-value= "' . $c['db_name'] . '"';
            $html .= 'data-loc="' . $_SERVER['REQUEST_URI'] . '">';
            $html .= $c['name'] . '</a></li>';

            $x++;
        }

        $html .= '</ul>';
        return $html;
    }

    function renderContainer($pluginId) {

        $array = $this->buildPluginStrucArray($pluginId,0);
        $str = "eq_tabultor_" . $_SESSION['x_equi'] . "_" . $pluginId;

        $x   = 0;
        $html = '<div class="tab_container">';

        foreach ($array as $c) {

            $structureInfo = PluginListBuilder::singleStrucArray($c['id']);
            $sortorder = false;
            //check if it is a sortorder listing
            if(strpos($structureInfo['listorder'],'sortorder') !== false) {
                $sortorder = true;
            }

            $className = '';
            if (isset($_SESSION[$str])) {
                if ($_SESSION[$str] == $c['db_name']) {
                    $className = 'display:block;';
                } else {
                    $className = 'display:none;';
                }
            } else {
                if ($x == 0) {
                    $className = 'display:block;';
                } else {
                    $className = 'display:none;';
                }
            }

            $html .= '<div style="' . $className . '" id="' . $c['db_name'] . '-tab" class="tab_content">';
            $html .= '<a href="' . _EQROOT_ . 'plugins/add/' . $pluginId . '?psid='.$c['id'].'" class="addComponentStructure modal"><i class="fa fa-plus-square fa-1x"></i>New ' . $c['recordname'] . '</a>';
            $html .= '<div class="floatright searchPluginDiv">
                      <label class="floatleft">Search:&nbsp;</label><form method="GET" action="' . $_SERVER['REQUEST_URI'] . '" class="floatleft"><input type="text" name="search' . $c['db_name'] . '" value="' . (isset($_GET['search' . $c['db_name']])?$_GET['search' . $c['db_name']]:'') . '"
                      class="searchPlugin" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }"></form></div>';
            $html .= '<div class="widget first">';
            $html .= '<table cellpadding="0" cellspacing="0" width="100%" class="tableStatic" style="border-top:1px solid #ccc;" data-pluginid="' . $pluginId . '" data-structureid="' . $c['id'] . '">';
            $html .= '<thead>';
            $html .= $this->generateTableHead($pluginId,$c['id']);
            $html .= '</thead>';
            $html .= '<tbody class="pluginList"></tbody>';
            $html .= '</table>';
            $html .= '</div>';
            if(!$sortorder) {
                $html .= '<div class="pagination pluginspaging floatright" ><ul class="pages" data-next=""><li class="prev"><a>Prev</a></li><li class="next" data-next=""><a>Next</a></li></ul></div>';
                $html .= '<label class="pluginsorderlabel floatleft">Items per page</label><select class="pluginscount floatleft"><option>5</option><option>10</option><option>25</option><option>50</option></select>';
            }
            $html .= '</div>';

            $x++;
        }

        $html .= '</div><div class="fix"></div></div>';
        return $html;
    }

    function generateTableHead($pluginId, $structureId) {

        global $con;
        $structureInfo = PluginListBuilder::singleStrucArray($structureId);
        $recordArray = PluginListBuilder::strucRecordArray($structureId);

        //begin with building the columns and get the user set columns
		    $listFields = explode(',',$structureInfo['listfields']);

		    //now that we have array'd the list fields, now to validate them in case that users have not entered teh right ones
      	foreach($listFields as $key => $val) {
    			$result_q = 'SELECT * FROM cms_records
    						 WHERE
    						 fieldsetid = "'.$recordArray['fieldsetId'].'"
    						 AND db_name LIKE "'.trim($val).'"';
    			$result = mysqli_query($con,$result_q);
    			if(mysqli_num_rows($result)==0){
    				unset($listFields[$key]);
    			}else{
    				$listFields[$key] = trim($val);
    			}
    		}
        $ptml = '<tr>';
        if(count($listFields)) {
        	foreach($listFields as $arr) {
	        	$ptml .= '<td>' . $recordArray[$arr]['name'] . '</td>';
        	}
        } else {
        	$ptml .= '<td>Id #</td>';
        }
        $ptml .= '</tr>';

        return $ptml;
    }

    function validateRecordViaFieldset($recordsArray,$fieldsetId) {

        global $con;

        foreach($recordsArray as $key => $val) {
            $result_q = 'SELECT * FROM cms_records
                         WHERE
                         fieldsetid = "'.$fieldsetId.'"
                         AND db_name LIKE "'.trim($val).'"';
            $result = mysqli_query($con,$result_q);
            if(mysqli_num_rows($result)==0){
                unset($recordsArray[$key]);
            }else{
                $recordsArray[$key] = trim($val);
            }
        }
        return $recordsArray;
    }

    function generateTableBody($pluginId,$structureId,$per_page, $page, $recordId = NULL) {

        global $con;
        $eqApp = new Apps();
        $pluginListBuilder = new PluginListBuilder();
  	    $structureInfo = $pluginListBuilder->singleStrucArray($structureId);

        $recordArray = $pluginListBuilder->strucRecordArray($structureId);

        $sortorder = false;
        //check if it is a sortorder listing
        if(strpos($structureInfo['listorder'],'sortorder') !== false) {
            $sortorder = true;
        }

        //begin with building the columns and get the user set columns
        $listFields = explode(',',$structureInfo['listfields']);
        $listFields = $pluginListBuilder->validateRecordViaFieldset($listFields,$recordArray['fieldsetId']);

        $listSearch = explode(',',$structureInfo['listsearch']);
        $listSearch = $pluginListBuilder->validateRecordViaFieldset($listSearch,$recordArray['fieldsetId']);

        if(!empty($_GET['search'.$structureInfo['db_name']]) && empty($listSearch)) {

            echo 'Search settings not set up...please notify developer';
        }

        if (!isset($page)){
            $page = 1;
        } else {
            $page = intval($page);
            if($page < 1) $page = 1;
        }

        $start_from = ($page - 1) * $per_page;
        $start_from = ($start_from > 0 ? $start_from - 1 : 0);
    		$query_q = 'SELECT SQL_CALC_FOUND_ROWS * FROM cms_content
          					WHERE
          					pluginid = "' . $pluginId . '"
          					AND recordset = "' . $structureInfo['db_name'] . '"';
                    //SEARCH PARAMETERS
                    if(!empty($recordId)) {
                        $query_q .= ' AND recordid = "'.$recordId.'"';
                    }
                    if(!empty($_GET['search'.$structureInfo['db_name']]) && !empty($listSearch)) {
                        $query_q .= ' AND (';
                        $g = 1;
                        foreach($listSearch as $search) {
                            $query_q .= $search . ' LIKE "%' . $_GET['search'.$structureInfo['db_name']] . '%"';
                            if ($g < count($listSearch)) {
                                $query_q .= ' OR ';
                            }
                            $g++;
                        }
                        $query_q .= ')';
                    }
                    if(isset($_POST['recordid'])) { ' AND recordid = "'.$_POST['recordid'].'"';}
        $query_q .= ' ORDER BY ' . ($structureInfo['listorder']?$structureInfo['listorder']:'id ASC');
        if(!$sortorder) {
            $query_q .= ' LIMIT ' . $start_from.', ' . $per_page;
        }
        $query_r  = mysqli_query($con,$query_q) or die(mysqli_error($con));

        $found_q = "SELECT FOUND_ROWS()";
        $found_r = mysqli_query($con,$found_q);
        $total_results = $found_r->fetch_row();

        $numpages = ceil($total_results[0]/$per_page);

		$ptml = '';
		while($query = mysqli_fetch_assoc($query_r)) {
			$ptml .= '<tr id="recordsArray_' . $query['id'] . '" data-totalresults="' . $total_results[0] . '" data-numpages="' . $numpages . '" data-currentpage="' . $page . '">';
 			if(count($listFields)) {
            $n=0;
        		foreach($listFields as $arr) {

                    if($recordArray[$arr]['type']=="foreignkey"){

                       $recordOptions = $eqApp->get_field_options($recordArray[$arr]['options'], $n);
                        ///IF IT IS FOREGIN KEY DO THE WORK
                         $fkeyval = explode(",",$recordOptions['record_fkeyvalue']);
                         $fkeyopt = explode(",",$recordOptions['record_fkeyoptions']);
                         if($recordOptions['record_fkeyvalue']){
                            $fetchKeys_q = 'SELECT * FROM cms_content WHERE ' . $recordOptions['record_fkeyvalue'] .' = "'.$query[$arr].'"';
                         }else{
                           echo $fetchKeys_q = 'SELECT * FROM cms_content WHERE id = "'.$query[$arr].'"';
                         }

                       $fetchKeys_r = mysqli_query($con,$fetchKeys_q);
                       $fetchKeys = mysqli_fetch_array($fetchKeys_r,MYSQLI_ASSOC);

                        if(count($fkeyopt)){
                            $countfkey=0;
                            foreach($fkeyopt as $name){
                                $recordDisplayVal = ($countfkey>0?" - ":"").$fetchKeys[$name];
                                $countfkey++;
                            }
                        } else {
                            $recordDisplayVal = $fetchKeys['id'];
                        }
                    }else{
                        $recordDisplayVal = $query[$arr];
                    }
                    $ptml .= '<td align="center">' .
                                 ($sortorder && $n==0 ? "<span class=\"handle\"><i class=\"fa fa-bars\"></i></span>" : "") .
                                 ($n==0?'<a href="' . _EQROOT_ . 'plugins/edit/' . $pluginId . '?id=' . $query['id'] . '&psid=' . $structureId . ($_POST['recordid'] ? '&recordid=' . $_POST['recordid'] :'') . '">':'') .
                                    ($recordArray[$arr]['type']=='photo'?'<img src="' . (!empty($query[$arr])? _UPLOADS_ . $query['id'] . '/' . $recordArray[$arr]['db_name'] . '/300xauto_' : _EQROOT_ . 'public/images/no-image.jpg') :'')
                                     . $recordDisplayVal .
                                    ($recordArray[$arr]['type']=='photo'?'" width="200">':'') .
                                 ($n==0?'</a>':'') .
                             '</td>';
                    $n++;
              }
	        } else {
	        	$ptml .= '<th>Id #</th>';
	        }
        	$ptml .= '</tr>';

		}

        echo $ptml;
    }

    function renderBody($pluginId) {

        return PluginListBuilder::buildPluginStrucTabs($pluginId) . PluginListBuilder::renderContainer($pluginId);
    }
}
