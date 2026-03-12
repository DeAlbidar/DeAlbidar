<?php

/**
 * Facebook Poster Controller
 * Handles automated posting of trending content to Facebook
 * 
 * Called via cron job:
 * curl "https://dealbidar.com/?url=facebookposter/post"
 */

class Facebookposter extends Controller {

    function __construct() {
        parent::__construct();
        //$this->loadModel('facebookposter');
    }
    
    /**
     * Post trending content to Facebook (called every 3 hours)
     */
    function post() {
        try {
            $result = $this->model->postTrendingContent();
            
            if ($result['success']) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Content posted successfully',
                    'post_id' => $result['post_id'],
                    'category' => $result['category'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => $result['error'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Get posting status and history
     */
    function status() {
        $result = $this->model->getStatus();
        echo json_encode($result);
    }

    /**
     * Get last posted content
     */
    function last() {
        $result = $this->model->getLastPosted();
        echo json_encode($result);
    }

    /**
     * Test method - doesn't post, just shows what would be posted
     */
    function test() {
        $result = $this->model->generateTestContent();
        echo json_encode($result);
    }
}

?>
