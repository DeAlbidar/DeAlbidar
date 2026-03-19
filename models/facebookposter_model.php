<?php

/**
 * Facebook Poster Model
 * Handles posting trending content to Facebook
 */

class Facebookposter_Model extends Model {

    private $contentGenerator;
    private $logFile;
    private $hasPageMetadataColumns = false;

    function __construct() {
        parent::__construct();
        require_once 'libs/FacebookAPI.php';
        require_once 'libs/ContentGenerator.php';
        $this->contentGenerator = new ContentGenerator($this->db);
        
        // Setup logging
        $logDir = 'logs/facebook_posts/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $this->logFile = $logDir . date('Y-m-d') . '.log';
        $this->ensurePageMetadataColumns();
    }

    /**
     * Post trending content to Facebook
     */
    public function postTrendingContent($pageKey = null, $category = null) {
        try {
            $pageConfig = getFacebookPageConfig($pageKey);
            $facebookAPI = $this->buildFacebookApi($pageConfig);
            $selectedCategory = $this->resolveCategory($pageConfig, $category);

            $this->log('=== POSTING STARTED ===');
            $this->log('Time: ' . date('Y-m-d H:i:s'));
            $this->log('Page: ' . $pageConfig['label'] . ' (' . $pageConfig['page_id'] . ')');
            $this->log('Daily limit: ' . $pageConfig['posts_per_day']);
            
            $this->log('Selected category: ' . $selectedCategory);

            if ($this->hasReachedDailyPostLimit($pageConfig['key'], $pageConfig['posts_per_day'])) {
                $message = 'Daily post limit reached for page: ' . $pageConfig['key'];
                $this->log($message);

                return [
                    'success' => true,
                    'skipped' => true,
                    'message' => $message,
                    'category' => $selectedCategory,
                    'page_key' => $pageConfig['key'],
                    'page_id' => $pageConfig['page_id']
                ];
            }
            
            // Generate content
            $content = $this->generateUniqueContent($selectedCategory, $pageConfig['key']);
            
            if (!$content) {
                throw new Exception('Failed to generate content for category: ' . $selectedCategory);
            }
            
            // Build message
            $message = $content['title'];
            if (!empty($content['link'])) {
                $message .= "\n\n" . $content['link'];
            }
            
            // Post to Facebook
            $this->log('Posting to Facebook...');
            $result = $facebookAPI->postContent(
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
            $this->savePostedContent($content, $result, $pageConfig);
            
            $this->log('SUCCESS: Post ID: ' . ($result['id'] ?? 'unknown'));
            $this->log('=== POSTING COMPLETED ===');
            
            return [
                'success' => true,
                'post_id' => $result['id'] ?? '',
                'category' => $content['category'],
                'page_key' => $pageConfig['key'],
                'page_id' => $pageConfig['page_id']
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
    public function generateTestContent($pageKey = null, $category = null) {
        try {
            $pageConfig = getFacebookPageConfig($pageKey);
            $selectedCategory = $this->resolveCategory($pageConfig, $category);
            $content = $this->contentGenerator->generateContent($selectedCategory);
            
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
                'page_key' => $pageConfig['key'],
                'page_id' => $pageConfig['page_id'],
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
    public function getLastPosted($pageKey = null) {
        try {
            $pageConfig = $pageKey ? getFacebookPageConfig($pageKey) : null;
            $query = "SELECT * FROM facebook_posts";
            $params = [];

            if ($pageConfig && $this->hasPageMetadataColumns) {
                $query .= " WHERE page_key = :page_key";
                $params[':page_key'] = $pageConfig['key'];
            }

            $query .= " ORDER BY posted_date DESC LIMIT 1";
            $result = $this->db->select($query, $params);
            
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
    public function getStatus($pageKey = null) {
        try {
            $pageConfig = $pageKey ? getFacebookPageConfig($pageKey) : null;
            $whereClause = '';
            $params = [];

            if ($pageConfig && $this->hasPageMetadataColumns) {
                $whereClause = " WHERE page_key = :page_key";
                $params[':page_key'] = $pageConfig['key'];
            }

            $query = "SELECT category, COUNT(*) as count FROM facebook_posts" . $whereClause . " GROUP BY category ORDER BY count DESC";
            $result = $this->db->select($query, $params);
            
            $query2 = "SELECT COUNT(*) as total FROM facebook_posts" . $whereClause;
            $totalResult = $this->db->select($query2, $params);
            
            return [
                'success' => true,
                'total_posts' => $totalResult[0]['total'] ?? 0,
                'by_category' => $result,
                'page_key' => $pageConfig['key'] ?? null
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
    private function isContentAlreadyPosted($title, $pageKey = null) {
        try {
            $query = "SELECT id FROM facebook_posts WHERE title = :title AND posted_date > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $params = [':title' => $title];

            if ($pageKey && $this->hasPageMetadataColumns) {
                $query .= " AND page_key = :page_key";
                $params[':page_key'] = $pageKey;
            }

            $query .= " LIMIT 1";
            $result = $this->db->select($query, $params);
            return !empty($result);
        } catch (Exception $e) {
            $this->log("Warning: Could not check if content was posted: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save posted content to database
     */
    private function savePostedContent($content, $result, $pageConfig) {
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

            if ($this->hasPageMetadataColumns) {
                $data['page_key'] = $pageConfig['key'];
                $data['page_id'] = $pageConfig['page_id'];
            }
            
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

    private function buildFacebookApi($pageConfig) {
        return new FacebookAPI(
            $pageConfig['access_token'],
            $pageConfig['page_id']
        );
    }

    private function resolveCategory($pageConfig, $category = null) {
        $category = strtolower(trim((string) ($category ?: $pageConfig['default_category'] ?: '')));

        $aliases = [
            'ict' => 'tech_ai',
            'ict_related' => 'tech_ai',
            'ict-related' => 'tech_ai',
            'tech' => 'tech_ai'
        ];

        if (isset($aliases[$category])) {
            $category = $aliases[$category];
        }

        $availableCategories = ContentGenerator::getCategories();

        if (in_array($category, $availableCategories, true)) {
            return $category;
        }

        return $availableCategories[array_rand($availableCategories)];
    }

    private function generateUniqueContent($category, $pageKey) {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $content = $this->contentGenerator->generateContent($category);

            if (!$content) {
                continue;
            }

            if (!$this->isContentAlreadyPosted($content['title'], $pageKey)) {
                return $content;
            }

            $this->log('Duplicate content detected on attempt ' . $attempt . ', retrying...');
        }

        throw new Exception('Could not generate unique content for page: ' . $pageKey);
    }

    private function hasReachedDailyPostLimit($pageKey, $dailyLimit) {
        if ($dailyLimit <= 0) {
            return false;
        }

        try {
            $query = "SELECT COUNT(*) as total FROM facebook_posts WHERE DATE(posted_date) = CURDATE()";
            $params = [];

            if ($pageKey && $this->hasPageMetadataColumns) {
                $query .= " AND page_key = :page_key";
                $params[':page_key'] = $pageKey;
            }

            $result = $this->db->select($query, $params);
            $todayCount = (int) ($result[0]['total'] ?? 0);

            return $todayCount >= $dailyLimit;
        } catch (Exception $e) {
            $this->log('Warning: Could not check daily post limit: ' . $e->getMessage());
            return false;
        }
    }

    private function ensurePageMetadataColumns() {
        try {
            $pageKeyColumn = $this->db->query("SHOW COLUMNS FROM facebook_posts LIKE 'page_key'");
            $pageIdColumn = $this->db->query("SHOW COLUMNS FROM facebook_posts LIKE 'page_id'");

            $hasPageKey = $pageKeyColumn && $pageKeyColumn->fetch(PDO::FETCH_ASSOC);
            $hasPageId = $pageIdColumn && $pageIdColumn->fetch(PDO::FETCH_ASSOC);

            if (!$hasPageKey) {
                $this->db->exec("ALTER TABLE facebook_posts ADD COLUMN page_key VARCHAR(100) NULL AFTER category");
            }

            if (!$hasPageId) {
                $this->db->exec("ALTER TABLE facebook_posts ADD COLUMN page_id VARCHAR(255) NULL AFTER page_key");
            }

            $this->hasPageMetadataColumns = true;
        } catch (Exception $e) {
            $this->hasPageMetadataColumns = false;
            $this->log('Warning: Could not ensure page metadata columns: ' . $e->getMessage());
        }
    }
}

?>
