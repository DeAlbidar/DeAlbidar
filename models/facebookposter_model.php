<?php

/**
 * Facebook Poster Model
 * Handles posting trending content to Facebook
 */

class Facebookposter_Model {

    private $facebookAPI;
    private $contentGenerator;
    private $db;
    private $logFile;

    function __construct() {
        require_once 'libs/FacebookAPI.php';
        require_once 'libs/ContentGenerator.php';
        
        $this->db = new Database();
        $this->facebookAPI = new FacebookAPI(
            FACEBOOK_PAGE_ID,
            FACEBOOK_ACCESS_TOKEN
        );
        $this->contentGenerator = new ContentGenerator($this->db);
        
        // Setup logging
        $logDir = 'logs/facebook_posts/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $this->logFile = $logDir . date('Y-m-d') . '.log';
    }

    /**
     * Post trending content to Facebook
     */
    public function postTrendingContent() {
        try {
            $this->log('=== POSTING STARTED ===');
            $this->log('Time: ' . date('Y-m-d H:i:s'));
            
            // Get a random category
            $categories = ContentGenerator::getCategories();
            $category = $categories[array_rand($categories)];
            
            $this->log("Selected category: $category");
            
            // Generate content
            $content = $this->contentGenerator->generateContent($category);
            
            if (!$content) {
                throw new Exception('Failed to generate content for category: ' . $category);
            }
            
            // Check if content was already posted
            if ($this->isContentAlreadyPosted($content['title'])) {
                $this->log('Content already posted, trying another category...');
                
                // Try another random category
                $altCategory = $categories[array_rand($categories)];
                $content = $this->contentGenerator->generateContent($altCategory);
                
                if (!$content) {
                    throw new Exception('Failed to generate alternative content');
                }
            }
            
            // Build message
            $message = $content['title'];
            if (!empty($content['link'])) {
                $message .= "\n\n" . $content['link'];
            }
            
            // Post to Facebook
            $this->log('Posting to Facebook...');
            $result = $this->facebookAPI->postContent(
                $message,
                $content['link'] ?? null,
                $content['image'] ?? null
            );
            
            if (isset($result['error'])) {
                $this->log('ERROR: ' . json_encode($result));
                return [
                    'success' => false,
                    'error' => $result['response']['error']['message'] ?? 'Unknown error'
                ];
            }
            
            // Save to database
            $this->savePostedContent($content, $result);
            
            $this->log('SUCCESS: Post ID: ' . ($result['id'] ?? 'unknown'));
            $this->log('=== POSTING COMPLETED ===');
            
            return [
                'success' => true,
                'post_id' => $result['id'] ?? '',
                'category' => $content['category']
            ];
            
        } catch (Exception $e) {
            $this->log('EXCEPTION: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate test content without posting
     */
    public function generateTestContent() {
        try {
            $categories = ContentGenerator::getCategories();
            $category = $categories[array_rand($categories)];
            $content = $this->contentGenerator->generateContent($category);
            
            if (!$content) {
                return [
                    'success' => false,
                    'error' => 'Failed to generate content'
                ];
            }
            
            return [
                'success' => true,
                'content' => $content,
                'category' => $content['category'],
                'message' => "This content will be posted to Facebook"
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get last posted content
     */
    public function getLastPosted() {
        try {
            $query = "SELECT * FROM facebook_posts ORDER BY posted_date DESC LIMIT 1";
            $result = $this->db->select($query);
            
            if (empty($result)) {
                return [
                    'success' => true,
                    'message' => 'No posts yet',
                    'data' => null
                ];
            }
            
            return [
                'success' => true,
                'data' => $result[0]
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get posting status
     */
    public function getStatus() {
        try {
            $query = "SELECT category, COUNT(*) as count FROM facebook_posts GROUP BY category ORDER BY count DESC";
            $result = $this->db->select($query);
            
            $query2 = "SELECT COUNT(*) as total FROM facebook_posts";
            $totalResult = $this->db->select($query2);
            
            return [
                'success' => true,
                'total_posts' => $totalResult[0]['total'] ?? 0,
                'by_category' => $result
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if content was already posted
     */
    private function isContentAlreadyPosted($title) {
        try {
            $query = "SELECT id FROM facebook_posts WHERE title = ? AND posted_date > DATE_SUB(NOW(), INTERVAL 24 HOUR) LIMIT 1";
            $result = $this->db->select($query, [$title]);
            return !empty($result);
        } catch (Exception $e) {
            $this->log("Warning: Could not check if content was posted: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save posted content to database
     */
    private function savePostedContent($content, $result) {
        try {
            $data = [
                'title' => $content['title'],
                'category' => $content['category'],
                'link' => $content['link'] ?? '',
                'image' => $content['image'] ?? '',
                'facebook_post_id' => $result['id'] ?? '',
                'posted_date' => date('Y-m-d H:i:s'),
                'status' => 'posted'
            ];
            
            if (method_exists($this->db, 'insert')) {
                $this->db->insert('facebook_posts', $data);
                $this->log('Saved to database');
            }
        } catch (Exception $e) {
            $this->log("Warning: Could not save to database: " . $e->getMessage());
        }
    }

    /**
     * Log a message
     */
    private function log($message) {
        $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}

?>
