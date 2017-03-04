<?php

class Pages_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    /*
    Below function fetches all categories
    and pages in each category and subpages of each page
    places it into an array and returns to controller
    */
    public function fetchAll() {

        global $con;

        $pagesc_q = 'SELECT * FROM cms_pages_cats ORDER BY id ASC';
        $pagesc_r = mysqli_query($con,$pagesc_q);
        $pagecats = array();
        while ($pagesc = mysqli_fetch_array($pagesc_r,MYSQLI_ASSOC)) {
            //create main array
            $pagecat         = array();
            $pagecat['id']   = $pagesc['id'];
            $pagecat['name'] = $pagesc['name'];
            //get pages from each page cat that have no parent pages
            $query           = 'SELECT * FROM cms_pages
                      					WHERE
                      					catid = "' . $pagesc['id'] . '"
                      					AND parentid = "0"
                      					ORDER BY sortorder ASC';
            $result          = mysqli_query($con,$query);
            $allpages        = array();

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                //create new array for pages to merge with main array
                $pages           = array();
                //populate pages array
                $pages['id']          = $row['id'];
                $pages['status']      = $row['pagestatus'];
                $pages['title']       = $row['title'];
                $pages['page_url']    = $row['page_url'];
                $pages['pagestatus']  = $row['pagestatus'];

                $query2          = 'SELECT * FROM cms_pages
                          					WHERE
                          					parentid = "' . $row["id"] . '"
                          					ORDER BY sortorder ASC';
                $result2         = mysqli_query($con,$query2);
                //if there is a sub page ...
                if (mysqli_num_rows($result2) > 0) {
                    $allsubpages = array();
                    while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
                        //create subpage array
                        $subpages                = array();
                        $subpages['id']          = $row2['id'];
                        $subpages['status']      = $row2['pagestatus'];
                        $subpages['title']       = $row2['title'];
                        $subpages['page_url']    = $row2['page_url'];
                        $subpages['pagestatus']  = $row2['pagestatus'];
                        //push array to parent page array
                        array_push($allsubpages, $subpages);
                    } //end while
                    $pages['subpages'] = $allsubpages;
                } //end mysqlnumrows;
                //push array of parent pages to main array
                array_push($allpages, $pages);
            } //end while parentid=0
            $pagecat['pages'] = $allpages;
            array_push($pagecats, $pagecat);
        } //end while all cats

        return $pagecats;
    }

    /*
    Below function adds a new page
    */
    public function add() {

        global $con;

        $eqApp = new Apps();

        $getnumPages_q = 'SELECT * FROM cms_pages
            						  WHERE
            						  catid = "' . $_POST['catid'] . '"';
        $getnumPages_r = mysqli_query($con,$getnumPages_q);
        $getnumPages_n = mysqli_num_rows($getnumPages_r);
        $sortorder     = $getnumPages_n + 1;

        $uri        = $eqApp->permaLink($_POST['title']);
        $checkuri_q = 'SELECT * FROM cms_pages
                       WHERE
                       page_url = "' . $uri . '"';
        $checkuri_r = mysqli_query($con,$checkuri_q);
        if (mysqli_num_rows($checkuri_r) == 1) {

            $uri = $uri . '-' . rand();
        } else {

            $uri = $uri;
        }
        //Create query for adding a new page NOTE! New page will automatically be set to 'live'
        $insertP_q = 'INSERT INTO cms_pages
            					 (
            						parentid,
            						catid,
            						page_url,
            						pagestatus,
            						title,
            						sortorder,
            						search_index
            					  )
            					 VALUES
            					 (
            					 	"' . $_POST['parentid'] . '",
            					 	"' . $_POST['catid'] . '",
            					 	"' . $uri . '",
            					 	"live",
            					 	"' . $_POST['title'] . '",
            					 	"' . $sortorder . '",
            					 	"yes"
            					 )';
        mysqli_query($con,$insertP_q);

        header('Location:' . _EQROOT_ . 'pages');
    }

    /*
    Below function loads a page with
    id given
    */
    public function edit($id) {
        //Create query for getting the page by id
        global $con;
        $query_q = 'SELECT * FROM cms_pages
              			WHERE
              			id = "' . $id . '" ';
        $query   = mysqli_query($con,$query_q) or die(mysql_error());
        if (mysqli_num_rows($query) == 0) {
            //re-route if the id doesnt exist
            header('Location:' . _EQROOT_ . 'pages');
        }
        $row = mysqli_fetch_array($query, MYSQLI_ASSOC);

        return $row;
    }

    /*
    Below function saves a page in an edit function
    */
    public function savepage() {

        global $con;

        $eqApp = new Apps();

        if (isset($_POST['title'])) {

            if (!empty($_POST['page_url'])) {
                //if the page url is not empty ...meaning one has already been set then set up a
                //custom url and check if it exists amungst other pages
                $uri        = $eqApp->permaLink($_POST['title']);
                $checkuri_q = 'SELECT * FROM cms_pages
                      			   WHERE
                      			   page_url = "' . $uri . '"
                      			   AND id <> "' . $_POST['id'] . '"';
                $checkuri_r = mysqli_query($con,$checkuri_q);
                if (mysqli_num_rows($checkuri_rm) == 1) {
                    $uri = $uri . '-' . rand();
                } else {
                    $uri = $uri;
                }
            } else {

                $uri        = $eqApp->permaLink($_POST['title']);
                $checkuri_q = 'SELECT * FROM cms_pages
                      			   WHERE
                      			   page_url = "' . $uri . '"
                      			   AND id <> "' . $_POST['id'] . '"';
                $checkuri_r = mysqli_query($con,$checkuri_q);
                if (mysqli_num_rows($checkuri_r) == 1) {
                    $uri = $uri . '-' . rand();
                } else {
                    $uri = $uri;
                }
            }
            //Create query for the edit page
            $updateP_q = 'UPDATE cms_pages
            						  SET page_url = "' . $uri . '",
            						  pagestatus = "' . $_POST['pagestatus'] . '",
            						  title = "' . $_POST['title'] . '",
            						  search_index = "' . $_POST['search_index'] . '",
            						  meta_title = "' . $_POST['meta_title'] . '",
            						  meta_description = "' . $_POST['meta_description'] . '",
            						  meta_keywords = "' . $_POST['meta_keywords'] . '",
            						  parentid = "' . $_POST['parentid'] . '",
            						  catid = "' . $_POST['catid'] . '",
            						  redirect = "' . $_POST['redirect'] . '"
            						  WHERE
            						  id = "' . $_POST['id'] . '"';

            $updateP_r = mysqli_query($con,$updateP_q) or die(mysqli_error($con));

            //Make sure all sub pages of this page change their cat id's in case this page's ids have been changed
            $subP_q = 'UPDATE cms_pages
					   SET catid = "' . $_POST['catid'] . '"
					   WHERE
					   parentid = "' . $_POST['id'] . '"';

            $subP_r = mysqli_query($con,$subP_q);
            //print the new uri of the page so that it outputs on the text-input of the page edit
            echo $uri;
            exit;
        }
    }


    /*
    Below function fetches parent page via category Id
    */
    public function getParentPages($catId) {

        global $con;

        $query_q = 'SELECT * FROM cms_pages
          					WHERE
          					parentid = "0" AND
          					catid = "' . $catId . '" AND
          					id <> "' . $catId . '"
          					ORDER by title DESC';
        $query_r = mysqli_query($con,$query_q);
        $pages = array();
        while ($row = mysqli_fetch_array($query_r,MYSQLI_ASSOC)) {
            $page          = array();
            $page['id']    = $row['id'];
            $page['title'] = $row['title'];
            array_push($pages, $page);
        }
        return $pages;
    }


    /*
    Function to get all page categires
    */
    public function pagecats() {

        global $con;
        //List all categories
        $query_q = 'SELECT * FROM cms_pages_cats
          					ORDER BY
          					name DESC';
        $query_r = mysqli_query($con,$query_q);
        $pagecats = array();
        while ($row = mysqli_fetch_array($query_r,MYSQLI_ASSOC)) {
            $cat         = array();
            $cat['id']   = $row['id'];
            $cat['name'] = $row['name'];
            array_push($pagecats, $cat);
        }
        return $pagecats;
    }

    /*
    Function that adds a new category
    */
    public function addcat() {

        $eqApp = new Apps();
        //Add new Page category
        if (isset($_POST['name'])) {
            $eqApp->insertSql("cms_pages_cats");
            header('Location:' . _EQROOT_ . 'pages/cats');
        }
    }

    /*
    Function for edit category. If there is a $_POST,
    then it will save the items, ther wise it will
    return a row of selected category by id.
    */
    public function editcat($id) {
        global $con;

        $eqapp = new Apps();

        if (isset($_POST['name'])) {
            header('Content-type: application/json');
            $getCat_q = 'SELECT * FROM cms_pages_cats
						 WHERE
						 name = "' . $_POST['name'] . '"
						 AND id <> "' . $_POST['id'] . '"';
            $getCat_r = mysqli_query($con,$getCat_q);
            if (mysqli_num_rows($getCat_r) > 0) {
                echo '{ "message": "error" }';
            } else {
                $eqapp->udpdateSql("cms_pages_cats");
                echo '{ "message": "success" , "id": "' . $_POST['id'] . '"  }';
            }
            exit;

        } else {

            $query_q = 'SELECT * FROM
						cms_pages_cats
						WHERE
						id = "' . $id . '" ';
            $query   = mysqli_query($con,$query_q);
            if (mysqli_num_rows($query) == 0) {

                header('Location:' . _EQROOT_ . 'pages/cats');
            }
            $row = mysqli_fetch_array($query,MYSQLI_ASSOC);
            return $row;
        }
    }


    /*
    Deleting Category by category id
    Only will delete if the category doesnt have
    an id = 1; This enables there to always be at least one category
    similar to pages
    */
    public function deletecat($id) {

        if ($id != 1) {
            global $con;
            $query_q = 'UPDATE cms_pages
              					SET
              					catid="1"
              					WHERE
              					catid = "' . $id . '" ';
            $query   = mysqli_query($con,$query_q);
            $query_q = 'DELETE FROM cms_pages_cats
            						WHERE
            						id = "' . $id . '" ';
            $query   = mysqli_query($con,$query_q);
        }
        header('Location:' . _EQROOT_ . 'pages/cats');
    }

    public function getparent() {

        global $con;

        echo "\n<option value=\"0\">None</option>";
        $getPC_q = 'SELECT * FROM cms_pages
          					WHERE
          					catid = "' . $_POST['id'] . '"
          					AND parentid = "0" ORDER BY sortorder ASC';
        if (isset($_POST['currpage'])) {
            $getPC_q .= 'AND id <> "' . $_POST['currpage'] . '"';
        }
        $getPC_r = mysqli_query($con,$getPC_q);
        while ($getPC = mysqli_fetch_array($getPC_r,MYSQLI_ASSOC)) {
            echo "\n<option value=\"" . $getPC['id'] . "\">" . $getPC['title'] . "</option>";
        }
        exit;
    }

    public function add_component() {
        global $con;
        $getC_q = 'SELECT * FROM cms_plugin_structure
        				   WHERE
        				   pluginid = "' . $_POST['cid'] . '"
        				   AND parentid = "0"
        				   ORDER BY sortorder
        				   ASC LIMIT 1';
        $getC_r = mysqli_query($con, $getC_q);
        $getC   = mysqli_fetch_array($getC_r,MYSQLI_ASSOC);

        $getR_q = 'SELECT * FROM cms_content WHERE pluginid = "' . $_POST['cid'] . '" AND recordset = "' . $getC['db_name'] . '"';
        $getR_r = mysqli_query($con,$getR_q);
	        echo "<br />";
        echo "<table width=\"100%\"><tr><td><h5>Add " . $getC['recordname'] . "</h5></td><td>\n";
        echo "<select align=\"left\" id=\"existingRecord\">";

        while ($getR = mysqli_fetch_array($getR_r,MYSQLI_ASSOC)) {

        	echo "\n<option value=\"".$getR['id']."\">" . $getR['name'] . "</option>";
        }
        echo "\n</select>\n";
        echo "</td></tr></tr>\n";
        echo "<script>\n";
		    echo "function save() {\n
      			$.ajax({
      				type: \"POST\",
      				url: \"" . _EQROOT_ . "pages/add_component_to_page\",
      				data: {
      					\"cid\": \"" . $_POST['cid'] . "\",
      					\"pageid\": \"" . $_POST['pageid'] . "\",
      					\"recordid\": $(\"#existingRecord\").val()
      				},success: function (data) {
      					$(\"#componentContent\").load(\"" . _EQROOT_ . "pages/show_pages_contents?pageid=" . $_POST['pageid'] . "\");
      					$(\"#existingComponents\").load(\"" . _EQROOT_ . "pages/existing_content_select?pageid=" . $_POST['pageid'] . "\");
      					$(\"#dialog3\").dialog(\"close\");
      					$(\"#dialog3\").html(\"\");
      				},
      				error: function(error) {
      					alert(error);
      				}
      			});
      		}
      		</script>";

        exit;

    }
    /*
    Below function adds a component to the page
    */
    public function add_component_to_page() {

        global $con;

        $getL_q = 'SELECT * FROM
        				   cms_pages_plugins
        				   WHERE pageid = "' . $_POST['pageid'] . '"';
        $getL_q = mysqli_query($con,$getL_q);
        $getL   = mysqli_num_rows($getL_q) + 1;
        $addC_q = 'INSERT INTO cms_pages_plugins (pageid,pluginid';
        if (isset($_POST['recordid'])) {
            $addC_q .= ',recordid';
        }
        $addC_q .= ',sortorder) VALUES ("' . $_POST['pageid'] . '","' . $_POST['cid'] . '"';
        if (isset($_POST['recordid'])) {
            $addC_q .= ',"' . $_POST['recordid'] . '"';
        }
        $addC_q .= ',"' . $getL . '")';
        mysqli_query($con,$addC_q);

        exit;

    }

    public function show_pages_contents($pageId) {

        global $con;

        $str = '';
        //show all pages components
        $getpageComp_q = 'SELECT * FROM cms_pages_plugins WHERE pageid = "' . $pageId . '" ORDER BY sortorder ASC';
        $getpageComp_r = mysqli_query($con,$getpageComp_q);
        while ($gpc = mysqli_fetch_array($getpageComp_r,MYSQLI_ASSOC)) {
            $getC_q  = mysqli_query($con,'SELECT * FROM cms_plugins WHERE id = "' . $gpc['pluginid'] . '"');
            $getC    = mysqli_fetch_array($getC_q,MYSQLI_ASSOC);
            $getR_q  = mysqli_query($con,'SELECT * FROM cms_content WHERE id = "' . $gpc['recordid'] . '"');
            $getR    = mysqli_fetch_array($getR_q,MYSQLI_ASSOC);
            $getCS_q = mysqli_query($con,'SELECT * FROM cms_plugin_structure WHERE db_name = "' . $getR['recordset'] . '"');
            $getCS   = mysqli_fetch_array($getCS_q,MYSQLI_ASSOC);

			$str .= "<div class=\"pagesComponents\" id=\"recordsArray_".$gpc['id']."\"><span class=\"handle\"><i class=\"fa fa fa-bars fa-1x\"></i></span>\n";
            $str .= "<a href=\"" . _EQROOT_ . "plugins/" . ($getC['type']=='single' ? "view/" . $getC['id'] : "edit/" . $getC['id'] . "?id=" . $getR['id'] . "&psid=" . $getCS['id'] . "&pageid=" . $pageId) . "\" class=\"name\">" . $getC['name'] . ($getC['type'] == 'multi' ? ": " . $getR['name'] : "") . "</a>\n";
            $str .= "<a class=\"delete\" onclick=\"removepagecontent(" . $gpc['id'] . "," . $pageId . ")\"><i class=\"fa fa-minus-square fa-1x\"></i></a></div>\n";
	    }
        return $str;
        exit;
    }

    public function new_content_select($pageId) {

        global $con;

        $comp_q = 'SELECT * FROM cms_plugins
                   WHERE
                   type <> "static"
                   AND type <> "single"
                   ORDER BY id ASC';
        $comp_r = mysqli_query($con,$comp_q);
        $str  = "<select style=\"margin-top:5px;\" onchange=\"document.location.href=this.value\">";
        $str .= "\n<option value=\"0\">Choose Content</option>";
            while($comp = mysqli_fetch_array($comp_r,MYSQLI_ASSOC)){
                $getFirstComp_q = 'SELECT * FROM cms_plugin_structure
                                   WHERE
                                   pluginid = "'.$comp['id'].'"
                                   AND parentid = "0"
                                   ORDER BY sortorder ASC LIMIT 1';
                $getFirstComp_r = mysqli_query($con,$getFirstComp_q);
                $getFirstComp = mysqli_fetch_array($getFirstComp_r,MYSQLI_ASSOC);
                $str .= "\n<option value=\"" . _EQROOT_. "plugins/add/" . $comp['id'] . "?psid=" . $getFirstComp['id'] . "&pageid=" . $pageId . "\">" . $getFirstComp['recordname'] . "</option>";
            }
        $str .= "\n</select>\n";
        return $str;
    }

    public function existing_content_select($pageId) {
        global $con;
        //Populate select for existing components
        $comp_q = 'SELECT * FROM cms_plugins
        				   WHERE type <> "static" ORDER BY id ASC';
        $comp_r = mysqli_query($con,$comp_q);
        $str  = "<select style=\"margin-top:5px;\" onchange=\"modalRun(this.value," . $pageId . ")\">";
        $str .= "\n<option value=\"0\">Choose Content</option>";
        while ($comp = mysqli_fetch_array($comp_r,MYSQLI_ASSOC)) {
            $getFirstComp_q = 'SELECT * FROM cms_plugin_structure
                               WHERE
                               pluginid = "'.$comp['id'].'"
                               AND parentid = "0"
                               ORDER BY sortorder ASC LIMIT 1';
            $getFirstComp_r = mysqli_query($con,$getFirstComp_q);
            $getFirstComp = mysqli_fetch_array($getFirstComp_r,MYSQLI_ASSOC);
            if ($comp['type'] == "single") {
                $getUsed_q = 'SELECT * FROM cms_pages_plugins
                              WHERE
                              pluginid = "' . $comp['id'] . '"';
                $getUsed_r = mysqli_query($con,$getUsed_q);
                $getUsed   = mysqli_num_rows($getUsed_r);
                if ($getUsed == 0) {
                    $str .= "\n<option value=\"" . $comp['id'] . "," . $comp['type'] . "\">" . $getFirstComp['recordname'] . "</option>";
                }
            } else {
                $str .= "\n<option value=\"" . $comp['id'] . "," . $comp['type'] . "\">" . $getFirstComp['recordname'] . "</option>";
            }

        }
        $str .= "\n</select>\n";
        return $str;
        exit;
    }

    public function deletepage($id) {
        global $con;
        //Delete page id
        if (isset($id) && $id != 1) {
            $updateP_q = 'UPDATE cms_pages
            						  SET parentid = "0"
            						  WHERE parentid = "' . $id . '"';
            $updateP_r = mysqli_query($con,$updateP_q);
            $deleteP_q = 'DELETE FROM cms_pages
						              WHERE id = "' . $id . '"';
            $deleteP_r = mysqli_query($con,$deleteP_q);
        }
        header('Location:' . _EQROOT_ . 'pages');
    }

    public function deletecontent() {
        global $con;
        //Delete Page Content
        $query_q = 'DELETE FROM cms_pages_plugins
					         WHERE id = "' . $_POST['pcid'] . '"';
        $query   = mysqli_query($con,$query_q);
        exit;
    }

    public function sortpages() {

        global $con;
        //Ajax sortable for order of pages
        $action             = mysqli_real_escape_string($con,$_POST['action']);
        $updateRecordsArray = $_POST['recordsArray'];

        if ($action == "updateRecordsListings") {
            $listingCounter = 1;
            foreach ($updateRecordsArray as $recordIDValue) {
                $query = 'UPDATE cms_pages
            						  SET sortorder = "' . $listingCounter . '"
            						  WHERE id = "' . $recordIDValue . '"';
                mysqli_query($con,$query) or die(mysql_error());
                $listingCounter++;
            }
        }
        exit;
    }

    public function sortcomp() {

        global $con;
        //Ajax sortable for pages components on a specific page
        $action             = mysqli_real_escape_string($con,$_POST['action']);

        $updateRecordsArray = $_POST['recordsArray'];

        if ($action == "updateRecordsListings") {
            $listingCounter = 1;
            foreach ($updateRecordsArray as $recordIDValue) {
                $query = 'UPDATE cms_pages_plugins
          					      SET sortorder = "' . $listingCounter . '"
          					      WHERE id = "' . $recordIDValue . '"';
                mysqli_query($con,$query) or die(mysql_error());
                $listingCounter++;
            }
        }
        exit;
    }

    public function MultipleRecordsToPage() {

        global $con;

        $comp_q = 'SELECT * FROM cms_plugins
                   WHERE
                   type = "multiple" ORDER BY id ASC';
        $comp_r = mysqli_query($con,$comp_q);
    }

}
