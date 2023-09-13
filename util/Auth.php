<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Auth {

    public static function handleLogin() {
        @session_start();
        $logged = $_SESSION['loggedIn'];
        if ($logged == false) {
            session_destroy();
            header('location: '. URL .'login');
            exit;
        }
    }
    
    public static function handleLogout(){
        @session_start();
        $logged = isset($_SESSION['loggedIn']);
        if ($logged == true) {
            header('location: '. URL .'index');
            exit;
        }
    }

}
