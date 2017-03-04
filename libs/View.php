<?php

class View {
    
    protected $jsString;
    protected $cssString;
    
    function __construct() {
        global $url;
    }
    
    public function render($name, $args = false) {
        //renders the view along with passed data
        global $url;
        if(file_exists('views/' . $name . '.phtml')) {
            require 'views/' . $name . '.phtml';
        } else {
            ob_clean();
            die('File: "views/' . $name . '.phtml" does not exist!');
        }
    }
    
    public function appendCssToHead($str) {
        
        $this->cssString[] = $str;
    }
    
    public function appendJsToHead($str) {
        
        $this->jsString[] = $str;
    }
    
    public function cssToHead() {
        
        if (!count($this->cssString)) {
            return false;
        }
        
        $str = "";
        $n=0;
        foreach ($this->cssString as $file) {
            $n++;
            $str .= "<link href=\"" . EQ_PUB . "css/" . $file . "\" rel=\"stylesheet\" type=\"text/css\" />\n";
        }
        echo $str;
    }
    
    public function jsToHead() {
        
        if (!count($this->jsString)) {
            return false;
        }
        
        $str = "";
        $n=0;
        foreach ($this->jsString as $file) {
            $n++;
            $str .= '<script src="' . EQ_PUB . 'js/' . $file . '" type="text/javascript"></script>';
        }
        echo $str;
    }
}