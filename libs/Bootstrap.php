<?php

class Bootstrap {
    
    function __construct() {
        
        global $url;
        $url = isset($_GET['url']) ? $_GET['url'] : null;
        $url = rtrim($url, '/');
        $url = explode('/', $url);

        $this->db = new Database();
        
        if(isset($url[1]) && ($url[1]!='api')){

        }else{
            if (!isset($_SESSION['x_equi']) && $url[0] != 'login') {
            
            	header("location:" . _EQROOT_ . 'login?return=' . $_GET['url']);
            }
        }
        
        if (empty($url[0])) {
            
            require 'controllers/dashboard.php';
            $controller = new Dashboard();
            $controller->index();
            return false;
        }
        
        $file = 'controllers/' . $url[0] . '.php';
        
        if (file_exists($file)) {
            
            require $file;
            $controller = new $url[0];
            $controller->loadModel($url[0]);
            
            // calling methods
            if (isset($url[2])) {
                if (method_exists($controller, $url[1])) {
                    
                    $controller->{$url[1]}($url[2]);
                } else {
                    
                    $this->error();
                }
            } else {
                if (isset($url[1])) {
                    if (method_exists($controller, $url[1])) {
                        
                        $controller->{$url[1]}();
                    } else {
                        
                        $this->error();
                    }
                } else {
                    $controller->index();
                }
            }
        } else {
            
            $this->error();
        }
    }
    
    function error() {
        
        require 'controllers/' . EQ_404 . '.php';
        $error = new Error();
        $error->index();
        return false;
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