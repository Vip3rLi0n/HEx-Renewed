<?php
class LRSys {

    public $name;
    /**
     * Summary of user
     * @var 
     */
    public $user;
    private $pass;
    public $email;
    public $keepalive;
    public $session;
    private $lang;
    private $pdo;
    private $process;

    private $log;
    private $ranking;
    private $storyline;
    private $clan;
    private $mission;
    
    function __construct() {
        require_once 'Session.class.php';
        $this->pdo = PDO_DB::factory();
        $this->session = new Session();
        require 'Player.class.php';
        require 'PC.class.php';
        require 'Ranking.class.php';
        require 'Storyline.class.php';
        require 'Clan.class.php';

        $this->log = new LogVPC();
        $this->ranking = new Ranking();
        $this->storyline = new Storyline();
        $this->clan = new Clan();

        $this->keepalive = FALSE;
        
    }

    public function set_keepalive($keep){
        $this->keepalive = $keep;
    }

    private function verifyRegister() {
        $system = new System();
        
        if(!$system->validate($this->user, 'username')){
            $this->session->addMsg(sprintf(_('Invalid username. Allowed characters are %s.'), '<strong>azAZ09._-</strong>'), 'error');
            return FALSE;
        }
        
        if(!$system->validate($this->email, 'email')){
            $this->session->addMsg(sprintf(_('The email %s is not valid.'), '<strong>'.$this->email.'</strong>'), 'error');
            return FALSE;
        }

        if ((strlen(preg_replace('![^A-Z]+!', '', $this->email)) >= 5 && preg_match_all("/[0-9]/", $this->email) >= 2) || preg_match_all("/[0-9]/", $this->email) >= 5){
            $this->session->addMsg(_('Registration complete. You can login now.'), 'notice');
            return FALSE;
        }

        if (strlen(preg_replace('![^A-Z]+!', '', $this->email)) >= 2 && strlen($this->email) <= 12){
            $this->session->addMsg(_('Registration complete. You can login now.'), 'notice');
            return FALSE;
        }
        
        $this->session->newQuery();
        $sqlQuery = "SELECT email FROM users WHERE login = ? OR email = ? LIMIT 1";
        $sqlLog = $this->pdo->prepare($sqlQuery);
        $sqlLog->execute(array($this->user, $this->email));

        if ($sqlLog->rowCount() == '1') {

            $dados = $sqlLog->fetch();

            if ($dados['email'] == $this->email) {
                $this->session->addMsg('This email is already used.', 'error');
            } else {
                $this->session->addMsg('This username is already taken.', 'error');
            }
            
            return FALSE;             
        } elseif (strlen($this->user) == '0' || strlen($this->pass) == '0' || strlen($this->email) == '0') {
            $this->session->addMsg('Some fields are empty.', 'error');
            return FALSE;
        }
        
        if(strlen($this->user) > 15){
            $this->session->addMsg('Yor username is too big :( Please, limit it to 15 characteres.', 'error');
            return FALSE;
        }
        return TRUE;
    }

    public function getLastInsertedID()
    {
        return $this->pdo->lastInsertId();
    }

    public function login($logUser, $logPass, $special = FALSE) {
        date_default_timezone_set('UTC');
        $remember = FALSE;
    
        if ($special) {
            if ($special == 'remember') {
                $remember = TRUE;
            } else {
                exit("Edit special");
            }
        }
    
        if (!$this->session) {
            $this->session = new Session();
        }
    
        require_once 'Mission.class.php';
        $this->mission = new Mission();
        $this->user = $logUser;
        $this->pass = $logPass;
    
        if ($this->verifyLogin(FALSE, $remember, FALSE)) {
            require 'BCrypt.class.php';
            $bcrypt = new BCrypt();
            $this->session->newQuery();
            $sqlQuery = "SELECT password, id FROM users WHERE BINARY login = ? LIMIT 1";
            $sqlLog = $this->pdo->prepare($sqlQuery);
            $sqlLog->execute(array($this->user));
    
            if ($sqlLog->rowCount() == '1') {
                $dados = $sqlLog->fetchAll();
    
                if ($bcrypt->verify($this->pass, $dados['0']['password']) || $remember) {
                    $log = $this->log;
                    $ranking = $this->ranking;
                    $storyline = $this->storyline;
                    $clan = $this->clan;
    
                    $sql = "SELECT COUNT(*) AS total FROM users_premium WHERE id = ".$dados['0']['id']." LIMIT 1";
                    $total = $this->pdo->query($sql)->fetch(PDO::FETCH_OBJ)->total;
    
                    if ($total == 1) {
                        $premium = 1;
                    } else {
                        $premium = 0;
                    }
    
                    $this->session->loginSession($dados['0']['id'], $this->user, $premium, $special);
                    self::loginDatabase($dados['0']['id']);
                    $certsArray = $ranking->cert_getAll();
                    $this->mission->restoreMissionSession($dados['0']['id']);
                    $this->session->certSession($certsArray);
    
                    if ($clan->playerHaveClan($dados['0']['id'])) {
                        $_SESSION['CLAN_ID'] = $clan->getPlayerClan($dados['0']['id']);
                    } else {
                        $_SESSION['CLAN_ID'] = 0;
                    }
    
                    $_SESSION['LAST_CHECK'] = new DateTime('now');
                    $_SESSION['ROUND_STATUS'] = $storyline->round_status();
    
                    if ($_SESSION['ROUND_STATUS'] == 1) {
                        $log->addLog($dados['0']['id'], $log->logText('LOGIN', Array(0)), '0');
                        $this->session->exp_add('LOGIN');
                    }
    
                    return TRUE;
                } else {
                    $this->session->addMsg('Username and password doesn\'t match. Try again!', 'error');
                    return FALSE;
                }
            } else {
                $this->session->addMsg('Username and password doesn\'t match. Try again!', 'error');
                return FALSE;
            }
        }
    }    
    
    private function loginDatabase($id){
        
        $this->session->newQuery();
        $sql = 'SELECT COUNT(*) AS total FROM users_online WHERE id = '.$id.' LIMIT 1';
        if($this->pdo->query($sql)->fetch(PDO::FETCH_OBJ)->total > 0){
            $this->session->newQuery();
            $sql = 'DELETE FROM users_online WHERE id = '.$id.' LIMIT 1';
            $this->pdo->query($sql);
        }
        require_once 'RememberMe.class.php';
        $key = pack("H*", '70617373776F7264243132333135313534');
        $rememberMe = new RememberMe($key, $this->pdo);
        $rememberMe->remember($id, false, $this->keepalive);
        $this->session->newQuery();
        $sql = 'UPDATE users SET lastLogin = NOW() WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(':id' => $id));
        setcookie('logged', '1', time() + 172800);
        
        
    }
    
    private function verifyLogin($fb, $tt, $rm) {
        if($fb || $rm || $tt){
            return TRUE;
        }
        if (strlen($this->user) == '0' || strlen($this->pass) == '0') {
            $this->session->addMsg('Some fields are empty.', 'error');
            return FALSE;
        } else {
            return TRUE;
        }
    }
}

?>