<?php

class Login extends Controller {
    
    function __construct() {
        parent::__construct();
    }
    
    public function index() {
        if (isset($_SESSION['x_equi'])) {
            header("location: " . _EQROOT_ . "dashboard");
        }
        $this->view->title = "Login";
        $this->view->appendJsToHead('scripts/login/loginPage.php');
        $this->view->render('header');
        $this->view->render('login/index');
        $this->view->render('footer');
    }
    
    public function process() {
        $this->model->process();
    }
    
    public function logout() {
        $this->model->logout();
    }
    
    public function check_status() {

        header('Content-type: application/json');
        
        if (isset($_SESSION['x_equi'])) {
            echo '{ "status": "loggedin"}';
        } else {
            echo '{ "status": "loggedout"}';
        }
        exit;
    }
}