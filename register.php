<?php
session_start();
require 'classes/Registration.class.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data and sanitize it
    $username = escapeshellarg($_POST['username']);
    $password = escapeshellarg($_POST['password']);
    $email = escapeshellarg($_POST['email']);
    $realIP = $_SERVER['REMOTE_ADDR'];
    $homeIP = $_SERVER['REMOTE_ADDR'];
    $social_network = 0;

    // Create a new instance of the Python class
    $Register = new Register();

    // Call the createUser() function
    $Register->createUser($username, $password, $email, $realIP, $homeIP, $social_network);

    // Set the success message
    //$python->addMsg('Registration complete. You can login now without email verification.', 'success');

    // Redirect to the login page
    header('Location: login.php');
    exit();
}
?>
