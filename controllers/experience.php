<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Experience extends Controller{

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
        $this->view->title = 'Professional Experience | Ebenezer Albidar Narh';
        $this->view->description = 'Review the professional experience of Ebenezer Albidar Narh across enterprise IT, software engineering, healthcare technology, and public-sector digital transformation.';
        $this->view->url = 'https://www.dealbidar.com/experience';
        $this->view->canonical = 'https://www.dealbidar.com/experience';
        $this->view->image = 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg';
        $this->view->author = 'Ebenezer Albidar Narh';
        $this->view->keywords = 'software engineer experience Ghana, enterprise IT experience, UNDP Ghana technology, Ebenezer Albidar Narh experience';
        $this->view->render('experience/index');
    }
    

}
