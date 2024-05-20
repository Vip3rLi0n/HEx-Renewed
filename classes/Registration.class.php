<?php

class Register {
    public function createUser($username, $password, $email, $realIP, $homeIP, $social_network) {
        // Construct command to execute Python script
        $command = "python /opt/hexc/python/create_user.py $username $password $email $realIP $homeIP $social_network";

        // Execute the command
        exec($command, $output, $returnCode);

        // Check if command execution was successful
        if ($returnCode === 0) {
            // Registration successful, add success message
            $this->addMsg('Registration complete. You can login now without email verification.', 'success');
        } else {
            // Registration failed, add error message
            $this->addMsg('Registration failed. Please try again later.', 'error');
        }
    }

    private function addMsg($message, $type) {
        // Set the message and type in session
        $_SESSION['MSG'] = $message;
        $_SESSION['TYP'] = $type; // REG, LOG, or other types
    }
}

?>
