<?php

class Controller {

	public $globalNav;
	
	function __construct() {
		
		global $url;
		$this->view 		   = new View();
		$nav 				   = new Navigation();
		$this->view->globalNav = $nav->renderNav();
	}
	
	public function loadModel($name) {
		
		global $url;
		$path = 'models/'.$name.'_model.php';
	
		if (file_exists($path)) {
			
			require $path;
			$modelName   = $name . '_Model';
			$this->model = new $modelName();
		}
	}
}