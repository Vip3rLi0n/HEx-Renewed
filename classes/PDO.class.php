<?php

class PDO_DB {

    public $dbh; 
    private static $dsn1  = 'mysql:host=localhost';
    private static $dsn2  = ';port=6666;dbname=game';
    private static $user = 'he'; 
    private static $pass = 'REDACTED'; 
    private static $dbOptions = array(
        PDO::ATTR_CASE => PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );

    public static function factory() {
            
        if(!isset(self::$dbh)){
            $dbh = new PDO(self::$dsn1.self::$dsn2,self::$user,self::$pass, self::$dbOptions); 
        }
        return $dbh;
    }
}
?>