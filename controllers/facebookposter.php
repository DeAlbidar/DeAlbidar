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
            $pageKey = $_GET['page'] ?? null;
            $category = $_GET['category'] ?? null;
            $result = $this->model->postTrendingContent($pageKey, $category);
            
            if ($result['success']) {
                echo json_encode([
                    'status' => 'success',
                    'message' => $result['message'] ?? 'Content posted successfully',
                    'post_id' => $result['post_id'] ?? null,
                    'category' => $result['category'],
                    'skipped' => $result['skipped'] ?? false,
                    'page_key' => $result['page_key'] ?? null,
                    'page_id' => $result['page_id'] ?? null,
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
        $pageKey = $_GET['page'] ?? null;
        $result = $this->model->getStatus($pageKey);
        echo json_encode($result);
    }

    /**
     * Get last posted content
     */
    function last() {
        $pageKey = $_GET['page'] ?? null;
        $result = $this->model->getLastPosted($pageKey);
        echo json_encode($result);
    }

    /**
     * Test method - doesn't post, just shows what would be posted
     */
    function test() {
        $pageKey = $_GET['page'] ?? null;
        $category = $_GET['category'] ?? null;
        $result = $this->model->generateTestContent($pageKey, $category);
        echo json_encode($result);
    }
}

?>
