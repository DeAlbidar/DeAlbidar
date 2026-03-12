#!/usr/bin/env php
<?php
/**
 * Cron Job: Post Trending Content to Facebook
 * 
 * This script calls the FacebookPoster controller through the framework
 * 
 * Setup: Add this to crontab -e
 *  /var/www/html/DeAlbidar/cron/facebook_poster_cron.php >> /var/log/facebook_poster.log 2>&1
 */

// Set timezone and include framework config
date_default_timezone_set('UTC');
chdir('/var/www/html/DeAlbidar');

// Include framework config
require 'config.php';

// Simulate the framework routing
$_GET['url'] = 'facebookposter/post';

// Include the framework bootstrap
require 'libs/Bootstrap.php';
require 'libs/Controller.php';
require 'libs/View.php';
require 'libs/Model.php';
require 'libs/Database.php';
require 'libs/FacebookAPI.php';
require 'libs/ContentGenerator.php';

// Auto-load other classes
spl_autoload_register(function($class) {
    $paths = [
        'libs/',
        'controllers/',
        'models/'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path . $class . '.php')) {
            include $path . $class . '.php';
            return;
        }
    }
});

// Initialize and run the framework
try {
    $app = new Bootstrap();
    $app->init();
} catch (Exception $e) {
    echo '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

?>
