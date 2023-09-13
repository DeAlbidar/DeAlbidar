<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller {

    public $view;
    public $model;

    function __construct() {
        //echo 'Main Controller';
        $this->view = new View();
    }

    public function loadModel($name, $modelPath = 'models/') {
        $path = $modelPath . $name.'_model.php';
        
        if (file_exists($path)) {
            require $modelPath .$name.'_model.php';
            
            $modelName = $name . '_Model';
            $this->model = new $modelName();
        }
    }

}
