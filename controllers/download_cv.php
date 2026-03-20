<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Download_Cv extends Controller{

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
        $this->view->title = 'Download CV | Ebenezer Albidar Narh';
        $this->view->description = 'Download the CV of Ebenezer Albidar Narh, AI and full-stack software engineer with experience in enterprise systems, digital transformation, and web application development.';
        $this->view->url = 'https://www.dealbidar.com/download_cv';
        $this->view->canonical = 'https://www.dealbidar.com/download_cv';
        $this->view->image = 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg';
        $this->view->author = 'Ebenezer Albidar Narh';
        $this->view->keywords = 'download CV Ebenezer Albidar Narh, software engineer resume Ghana, full-stack developer CV';
        $this->view->render('download/index');
    }
    

}
