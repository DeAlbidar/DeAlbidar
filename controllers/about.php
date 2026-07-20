<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class About extends Controller{

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
        $this->view->title = 'About Ebenezer Albidar Narh | Founder, InnInk Limited';
        $this->view->description = 'Learn about Ebenezer Albidar Narh, Founder of InnInk Limited — a full-stack and AI software engineer with prior enterprise systems experience at UNDP Ghana.';
        $this->view->url = 'https://www.dealbidar.com/about';
        $this->view->canonical = 'https://www.dealbidar.com/about';
        $this->view->image = 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg';
        $this->view->author = 'Ebenezer Albidar Narh';
        $this->view->keywords = 'about Ebenezer Albidar Narh, InnInk Limited founder, software engineer biography, full-stack engineer Ghana, AI engineer profile, UNDP Ghana experience';
        $this->view->render('about/index');
    }
    

}
