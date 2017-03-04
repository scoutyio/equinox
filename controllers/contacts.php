<?php

class Contacts extends Controller {

	function __construct() {
		
		parent::__construct();
	}
	
	public function index() {

		$this->view->appendJsToHead('scripts/contacts/index.php');
		
		$this->view->contacts = $this->model->index();
		$this->view->contactCats = $this->model->cats();

		$this->view->eqapp = new Apps();
				
		$this->view->title = 'Contacts';
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('contacts/index');
		$this->view->render('footer');	
	}
	
	public function add() {

		$this->view->appendJsToHead('scripts/contacts/addContact.php');
		
		if(isset($_POST['fname'])) {
			
			$this->model->add();
		} else {

			$this->view->contactCats = $this->model->cats();
			$this->view->title = 'Add New Contact';
	
			$this->view->render('header');
			$this->view->render('topnav');
			$this->view->render('menu');
			$this->view->render('contacts/addcontact');
			$this->view->render('footer');
		}	
	}
	
	public function edit($id) {

		$this->view->appendJsToHead('scripts/contacts/editContact.php');
		
		$this->view->contactInfo = $this->model->edit($id);
		$this->view->contactCats = $this->model->cats();
		
		$this->view->title = 'Edit Contact \''.$this->view->contactInfo['fname'].' '.$this->view->contactInfo['lname'].'\'';
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('contacts/edit');
		$this->view->render('footer');
	}

	public function delete($id) {

		$this->model->delete($id);
	}
	
	public function cats() {
		
		$this->view->cats = $this->model->cats();
				
		$this->view->title = 'Categories';
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('contacts/cats');
		$this->view->render('footer');		
	}
	
	public function addcat() {
		
		$this->view->appendJsToHead('scripts/contacts/addCat.php');

		$this->model->addcat();

		$this->view->title = 'Add Category';
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('contacts/addcat');
		$this->view->render('footer');		
	}
	
	public function editcat($id) {
		
		$this->view->appendJsToHead('scripts/contacts/editCat.php');

		$this->view->catInfo = $this->model->editcat($id);
		
		$this->view->title = 'Edit Category "'.$this->view->catInfo['name'].'"';
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('contacts/editcat');
		$this->view->render('footer');		
	}
}