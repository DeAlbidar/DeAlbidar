<?php
ob_start();
session_start();
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('Africa/Accra');

require 'config.php';
require 'util/Auth.php';
require_once('libs/geoplugin/geoplugin.class.php');

spl_autoload_register(function($class) {
    include LIBS . $class . '.php';
});

$app = new Bootstrap();

$app->init();