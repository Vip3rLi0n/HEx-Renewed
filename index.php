<?php
require 'config.php';
require 'classes/Session.class.php';
require 'classes/System.class.php';
session_start();
//$session = new Session();
$remembered = FALSE;
$sub = 'Home';


if(isset($_COOKIE['PHPSESSID'])){
    $session = new Session();
}

if(!isset($_SESSION['id'])){
    
    if(isset($_GET['nologin'])){
        $_SESSION = NULL;
        session_destroy();
        header("Location:index");
        exit();
    }
    require 'classes/RememberMe.class.php';
    $key = pack("H*", '70617373776F7264243132333135313534');
    $remember = new RememberMe($key, PDO_DB::factory());
    $remember->rememberlogin();
    $remembered = TRUE;
    
}

if (isset($_SESSION['id'])) {
    
    $session = new Session();
    
    if(!$remembered) require_once 'classes/Player.class.php';
    $player = new Player($_SESSION['id']);

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)){

        $player->handlePost();

    }    
    ob_start();
    require 'template/contentStart.php';

    if($session->issetMsg()){
        $session->returnMsg();
    }
    
    if($_SESSION['ROUND_STATUS'] == 1){

        $player->showIndex();

    } else {

        $player->showGameOver();

    }
    ob_end_flush();
    ob_start();
    require 'template/contentEnd.php';
    ob_end_flush();
} else {
    if(isset($_SESSION)) unset($_SESSION['GOING_ON']);
    ob_start();
    require 'template/default.php';
    ob_end_flush();
}
