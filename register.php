<?php
session_start();
require 'classes/Python.class.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Create a new instance of the Python class
    $python = new Python();
    $facebook = 0;
    $social_network = '';

    // Call the createUser() function
    $python->createUser($username, $password, $email, $facebook, $social_network);

    // Set the success message
    $python->addMsg('Registration complete. You can login now without email verification.', 'success');

    // Redirect to the login page
    header('Location: login.php');
    exit();
}
?>