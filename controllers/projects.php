<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Projects extends Controller{

    function __construct() {
        parent::__construct();
        $this->view->css = array(
            'public/assets/css/style.css'
            );
        $this->view->js = array(
            'public/assets/js/main.js'
            );
    }
    
    function index(){
        $this->view->title = 'Projects | InnInk Limited & Enterprise Systems';
        $this->view->description = 'Software projects by Ebenezer Albidar Narh, including InnInk Limited products like Big Book, and enterprise systems delivered for UNDP Ghana.';
        $this->view->url = 'https://www.dealbidar.com/projects';
        $this->view->canonical = 'https://www.dealbidar.com/projects';
        $this->view->image = 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg';
        $this->view->author = 'Ebenezer Albidar Narh';
        $this->view->keywords = 'InnInk Limited projects, Big Book inventory system, UNDP Ghana projects, enterprise systems portfolio, software projects Ghana, Ebenezer Albidar Narh projects';
        $this->view->render('projects/index');
    }
    

}
