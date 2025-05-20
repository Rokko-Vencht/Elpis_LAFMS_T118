<?php
session_start();

// LOGGED IN?
if (!isset($_SESSION['user_id'])) {
    
    // Redirect to login page if not logged in
    header('Location: ../login.php');
    exit();
}

// FETCH USER INFO
$username = $_SESSION['username'];
$firstName = $_SESSION['first_name'];
$lastName = $_SESSION['last_name'];

$fullName = $firstName . ' ' . $lastName;

// MORE NEEDED INFO
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$contactNum = isset($_SESSION['contact_num']) ? $_SESSION['contact_num'] : '';

if (isset($_SESSION['success'])) {
    // Display success message
    unset($_SESSION['success']); 
}
?> 