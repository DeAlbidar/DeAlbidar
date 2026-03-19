<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'w3multim_Albidar');
define('DB_USER', 'w3multim_albidar');
define('DB_PASS', 'U=U2tR*%&gs.');

define('CREATED', 101);
define('EXISTS', 102);
define('FAILURE', 103);
define('AUTHENTICATED', 201);
define('NOT_FOUND', 202);
define('PASSWORD_DO_NOT_MATCH', 203);
define('ENABLE_RTL_MODE', 'false');

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

// This is for other hash keys... Not sure yet
define('HASH_GENERAL_KEY', 'MixitUp200');

// This is for database passwords only
define('HASH_PASSWORD_KEY', 'catsFLYhigh2000miles');

define('LIBS', 'libs/');

define('MAILER_DIR', 'libs/PHPMailer/');

// Facebook Graph API Configuration
define('FACEBOOK_PAGE_ID', '101849324975567');
define('FACEBOOK_ACCESS_TOKEN', 'EAANU5a3nV4MBQ1vCvGjb7UhyWIOqJVdZCbZBeKKZCjsMkY5scAJvXg55vtDrJv9mhOv3WCGz7mwWhujZCk92GriifLMmqsA7aJPuqeRvNQsL2DqwofQ88AKokPquqP0gxRYIOqRifJB5tgk9z6uFpOpjn2QykhMs4nyfHplkbTTZB59dV7yfapLqmOKWQdZCKEQzIOeCMggkZA7vUN79zNBLz2IRbR9avutZCZAhPUh4mQgZDZD');
