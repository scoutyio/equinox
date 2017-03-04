<?php

class Dashboard extends Controller {
    
    function __construct() {
        parent::__construct();
    }
    
    public function index() {

        $this->view->title = 'Dashboard';
        
        $this->view->render('header');
        $this->view->render('topnav');
        $this->view->render('menu');
        $this->view->render('dashboard/index');
        $this->view->render('footer');
    }
}