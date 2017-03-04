<?php

class Cms_Plugins_Builder {

    public function structureArray($id, $api = false) {

        global $con;

        $pluginStruc = array();
        $pS_q = 'SELECT * FROM cms_plugin_structure
                WHERE
                pluginid = "' . $id . '"
                AND parentid = 0 ORDER BY sortorder ASC';
        $pS_r = mysqli_query($con,$pS_q);
        while($pS = mysqli_fetch_array($pS_r,MYSQLI_ASSOC)) {

            $pluginStrucs = array();
            //Build Structure
            $pluginStrucs['id'] = $pS['id'];
            $pluginStrucs['parentid'] = '0';
            $pluginStrucs['type'] = $pS['type'];
            $pluginStrucs['name'] = $pS['name'];
            $pluginStrucs['recordname'] = $pS['recordname'];
            $pluginStrucs['db_name'] = $pS['db_name'];
            $pluginStrucs['listorder'] = $pS['listorder'];
            $pluginStrucs['listsearch'] = $pS['listsearch'];
            $pluginStrucs['listfields'] = $pS['listfields'];
            $pluginStrucs['sortorder'] = $pS['sortorder'];

            //Build Fieldset
            $fieldSet = array();
            $f_q = 'SELECT * FROM cms_fieldsets
                    WHERE
                    com_struc_id = "' . $pS['id'] . '"';
            $f_r = mysqli_query($con,$f_q);
            if(mysqli_num_rows($f_r)==1){

                $fieldset = mysqli_fetch_array($f_r,MYSQLI_ASSOC);
                $fSet = array();
                $fSet['id'] = $fieldset['id'];
                $fSet['com_struc_id'] = $fieldset['com_struc_id'];
                $fSet['name'] = $fieldset['name'];

                //Build records
                    $recordSet = array();
                    $rec_q = 'SELECT * FROM cms_records
                              WHERE
                              fieldsetid = "' . $fSet['id'] . '"
                              ORDER BY sortorder ASC';
                    $rec_r = mysqli_query($con,$rec_q);
                    while($rec = mysqli_fetch_array($rec_r,MYSQLI_ASSOC)){

                        $rSet = array();
                        $rSet['id'] = $rec['id'];
                        $rSet['name'] = $rec['name'];
                        $rSet['fieldsetid'] = $rec['fieldsetid'];
                        $rSet['name'] = $rec['name'];
                        $rSet['type'] = $rec['type'];
                        $rSet['db_name'] = $rec['db_name'];
                        $rSet['helper'] = $rec['helper'];
                        $rSet['options'] = $rec['options'];
                        $rSet['fkey'] = $rec['fkey'];
                        $rSet['photoresize'] = $rec['photoresize'];
                        $rSet['custom_url'] = $rec['custom_url'];
                        array_push($recordSet,$rSet);
                    }
                    $fSet['records'] = $recordSet;
                array_push($fieldSet,$fSet);
            }
            $pluginStrucs['fieldset'] = $fieldSet;

            //Build Child Plugin Structures
            $childStucture = array();
            $childC_q = 'SELECT * FROM cms_plugin_structure
                         WHERE pluginid = "' . $id . '"
                         AND parentid = "' . $pS['id'] . '"';
            $childC_r = mysqli_query($con,$childC_q);
            while($childC = mysqli_fetch_array($childC_r,MYSQLI_ASSOC)){
                $childStruc = array();
                //Build Child Structure
                $childStruc['id'] = $childC['id'];
                $childStruc['parentid'] = $childC['parentid'];
                $childStruc['type'] = $childC['type'];
                $childStruc['name'] = $childC['name'];
                $childStruc['recordname'] = $childC['recordname'];
                $childStruc['db_name'] = $childC['db_name'];
                $childStruc['listorder'] = $childC['listorder'];
                $childStruc['listsearch'] = $childC['listsearch'];
                $childStruc['listfields'] = $childC['listfields'];
                $childStruc['sortorder'] = $childC['sortorder'];

                //Build Child Fieldset
                $childFieldSet = array();
                $cF_q = 'SELECT * FROM cms_fieldsets
                         WHERE
                         com_struc_id = "' . $childC['id'] . '"';
                $cF_r = mysqli_query($con,$cF_q);
                if(mysqli_num_rows($cF_r)==1){
                    $cfieldset = mysqli_fetch_array($cF_r,MYSQLI_ASSOC);
                    $cfSet = array();
                    $cfSet['id'] = $cfieldset['id'];
                    $cfSet['com_struc_id'] = $cfieldset['com_struc_id'];
                    $cfSet['name'] = $cfieldset['name'];
                    //Build records
                    $cRecordSet = array();
                    $crec_q = 'SELECT * FROM cms_records
                               WHERE
                               fieldsetid = "' . $cfieldset['id'] . '"
                               ORDER BY sortorder ASC';
                    $crec_r = mysqli_query($con,$crec_q);
                    while($crec = mysqli_fetch_array($crec_r,MYSQLI_ASSOC)){

                        $cRset = array();

                        $cRset['id'] = $crec['id'];
                        $cRset['name'] = $crec['name'];
                        $cRset['fieldsetid'] = $crec['fieldsetid'];
                        $cRset['name'] = $crec['name'];
                        $cRset['type'] = $crec['type'];
                        $cRset['db_name'] = $crec['db_name'];
                        $cRset['helper'] = $crec['helper'];
                        $cRset['options'] = $crec['options'];
                        $cRset['fkey'] = $crec['fkey'];
                        $cRset['photoresize'] = $crec['photoresize'];
                        $cRset['custom_url'] = $crec['custom_url'];
                        array_push($cRecordSet,$cRset);
                    }
                    $cfSet['records'] = $cRecordSet;
                    array_push($childFieldSet,$cfSet);
                }
                $childStruc['fieldset'] = $childFieldSet;
                array_push($childStucture,$childStruc);
            }
            $pluginStrucs['childStruc'] = $childStucture;
            array_push($pluginStruc,$pluginStrucs);
        }
        if($api == true) {
            return json_encode($pluginStruc);
        }
        return $pluginStruc;
    }

    public function buildStructure($pluginId,$structureArray) {

    	$structure = '';
    	foreach ($structureArray as $pluginStruc) { //Begin Structure
	        $structure .= '<div class="component main" id="recordsArray_' . $pluginStruc['id'] . '">';
	        $structure .= '	        <span class="handle"><i class="fa fa-arrows-v"></i></span>';
	        $structure .= '	            <div class="title">';
	        $structure .= '	                <a class="modal" onclick="modalRun(\'Edit Plugin Structure\',\'' . $pluginId . '\',\'' . $pluginStruc['id'] . '\',\'\',\'\')"><strong>' . $pluginStruc['name'] . '</strong> (' . $pluginStruc['db_name'] . ')</a>';

			      	if(!$pluginStruc['fieldset']) {
			      		//Only create add Fiedlset Button if there is no fieldset added'
			        $structure .= '<a class="modal addButton right" onclick="modalRun(\'Add Fieldset\',\'' . $pluginId . '\',\'' . $pluginStruc['id'] . '\',\'\',\'\')" title="Create Fieldset"><i class="fa fa-plus-square-o"></i></a>';
			        }
	        $structure .= '	            </div>';
	        //Begin Fieldset

	       	foreach($pluginStruc['fieldset'] as $fieldset) {
		        $structure .= '<div class="fieldset">';
		        $structure .= '<div class="title">';
		        $structure .= '<a class="modal" onclick="modalRun(\'Edit Field Structure\',\'' . $pluginId . '\',\'' . $pluginStruc['id'] . '\',\'' . $fieldset['id'] . '\',\'\')">Field - ' . $fieldset['name'] . '</a>';
		        $structure .= '<a class="modal addButton right" onclick="modalRun(\'Add Record Structure\',\'' . $pluginId . '\',\'' . $pluginStruc['id'] . '\',\'' . $fieldset['id'] . '\',\'\')"><i class="fa fa-plus-square-o"></i></a>';
		        $structure .= '</div>';
		        $structure .= '<div class="holder">';
		        //Begin Record
		        foreach ($fieldset['records'] as $records){
			        $structure .= '<div class="recordset" id="recordsArray_' . $records['id'] . '"><span class="handle"><i class="fa fa-bars"></i></span>';
			        $structure .= '<div class="title">';
			        $structure .= '<a class="modal" onclick="modalRun(\'Edit Record Structure\',\'' . $pluginId . '\',\'' . $pluginStruc['id'] . '\',\'' . $fieldset['id'] . '\',\'' . $records['id'] . '\')">' . $records['name'] . ' (' . $records['db_name'] . ')</a>';
			        $structure .= '</div>';
			        $structure .= '<div class="fix"></div>';
			        $structure .= '</div>';
		        } //End Record
		        $structure .= '</div>';
		        $structure .= '<div class="fix"></div>';
		        $structure .= '</div>';
	        } //End Fieldset
            //Begin Child Structure
            foreach($pluginStruc['childStruc'] as $childStruc){
            	$structure .= '<div class="component" style="width:98%; border-top:0px;">';
                $structure .= '<div class="title">';
                $structure .= '<a class="modal" onclick="modalRun(\'Edit Plugin Structure\',\'' . $pluginId . '\',\'' . $childStruc['id'] . '\',\'\',\'\')"><strong>' . $childStruc['name'] . '</strong> (' . $childStruc['db_name'] . ')</a>';
                        if(!$childStruc['fieldset']){
                            //Only create add Fiedlset Button if there is no fieldset added'
                        $structure .= '<a class="modal addButton right" onclick="modalRun(\'Add Fieldset\',\'' . $pluginId . '\',\'' . $childStruc['id'] . '\')"><i class="fa fa-plus-square-o"></i></a>';
                        }
                $structure .= '</div>';

					 //Begin Child Fieldset
					foreach($childStruc['fieldset'] as $childField) {

                        $structure .= '<div class="fieldset">';
                        $structure .= '<div class="title">';
                        $structure .= '<a class="modal" onclick="modalRun(\'Edit Field Structure\',\'' . $pluginId . '\',\'' . $childStruc['id'] . '\',\'' . $childField['id'] . '\',\'\')">Field - ' . $childField['name'] . '</a>';
                        $structure .= '<a class="modal addButton right" onclick="modalRun(\'Add Record Structure\',\'' . $pluginId . '\',\'' . $childStruc['id'] . '\',\'' . $childField['id'] . '\',\'\')"><i class="fa fa-plus-square-o"></i></a>';
                        $structure .= '</div>';
                        $structure .= '<div class="holder">';
								//Begin Child Records
							foreach ($childField['records'] as $childRecord){
                            	$structure .= '<div class="recordset" id="recordsArray_' . $childRecord['id'] . '"><span class="handle"><i class="fa fa-bars"></i></span>';
                                $structure .= '<div class="title">';
                                $structure .= '<a class="modal" onclick="modalRun(\'Edit Record Structure\',\'' . $pluginId . '\',\'' . $childStruc['id'] . '\',\'' . $childField['id'] . '\',\'' . $childRecord['id'] . '\')">' . $childRecord['name'] . ' (' . $childRecord['db_name'] . ')</a>';
                                $structure .= '</div>';
                                $structure .= '<div class="fix"></div>';
                                $structure .= '</div>';

                                } //End Child Records

                        $structure .= '</div>';
                        $structure .= '<div class="fix"></div>';
                        $structure .= '</div>';

                    } //End Child Fieldset
                $structure .= '</div>';

            } //End Child Struture
	        $structure .= '<div class="fix"></div>';
	        $structure .= '</div>';

	    }

    return $structure;
    }

    public function importStructure($id) {

        global $con;

        $requiredarray = array('name','recordname', 'db_name','listorder','listsearch','listfields','sortorder');
        $struc = json_decode($_POST['importstring'], true);

        $response = array();

        foreach($struc as $structure) {

                 //GET LAST ID FOR THE cms_content SO TO CREATE A FOLDER FOR THE IMAGES
                $latest_q = 'SHOW TABLE STATUS
                             LIKE
                             "cms_plugin_structure"';
                $latest_r = mysqli_query($con,$latest_q);
                $latestA  = mysqli_fetch_array($latest_r,MYSQLI_ASSOC);
                $latest   = $latestA['Auto_increment'];

                $latestf_q = 'SHOW TABLE STATUS
                             LIKE
                             "cms_fieldsets"';
                $latestf_r = mysqli_query($con,$latestf_q);
                $latestfA  = mysqli_fetch_array($latestf_r,MYSQLI_ASSOC);
                $latestf   = $latestfA['Auto_increment'];

                if(count(array_intersect_key(array_flip($requiredarray), $structure)) !== count($requiredarray)) {
                    $response['error'] = 'db_name missing';
                    break;
                }

                $check_plugin_structure_q = 'SELECT * FROM cms_plugin_structure WHERE db_name = "' . $structure['db_name'] . '"';
                $check_plugin_structure_r = mysqli_query($con,$check_plugin_structure_q);

                if(mysqli_num_rows($check_plugin_structure_r)){
                    $response['error'] = 'plugin structure with that name exists';
                    break;
                }

                $plugin_structure_q = 'INSERT INTO cms_plugin_structure
                    (pluginid, parentid, name, recordname, db_name, listorder,listsearch, listfields, sortorder)
                VALUES ("' . $id . '","0","' .
                    $structure['name'] . '","' .
                    $structure['recordname'] . '","' .
                    $structure['db_name'] . '","' .
                    $structure['listorder'] . '","' .
                    $structure['listsearch'] . '","' .
                    $structure['listfields'] . '","' .
                    $structure['sortorder'] . '")';
                //structure
                // echo $plugin_structure_q;
                mysqli_query($con,$plugin_structure_q);
                foreach($structure['fieldset'] as $fieldset){

                    $fieldset_structure_q = 'INSERT INTO cms_fieldsets (com_struc_id, name) VALUES ("' . $latest . '","' . $fieldset['name'] . '")';
                    //fieldset
                    //echo $plugin_structure_q;
                    mysqli_query($con,$fieldset_structure_q);
                    $record_structure_q = 'INSERT INTO cms_records(
                                        fieldsetid,
                                        name,
                                        type,
                                        db_name,
                                        helper,
                                        options,
                                        fkey,
                                        photoresize,
                                        custom_url,
                                        sortorder) VALUES ';
                    $n = 0;
                    foreach($fieldset['records'] as $record) {

                        $record_structure_q .= ($n > 0 ? ',': '') . '("' . $latestf . '","' .
                                                    $record['name'] . '","' .
                                                    $record['type'] . '","' .
                                                    $record['db_name'] . '","' .
                                                    $record['helper'] . '","' .
                                                    $record['options'] . '","' .
                                                    $record['fkey'] . '","' .
                                                    $record['photoresize'] . '","' .
                                                    $record['custom_url'] . '","' .
                                                    ($n + 1) . '")';
                        $n++;
                    }
                    // record
                    // echo $record_structure_q;
                    mysqli_query($con,$record_structure_q);
                }

        }

        return $response;
    }
}
