<?php

/* 
 * Company: W3 Multimedia Ghana Limited
 * 
 * Website: https://www.w3-multimedia.com
 * 
 * Email: info@w3-multimedia.com
 * 
 * Phone: +233 312295283 / +233 244285651
 * 
 * Author: Ebenezer Albidar Narh
 * 
 * Our mission is to solve real world challenges by harnessing the power of technology. We will pursue innovation focused on simplifying lives providing competitive advantage to our clients as the real world and IT world converges. 
 * 
 * In pursuing our mission, we will deliver Cost Effective products & services, maintain sustainable growth, build mutually beneficial and enduring partnerships and create long term value by following sound business practices in dealing with our clients, partners, employees and shareholders. 
 * 
 * W3 Multimedia Inc is a premium web design and digital marketing company that focuses on quality, innovation, & speed. We utilize technology to bring results to grow our clients businesses. We pride ourselves in great work ethic, integrity, and end-results. 
 * 
 * We have been designing websites in Ghana since 2015. If you are looking for experience, creativity and passion, then get in touch with us today! Send us your request for a free quote. 
 * 
 * As a learning organisation, W3-Multimedia will pursue continual self improvement, personal excellence and accountability delivering better than expected business outcomes to our clients – right, the first time.
 * 
 */

class Errors extends Controller{

    function __construct() {
        parent::__construct();
        $this->view->css = array(
            ''
            );
        $this->view->js = array(
            ''
            );
    }
    
    function index(){
        $this->view->title = 'Ebenezer Albidar Narh - Software Engineer and Web Developer.';
        $this->view->description = "";
        $this->view->url = 'https://www.dealbidar.com/errors';
        $this->view->image = 'https://www.dealbidar.com/public/assets/images/bg/bg-image-11.jpg';
        $this->view->author = 'W3 Multimedia Ghana Limited';
        $this->view->keywords = 'dealbidar, Ebenezer Albidar Narh, Software Engineer, Web Developer, Mobile App Developer, Software Development, Programming, Responsive Design, UI/UX Design, Problem-Solving, Technology, Coding, Tech Stack, Client Projects, Optimization, Open Source, Testimonials, Blog, Freelance Developer, Contact Information, LinkedIn Profile';    
        $this->view->render('index/index');
    }
    
}