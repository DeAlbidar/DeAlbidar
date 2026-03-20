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
        $this->view->title = 'Projects by Ebenezer Albidar Narh | Software, AI and Enterprise Systems';
        $this->view->description = 'Browse software projects by Ebenezer Albidar Narh, including enterprise systems, web applications, AI-assisted solutions, and digital products built for real-world impact.';
        $this->view->url = 'https://www.dealbidar.com/projects';
        $this->view->canonical = 'https://www.dealbidar.com/projects';
        $this->view->image = 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg';
        $this->view->author = 'Ebenezer Albidar Narh';
        $this->view->keywords = 'software projects Ghana, AI projects Ghana, enterprise systems portfolio, web development portfolio, Ebenezer Albidar Narh projects';
        $this->view->render('projects/index');
    }
    

}
