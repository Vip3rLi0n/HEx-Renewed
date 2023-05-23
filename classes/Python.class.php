<?php

class Python {

    private $python_path;
    private $game_path;
    private $path;
    private $file;
    private $args;
    private $queries = 0;
    private $log;
    public $session;
    
    function __construct(){
        $this->python_path = 'C:/Users/User/AppData/Local/Programs/Python/Python310/python.exe';
        $this->game_path = 'C:/xampp/htdocs/'; // CHANGE TO YOUR ABSOLUTE GAME PATH
        $this->args = '';
        $this->log = ' 2>&1 ';
        
    }
    public function addMsg($msg, $type) {
        $_SESSION['MSG'] = _($msg);
        $_SESSION['TYP'] = $type; // REG, LOG, or other types
    }

    public function add_badge($userID, $badgeID, $clan = ''){
        
        if($clan == ''){
            $userBadge = 'user';
        } else {
            $userBadge = 'clan';
        }
        
        $this->path = 'python/';
        $this->file = 'badge_add.py';
        $this->args = escapeshellarg($userBadge).' '.escapeshellarg($userID).' '.escapeshellarg($badgeID);
        $this->queries = 15;

        self::call();
        
    }
    
    public function generateProfile($id, $l = 'en'){
        
        $this->path = 'python/';
        $this->file = 'profile_generator.py';
        $this->args = escapeshellarg($id).' '.escapeshellarg($l);
        $this->queries = 20;

        self::call();

    }
    
    public function createUser($username, $password, $email, $facebook = 0, $social_network = ''){
        
        $this->path = 'python/';
        $this->file = 'create_user.py';
        $this->args = escapeshellarg($username).' '.escapeshellarg($password).' '.escapeshellarg($email).' '.escapeshellarg($facebook).' '.escapeshellarg($social_network);
        $this->queries = 10;
        $this->addMsg('Registration complete. You can login now without email verification.', 'success');
        self::call();
    }
    
    private function call(){
        exec($this->python_path.' '.$this->game_path.$this->path.$this->file.' '.$this->args.$this->log);
        $this->game_path = 'C:/xampp/htdocs/';
        $this->python_path = 'C:/Users/User/AppData/Local/Programs/Python/Python310/python.exe';
        $this->path = 'python/query_counter.py';
        exec('cd '.$this->game_path.' && '.$this->python_path.' '.$this->path.' '.$this->queries);

                
    }
    
}

?>
