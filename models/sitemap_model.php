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

class Sitemap_Model extends Model {

    function __construct() {
        parent::__construct();
    }
    
    public function Pages() {
        return $this->db->select('SELECT * FROM PAGE WHERE status = "APPROVED" LIMIT 8;');
    }
    
    public function Sub_Pages() {
        return $this->db->select('SELECT * FROM SUBPAGE WHERE status = "APPROVED" ;');
    }
    
    public function findMostRecent() {
        return $this->db->select('SELECT NEWS.*, 
                (SELECT SUBPAGE.sub_page
                FROM SUBPAGE
                WHERE id = NEWS.category_id) AS sub_page
                FROM NEWS 
                WHERE NEWS.status = "APPROVED" ORDER BY NEWS.created_at DESC LIMIT 1000;');
    }
    
    function Counter($page = false) {
        $geoplugin = new geoPlugin();
        $geoplugin->locate();
        $stmt = $this->db->insert('tbl_visitors_counter', array(
            'visits' => 1,
            'city' => $geoplugin->city,
            'region' => $geoplugin->regionName,
            'country' => $geoplugin->countryName,
            'latitude' => $geoplugin->latitude,
            'longitude' => $geoplugin->longitude,
            'timezone' => $geoplugin->timezone,
            'ip_address' => $geoplugin->ip,
            'page' => $page
        ));
    }

}