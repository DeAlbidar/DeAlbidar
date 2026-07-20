<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('DB_TYPE', 'mysql');

define('CREATED', 101);
define('EXISTS', 102);
define('FAILURE', 103);
define('AUTHENTICATED', 201);
define('NOT_FOUND', 202);
define('PASSWORD_DO_NOT_MATCH', 203);
define('ENABLE_RTL_MODE', 'false');

// Local/secret configuration (DB credentials, hash keys, reCAPTCHA secret, SMTP
// credentials). Real values live in the gitignored config.local.php; see
// config.local.php.example for the template.
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

if (!defined('DB_HOST')) {
    define('DB_HOST', '');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', '');
}

if (!defined('DB_USER')) {
    define('DB_USER', '');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

// Only set these if we're in a web context
if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI']) && isset($_SERVER['SERVER_NAME'])) {
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    define('ACTUAL_URL', $actual_link);
    
    // Always provide a TRAILING SLASH (/) AFTER A PATH
    define('URL', 'https://'.$_SERVER['SERVER_NAME'].'/');
} else {
    // CLI context
    define('ACTUAL_URL', 'https://dealbidar.com/');
    define('URL', 'https://dealbidar.com/');
}

if (!defined('HASH_GENERAL_KEY')) {
    define('HASH_GENERAL_KEY', '');
}

if (!defined('HASH_PASSWORD_KEY')) {
    define('HASH_PASSWORD_KEY', '');
}

if (!defined('RECAPTCHA_SECRET_KEY')) {
    define('RECAPTCHA_SECRET_KEY', '');
}

if (!defined('SMTP_HOST')) {
    define('SMTP_HOST', '');
}

if (!defined('SMTP_USERNAME')) {
    define('SMTP_USERNAME', '');
}

if (!defined('SMTP_PASSWORD')) {
    define('SMTP_PASSWORD', '');
}

if (!defined('SMTP_PORT')) {
    define('SMTP_PORT', 587);
}

if (!defined('SMTP_SECURE')) {
    define('SMTP_SECURE', 'tls');
}

if (!defined('SMTP_FROM_EMAIL')) {
    define('SMTP_FROM_EMAIL', '');
}

if (!defined('SMTP_FROM_NAME')) {
    define('SMTP_FROM_NAME', '');
}

if (!defined('SMTP_CC')) {
    define('SMTP_CC', '');
}

if (!defined('SMTP_BCC')) {
    define('SMTP_BCC', '');
}

define('LIBS', 'libs/');

define('MAILER_DIR', 'libs/PHPMailer/');

// Facebook Graph API Configuration
require_once __DIR__ . '/facebook_pages.php';
