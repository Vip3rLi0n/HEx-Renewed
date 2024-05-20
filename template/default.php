<?php
$l = 'en_US';
putenv("LANG=" . $l);
setlocale(LC_ALL, $l);

$domain = "messages";
bindtextdomain($domain, "locale");
bind_textdomain_codeset($domain, 'UTF-8');

textdomain($domain);
$url = 'https://ha.meowing.cfd';


$script = $msgRegister = $msgLogin = $msgIndex = FALSE;

if(isset($_SESSION['TYP'])){
        
    $script = '<script type="text/javascript"> var header = ';
    
    if($_SESSION['TYP'] == 'REG'){
        $msgIndex = TRUE;
        $script .= '</script>';
    } elseif($_SESSION['TYP'] == 'LOG') {
        $msgLogin = TRUE;
        $script .= '"login";</script>';
    } else {
        $msgIndex = TRUE;
        $script = '</script>';
    }
    
    $error = isset($_SESSION['MSG_TYPE']) && $_SESSION['MSG_TYPE'] == 'error' ? 'alert-danger' : 'alert-success';
    
    $msg = $_SESSION['MSG'];
    
    unset($_SESSION['MSG']);
    unset($_SESSION['TYP']);
    unset($_SESSION['MSG_TYPE']);
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Hacker Apocalypse</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Hacker Apocalypse - The End Of Internet is an online hacking simulation game. Play as a hacker seeking for fame and money. Join now for free.">
        <meta name="keywords" content="Hacker, hacker game, hacking simulation, online hacker game, browser game, pbbg, hacker Apocalypse, computer science game, programming game" />
        <meta name="google-site-verification" content="mHONAFYPI5E0WSX_C4oX4SX5dPGss2EPzm5kXChRrC8" />
<?php if(!isset($_GET['fb_locale']) || ($_GET['fb_locale'] != 'pt_BR')){ ?>
        <meta property="og:locale" content="en_US">
        <meta property="og:title" content="Hacker Apocalypse"/>
        <meta property="og:image" content="./images/og.png"/>
        <meta property="og:url" content="https://ha.meowing.cfd/"/>
        <meta property="og:description" content="Hacker Apocalypse is a browser-based hacking simulation game, where you play the role of a hacker seeking for money and power. Join now!"/>
<?php } ?>
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
        <link href="css/he_index.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/tipTip.css">
        <script src="https://cdn.jsdelivr.net/npm/smooth-scroll/dist/smooth-scroll.min.js"></script>
        <script src="js/rotate.js"></script>
    </head>
    <script>document.addEventListener('DOMContentLoaded',function(){var scroll=new SmoothScroll('a.goto-about[href="#About"]',{speed:800,offset:function(anchor,toggle){var offset=50;return offset;},updateURL:!1})});</script>
    <body>
        <div id="terminal"></div>
        <div class="intro-header">
<?php

$current = 'en';

?>

            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
<?php
if($msgIndex){
?>
                        <div class="alert <?php echo $error; ?> ">
                           <?php echo $msg; ?>
                        </div>
<?php
}
?>
                        <div class="intro-message">
                            <h1><span style="color: grey;">Hacker </span><span style="color: #ff4747;">Apocalypse</span></h1>
                            <h3 class="txt-rotate" data-rotate='["The Internet Is Under Attack", "The Beginning Of The End", "Doom is coming closer", "Why are you still here?"]'></h3>
                            <hr class="intro-divider">
                            <ul class="list-inline intro-social-buttons">
                                <li><a class="btn btn-default btn-lg btn-front goto-login"><i class="fa fa-power-off fa-fw"></i> <span class="network-name"><?php echo _('Login'); ?></span></a></li>
                                <li><a class="btn btn-default btn-lg btn-front goto-signup"><i class="fa fa-plus fa-fw"></i> <span class="network-name"><?php echo _('Register'); ?></span></a></li>
                                <li><a class="btn btn-default btn-lg btn-front goto-about" href="#About"><i class="fa fa-info-circle fa-fw"></i> <span class="network-name"><?php echo _('About'); ?></span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-section-a" id="Login">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-sm-6">
                        <hr id="mLogin" class="section-heading-spacer">
                        <div class="clearfix"></div>
                        <h2 class="section-heading"><?php echo _('Welcome back!'); ?></h2>
                        <p class="lead">
                            <div style="margin-left: 10px"><a href="reset"><?php echo _('I forgot my password.'); ?></a></div>
                            <div style="margin-left: 10px; margin-top: 5px"><a class="goto-signup link"><?php echo _('I don\'t have an account.'); ?></a></div>
                        </p>
                    </div>
                    <div class="col-lg-5 col-lg-offset-2 col-sm-6">
<?php
if($msgLogin){
?>
                        <div class="alert <?php echo $error; ?> alert-login">
                           <?php echo $msg; ?>
                        </div>
<?php
}
?>
                        <div id="container">
                            <form id="login-form" action="login.php" method="POST">
                                <label for="username"><?php echo _('Username'); ?>:</label>
                                <input class="login-input" type="text" id="login-username" name="username">
                                <label for="password"><?php echo _('Password'); ?>:</label>
                                <input class="login-input" type="password" id="login-password" name="password">
                                <div id="lower">
                                    <input class="login-input" type="checkbox" id="keepalive" name="keepalive" checked>
                                    <label class="check" for="keepalive"><?php echo _('Keep me logged in'); ?></label>
                                    <input id="login-submit" class="login-input btn btn-success" type="submit" value="<?php echo _('Login'); ?>">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-section-b" id="About">
            <div class="container">
                <div class="row">
                    <div id="mAbout" class="col-lg-5 col-sm-6">
                        <hr class="section-heading-spacer">
                        <div class="clearfix"></div>
                        <h2 class="section-heading"><?php echo _('About Hacker Apocalypse'); ?></h2>
                        <p class="lead">
                            <?php echo _('Hacker Apocalypse is a browser-based hacking simulation game, where you play the role of a hacker seeking for money and power.'); ?>
                        </p>
                        <p class="lead">
                            <?php echo _('Play online against other users from all the globe on an exciting battle to see who can conquer the Internet.'); ?>
                        </p>
                        <p class="lead">
                            <?php echo _('Hack, install viruses, research better software, complete missions, steal money from bank accounts and much more.'); ?>
                        </p>
                        <p class="lead">
                            <?php echo sprintf(_('%sSign up now%s for free and join thousands of other players trying to be the most powerful hacker of the game.'), '<a class="goto-signup link">', '</a>'); ?>
                        </p>
                        
                    </div>
                    <div class="col-lg-5 col-lg-offset-2 col-sm-6">
                        <ul class="nav ul-about">
                            <li class="about1 about btn btn-success" data-toggle="tooltip" title="Hack players, companies, banks and even the NSA! Use the best exploits, port scanners and brute-force crackers you find!"><i class="fa fa-terminal fa-fw"></i> <span>Hack players</span></li>
                            <li class="about btn btn-success" data-toggle="tooltip" title="Spam bots, warez, bitcoin miners or DDoS slaves. Create your own virus army."><i class="fa fa-bug fa-fw"></i> <span>Install viruses</span></li>
                            <li class="about btn btn-success" data-toggle="tooltip" title="Collect money from your virus, or complete missions for it! Hacking bank accounts is also an option."><i class="fa fa-dollar fa-fw"></i> <span>Earn money</span></li>
                            <li class="about btn btn-success" data-toggle="tooltip" title="Hack the system! Your bitcoin miners will do the hard hashing work for you. Buy or sell bitcoins at real price market. If you manage to get someone's key, you can transfer all his BTC to you :)"><i class="fa fa-btc fa-fw"></i> <span>Mine bitcoins</span></li>
                            <li class="about btn btn-success" data-toggle="tooltip" title="Get yourself a better processor, memory, HD or increase your internet connection speed. Buy a shiny external HD to safely backup your files."><i class="fa fa-desktop fa-fw"></i> <span>Upgrade hardware</span></li>
                            <li class="about btn btn-success" data-toggle="tooltip" title="You don't want to rely on a basic 1.0 firewall, right? Research better software. Awesome hackers have awesome tools!"><i class="fa fa-flask fa-fw"></i> <span>Research software</span></li>
                            <li class="about btn btn-success" data-toggle="tooltip" title="Create or join a clan and develop your own team strategies. Engage in exciting clans wars and head to the bounty."><i class="fa fa-users fa-fw"></i> <span>Join a clan</span></li>
                            <li class="about btn btn-success" data-toggle="tooltip" title="Show your power and DDoS your enemies! Severely damage their hardware and overload their network. But be careful, you don't want to get into the FBI Most Wanted list."><i class="fa fa-globe fa-fw"></i> <span>DDoS the world!</span></li><br/>
                            <li class="about-more center" data-toggle="tooltip" data-placement="bottom" title="Really, much more. There is no space here, why don't you join to find out? ;)"><span>... and <strong>much</strong> more!</span></li><br/>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-section-a" id="SignUp">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-lg-offset-1 col-sm-push-6  col-sm-6">
                        <hr class="section-heading-spacer">
                        <div class="clearfix"></div>
                        <h2 class="section-heading"><?php echo sprintf(_('Sign up now. It\'s %sfree%s!'), '<font color="black">', '</font>'); ?></h2>
                        <p class="lead">
                        <div style=""><a class="goto-faq link"><?php echo _('I am scared.'); ?></a></div>
                        <h5 class="play"><?php echo _('Play anywhere!'); ?></h5>
                        <div class="play-icons">
                            <i class="fa fa-windows fa-4x appico"></i>
                            <i class="fa fa-linux fa-4x appico"></i>
                            <i class="fa fa-apple fa-4x appico"></i>
                            <i class="fa fa-mobile fa-4x appico"></i>
                            <i class="fa fa-android fa-4x appico"></i>
                        </div>
                        </p>
                    </div>
                    <div id="mSignUp" class="col-lg-5 col-sm-pull-6  col-sm-6">
<?php
if($msgRegister){
?>
                        <div class="alert <?php echo $error; ?> alert-register">
                           <?php echo _($msg); ?>
                        </div>
<?php
}
?>
                        <form class="form-horizontal" id="signup-form" action="register.php" method="POST">
                        <fieldset class="signup">
                                <br/>
                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="username">Username</label>  
                                    <div class="col-md-8">
                                        <input id="signup-username" name="username" placeholder="Your in-game name." class="form-control input-md" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="password">Password</label>
                                    <div class="col-md-8">
                                        <input id="password" name="password" placeholder="123456" class="form-control input-md" type="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="confirm-password">Confirm</label>
                                    <div class="col-md-8">
                                        <input id="confirm-password" name="confirm-password" placeholder="Re-enter Password" class="form-control input-md" type="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="email">E-mail</label>  
                                    <div class="col-md-8">
                                        <input id="email" name="email" placeholder="We don't spam." class="form-control input-md" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="checkboxes"></label>
                                    <div class="col-md-8">
                                        <div class="checkbox">
                                            <label for="terms">
                                                <input name="terms" id="terms" value="1" type="checkbox">
                                                I accept the <a target="__blank" href="TOS">terms of service</a>.
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="signup"></label>
                                    <div class="col-md-8">
                                        <button id="signup-submit" class="btn btn-success">Sign up</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-section-b" id="FAQ">
            <div class="container">
                <div class="row">
                    <h2 id="freq" class="text-center" style="margin-top: -10px;">Frequent Asked Questions</h2>
                    <br/>
                    <div id="accordion">
                        <h3>Will anything happen to my computer?</h3>
                        <div>
                            <p>There is no need to worry! This is a simulation game that takes place in a virtual world.</p>
                            <p>By the way, no download is needed. You will play the whole time using your browser.</p>
                        </div>
                        <h3>Is this real-life hacking?</h3>
                        <div>
                            <p>No! The whole game is virtual, and you will be hacking "made up" servers from other players. You also won't learn how to hack in real life. No technical knowledge is needed to play the game.</p>
                            <p>You don't actually kill anyone when playing Valorant, right? ;)</p>
                        </div>
                        <h3>Where do I download the game?</h3>
                        <div>
                            <p>You don't! The whole game can be played through your browser, whether you are using a PC with Linux, Windows, Mac, tablet, or mobile phone.</p>
                        </div>
                        <h3>Is it really free?</h3>
                        <div>
                            <p>Oh yeah. You can play the whole game, <strong>with all features</strong>, for free.</p>
                            <p>The user can opt for a premium account to help us directly. This is not a "pay to win" game, though. Premium users have no tactical advantages over basic accounts.</p>
                        </div>
                        <h3>Shouldn't it be "cracker"?</h3>
                        <div>
                            <p>Here comes a <a href="http://www.paulgraham.com/gba.html">looong discussion</a>. Many believe the word <em>hacker</em> should designate the so-called white hat (talented programmer, or an ethical hacker). Others assume it means criminals behind the screen.</p>
                            <p><a href="http://duartes.org/gustavo/blog/post/first-recorded-usage-of-hacker/">History has shown us</a> that maybe it was meant to define the bad guys, however, we do believe that "hacker" means <a href="https://stallman.org/articles/on-hacking.html">way more</a> than that.</p>
                            <p>Regardless of the definition, we want our users to enjoy the game, whether they call it Hacker or Cracker Apocalypse. That's it, name it whatever you want.</p>
                            <p>Meanwhile, we have a special <a href="https://forum.hackerApocalypse.com/">board designated to teach computer science and programming</a> for people. Instead of engaging in useless flame wars, feel free to join and share your knowledge with others. I'd call <em>that</em> hacker :)</p>
                        </div>
                        <h3>Isn't this Hacker Apocalypse / Hacker Wars?</h3>
                        <div>
                            <p>Nope, this is an actively maintained fork of the original Hacker Apocalypse game (Legacy aka Hacker Apocalypse 1 aka HE1 aka HEx).</p>
                            <p>The source was provided to me by the original developer and owner (Renato Massaro), under the MIT license.</p>
                            <p>This source code is still messy, with some improvements, bug fixes, and other changes to make it at least work, unlike the original source code itself.</p>
                            <p>It is now updated to work with PHP 8.x and Python 3.12 so far. I also removed the <strong>Email Registration</strong> function temporarily from the code as it never works as it was quite messy.</p>
                            <p>Captcha also not available for <strong>now</strong> but will be implemented in the future.</p>
                        </div>
                    </div>
                    <div class="faq-buttons-intro">
                        <h3 class="text-center" style="margin-bottom: 20px;">So, what are you waiting?</h2>
                        <ul class="list-inline intro-social-buttons">
                            <li><a class="btn btn-default btn-lg btn-front goto-login btn-faq"><i class="fa fa-power-off fa-fw"></i> <span class="network-name">Login</span></a></li>
                            <li><a class="btn btn-default btn-lg btn-front goto-signup btn-faq"><i class="fa fa-plus fa-fw"></i> <span class="network-name">Register</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>    
        <section id="footer">
            <div class="row">
                <div class="one column"></div>
                <div id="navigate" class="three columns">
                    <h5 class="footer-title">NAVIGATE</h5>
                    <ul>
                        <li><a target="__blank" href="https://ha.meowing.cfd/tos" class="scroll">TOS</a></li>
                        <li><a target="__blank" href="https://ha.meowing.cfd/privacy-policy" class="scroll">PRIVACY</a></li>
                        <li><a href="https://ha.meowing.cfd" class="scroll">STATUS</a></li>
                    </ul>
                </div>
                <div id="legal-disclaimer" class="three columns">
                    <h5 class="footer-title">LEGAL DISCLAIMER</h5>
                    <p style="margin-top: -10px;">
                        Hacker Apocalypse is <strong>NOT</strong> related to any real hacking activity.
                        All in-game content is purely fictional and does not represent real user identification. IP addresses are randomly generated.
                    </p>
                </div>
                <div id="contact" class="four columns text-right">
                    <h5 class="footer-title">CONTACT US</h5>
                    <div class="mail-link">
                        <a href="https://ha.meowing.cfd"><i class="fa fa-home"></i>ha.meowing.cfd</a><br/>
                        <a href="mailto:support@ha.meowing.cfd"><i class="fa fa-envelope-o"></i>support@ha.meowing.cfd</a><br/>
                    </div>
                    <div class="footer-social">
                        <a href="https://facebook.com/HackerApocalypse"><i class="fa fa-facebook-square"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="one column"></div>
                <div class="three columns">
                    <h3 class="footer-title left footer-social"><i class="fa fa-linux" style="color: #fff; font-size: 40px; opacity: 1; cursor: default;" title="Powered by Linux!"></i></h3>
                </div>
                <div class="three columns">
                    <span id="hand" class="footer-social left small" style="margin-left: 35%;">Updated by Meow & Bobokev</span>
                </div>
                <h3 id="neoart" class="footer-title right">2024 &copy; <a href="" style="color: #fff; margin-right: 10px;">Meow - Bobokev</a></h3>
            </div>

        </section>
        <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>-->
        <script src="js/jquery.min.js"></script>
        <script src="js/tooltip.js"></script>
        <script src="js/typed.js"></script>
        <script src="js/jquery.validate.js"></script>
        <script src="js/jquery-ui.js"></script>
        <script src="js/he_index.js"></script>
    </body>
</html>
