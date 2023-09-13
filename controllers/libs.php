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

class Libs extends Controller {

    function __construct() {
        parent::__construct();
    }
    
    function xcrud($pram){
        $this->view->custom_render('xcrud/'.$pram);
    }

}