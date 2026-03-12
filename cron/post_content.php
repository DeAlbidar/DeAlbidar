<?php
/**
 * Cron Job: Post Trending Content to Facebook
 * 
 * Run every 3 hours using cron:
 * /usr/bin/php /var/www/html/DeAlbidar/cron/post_content.php >> /var/log/facebook_poster.log 2>&1
 */

// Set timezone
date_default_timezone_set('UTC');

// Include configuration and libraries
require_once dirname(__DIR__) . '/cron/config.cron.php';
require_once dirname(__DIR__) . '/libs/FacebookAPI.php';
require_once dirname(__DIR__) . '/libs/ContentGenerator.php';
require_once dirname(__DIR__) . '/libs/Database.php';

class FacebookPosterCron {
    private $facebookAPI;
    private $contentGenerator;
    private $database;
    private $logFile;

    public function __construct() {
        // Setup logging first
        $this->logFile = CRON_LOG_DIR . date('Y-m-d') . '.log';
        
        try {
            // Initialize database
            $this->database = new Database();
            
            // Initialize Facebook API
            $this->facebookAPI = new FacebookAPI(
                FACEBOOK_ACCESS_TOKEN,
                FACEBOOK_PAGE_ID
            );
            
            // Initialize content generator
            $this->contentGenerator = new ContentGenerator($this->database);
            
        } catch (Exception $e) {
            $this->log('Fatal Error: ' . $e->getMessage());
            exit(1);
        }
    }

    /**
     * Execute the cron job
     */
    public function execute() {
        try {
            $this->log('=== CRON JOB STARTED ===');
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
                $this->log('Content already posted, generating new content...');
                // Try another category
                $altCategory = $categories[array_rand($categories)];
                $content = $this->contentGenerator->generateContent($altCategory);
            }
            
            // Post to Facebook
            $this->log('Posting content to Facebook...');
            $message = $content['title'];
            
            if (!empty($content['link'])) {
                $message .= "\n\n" . $content['link'];
            }
            
            $result = $this->facebookAPI->postContent(
                $message,
                $content['link'] ?? null,
                $content['image'] ?? null
            );
            
            if (isset($result['error'])) {
                throw new Exception('Facebook API Error: ' . json_encode($result));
            }
            
            // Save to database
            $this->savePostedContent($content, $result);
            
            $this->log('SUCCESS: Content posted! Post ID: ' . ($result['id'] ?? 'unknown'));
            $this->log('=== CRON JOB COMPLETED ===');
            
            return true;
            
        } catch (Exception $e) {
            $this->log('ERROR: ' . $e->getMessage());
            $this->log('=== CRON JOB FAILED ===');
            return false;
        }
    }

    /**
     * Check if content was already posted
     */
    private function isContentAlreadyPosted($title) {
        try {
            $table = 'facebook_posts';
            $query = "SELECT id FROM $table WHERE title = ? AND posted_date > DATE_SUB(NOW(), INTERVAL 24 HOUR) LIMIT 1";
            $result = $this->database->select($query, [$title]);
            return !empty($result);
        } catch (Exception $e) {
            $this->log('Warning: Could not check if content was posted: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save posted content to database
     */
    private function savePostedContent($content, $result) {
        try {
            $table = 'facebook_posts';
            $data = [
                'title' => $content['title'],
                'category' => $content['category'],
                'link' => $content['link'] ?? '',
                'image' => $content['image'] ?? '',
                'facebook_post_id' => $result['id'] ?? '',
                'posted_date' => date('Y-m-d H:i:s'),
                'status' => 'posted'
            ];
            
            // Check if the insert method exists in your Database class
            if (method_exists($this->database, 'insert')) {
                $this->database->insert($table, $data);
                $this->log('Content saved to database');
            }
        } catch (Exception $e) {
            $this->log('Warning: Could not save content to database: ' . $e->getMessage());
        }
    }

    /**
     * Log message
     */
    private function log($message) {
        $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        
        // Log to file
        if (ENABLE_CRON_LOGGING) {
            file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        }
        
        // Also output to console
        echo $logMessage;
    }
}

// Run the cron job
$cron = new FacebookPosterCron();
$cron->execute();

?>
