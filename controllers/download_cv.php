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
        $this->view->title = 'Ebenezer Albidar Narh - Software Engineer and Web Developer.';
        $this->view->description = "Welcome to the portfolio of Ebenezer Albidar Narh, a highly skilled Software Engineer and Web Developer. Explore a diverse range of projects showcasing expertise in software development, mobile app development, responsive design, and problem-solving. Discover how I use cutting-edge technology to bring creative solutions to life.";
        $this->view->url = 'https://www.dealbidar.com/download_cv';
        $this->view->image = 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg';
        $this->view->author = 'W3 Multimedia Ghana Limited';
        $this->view->keywords = 'dealbidar, Ebenezer Albidar Narh, Software Engineer, Web Developer, Mobile App Developer, Software Development, Programming, Responsive Design, UI/UX Design, Problem-Solving, Technology, Coding, Tech Stack, Client Projects, Optimization, Open Source, Testimonials, Blog, Freelance Developer, Contact Information, LinkedIn Profile';    
        $this->view->render('download/index');
    }
    

}