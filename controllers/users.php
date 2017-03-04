<?php

class Users extends Controller {

	function __construct() {
		
		parent::__construct();
	}
	
	function index() {
		
		$this->view->title = 'Admin Users';
		$this->view->usersList = $this->model->usersList();
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('users/index');
		$this->view->render('footer');	
	}
	
	function add() {
		
		$this->view->appendJsToHead('scripts/users/addUser.php');
		$this->view->title = 'Add User';
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('users/add');
		$this->view->render('footer');	
	}
	
	function edit($id){
		//Existing user to view and edit.
		$this->view->user = $this->model->editUser($id);
		$this->view->appendJsToHead('scripts/users/editUser.php');

		$this->view->title = 'Edit User "'.$this->view->user['fname'].' '.$this->view->user['lname'].'"';
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('users/edit');
		$this->view->render('footer');	
	}
	
	function adduser() {
		//Adding a new user
		$this->model->addUser();
	}
	
	function saveuser() {
		//Saving an existing user
		$this->model->saveUser();
	}
	
	function deleteuser($id){
		//Saving an existing user
		$this->model->deleteUser($id);
	}
}