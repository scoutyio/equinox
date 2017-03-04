<?php

class Pages extends Controller {

	function __construct() {

		parent::__construct();
	}
	
	public function index() {
		
		$this->view->appendJsToHead('scripts/pages/indexPage.php');
		$this->view->pageList = $this->model->fetchAll();


			$this->view->title = 'Pages';
			
			$this->view->render('header');
			$this->view->render('topnav');
			$this->view->render('menu');
			$this->view->render('pages/index');
			$this->view->render('footer');	
	}
	
	public function add() {
		
		if(isset($_POST['title'])) {
			
			$this->model->add();
		} else {
			
        	$this->view->appendJsToHead('scripts/pages/addPage.php');
			$this->view->pageCats = $this->model->pagecats();
			$this->view->title = 'Add Page';
	
			$this->view->render('header');
			$this->view->render('topnav');
			$this->view->render('menu');
			$this->view->render('pages/add');
			$this->view->render('footer');
		}
	}
	
	public function edit($id) {
		
		$this->view->appendJsToHead('scripts/pages/editPage.php');
		$this->view->pageInfo = $this->model->edit($id);
		$this->view->pageCats = $this->model->pagecats();

		$this->view->newContentSelect = $this->model->new_content_select($id);
		$this->view->existingSelect   = $this->model->existing_content_select($id);
		$this->view->pagesComponents  = $this->model->show_pages_contents($id);

		$this->view->title = 'Edit Page \''.$this->view->pageInfo['title'].'\'';

		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('pages/edit/edit');
		$this->view->render('footer');
	}

	public function save() {

		$this->model->savepage();
	}
	
	public function cats() {
		
		$this->view->cats = $this->model->pagecats();
		
		$this->view->title = 'Page Categories ';
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('pages/cats');
		$this->view->render('footer');
	}
	
	public function editcat($id) {
		
		$this->view->appendJsToHead('scripts/pages/editCat.php');
		$this->view->appendCssToHead('scripts/pages/editCat.php');

		$this->view->data = $this->model->editcat($id);
		
		$this->view->title = 'Edit Category \''.$this->view->data['name'].'\'';
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('pages/editcat');
		$this->view->render('footer');		
	}
	
	public function addcat() {
		
		$this->model->addcat();
		
		$this->view->title = 'Add Page Category';
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('pages/addcat');
		$this->view->render('footer');		
	}
	
	public function deletecat($id){
		
		$this->model->deletecat($id);
	}
	
	public function getparent() {
		
		$this->model->getparent();
	}
	
	public function add_component() {
	
		$this->model->add_component();
	}
	
	public function add_component_to_page() {
	
		$this->model->add_component_to_page();
	}
	
	public function show_pages_contents() {
	
		echo $this->model->show_pages_contents($_GET['pageid']);
	}
	
	public function existing_content_select() {
	
		echo $this->model->existing_content_select($_GET['pageid']);
	}
	
	public function deletepage($id){
	
		$this->model->deletepage($id);
	}
	
	public function deletecontent() {
	
		$this->model->deletecontent();
	}
	
	public function sortpages() {
	
		$this->model->sortpages();
	}

	public function sortcomp() {
	
		$this->model->sortcomp();
	}

}