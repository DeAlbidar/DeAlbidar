<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Index extends Controller{

    function __construct() {
        parent::__construct();
        $this->view->css = array(
            'public/assets/css/vendor/bootstrap.min.css',
            'public/assets/css/vendor/slick.css',
            'public/assets/css/vendor/slick-theme.css',
            'public/assets/css/vendor/aos.css',
            'public/assets/css/plugins/feature.css',
            'public/assets/css/style.css'
            );
        $this->view->js = array(
            'public/assets/js/vendor/jquery.js',
            'public/assets/js/vendor/modernizer.min.js',
            'public/assets/js/vendor/feather.min.js',
            'public/assets/js/vendor/slick.min.js',
            'public/assets/js/vendor/bootstrap.js',
            'public/assets/js/vendor/text-type.js',
            'public/assets/js/vendor/wow.js',
            'public/assets/js/vendor/aos.js',
            'public/assets/js/vendor/particles.js',
            'public/assets/js/vendor/jquery-one-page-nav.js',
            'public/assets/js/main.js'
            );
    }
    
    function index(){
        $this->view->title = 'Ebenezer Albidar Narh - Software Engineer and Web Developer.';
        $this->view->description = "Welcome to the portfolio of Ebenezer Albidar Narh, a highly skilled Software Engineer and Web Developer. Explore a diverse range of projects showcasing expertise in software development, mobile app development, responsive design, and problem-solving. Discover how I use cutting-edge technology to bring creative solutions to life.";
        $this->view->url = 'https://www.dealbidar.com/index';
        $this->view->image = 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg';
        $this->view->author = 'W3 Multimedia Ghana Limited';
        $this->view->keywords = 'dealbidar, Ebenezer Albidar Narh, Software Engineer, Web Developer, Mobile App Developer, Software Development, Programming, Responsive Design, UI/UX Design, Problem-Solving, Technology, Coding, Tech Stack, Client Projects, Optimization, Open Source, Testimonials, Blog, Freelance Developer, Contact Information, LinkedIn Profile';    
        $this->view->render('index/index');
    }
    
    public function contact(){
        if (isset($_POST['g-recaptcha-response'])) {
            $captcha = $_POST['g-recaptcha-response'];
        } else {
            $captcha = false;
        }

        if (!$captcha) {
            echo '<h2>Please check the captcha form.</h2>';
            echo '<a href="javascript:history.go(-1)" title="Return to the previous page">&laquo; Go back</a>';
            exit;
        } else {
            $secret = '6Ld1XjoiAAAAAINqNzl4M7lTjESeWXr7cqvx8KSZ';
            $response = file_get_contents(
                    "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']
            );
            $response = json_decode($response);
            if ($response->success === false) {
                echo '<h2>Please check the captcha form.</h2>';
                echo '<a href="javascript:history.go(-1)" title="Return to the previous page">&laquo; Go back</a>';
                exit;
            }
        }
        if ($response->success == true && $response->score <= 0.5) {
            echo '<h2>Please check the captcha form.</h2>';
            echo '<a href="javascript:history.go(-1)" title="Return to the previous page">&laquo; Go back</a>';
            exit;
        } else {
            $this->model->contact($_POST);
            header('location: '.URL.'index');
        }
    }
    

}