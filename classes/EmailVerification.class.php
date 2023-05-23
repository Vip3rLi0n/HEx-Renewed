<?php

class EmailVerification {

    private $pdo;
    public $user;
    private $pass;
    public $email;
    private $session;

    public function __construct() {
        $this->pdo = PDO_DB::factory();
    }

    public function generateKey() {
        return md5(uniqid());
    }

    private function saveKey($userID, $email, $verificationKey) {
        $sql = 'INSERT INTO email_verification (userID, email, code) VALUES (:userID, :email, :code)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(':userID' => $userID, ':email' => $email, ':code' => $verificationKey));

        return $stmt->rowCount() > 0;
    }

    public function sendMail($userID, $regEmail, $username) {
        $verificationKey = self::generateKey();

        if (!self::saveKey($userID, $regEmail, $verificationKey)) {
            return false;
        }

        // Prepare the email content
        $to = $regEmail;
        $subject = "Registration Confirmation";
        $message = "Dear " . $username . ",\n\n";
        $message .= "Thank you for registering at our website. Your registration was successful!\n\n";
        $message .= "Please click on the following link to verify your email:\n";
        $message .= "https://localhost/welcome?code=" . $verificationKey . "\n\n";
        $message .= "Best regards,\n";
        $message .= "Nizzy Inc";

        // Send the email using sendmail
        $headers = "From: nizzyworkonly@gmail.com"; // Replace with your own email address
        mail($to, $subject, $message, $headers);

        return true;
    }

    public function isVerified($userID) {
        $sql = "SELECT COUNT(*) AS total FROM email_verification WHERE userID = :userID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(':userID' => $userID));
        $count = $stmt->fetch(PDO::FETCH_OBJ)->total;
        return $count > 0;
    }

    public function verify($userID, $code) {
        $sql = "SELECT COUNT(*) AS total FROM email_verification WHERE userID = :userID AND code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(':userID' => $userID, ':code' => $code));
        $count = $stmt->fetch(PDO::FETCH_OBJ)->total;

        if ($count > 0) {
            // Verification successful, delete the entry from the table
            $sql = "DELETE FROM email_verification WHERE userID = :userID AND code = :code";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(':userID' => $userID, ':code' => $code));
            return true;
        } else {
            return false;
        }
    }

    public function codeOnlyVerification($code) {
        $sql = "SELECT userID FROM email_verification WHERE code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(':code' => $code));
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return ($result) ? $result->userID : 0;
    }

    public function removeVerificationEntry($userID) {
        $sql = "DELETE FROM email_verification WHERE userID = :userID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(':userID' => $userID));
    }
}