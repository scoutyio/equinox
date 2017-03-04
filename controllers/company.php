<?php

class Company extends Controller {

	function __construct() {
		
		parent::__construct();
	}
	
	function index(){
		
		$this->view->title = 'Company Settings';
		$this->view->comp = $this->model->loadCompany();
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('company/index');
		$this->view->render('footer');	
	}
	
	function update(){
		// Add or Update company details
		$this->model->update();
	}
	
	function logo(){
		// Add or Update company details
		$this->model->logo();
	}
}