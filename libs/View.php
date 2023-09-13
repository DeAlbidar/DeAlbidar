<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class View {

    public $css = array();
    public $js = array();
    public $title;
    public $url;
    public $image;
    public $author;
    public $description;
    public $keywords;

    function __construct() {
        //echo 'This is the view';
    }
    
    public function render($name, $noInclude = false){
        if ($noInclude == true) {
            require 'views/' . $name . '.php';    
        }
        else {
            require 'views/partials/header.php';
            require 'views/partials/navigation.php';
            require 'views/' . $name . '.php';
            require 'views/partials/footer.php';    
        }
    }
    
    public function custom_render($name, $noInclude = false){
        if ($noInclude == true) {
            require 'libs/' . $name . '.php';    
        }
        else {
            require 'libs/' . $name . '.php';
        }
    }

}
