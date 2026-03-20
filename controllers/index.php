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
            'public/assets/css/style.css'
            );
        $this->view->js = array(
            'public/assets/js/main.js'
            );
    }
    
    function index(){
        $this->view->title = 'Ebenezer Albidar Narh | AI and Full-Stack Software Engineer in Ghana';
        $this->view->description = 'Explore the portfolio of Ebenezer Albidar Narh, an AI and full-stack software engineer building enterprise systems, web applications, and digital transformation solutions in Ghana.';
        $this->view->url = 'https://www.dealbidar.com/';
        $this->view->canonical = 'https://www.dealbidar.com/';
        $this->view->image = 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg';
        $this->view->author = 'Ebenezer Albidar Narh';
        $this->view->keywords = 'Ebenezer Albidar Narh, AI engineer Ghana, full-stack developer Ghana, software engineer Ghana, enterprise systems developer, web developer Ghana, portfolio';
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
            header('location: '.URL.'contact');
        }
    }
    

}
