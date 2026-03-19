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
    private $contentGenerator;
    private $database;
    private $logFile;
    private $hasPageMetadataColumns = false;
    private $pageConfig;
    private $category;

    public function __construct($pageKey = null, $category = null) {
        // Setup logging first
        $this->logFile = CRON_LOG_DIR . date('Y-m-d') . '.log';
        
        try {
            // Initialize database
            $this->database = new Database(
                CRON_DB_TYPE,
                CRON_DB_HOST,
                CRON_DB_NAME,
                CRON_DB_USER,
                CRON_DB_PASS
            );
            $this->pageConfig = getFacebookPageConfig($pageKey);
            $this->category = $this->resolveCategory($this->pageConfig, $category);
            
            // Initialize content generator
            $this->contentGenerator = new ContentGenerator($this->database);
            $this->ensurePageMetadataColumns();
            
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
            $this->log('Page: ' . $this->pageConfig['label'] . ' (' . $this->pageConfig['page_id'] . ')');
            $this->log('Daily limit: ' . $this->pageConfig['posts_per_day']);
            $this->log('Selected category: ' . $this->category);

            if ($this->hasReachedDailyPostLimit($this->pageConfig['key'], $this->pageConfig['posts_per_day'])) {
                $this->log('Daily post limit reached for page: ' . $this->pageConfig['key']);
                $this->log('=== CRON JOB SKIPPED ===');
                return true;
            }
            
            // Generate content
            $content = $this->generateUniqueContent($this->category, $this->pageConfig['key']);
            
            if (!$content) {
                throw new Exception('Failed to generate content for category: ' . $this->category);
            }
            
            // Post to Facebook
            $this->log('Posting content to Facebook...');
            $message = $content['title'];
            
            if (!empty($content['link'])) {
                $message .= "\n\n" . $content['link'];
            }
            
            $facebookAPI = new FacebookAPI(
                $this->pageConfig['access_token'],
                $this->pageConfig['page_id']
            );

            $result = $facebookAPI->postContent(
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
    private function isContentAlreadyPosted($title, $pageKey = null) {
        try {
            $table = 'facebook_posts';
            $query = "SELECT id FROM $table WHERE title = :title AND posted_date > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $params = [':title' => $title];

            if ($pageKey && $this->hasPageMetadataColumns) {
                $query .= " AND page_key = :page_key";
                $params[':page_key'] = $pageKey;
            }

            $query .= " LIMIT 1";
            $result = $this->database->select($query, $params);
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

            if ($this->hasPageMetadataColumns) {
                $data['page_key'] = $this->pageConfig['key'];
                $data['page_id'] = $this->pageConfig['page_id'];
            }
            
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

    private function resolveCategory($pageConfig, $category = null) {
        $availableCategories = ContentGenerator::getCategories();
        $preferredCategories = [];

        if ($category !== null && $category !== '') {
            $preferredCategories[] = $category;
        } else {
            $defaultCategory = $pageConfig['default_category'] ?? [];
            $preferredCategories = is_array($defaultCategory) ? $defaultCategory : [$defaultCategory];
        }

        $resolvedCategories = [];
        foreach ($preferredCategories as $preferredCategory) {
            $normalizedCategory = $this->normalizeCategoryKey($preferredCategory);
            if (in_array($normalizedCategory, $availableCategories, true)) {
                $resolvedCategories[] = $normalizedCategory;
            }
        }

        if (!empty($resolvedCategories)) {
            return $resolvedCategories[array_rand($resolvedCategories)];
        }

        return $availableCategories[array_rand($availableCategories)];
    }

    private function normalizeCategoryKey($category) {
        $category = strtolower(trim((string) $category));

        $aliases = [
            'ict' => 'tech_ai',
            'ict_related' => 'tech_ai',
            'ict-related' => 'tech_ai',
            'tech' => 'tech_ai'
        ];

        return $aliases[$category] ?? $category;
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

            $result = $this->database->select($query, $params);
            $todayCount = (int) ($result[0]['total'] ?? 0);

            return $todayCount >= $dailyLimit;
        } catch (Exception $e) {
            $this->log('Warning: Could not check daily post limit: ' . $e->getMessage());
            return false;
        }
    }

    private function ensurePageMetadataColumns() {
        try {
            $pageKeyColumn = $this->database->query("SHOW COLUMNS FROM facebook_posts LIKE 'page_key'");
            $pageIdColumn = $this->database->query("SHOW COLUMNS FROM facebook_posts LIKE 'page_id'");

            $hasPageKey = $pageKeyColumn && $pageKeyColumn->fetch(PDO::FETCH_ASSOC);
            $hasPageId = $pageIdColumn && $pageIdColumn->fetch(PDO::FETCH_ASSOC);

            if (!$hasPageKey) {
                $this->database->exec("ALTER TABLE facebook_posts ADD COLUMN page_key VARCHAR(100) NULL AFTER category");
            }

            if (!$hasPageId) {
                $this->database->exec("ALTER TABLE facebook_posts ADD COLUMN page_id VARCHAR(255) NULL AFTER page_key");
            }

            $this->hasPageMetadataColumns = true;
        } catch (Exception $e) {
            $this->hasPageMetadataColumns = false;
            $this->log('Warning: Could not ensure page metadata columns: ' . $e->getMessage());
        }
    }
}

$pageKey = $argv[1] ?? null;
$category = $argv[2] ?? null;

if ($pageKey) {
    $cron = new FacebookPosterCron($pageKey, $category);
    $cron->execute();
    exit;
}

foreach (array_keys(getFacebookPageTargets()) as $configuredPageKey) {
    $cron = new FacebookPosterCron($configuredPageKey, $category);
    $cron->execute();
}

?>
