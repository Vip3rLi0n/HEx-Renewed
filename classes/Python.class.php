<?php

class Python {
    private $pythonPath;
    private $gamePath;
    private $path;
    private $file;
    private $args;
    private $query;
    private $queries = 0;
    private $log;
    public $session;

    public function __construct() {
        $this->pythonPath = '/usr/bin/python'; // Path to Python executable within the venv
        $this->gamePath = '/root/hexc';
        $this->path = 'python/';
        $this->args = '';
        $this->log = ' 2>&1 ';
    }

    public function addMsg($msg, $type) {
        $_SESSION['MSG'] = _($msg);
        $_SESSION['TYP'] = $type;
    }

    public function addBadge($userID, $badgeID, $clan = '') {
        $userBadge = ($clan == '') ? 'user' : 'clan';
        $this->path = 'python/';
        $this->file = 'badge_add.py';
        $this->args = escapeshellarg($userBadge).' '.escapeshellarg($userID).' '.escapeshellarg($badgeID);
        $this->queries = 15;
        $this->call();
    }

    public function generateProfile($id, $l = 'en') {
        $this->path = 'python/';
        $this->file = 'profile_generator.py';
        $this->args = escapeshellarg($id).' '.escapeshellarg($l);
        $this->queries = 20;
        $this->call();
    }

    private function call() {
        exec($this->pythonPath.' '.$this->gamePath.'/'.$this->path.$this->file.' '.$this->args.' '.$this->log);
        $this->path = 'python/query_counter.py';
        exec('cd '.$this->gamePath.' && '.$this->pythonPath.' '.$this->path.' '.$this->queries);
    }
}

?>