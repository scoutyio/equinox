<?php

class Settings extends Controller {
    
    function __construct() {
        parent::__construct();
    }
    
    public function index() {

        $this->view->appendJsToHead('scripts/settings/index.php');
        
        $this->view->companyInfo = $this->model->fetchAll();
        $this->view->title       = 'Website Settings';
        
        $this->view->render('header');
        $this->view->render('topnav');
        $this->view->render('menu');
        $this->view->render('settings/index');
        $this->view->render('footer');
    }
    
    public function save() {
        
        $this->model->saveSettings();
    }
    
    public function saveLogo() {
        
        $this->model->saveLogo();
    }
}