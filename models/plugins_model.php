<?php

class Plugins_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function valPlugin($id) {
        global $con;
        // Validate if the plugin id exists in the plugins table
        $check_q = 'SELECT * FROM cms_plugins
                    WHERE
                    id = "' . $id . '"';
        $check_r = mysqli_query($con,$check_q);
        if (!mysqli_num_rows($check_r)) {

            header("location: " . _EQROOT_);
        } else {
            $pluginInfo = mysqli_fetch_array($check_r,MYSQLI_ASSOC);
            return $pluginInfo;
        }
    }

    public function valStructure($pluginId, $psid) {

        if (!isset($pluginId) || !isset($psid)) {

            header("location:" . _EQROOT_);
        } else {
            global $con;

            $valPlugin_q = 'SELECT * FROM cms_plugins
                            WHERE
                            id = "' . $pluginId . '"';
            $valPlugin_r = mysqli_query($con,$valPlugin_q);
            if (mysqli_num_rows($valPlugin_r)) {

                $check_c = 'SELECT * FROM cms_plugin_structure
                            WHERE
                            id = "' . $psid . '"
                            AND pluginid = "' . $pluginId . '"';
                $check_r = mysqli_query($con,$check_c);
                if (mysqli_num_rows($check_r)) {
                    return true;
                } else {
                    header("location:" . _EQROOT_ . "plugins/view/" . $pluginId);
                }
            } else {
                header("location:" . _EQROOT_);
            }
        }
    }

    public function valRecord($pluginId, $psid, $id) {

        if (!isset($pluginId) || !isset($psid) || !isset($id)) {

            header("location: " . _EQROOT_);
        } else {
            global $con;
            $valPlugin_q = 'SELECT * FROM cms_plugins
                            WHERE
                            id = "' . $pluginId . '"';
            $valPlugin_r = mysqli_query($con,$valPlugin_q);
            if (mysqli_num_rows($valPlugin_r)) {

                $check_c = 'SELECT * FROM cms_plugin_structure
                            WHERE
                            id = "' . $psid . '"
                            AND pluginid = "' . $pluginId . '"';
                $check_r = mysqli_query($con,$check_c);
                if (mysqli_num_rows($check_r)) {

                    $pstruc     = mysqli_fetch_array($check_r,MYSQLI_ASSOC);
                    $checkRec_q = 'SELECT * FROM cms_content
                                   WHERE
                                   id = "' . $id . '"
                                   AND recordset = "' . $pstruc['db_name'] . '"';
                    $checkRec_r = mysqli_query($con,$checkRec_q);
                    if (mysqli_num_rows($checkRec_r)==1) {
                        return true;
                    } else {
                        header("location:" . _EQROOT_ . "plugins/view/" . $pluginId);
                    }
                } else {
                    header("location: " . _EQROOT_);
                }
            } else {
                header("location: " . _EQROOT_);
            }
        }
    }

    public function addNew() {

        global $con;
        $eqApp = new Apps();

        $getFinfo_q = 'SELECT * FROM
                       cms_records WHERE
                       fieldsetid = "' . $_POST['fieldsetid'] . '"
                       ORDER BY sortorder ASC';
        $getFinfo_r = mysqli_query($con,$getFinfo_q);
        $getFinfo_n = mysqli_num_rows($getFinfo_r);
        $pnum       = 1;

        //GET LAST ID FOR THE cms_content SO TO CREATE A FOLDER FOR THE IMAGES
        $latest_q = 'SHOW TABLE STATUS
                     LIKE
                     "cms_content"';
        $latest_r = mysqli_query($con,$latest_q);
        $latestA  = mysqli_fetch_array($latest_r,MYSQLI_ASSOC);
        $latest   = $latestA['Auto_increment'];

        //START INSERT QUERY BEGINNING WITH USUAL STARTING VALUES
        $query_q = 'INSERT INTO cms_content
                    (
                        pluginid,
                        recordset,
                        recordid,
                        timestamp';
        //WHILE LOOP FOR ADDING THE DATABASE VALUES
        while ($getFinfo = mysqli_fetch_array($getFinfo_r,MYSQLI_ASSOC)) {
            if ($getFinfo['type'] == "file" || $getFinfo['type'] == "photo") {
                // ADD FILE FIELDSET ONLY IF FILEINPUT HAS SOMETHING IN IT
                if (!empty($_FILES[$_POST['recordset'] . '_' . $getFinfo['db_name']]['name'])) {
                    $query_q .= ', ' . $getFinfo['db_name'];
                }
            } elseif ($getFinfo['type'] == "custom_url") {
                // If it is custom_url, then add the custom url and the meta info..
                $query_q .= ', ' . $getFinfo['db_name'].', meta_title, meta_description, meta_keywords, search_index';
            } else {
                $query_q .= ', ' . $getFinfo['db_name'];
            }
            $pnum++;
        }
        //START INSERTING VALUES STARING WITH THE USUAL VALUES FIRST
        $query_q .= ")
                        VALUES
                    (
                        '" . $_POST['pluginid'] . "',
                        '" . $_POST['recordset'] . "',
                        '" . $_POST['recordid'] . "',
                        '" . date("Y-m-d H:i:s") . "'
                    ";

        //RUN THE FILE RECORDS LOOP AGAIN SO THAT WE CAN CREATE FILE INPUT NAMES :)
        $getFinfo_q = 'SELECT * FROM
                       cms_records
                       WHERE fieldsetid = "' . $_POST['fieldsetid'] . '"
                       ORDER BY sortorder ASC';
        $getFinfo_r = mysqli_query($con,$getFinfo_q);
        $getFinfo_n = mysqli_num_rows($getFinfo_r);
        $pnum       = 1;
        while ($getFinfo = mysqli_fetch_array($getFinfo_r,MYSQLI_ASSOC)) {

            switch ($getFinfo['type']) {

                case "file":

                    if (isset($_FILES[$_POST['recordset'] . '_' . $getFinfo['db_name']]["name"])) {

                        $fileInput = rand().$_FILES[$_POST['recordset'].'_'.$getFinfo['db_name']]["name"];

                        if (!file_exists('../uploads/')) {
                            mkdir('../uploads/', 0777);
                        }

                        if (!file_exists('../uploads/' . $latest . '/')) {
                            mkdir('../uploads/' . $latest . '/', 0777);
                        }

                        if (!file_exists('../uploads/' . $latest . '/' . $getFinfo['db_name'])) {
                            mkdir('../uploads/' . $latest . '/' . $getFinfo['db_name'], 0777);
                        }

                        move_uploaded_file($_FILES[$_POST['recordset'].'_'.$getFinfo['db_name']]['tmp_name'],'../uploads/' . $latest . '/' . $getFinfo['db_name'] . '/' . $fileInput);

                        $query_q .= ',"' . $fileInput . '"';
                    }

                break;

                case "photo":
                    //IF VALUE IS TYPE FILE THEN MAKE SURE THE $_FILE PARAMETER PRECEEDS THE VALUE
                    //UPLOAD FILE ONLY IF IT HAS BEEN PICKED
                    if (!empty($_FILES[$_POST['recordset'] . '_' . $getFinfo['db_name']]['name'])) {

                        if (!file_exists('../uploads/')) {
                            mkdir('../uploads/', 0777);
                        }

                        if (!file_exists('../uploads/' . $latest . '/')) {
                            mkdir('../uploads/' . $latest . '/', 0777);
                        }

                        if (!file_exists('../uploads/' . $latest . '/' . $getFinfo['db_name'])) {
                            mkdir('../uploads/' . $latest . '/' . $getFinfo['db_name'], 0777);
                        }
                        $filename = pathinfo($_FILES[$_POST['recordset'] . '_' . $getFinfo['db_name']]['name'], PATHINFO_FILENAME);
                        $fileext  = strtolower(pathinfo($_FILES[$_POST['recordset'] . '_' . $getFinfo['db_name']]['name'], PATHINFO_EXTENSION));
                        $filename = strtolower(rand() . $filename);

                        $img = new abeautifulsite\SimpleImage($_FILES[$_POST['recordset'].'_'.$getFinfo['db_name']]['tmp_name']);
                        $img->fit_to_width(300)->save('../uploads/' . $latest . '/' . $getFinfo['db_name'] . '/300xauto_' . $filename . '.' . $fileext);
                        $img->save('../uploads/' . $latest . '/' . $getFinfo['db_name'] . '/' . $filename . '.' . $fileext);

                        move_uploaded_file($_FILES[$_POST['recordset'].'_'.$getFinfo['db_name']]['tmp_name'],'../uploads/' . $latest . '/' . $getFinfo['db_name'] . '/' . $filename . '.' . $fileext);

                        $query_q .= ',"' . $filename . '.' . $fileext . '"';
                    }

                break;

                case "html":

                    $query_q .= ',"' . addslashes(htmlspecialchars($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']])) . '"';

                break;

                case "multiselect":
                case "checkboxes":

                    $vals = '';
                    if (isset($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']])) {

                        $x = 0;
                        foreach ($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']] as $key => $value) {
                            $x++;
                            $vals .= $value . ($x < count($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]) ? ',' : '');
                        }
                    }
                    $query_q .= ',"' . $vals . '"';

                break;

                case "radio":

                    $value = '';
                    if (!empty($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']])) {

                        $value = htmlspecialchars($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]);
                    }
                    $query_q .= ',"' . $value . '"';

                break;

                case "foreignkey":

                    $fkeyOptions = $eqApp->get_field_options($getFinfo['options'], ($pnum - 1));

                    if ($fkeyOptions['record_fkeytype'] == "select" || !isset($fkeyOptions['record_fkeytype'])) {

                        $query_q .= ',"' . htmlspecialchars($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]) . '"';
                    }
                    elseif ($fkeyOptions['record_fkeytype'] == "multiselect") {

                        $vals = '';

                        if (isset($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']])) {
                            $d = 0;
                            foreach ($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']] as $key => $value) {
                                $d++;
                                $vals .= htmlspecialchars($value) . ($d < count($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]) ? ',' : '');
                            }
                        }
                        $query_q .= ',"' . $vals . '"';
                    }

                break;

                case "custom_url":

                    $custom_url = ltrim(rtrim($getFinfo['custom_url'], '/'), '/');
                    $perma = explode('/', $custom_url);

                    $postedCustomUrl = "";
                    $ddnum           = 0;
                    foreach ($perma as $links) {
                        $ddnum++;
                        if (isset($_POST[$links])) {
                            $postedCustomUrl .= $eqApp->permaLink($_POST[$links]) . '/';
                        }
                    }
                    $postedCustomUrl = rtrim($postedCustomUrl, '/');
                    $checkCU_q       = 'SELECT * FROM cms_content
                                        WHERE
                                        custom_url = "' . $postedCustomUrl . '"';
                    $checkCU_r       = mysqli_query($con,$checkCU_q);


                    $query_q .= ',"' . $postedCustomUrl . (mysqli_num_rows($checkCU_r) > 0?rand():'') . '", "'
                                     . $_POST['meta_title'] . '","' . $_POST['meta_description'] . '","' . $_POST['meta_keywords'] . '","' . (isset($_POST['search_index'])?$_POST['search_index']:'yes') . '"';

                break;

                default:
                    //for text,textarea,colorpicker,date,select
                    $query_q .= ',"' . addslashes($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]) . '"';

                break;
            }
            $pnum++;
        }
        $query_q .= ")";
        $done = mysqli_query($con,$query_q) or die(mysqli_error($con));

        if (isset($_POST['pageid'])) {

            $getLatest2_q = 'SELECT * FROM cms_pages_plugins
                             WHERE
                             pageid = "' . $_POST['pageid'] . '"
                             ORDER BY sortorder DESC LIMIT 1';
            $getLatest2_r = mysqli_query($con,$getLatest2_q);
            $latest2      = mysqli_fetch_array($getLatest2_r,MYSQLI_ASSOC);
            $latesttwo    = $latest2['sortorder'] + 1;
            $addToPage    = 'INSERT INTO cms_pages_plugins
                            (
                             pageid,
                             pluginid,
                             recordid,
                             sortorder
                            )
                             VALUES
                            (
                             "' . $_POST['pageid'] . '",
                             "' . $_POST['pluginid'] . '",
                             "' . $latest . '",
                             "' . $latesttwo . '"
                             )';
            mysqli_query($con,$addToPage) or die(mysqli_error($con));
        }

        header('Content-type: application/json');
        //SEND JSON with LATEST ID ADDED AND REDIRECT TO THAT PAGE
        if($done) {
            $a = array(
              'message' => 'success',
              'redirect' => _EQROOT_ . (isset($_POST['pageid'])? 'pages/edit/' . $_POST['pageid'] : 'plugins/edit/' . $_POST['pluginid'] . '?id=' . $latest . '&psid=' . $_POST['plugstrucid'] . ($_POST['recordid'] != 0 ? '&recordid=' . $_POST['recordid'] : "") )
            );
          }else{
            $a = array(
              'message' => 'fail'
            );
          }
          echo json_encode($a);
        exit;
    }

    public function save() {

        global $con;
        $eqApp = new Apps();
        $getFinfo_q  = 'SELECT * FROM cms_records
                       WHERE
                       fieldsetid = "' . $_POST['fieldsetid'] . '"
                       ORDER BY sortorder ASC';
        $getFinfo_r  = mysqli_query($con,$getFinfo_q);
        $getFinfo_n  = mysqli_num_rows($getFinfo_r);
        $pnum        = 1;
        $checkPosted = 0;
        $hasImage    = 0;
        //START INSERT QUERY BEGINNING WITH USUAL STARTING VALUES
        $query_q    = 'UPDATE cms_content SET recordid = "' . $_POST['recordid'] . '", timestamp = "'.date("Y-m-d H:i:s").'", ';
        //WHILE LOOP FOR ADDING THE DATABASE VALUES
        while ($getFinfo = mysqli_fetch_array($getFinfo_r,MYSQLI_ASSOC)) {

            switch ($getFinfo['type']) {

                case "file":

                    if (isset($_FILES[$_POST['recordset'] . '_' . $getFinfo['db_name']]["name"])) {

                        $fileInput = rand().$_FILES[$_POST['recordset'].'_'.$getFinfo['db_name']]["name"];

                        if (!file_exists('../uploads/' . $_POST['id'] . '/')) {
                            mkdir('../uploads/' . $_POST['id'] . '/', 0777);
                        }

                        if (!file_exists('../uploads/' . $_POST['id'] . '/' . $getFinfo['db_name'])) {
                            mkdir('../uploads/' . $_POST['id'] . '/' . $getFinfo['db_name'], 0777);
                        } else {
                            foreach(glob('../uploads/' . $_POST['id'] . '/' . $getFinfo['db_name'] . '/*') as $file) {
                                if(is_dir($file)) {
                                    recursiveRemoveDirectory($file);
                                } else {
                                    unlink($file);
                                }
                            }
                        }

                        move_uploaded_file($_FILES[$_POST['recordset'].'_'.$getFinfo['db_name']]["tmp_name"],'../uploads/' . $_POST['id'] . '/' . $getFinfo['db_name'] . '/' . $filename . '.' . $fileext);

                        $query_q .= ($pnum > 1 ? ", " : " ") . $getFinfo['db_name'] . '="' . $fileInput . '" ';
                        $checkPosted++;
                    }

                break;

                case "photo":

                    if (!empty($_FILES[$_POST['recordset'] . '_' . $getFinfo['db_name']]["name"])) {

                        $hasImage++;

                        if (!file_exists('../uploads/' . $_POST['id'] . '/')) {
                            mkdir('../uploads/' . $_POST['id'] . '/', 0777);
                        }

                        if (!file_exists('../uploads/' . $_POST['id'] . '/' . $getFinfo['db_name'])) {
                            mkdir('../uploads/' . $_POST['id'] . '/' . $getFinfo['db_name'], 0777);
                        } else {
                            foreach(glob('../uploads/' . $_POST['id'] . '/' . $getFinfo['db_name'] . '/*') as $file) {
                                if(is_dir($file)) {
                                    recursiveRemoveDirectory($file);
                                } else {
                                    unlink($file);
                                }
                            }
                        }

                        $filename = pathinfo($_FILES[$_POST['recordset'] . '_' . $getFinfo['db_name']]["name"], PATHINFO_FILENAME);
                        $fileext  = strtolower(pathinfo($_FILES[$_POST['recordset'] . '_' . $getFinfo['db_name']]["name"], PATHINFO_EXTENSION));
                        $filename = strtolower(rand() . $filename);

                        $img = new abeautifulsite\SimpleImage($_FILES[$_POST['recordset'].'_'.$getFinfo['db_name']]['tmp_name']);
                        $img->fit_to_width(300)->save('../uploads/' . $_POST['id'] . '/' . $getFinfo['db_name'] . '/300xauto_' . $filename . '.' . $fileext);
                        $img->save('../uploads/' . $_POST['id'] . '/' . $getFinfo['db_name'] . '/' . $filename . '.' . $fileext);

                        move_uploaded_file($_FILES[$_POST['recordset'].'_'.$getFinfo['db_name']]["tmp_name"],'../uploads/' . $_POST['id'] . '/' . $getFinfo['db_name'] . '/' . $filename . '.' . $fileext);

                        $query_q .= ($checkPosted > 0 ? ", " : " ") . $getFinfo['db_name'] . '="' . $filename . '.' . $fileext . '" ';
                        $checkPosted++;
                    }

                break;

                case "html":

                    $query_q .= ($checkPosted > 0 ? ", " : " ") . $getFinfo['db_name'] . '="' . htmlspecialchars($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]) . '" ';
                    $checkPosted++;

                break;

                case "yesno":

                    $query_q .= ($checkPosted > 0 ? ", " : " ") . $getFinfo['db_name'] . '="' . $_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']] . '" ';
                    $checkPosted++;

                break;

                case "multiselect":
                case "checkboxes":

                    $vals = "";
                    $x    = 0;
                    if (isset($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']])) {

                        foreach ($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']] as $key => $value) {
                            $x++;
                            $vals .= $value . ($x < count($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]) ? "," : "");

                        }
                        $query_q .= ($checkPosted > 0 ? ", " : " ") . $getFinfo['db_name'] . '="' . $vals . '" ';
                        $checkPosted++;
                    }

                break;

                case "radio":

                    $vals = "";
                    $x    = 0;
                    if (!empty($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']])) {

                        $query_q .= ($checkPosted > 0 ? ", " : " ") . $getFinfo['db_name'] . '="' . htmlspecialchars($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]) . '" ';
                        $checkPosted++;
                    }

                    break;

                case "foreignkey":

                    $fkeyOptions = $eqApp->get_field_options($getFinfo['options'], ($pnum - 1));

                    if ($fkeyOptions['record_fkeytype'] == "select" || empty($fkeyOptions['record_fkeytype'])) {

                        $query_q .= ($checkPosted > 0 ? ", " : " ") . $getFinfo['db_name'] . '="' . htmlspecialchars($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]) . '" ';
                        $checkPosted++;

                    } elseif ($fkeyOptions['record_fkeytype'] == "multiselect") {

                        $vals = '';
                        if (isset($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']])) {

                            $d = 0;
                            foreach ($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']] as $key => $value) {
                                $d++;
                                $vals .= htmlspecialchars($value) . ($d < count($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]) ? "," : "");
                            }
                        }
                        $query_q .= ($checkPosted > 0 ? ", " : " ") . $getFinfo['db_name'] . '="' . $vals . '" ';
                        $checkPosted++;
                    }

                break;

                case "custom_url":

                    $custom_url = ltrim(rtrim($getFinfo['custom_url'], '/'), '/');
                    $perma = explode('/', $custom_url);

                    $postedCustomUrl = "";
                    $ddnum           = 0;
                    foreach ($perma as $links) {
                        $ddnum++;
                        if (isset($_POST[$links])) {
                            $postedCustomUrl .= $eqApp->permaLink($_POST[$links]) . '/';
                        }
                    }

                    $postedCustomUrl = rtrim($postedCustomUrl, '/');
                    $checkCU_q       = 'SELECT * FROM cms_content
                                        WHERE
                                        custom_url = "' . $postedCustomUrl . '"
                                        AND id <> "' . $_POST['id'] . '"';
                    $checkCU_r       = mysqli_query($con,$checkCU_q);

                    $query_q .= ($checkPosted > 0 ? ", " : " ") . $getFinfo['db_name'] . '="' . $postedCustomUrl . (mysqli_num_rows($checkCU_r) > 0?rand():'') . '", ' .
                                'meta_title = "' . $_POST['meta_title'] . '", meta_description = "' . $_POST['meta_description'] . '",meta_keywords = "' . $_POST['meta_keywords'] . '",search_index = "' . (isset($_POST['search_index'])?$_POST['search_index']:'yes') . '"';
                    $checkPosted++;

                break;

                default: //for text,textarea,colorpicker,date,select

                    $query_q .= ($checkPosted > 0 ? ", " : " ") . $getFinfo['db_name'] . '="' . htmlspecialchars($_POST[$_POST['recordset'] . '_' . $getFinfo['db_name']]) . '" ';
                    $checkPosted++;

                break;
            }
            $pnum++;
        }
        $query_q .= ' WHERE id = "' . $_POST['id'] . '"';
        mysqli_query($con,$query_q) or die(mysqli_error($con));
        $result = array(
          'r' => 'success',
          'custom_url' => $postedCustomUrl
        );
        echo json_encode($result);
        exit;
    }

    public function deleterecord($id) {

        $eqApp = new Apps();
        global $con;

        $checkRec_q = 'SELECT * FROM cms_content
                       WHERE
                       id = "' . $id . '"';
        $checkRec_r = mysqli_query($con,$checkRec_q);

        if (mysqli_num_rows($checkRec_r) > 0) {
            $deleteR_q = 'DELETE FROM cms_content
                          WHERE
                          id = "' . $id . '"';
            $deleteR_r = mysqli_query($con,$deleteR_q);
            //if there exists a folder for this record delete it and its contents as well using rrmdir()
            if (file_exists('../uploads/' . $id . '/')) {
                $eqApp->rrmdir('../uploads/' . $id . '/');
            }
            header('location:' . _EQROOT_ . 'plugins/view/' . $_GET['pid']);
        } else {
            header('location:' . _EQROOT_);
        }
    }

    public function api($recordset,$api = false) {

        global $con;

        $query_q = 'SELECT r.db_name "name"
                    FROM cms_records r, cms_plugin_structure p, cms_fieldsets f
                    WHERE r.fieldsetid = f.id
                    AND f.com_struc_id = p.id
                    AND p.db_name =  "' . $recordset . '"';
        $query_r = mysqli_query($con,$query_q);
        $recordId = array();
        while($d = mysqli_fetch_array($query_r,MYSQLI_ASSOC)) {
             $recordId[] = $d['name'];
        }
        $recordId[] = 'id';
        $recordId[] = 'pluginid';
        $recordId[] = 'recordset';
        $recordId[] = 'recordid';
        $recordId[] = 'sortorder';
        $recordId[] = 'timestamp';
        $recordId[] = 'search_index';
        $recordId[] = 'meta_title';
        $recordId[] = 'meta_description';
        $recordId[] = 'meta_keywords';

        //setup paging page number and starting from positions
        if($_GET['per_page']) {
            $per_page = $_GET['per_page'];
        }else{
            $per_page = 4;
        }
        if (isset($_GET['curr_page'])){
            $page = intval($_GET['curr_page']);
            if($page < 1) $page = 1;
        } else {
            $_GET['curr_page'] = 1;
            $page=1;
        }
        $start_from = ($page - 1) * $per_page;

        $query2_q = 'SELECT SQL_CALC_FOUND_ROWS * FROM cms_content WHERE recordset = "' . $recordset .'"';
        if(isset($_GET)){
            foreach($_GET as $key => $val){

                if($key != 'url' && in_array($key, $recordId)) {
                    $query2_q .= ' AND ' . $key . ' = "' . $val . '"';
                }
            }
        }
        if($_GET['order_by']) {
            $query2_q .= ' ORDER BY ' . $_GET['order_by'];
        }
        $query2_q .= ' LIMIT ' . $start_from.', ' . $per_page;
        $query2_r = mysqli_query($con,$query2_q);
        $data = array();
        while($query2 = mysqli_fetch_array($query2_r,MYSQL_ASSOC)){
            $d = array();
            $d['id'] = $query2['id'];
            foreach($recordId as $record) {
                $d[$record] = $query2[$record];
            }
            array_push($data,$d);
        }
        if($api) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    public function sortplugin() {
        global $con;
        //Ajax sortable for order of plugin
        $action             = mysqli_real_escape_string($con,$con,$_POST['action']);
        $updateRecordsArray = $_POST['recordsArray'];

        if ($action == "updateRecordsListings") {
            $listingCounter = 1;
            foreach ($updateRecordsArray as $recordIDValue) {
                $query = 'UPDATE cms_content
                          SET sortorder = "' . $listingCounter . '"
                          WHERE id = "' . $recordIDValue . '"';
                mysqli_query($con,$query) or die(mysqli_error($con));
                $listingCounter++;
            }
        }
        exit;
    }
}
