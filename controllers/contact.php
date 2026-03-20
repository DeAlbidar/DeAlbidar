<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Contact extends Controller{

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
        $this->view->title = 'Contact Ebenezer Albidar Narh | Software Engineering and AI Consulting';
        $this->view->description = 'Contact Ebenezer Albidar Narh for software engineering, enterprise systems, AI-assisted development, and digital transformation consulting in Ghana and beyond.';
        $this->view->url = 'https://www.dealbidar.com/contact';
        $this->view->canonical = 'https://www.dealbidar.com/contact';
        $this->view->image = 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg';
        $this->view->author = 'Ebenezer Albidar Narh';
        $this->view->keywords = 'contact software engineer Ghana, hire full-stack developer Ghana, AI consultant Ghana, contact Ebenezer Albidar Narh';
        $this->view->loadRecaptcha = true;
        $this->view->render('contact/index');
    }
    

}
