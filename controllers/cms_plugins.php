<?php

/*
 * The Cms_Plugins Controller is used to control
 * the Cms_plugins page which controls all the structures,
 * fieldsets and records relationships 
 */
class Cms_plugins extends Controller {

	function __construct() {
		parent::__construct();
	}

	/*
	 * Controller for Cms Plugins Index page
	 * Holds all Plugins as a list format.
	 */
	public function index() {
		
		$this->view->appendJsToHead('scripts/cms_plugins/indexPage.php');
		$this->view->title = 'Plugins';
		//Fetches all plugin informations
		$this->view->pluginInfo = $this->model->index();
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('cms_plugins/index');
		$this->view->render('footer');	
	}
	
	/*
	 * Controller for adding a new plugin
	 * as well as rending the form
	 */
	public function add() {
		
		//If there is a post then add a new Plugin
		if (isset($_POST['name'])) {
			
			$this->model->add();
		} else {
			//Render the page for add form	
			$this->view->title = 'Add Plugin';
			$this->view->render('header');
			$this->view->render('topnav');
			$this->view->render('menu');
			$this->view->render('cms_plugins/add');
			$this->view->render('footer');
		}
	
	}
	
	/*
	 * Controller for editing an existing plugin
	 * the plugin's id is passed as $id via url
	 * and using various functions we build an array 
	 * helps render the plugin structure's GUI.
	 */
	public function edit($id) {

		$pluginsBuilder = new Cms_Plugins_Builder();
		//Fetch Plugin info using the edit function from the model
		
		$this->view->pluginInfo = $this->model->edit($id);
		//Build the array which has all the plugin structures using the below two functions
		$structureArray = $pluginsBuilder->structureArray($id,0);
		$this->view->pluginStructure = $pluginsBuilder->buildStructure($id,$structureArray);

		$this->view->appendJsToHead('scripts/cms_plugins/editCmsPlugin.php');

		$this->view->title = 'Edit Plugin "'.$this->view->pluginInfo['name'].'"';
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('cms_plugins/edit');
		$this->view->render('footer');
	}
	
	/*
	 * Controller for deleting an existing plugin
	 */
	public function delete($id){
		
		$this->model->delete($id);
	}

	/*
	 * The below basically renders the GUI for plugin 
	 * structure again as this will be rendered via AJAX 
	 * after completing any edits or deletes or saves 
	 * to any records, fieldsets or plugin structures
	 */
	public function show_plugin_structure() {

		$pluginsBuilder = new Cms_Plugins_Builder();
			
		$structureArray = $pluginsBuilder->structureArray($_GET['pluginid'],0);
		$pluginStructure = $pluginsBuilder->buildStructure($_GET['pluginid'],$structureArray);
		echo $pluginStructure;
	}

	/*
	 * Below function generates the form in the popup modal
	 * for adding a new plugin structure
	 */
	public function add_plugin_structure() {

		$this->view->structureInfo = $this->model->fetchPluginStructureParents($_POST['pluginid'],0);
		$this->view->pluginId = $_POST['pluginid'];
		$this->view->render('cms_plugins/cms_plugins_ajax/_addPluginStructure');
	}

	/*
	 * Below function generates the form in the popup modal
	 * for editing an existing plugin structure
	 */
	public function edit_plugin_structure() {

		$this->view->structureInfo = $this->model->fetchPluginStructureParents($_POST['pluginid'],$_POST['plugstruid']);
		$this->view->pluginStructureInfo = $this->model->pluginStructureInfo($_POST['pluginid'],$_POST['plugstruid']);
		$this->view->render('cms_plugins/cms_plugins_ajax/_editPluginStructure');
	}

	/*
	 * Below function adds a new plugin structure via 
	 * the CmsPluginsFunctions Helper
	 */
	public function add_plugin_structure_add() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->add_plugin_structure_add();
	}
	
	/*
	 * Below function saves an existing plugin structure via 
	 * the CmsPluginsFunctions Helper
	 */
	public function save_plugin_structure() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->save_plugin_structure();
	}
	
	/*
	 * Below function deletes an existing plugin structure via 
	 * the CmsPluginsFunctions Helper
	 */
	public function delete_plugin_structure() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->delete_plugin_structure();
	}
	
	/*
	 * Below function simply renders the add new fieldset form
	 */
	public function add_fieldset_structure() { 

		$this->view->render('cms_plugins/cms_plugins_ajax/_addFieldsetStructure');
	}
	
	/*
	 * Below function simply renders the edit existing fieldset form
	 * It gets the fieldset info from the Cms_plugins_model class 
	 * and sends it to the view in an array
	 */
	public function edit_fieldset_structure() {
		
		$this->view->fieldsetInfo = $this->model->fieldsetInfo($_POST['pluginid'],$_POST['plugstrcuid'],$_POST['fid']);
		$this->view->render('cms_plugins/cms_plugins_ajax/_editFieldsetStructure');
	}

	/*
	 * Below function adds a new fieldset structure  via 
	 * the CmsPluginsFunctions Helper
	 */
	public function add_fieldset_structure_add() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->add_fieldset_structure_add();
	}

	/*
	 * Below function saves an existing fieldset structure via 
	 * the CmsPluginsFunctions Helper
	 */
	public function save_fieldset_structure() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->save_fieldset_structure();
	}

	/*
	 * Below function deletes an existing fieldset structure via 
	 * the CmsPluginsFunctions Helper
	 */
	public function delete_fieldset_structure() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->delete_fieldset_structure();
	}	
	
	
	/*
	 * Below function simply renders the add new recordset form
	 */
	public function add_record_structure() {

		$this->view->render('cms_plugins/cms_plugins_ajax/_addRecordStructure');
	}

	/*
	 * Below function simply renders the edit existing fieldset form
	 * It gett the recordset info from the Cms_plugins_model class 
	 * and sends it to the view in an array
	 */
	public function edit_record_structure() {
		
		$this->view->recordsetInfo = $this->model->recordsetInfo($_POST['pluginid'],$_POST['plugstrcuid'],$_POST['fid'],$_POST['rid']);
		$this->view->render('cms_plugins/cms_plugins_ajax/_editRecordStructure');
	}	

	/*
	 * Below function adds a new recordset structure via 
	 * the CmsPluginsFunctions Helper
	 */
	public function add_record_structure_add() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->add_record_structure_add();
	}

	/*
	 * Below function saves an existing recordset structure via 
	 * the CmsPluginsFunctions Helper
	 */
	public function save_record_structure() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->save_record_structure();
	}

	/*
	 * Below function delets an existing recordset structure via 
	 * the CmsPluginsFunctions Helper
	 */
	public function delete_record_structure() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->delete_record_structure();
	}

	/*
	 * Below function is for the ajax call for drag and drop
	 * sorting of the plugins
	 */
	public function sort_plugin() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->sort_plugin();
	}

	/*
	 * Below function is for the ajax call for drag and drop
	 * sorting of the plugin structure
	 */
	public function sort_plugin_structure() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->sort_plugin_structure();
	}

	/*
	 * Below function is for the ajax call for drag and drop
	 * sorting of the plugin structure
	 */
	public function sort_record() {

		$cmsPluginsFunctions = new CmsPluginsFunctions();
		
		$cmsPluginsFunctions->sort_record();
	}	

    public function export($id) {

        $cmsPluginsBuilder = new Cms_Plugins_Builder();

        echo $cmsPluginsBuilder->structureArray($id,1);
    }

    public function import($id) {

    	$cmsPluginsBuilder = new Cms_Plugins_Builder();

    	$cmsPluginsBuilder->importStructure($id,$_POST['importstring']);
			
		$structureArray = $cmsPluginsBuilder->structureArray($id,0);
		$pluginStructure = $cmsPluginsBuilder->buildStructure($id,$structureArray);

		echo $pluginStructure;
    }
}