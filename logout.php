<?php

require 'classes/Session.class.php';
require 'classes/Ranking.class.php';

$session  = new Session();
$ranking = new Ranking();

$ranking->updateTimePlayed();


$session->logout();



if($session->issetFBLogin()){
    
    require_once 'classes/Facebook.class.php';

    $facebook = new Facebook(array(
        'appId' => 'REDACTED',
        'secret' => 'REDACTED'
    ));

    $facebook->destroySession();
    
}

header("Location:index.php");
exit();