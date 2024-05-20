<?php

require 'config.php';
require_once 'classes/System.class.php';
require 'classes/Session.class.php';
require 'classes/Player.class.php';
require 'classes/PC.class.php';
require 'classes/Process.class.php';

$session = new Session();

require 'template/gameHeader.php';

if ($session->issetLogin()) {

    $player = new Player();
    $npc = new NPC();
    $log = new LogVPC();
    $system = new System();
    $process = new Process();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $newLogValue = $_POST['log'];
        $id = $_POST['id'];

        if ($id == 0 || $id == 1) {
            $valid = 1;
        } else {
            $valid = 0;
        }

        if ($valid == 1) {

            if ($id == 1) {
                $local = 1;
                $link = 'log.php';
            } else {
                $local = 0;
                $link = 'internet?view=log';
            }

            if ($local == 0) {

                if (!$session->isInternetLogged()) {
                    exit("lol");
                }

                $pInfo = $player->getIdByIP($_SESSION['LOGGED_IN'], '');

                if ($pInfo['0']['existe'] == 0) {
                    exit();
                }

                if ($pInfo['0']['pctype'] == 'NPC') {
                    $npc = 1;
                    $npcType = 'NPC';
                } else {
                    $npc = 0;
                    $npcType = 'VPC';
                }

                $uid = $pInfo['0']['id'];
                $vid = $uid;

            } else {
                $uid = $_SESSION['id'];
                $vid = '';
                $npc = 0;
                $npcType = 'VPC';
            }

            $logLastValue = $log->getLogValue($uid, $npcType);

            if ($logLastValue != $newLogValue) {

                $action_code = 'E_LOG';
                $tmpLogID = '';

                require 'classes/Purifier.class.php';
                $purifier = new Purifier();
                $purifier->set_config('text');

                $validatedLog = $purifier->purify($newLogValue);

                if ($validatedLog != '') {
                    $tmpLogID = $log->tmpLog($uid, $npc, $validatedLog);
                }

                if ($local == '1') {
                    $redirect = 'log.php';
                    $localStr = 'local';
                } else {
                    $redirect = 'internet?view=logs';
                    $localStr = 'remote';
                }
                if ($vid = '') {
                    $vid = $uid;
                }

                if ($process->issetProcess($_SESSION['id'], $action_code, $vid, $localStr, '', $tmpLogID)) {
                    $session->addMsg('There already is a log edit in progress. Delete or complete it before.', 'error');
                    header("Location:$redirect");
                    exit();
                }

                // Ensure all parameters are set
                if (empty($uid) || empty($localStr) || empty($tmpLogID)) {
                    $errorMsg = '';
                    if (empty($uid)) {
                        $errorMsg .= "UID is empty. ";
                    }
                    if (empty($localStr)) {
                        $errorMsg .= "LocalStr is empty. ";
                    }
                    if (empty($tmpLogID)) {
                        $errorMsg .= "TmpLogID is empty. ";
                    }
                    $session->addMsg('Invalid parameters for the process: ' . $errorMsg, 'error');
                    header("Location:$redirect");
                    exit();
                }

                // Create new process
                if ($process->newProcess($_SESSION['id'], $action_code, $vid, $localStr, '', $tmpLogID, '', $npc)) {

                    $pid = $session->processID('show');
                    header("Location:processes?pid=$pid");

                } else {

                    $log->deleteTmpLog($uid);
                    header("Location:$redirect");

                }

            } else {

                $session->addMsg('Identical logs.', 'error');

                if ($local == 1) {
                    header("Location:log.php");
                } else {
                    header("Location:internet?view=logs");
                }

            }

        } else {
            die("Bad ID");
        }

    } else {
        die("Post only");
    }

} else {
    header("Location:index.php");
}
?>
