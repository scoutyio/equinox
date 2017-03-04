<?php

/*
 * This class holds all the functions that are in the ajax
 * modal call for plugin structures, all the adds, saves, and deletes
 * can be found in here and are called via the cms_plugins controller
 */
class CmsPluginsFunctions {

    /*
     * Below function adds a new plugin structure.
     * Uses JSON to check if all values have been entered
     * and returns respective results.
     */
    public function add_plugin_structure_add() {

        global $con;

        header('Content-type: application/json');
        if (!empty($_POST['name']) && !empty($_POST['recordname']) && !empty($_POST['dbname'])) {

        	$validateDbName_q = 'SELECT * FROM cms_plugin_structure
        						 WHERE
        						 db_name = "' . $_POST['dbname'] . '"';
        	$validateDbName_r = mysqli_query($con,$validateDbName_q);
        	if(mysqli_num_rows($validateDbName_r)) {

        		$arrResult = array(
	                'response' => 'exists'
	            );
        	} else {

	            $checkR_q  = 'SELECT * FROM cms_plugin_structure
							  WHERE
							  pluginid = "' . $_POST['pluginid'] . '"
							  AND parentid = "' . $_POST['parentid'] . '"
							  ORDER BY sortorder DESC LIMIT 1';
	            $checkR_r  =  mysqli_query($con,$checkR_q);
	            $sortorder =  mysqli_fetch_array($checkR_r,MYSQLI_ASSOC);
	            $order     =  $sortorder['sortorder'] + 1;

	            $addCompnent_q = 'INSERT INTO cms_plugin_structure
								  (
									pluginid,
									parentid,
									name,
									recordname,
                                    db_name,
                                    listorder,
                                    listsearch,
                                    listfields,
									sortorder
								  )
								 VALUES
								  (
								  	"' . $_POST['pluginid'] . '",
								  	"' . $_POST['parentid'] . '",
								  	"' . $_POST['name'] . '",
								  	"' . $_POST['recordname'] . '",
                                    "' . $_POST['dbname'] . '",
                                    "' . $_POST['listorder'] . '",
                                    "' . $_POST['listsearch'] . '",
                                    "' . $_POST['listfields'] . '",
								  	"' . $order . '"
								  )';
	            $addComponent_r = mysqli_query($con,$addCompnent_q) or die(mysqli_error($con));
	            $arrResult = array(
	                'response' => 'success'
	            );
	        }
        } else {

            $arrResult = array(
                'response' => 'empty'
            );
        }
        echo json_encode($arrResult);
        exit;
    }

    /*
     * Below saves an existing plugin structure.
     * Uses JSON to check if all values have been entered
     * and returns respective results.
     */
    public function save_plugin_structure() {

        global $con;

        header('Content-type: application/json');
        if (!empty($_POST['name']) && !empty($_POST['recordname']) && !empty($_POST['dbname'])) {


        	$validateDbName_q = 'SELECT * FROM cms_plugin_structure
                  						 WHERE
                  						 db_name = "' . $_POST['dbname'] . '"
                  						 AND id <> "' . $_POST['pluginstrucid'] . '"';
        	$validateDbName_r = mysqli_query($con,$validateDbName_q);
        	if(mysqli_num_rows($validateDbName_r)) {

        		$arrResult = array(
	                'response' => 'exists'
	            );
        	} else {

	            $saveF_q = 'UPDATE cms_plugin_structure
							SET
							parentid = "' . $_POST['parentid'] . '",
							name = "' . $_POST['name'] . '",
							recordname = "' . $_POST['recordname'] . '",
                            db_name = "' . $_POST['dbname'] . '",
                            listorder = "' . $_POST['listorder'] . '",
                            listsearch = "' . $_POST['listsearch'] . '",
                            listfields = "' . $_POST['listfields'] . '"
							WHERE id = "' . $_POST['pluginstrucid'] . '"';
	            $saveF_r = mysqli_query($con,$saveF_q) or die(mysqli_error($con));
	            $arrResult = array(
	                'response' => 'success'
	            );
        	}
        } else {

            $arrResult = array(
                'response' => 'empty'
            );
        }
        echo json_encode($arrResult);
        exit;
    }

    /*
     * Below delets an existing plugin structure
     * and also deletes recursive fieldsets and records as well
     */
    public function delete_plugin_structure() {

        global $con;

        $findf_q  = 'SELECT * FROM cms_fieldsets
          					 WHERE
          					 com_struc_id = "' . $_POST['pluginstrucid'] . '"';
        $findf_r  = mysqli_query($con,$findf_q);
        $findf    = mysqli_fetch_array($findf_r,MYSQLI_ASSOC);
        $delete_r = mysqli_query($con,'DELETE FROM cms_records WHERE fieldsetid = "' . $findf['id'] . '"');

        $delete_q = 'DELETE FROM cms_fieldsets
          					 WHERE
          					 com_struc_id = "' . $_POST['pluginstrucid'] . '"';
        $delete_r = mysqli_query($con,$delete_q);

        $delete_q = 'DELETE FROM cms_plugin_structure
          					 WHERE
          					 id = "' . $_POST['pluginstrucid'] . '"';
        $delete_r = mysqli_query($con,$delete_q);
        exit;
    }

    /*
     * Below adds a new fieldset and using JSON
     * validates that a name has been entered,
     * and tries to check if someone is making a hack as each
     * structure can only have one fieldset
     */
    public function add_fieldset_structure_add() {

        global $con;

        header('Content-type: application/json');
        if (!empty($_POST['name'])) {
            $checkFieldset_q = 'SELECT * FROM cms_fieldsets
              							    WHERE
              							    com_struc_id = "' . $_POST['pluginstrucid'] . '"';
            $checkFieldset_r = mysqli_query($con,$checkFieldset_q);
            if (mysqli_num_rows($checkFieldset_r)) {

                $arrResult = array(
                    'response' => 'exists'
                );
            } else {
                $addFieldset_q = 'INSERT INTO cms_fieldsets
                  								(
                  									com_struc_id,
                  									name
                  								) VALUES (
                  									"' . $_POST['pluginstrucid'] . '",
                  									"' . $_POST['name'] . '"
                  								)';
                $addComponent_r = mysqli_query($con,$addFieldset_q) or die(mysqli_error());
                $arrResult = array(
                    'response' => 'success'
                );
            }
        } else {

            $arrResult = array(
                'response' => 'error'
            );
        }
        echo json_encode($arrResult);
        exit;
    }

    /*
     * Below functino saves an existing fieldset using post
     * values, and again uses JSON to validate that a name has been
     * entered.
     */
    public function save_fieldset_structure() {

        global $con;

        header('Content-type: application/json');
        if (!empty($_POST['name'])) {

            $saveF_q = 'UPDATE cms_fieldsets
            						SET
            						name = "' . $_POST['name'] . '"
            						WHERE id = "' . $_POST['fid'] . '"';
            $saveF_r = mysqli_query($con,$saveF_q) or die(mysqli_error());
            $arrResult = array(
                'response' => 'success'
            );
        } else {

            $arrResult = array(
                'response' => 'error'
            );
        }
        echo json_encode($arrResult);
        exit;
    }

    /*
     * Below deletes an existing fieldset using the post values,
     * and deletes any recursive records that may fall under
     */
    public function delete_fieldset_structure() {

        global $con;

        $delete_q = 'DELETE FROM cms_records
          					 WHERE
          					 fieldsetid = "' . $_POST['fid'] . '"';
        $delete_r = mysqli_query($con,$delete_q);

        $delete_q = 'DELETE FROM cms_fieldsets
          					 WHERE
          					 id = "' . $_POST['fid'] . '"';
        $delete_r = mysqli_query($con,$delete_q);
        exit;
    }



    public function add_record_structure_add() {

        global $con;

        header('Content-type: application/json');

        $eqApp = new Apps();

        $recordset_q = 'SELECT db_name FROM cms_plugin_structure WHERE id = "' . $_POST['plugstruid'] . '"';
        $recordset_r = mysqli_query($con,$recordset_q);
        $recordset = mysqli_fetch_array($recordset_r,MYSQLI_ASSOC);

        if (empty($_POST['record_name']) || empty($_POST['record_dbname'])) {

            $arrResult = array(
                'response' => 'emptyfields'
            );
        } else {

            if (in_array($_POST['record_dbname'], $eqApp->reservedSqlWords())) {

                $arrResult = array(
                    'response' => 'sqlerror'
                );
            } else {

                $result = mysqli_query($con,"SHOW COLUMNS FROM `cms_content` LIKE '" . $_POST['record_dbname'] . "'");
                $exists = (mysqli_num_rows($result));

                if ($exists == 0) {
                    $addColumn = mysqli_query($con,"ALTER TABLE `cms_content` ADD COLUMN " . $_POST['record_dbname'] . " LONGTEXT COLLATE utf8_general_ci NULL");
                }

                $checkDouble_q = 'SELECT * FROM cms_records
                								  WHERE
                								  fieldsetid = "' . $_POST["fid"] . '"
                								  AND db_name = "' . $_POST["record_dbname"] . '"';
                $checkDouble_r = mysqli_query($con,$checkDouble_q);
                if (mysqli_num_rows($checkDouble_r) > 0) {
                    $arrResult = array(
                        'response' => 'matchingfields'
                    );
                } else {

                    $checkR_q  = 'SELECT * FROM cms_records
                  								 WHERE
                  								 fieldsetid = "' . $_POST['fid'] . '"
                  								 ORDER BY sortorder DESC LIMIT 1';
                    $checkR_r  = mysqli_query($con,$checkR_q);
                    $sortorder = mysqli_fetch_array($checkR_r,MYSQLI_ASSOC);
                    $order     = $sortorder['sortorder'] + 1;

                    $addCompnent_q = 'INSERT INTO cms_records
                    									 (
                    									 	fieldsetid,
                                        recordset,
                    									 	name,
                    									 	type,
                    									 	db_name,
                    									 	helper,
                    									 	options,
                    									 	sortorder,
                    									 	fkey,
                                        photoresize,
                                        custom_url
                    									 ) VALUES (
                                                            "' . $_POST['fid'] . '",
                    									 	"' . $recordset['db_name'] . '",
                    									 	"' . $_POST['record_name'] . '",
                    									 	"' . $_POST['record_type'] . '",
                    									 	"' . $_POST['record_dbname'] . '",
                    									 	"' . $_POST['record_helper'] . '",
                    									 	"' . $_POST['record_options'] . '",
                    									 	"' . $order . '",
                                        "' . $_POST['record_fkey'] . '",
                                        "' . $_POST['record_photoresize'] . '",
                                        "' . $_POST['record_customurl'] . '"
                                       )';
                    $addComponent_r = mysqli_query($con,$addCompnent_q) or die(mysqli_error());
                    $arrResult = array(
                        'response' => 'success'
                    );
                }
            }
        }
        echo json_encode($arrResult);
        exit;
    }


    public function save_record_structure() {

        global $con;

        header('Content-type: application/json');

        $eqApp = new Apps();

        $recordset_q = 'SELECT db_name FROM cms_plugin_structure WHERE id = "' . $_POST['plugstruid'] . '"';
        $recordset_r = mysqli_query($con,$recordset_q);
        $recordset = mysqli_fetch_array($recordset_r,MYSQLI_ASSOC);

        if (empty($_POST['record_name']) || empty($_POST['record_dbname'])) {

            $arrResult = array(
                'response' => 'emptyfields'
            );
        } else {

            if (in_array($_POST['record_dbname'], $eqApp->reservedSqlWords())) {

                $arrResult = array(
                    'response' => 'sqlerror'
                );
            } else {
            	$checkColumn_q = 'SHOW COLUMNS FROM `cms_content`
            					  LIKE "' . $_POST['record_dbname'] . '"';
                $checkColumn_r = mysqli_query($con,$checkColumn_q);
                $exists        = (mysqli_num_rows($checkColumn_r));
                if ($exists == 0) {
                	$addColumn_q = 'ALTER TABLE `cms_content`
                					ADD COLUMN ' . $_POST['record_dbname'] . ' LONGTEXT';
                    $addColumn_r = mysqli_query($con,$addColumn_q);
                }

                $checkDouble_q = 'SELECT * FROM cms_records
                								  WHERE
                								  fieldsetid = "' . $_POST["fid"] . '"
                								  AND db_name = "' . $_POST["record_dbname"] . '"
                								  AND id <> "' . $_POST["rid"] . '"';
                $checkDouble_r = mysqli_query($con,$checkDouble_q);
                if (mysqli_num_rows($checkDouble_r) > 0) {

                    $arrResult = array(
                        'response' => 'matchingfields'
                    );
                } else {

                    $saveF_q = 'UPDATE cms_records
                								SET
                								name = "' . $_POST['record_name'] . '",
                                type = "' . $_POST['record_type'] . '",
                								recordset = "' . $recordset['db_name'] . '",
                								db_name = "' . $_POST['record_dbname'] . '",
                								helper = "' . $_POST['record_helper'] . '",
                								options= "' . $_POST['record_options'] . '",
                                fkey = "' . $_POST['record_fkey'] . '",
                                photoresize = "' . $_POST['record_photoresize'] . '",
                                custom_url = "' . $_POST['record_customurl'] . '"
                								WHERE id = "' . $_POST['rid'] . '"';
                    $saveF_r = mysqli_query($con,$saveF_q) or die(mysqli_error($con));
                    $arrResult = array(
                        'response' => 'success'
                    );
                }
            }
        }
        echo json_encode($arrResult);
        exit;
    }

	public function delete_record_structure(){

    global $con;

		$delete_q = 'DELETE FROM cms_records
      					 WHERE
      					 id = "'.$_POST['rid'].'"';
		$delete_r = mysqli_query($con,$delete_q);
		exit;
	}

    public function has_tinymce($pluginStrucId, $fieldsetId = NULL) {
        global $con;

        $findRecords_q = 'SELECT * FROM cms_fieldsets
                          WHERE
                          com_struc_id = "'.$pluginStrucId.'"';
        $findRecords_r = mysqli_query($con,$findRecords_q);
        $findRecords   = mysqli_fetch_array($findRecords_r,MYSQLI_ASSOC);

        $records_q = 'SELECT * FROM cms_records
                      WHERE
                      fieldsetid = "'.$findRecords['id'].'"
                      AND type = "html"';
        $records_r = mysqli_query($con,$records_q);
        if (mysqli_num_rows($records_r) > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function sort_plugin() {

        global $con;

        $action             = mysqli_real_escape_string($con,$_POST['action']);
        $updateRecordsArray = $_POST['recordsArray'];

        if ($action == "updateRecordsListings") {

            $listingCounter = 1;
            foreach ($updateRecordsArray as $recordIDValue) {

                $query = 'UPDATE cms_plugins
                          SET
                          sortorder = "' . $listingCounter . '"
                          WHERE id = "' . $recordIDValue . '"';
                mysqli_query($con,$query) or die(mysqli_error($con));
                $listingCounter = $listingCounter + 1;
            }
        }
        exit;
    }


	public function sort_plugin_structure(){

		$action 			     	= mysqli_real_escape_string($con,$_POST['action']);
		$updateRecordsArray = $_POST['recordsArray'];

		if ($action == "updateRecordsListings"){

			$listingCounter = 1;
			foreach ($updateRecordsArray as $recordIDValue) {

				$query = 'UPDATE cms_plugin_structure
    						  SET
    						  sortorder = "'.$listingCounter.'"
    						  WHERE id = "'. $recordIDValue.'"';
				mysqli_query($con,$query) or die(mysqli_error($con));
				$listingCounter = $listingCounter + 1;
			}
		}
		exit;
	}

    public function sort_record(){
      global $con;
        $action             = mysqli_real_escape_string($con,$_POST['action']);
        $updateRecordsArray = $_POST['recordsArray'];

        if ($action == "updateRecordsListings"){

            $listingCounter = 1;
            foreach ($updateRecordsArray as $recordIDValue) {

                $query = 'UPDATE cms_records
                    		  SET
                    		  sortorder = "'.$listingCounter.'"
                    		  WHERE id = "'. $recordIDValue.'"';
                mysqli_query($con,$query) or die(mysqli_error($con));
                $listingCounter = $listingCounter + 1;
            }
        }
        exit;
    }

    public function removerecordfile($id,$recordname,$filename) {

        global $con;
        $query_q = 'UPDATE cms_content SET ' . $recordname . ' = "" WHERE id = "' . $id . '"';
        $query_r = mysqli_query($con,$query_q);

        $eqApp = new Apps();

        foreach(glob('../uploads/' . $id . '/' . $recordname . '/*') as $file) {
            if(is_dir($file)) {
                recursiveRemoveDirectory($file);
            } else {
                unlink($file);
            }
        }
        $eqApp->rrmdir('../resources/uploads/' . $id . '/' . $recordname);
        exit;
    }

    public function uploadFileResize($filename,$recordId,$resizeArray) {

        $imgUp = new Upload($_FILES[$filename]);
        //IF FILE IS UPLOADED
        //$filename = pathinfo($_FILES[$_POST['recordset'].'_'.$getFinfo['db_name']]["name"], PATHINFO_FILENAME);
        $fileInput = rand().$_FILES[$filename]['name'];
        $imgUp->file_new_name_body = $fileInput;
        $imgUp->Process('resources/uploads/' . $recordId . '/');

        $resizing = explode(',',$resizeArray);

        if(count($resizing)>0){

            foreach($resizing as $resize){

                $imgUp->image_resize = true;
                $dimension = explode("x",$resize);
                $dimCount = 0;

                foreach($dimension as $dim){
                    if($dimCount==0){
                        if($dim == 'auto'){
                            $imgUp->image_ratio_x = true;
                        }else{
                            $imgUp->image_x = $dim;
                        }
                    }else{
                        if($dim == 'auto'){
                            $imgUp->image_ratio_y = true;
                        }else{
                            $imgUp->image_y = $dim;
                        }
                    }
                    $dimCount++;
                }

            $imgUp->file_new_name_body = $resize . "_" . $fileInput;
            $imgUp->Process('resources/uploads/' . $recordId . '/');
            }
        }
        if ($imgUp->processed) {
            echo 'success';
        } else {
            echo 'error : ' . $imgUp->error;
        }
    }
}
