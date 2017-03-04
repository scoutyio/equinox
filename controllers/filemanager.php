<?php

class Filemanager extends Controller {

	function __construct() {
		parent::__construct();
	}
	
	function index() {
		
        $this->view->appendJsToHead('scripts/filemanager/index.php');
		$this->view->title = 'File Manager';
		
		$this->view->render('header');
		$this->view->render('topnav');
		$this->view->render('menu');
		$this->view->render('files/index');
		$this->view->render('footer');
	}
	
}