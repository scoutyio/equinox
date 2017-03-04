<?php

class Plugins extends Controller {
    
    function __construct() {
        
        parent::__construct();
    }
    
    public function index() {
        //index should not be reached ever
        header('location:' . _EQROOT_);
    }
    
    public function view($id) {

        $this->view->appendJsToHead('scripts/plugins/listPlugin.php');
        //validates plugin id to check if it exists
        $pluginInfo = $this->model->valPlugin($id);

        $pluginListBuilder    = new PluginListBuilder();
        $this->view->html     = $pluginListBuilder->renderBody($id);
        $this->view->pluginId = $id;
        $this->view->title    = $pluginInfo['name'].' plugin';
        
        $this->view->render('header');
        $this->view->render('topnav');
        $this->view->render('menu');
        $this->view->render('plugins/main');
        $this->view->render('footer');
    }
    
    public function add($pluginId) {

        //Validate both plugin id and structure to see if they are related
        //if not then shoot back to the plugin id   
        $pluginFormBuilder   = new PluginFormBuilder();
        $pluginListBuilder   = new PluginListBuilder();
        $cmsPluginsFunctions = new CmsPluginsFunctions();

        $plugindetail = $pluginListBuilder->singleStrucArray($_GET['psid']);

        $this->model->valStructure($pluginId, $_GET['psid']); 
        //Build the form simply using plugin id and component structure id 
        $this->view->renderForm = $pluginFormBuilder->renderPluginForm($pluginId, $_GET['psid'],'');
        $hastiny                = $cmsPluginsFunctions->has_tinymce($_GET['psid']);
        
        $this->view->appendJsToHead('scripts/plugins/addNewPlugin.php?pluginId='.$pluginId.'&psid='.$_GET['psid'].($hastiny==true?'&tiny=true':''));

        $this->view->title = 'Add ' . $plugindetail['recordname'];
        $this->view->render('header');
        $this->view->render('topnav');
        $this->view->render('menu');
        $this->view->render('plugins/add');
        $this->view->render('footer');
    }

    public function edit($pluginId) {

        //Validate plugin id, structure and the record id to see if they are related
        //if not then shoot back to the plugin id
        $pluginFormBuilder   = new PluginFormBuilder();
        $pluginListBuilder   = new PluginListBuilder();
        $cmsPluginsFunctions = new CmsPluginsFunctions();

        $plugindetail = $pluginListBuilder->singleStrucArray($_GET['psid']);
        
        $this->model->valRecord($pluginId, $_GET['psid'], $_GET['id']); 
        //Build the form simply using plugin id and component structure id 
        $this->view->renderForm = $pluginFormBuilder->renderPluginForm($pluginId, $_GET['psid'], $_GET['id']);
        $hastiny                = $cmsPluginsFunctions->has_tinymce($_GET['psid']);

        $this->view->appendJsToHead('scripts/plugins/savePlugin.js?pid='.$pluginId.'&psid='.$_GET['psid'].'&id='.$_GET['id'].($hastiny==true?'&tiny=true':''));

        $this->view->title = 'Edit ' . $plugindetail['recordname'];
        $this->view->render('header');
        $this->view->render('topnav');
        $this->view->render('menu');
        $this->view->render('plugins/edit');
        $this->view->render('footer');
    }

    public function ajaxRenderform($pluginId) {

        $pluginFormBuilder   = new PluginFormBuilder();

        $query_q    = 'SELECT * FROM cms_fieldsets WHERE com_struc_id = "'.$_POST['psid'].'"';
        $query_r    = mysql_query($query_q);
        $row        = mysql_fetch_array($query_r);
        $renderForm = $pluginFormBuilder->formBody($_POST['psid'], $row['id'], $_POST['id']);
        echo $renderForm;
    }

    public function pluginListTableBody() {
        
        $pluginListBuilder = new PluginListBuilder();

        $pluginListBuilder->generateTableBody($_POST['pluginid'], $_POST['strucid'], $_POST['perpage'], $_POST['page'], $_POST['recordid']);
    }

    public function addNew() {

        $this->model->addNew();
    }

    public function save() {

        $this->model->save();
    }

    public function deleterecord($id) {
        
        $this->model->deleterecord($id);
    }

    public function removerecordfile($id) {

        $cmsPluginsFunctions = new CmsPluginsFunctions();

        $cmsPluginsFunctions->removerecordfile($id, $_POST['recordname'], $_POST['filename']);
    }

    public function multipleupload() {

        var_dump($_FILES);
    }

    public function api($recordset,$api=false) {
        $this->model->api($recordset,1);
    }

    public function sortplugin() {
        $this->model->sortplugin();
    }
}