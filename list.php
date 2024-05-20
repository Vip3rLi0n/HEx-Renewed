<?php

require 'classes/Session.class.php';
require 'config.php';
require 'classes/System.class.php';
require 'classes/Player.class.php';
require 'classes/PC.class.php';
require 'classes/List.class.php';
require_once 'classes/Finances.class.php';
require_once 'classes/Ranking.class.php';

$session = new Session();
$system = new System();
ob_start();
$sub = 'Hacked Database';
require 'template/contentStart.php';

$software = new SoftwareVPC();
$finances = new Finances();
$ranking = new Ranking();



$list = new Lists();
$virus = new Virus();

$ipList = 'active';
$bankAcc = '';
$collect = '';
$ddos = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)){

    $list->handlePost();

}    

if($system->issetGet('show')){
    if($_GET['show'] == 'bankaccounts'){
        $bankAcc = 'active';
        $ipList = '';
    }
}

$add = '';

if($system->issetGet('action')){

    $actionInfo = $system->switchGet('action', 'collect', 'ddos');

    if($actionInfo['ISSET_GET'] == 1){

        $bankAcc = '';
        $ipList = '';            

        switch($actionInfo['GET_VALUE']){
            case 'collect':
                $collect = 'active';
                break;
            case 'ddos':
                $ddos = 'active';
                break;
            case 'manage':
                $ipList = 'active';
                break;
        }
        
        $add = $actionInfo['GET_VALUE'];

    } else {
        $system->handleError('INVALID_GET');
    }
}

?>


<div class="span12">

    <?php

    if($session->issetMsg()){
        $session->returnMsg();
    }        

    ?>

    <div class="widget-box">

        <div class="widget-title">
            <ul class="nav nav-tabs">
                <li class="link <?php echo $ipList; ?>"><a href="list.php"><span class="icon-tab he16-list_ip"></span><span class="hide-phone"><?php echo _('IP List'); ?></span></a></li>
                <li class="link <?php echo $bankAcc; ?>"><a href="?show=bankaccounts"><span class="icon-tab he16-list_bank"></span><span class="hide-phone"><?php echo _('Bank Accounts'); ?></span></a></li>
                <li class="link <?php echo $collect; ?>"><a href="?action=collect"><span class="icon-tab he16-list_collect"></span><span class="hide-phone"><?php echo _('Collect money'); ?></span></a></li>
                <li class="link <?php echo $ddos; ?>"><a href="?action=ddos"><span class="icon-tab he16-ddos"></span><span class="hide-phone">DDoS</span></a></li>
                <a href="<?php echo $session->help('list', $add); ?>"><span class="label label-info"><?php echo _("Help"); ?></span></a>
            </ul>
        </div>

        <div class="widget-content padding noborder">

            <div class="span12">


<?php

$gotAction = '0';

if ($system->issetGet('action')) {
    $actionInfo = $system->verifyStringGet('action');
    $gotAction = '1';
}

if ($gotAction == '1') {

    switch ($actionInfo['GET_VALUE']) {

        case 'collect':

            if($system->issetGet('show')){
                
                $list->show_lastCollect();
                
            } else {
                
                $list->show_collect();
                

                
            }
            
?>
            </div>
        </div>
        <div class="nav nav-tabs" style="clear:both;">&nbsp;</div>
<?php

            break;
        case 'ddos':

            if(!$ranking->cert_have(4)){
                $system->handleError('NO_CERTIFICATION', 'list.php');
            }

            $process = new Process();
            $issetDDoS = $process->issetDDoSProcess();

            if(!$issetDDoS || $system->issetGet('ignore')){

                $list->show_ddos();

            } else {

                $process->getProcessInfo($issetDDoS);
                $process->showProcess();
                        
?>
            </div>
        </div>
        <div class="nav nav-tabs" style="clear:both;">&nbsp;</div>
<?php

            }

            break;
        default:
            $list->showList();
            break;
    }
} else {

    if($system->issetGet('show')){

        $pageInfo = $system->switchGet('show', 'bankaccounts', 'ip');

        if($pageInfo['ISSET_GET'] == 1){

            switch($pageInfo['GET_VALUE']){

                case 'bankaccounts':
                    $list->showBankList();
                    break;
                case 'ip':
                    $list->showList();
                    break;

            }

        } else {
            $system->handleError('INVALID_GET', 'list.php');
        }

    } else {

        $list->listNotification();

        $list->showList();

    }

}
ob_end_flush();
ob_start();
require 'template/contentEnd.php';
ob_end_flush();

?>