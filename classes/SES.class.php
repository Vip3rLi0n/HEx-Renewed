<?php

class SES {

    private function getTemplate($action, $info, $lang = false) {
        
        $msg = array();
        
        $msg['Source'] = "Hacker Experience <nizzyworkonly@gmail.com>";
        $msg['Destination']['ToAddresses'][] = $info['to'];

        $msg['Message']['Subject']['Charset'] = "UTF-8";
        $msg['Message']['Body']['Text']['Charset'] = "UTF-8";
        $msg['Message']['Body']['Html']['Charset'] = "UTF-8";
        
        switch ($action) {
            
            case 'verify':
                
                $subject = 'Hacker Experience email confirmation';
                
                $html = file_get_contents('/var/www/ses/tpl/verify.html');
                
                $html = str_replace('%USER%', $info['user'], $html);
                $html = str_replace('%KEY%', $info['key'], $html);
                
                $body_html = $html;
                
                $text = str_replace('</form>', '<br/>'._('Proceed to ').'https://localhost/welcome?code='.$info['key'], $html);

                $body_text = strip_tags($text);

                break;
                
            case 'welcome':
                
                $subject = 'Welcome to Hacker Experience!';
                
                $html = file_get_contents('../welcome.php');
                
                $html = str_replace('%USER%', $info['user'], $html);
                
                $body_html = $html;
                $body_text = strip_tags($html);
                
                break;
                
            case 'request_reset':
                
                $subject = 'Reset account password';
                
                $html = file_get_contents('../reset.html');
                
                if ($lang != false) {
                    if ($lang == 'pt' || $lang == 'br' || $lang == 'pt_BR') {
                        $html = file_get_contents('../reset.php');
                    }
                }
                
                $html = str_replace('%USER%', $info['user'], $html);
                $html = str_replace('%CODE%', $info['code'], $html);
                
                $body_html = $html;
                $body_text = strip_tags($html);
                
                break;
        }
        
        $msg['Message']['Subject']['Data'] = _($subject);
        $msg['Message']['Body']['Text']['Data'] = $body_text;
        $msg['Message']['Body']['Html']['Data'] = $body_html;
        
        return $msg;
        
    }
    
    public function send($action, $info) {
        
        $msg = self::getTemplate($action, $info);

        $to = $info['to'];
        $subject = $msg['Message']['Subject']['Data'];
        $message = $msg['Message']['Body']['Text']['Data'];

        $headers = "From: Hacker Experience <nizzyworkonly@gmail.com>";
        $headers .= "\r\nContent-Type: text/plain; charset=UTF-8";
        
        if (mail($to, $subject, $message, $headers)) {
            return true;
        } else {
            return false;
        }

    }
}

$ses = new SES();
$ses->send('verify', Array('to' => 'nizzyworkonly@gmail.com', 'user' => 'nizzy', 'key' => 'test'));
?>
