<?php

use Dotenv\Dotenv;

session_start();

// VENDOR CHECKER
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
    
    
/**LOADING .ENV KEYS NEEDED */
    if (class_exists('Dotenv\Dotenv') && file_exists('.env')) {
        try {

            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
            
            $recaptchaSecret = $_ENV['RECAPTCHA_SECRET_KEY'] ?? '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
            
            $recaptchaSecret = trim($recaptchaSecret, '"\'');
        } catch (Exception $e) {

            $recaptchaSecret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

        }
    } else {

        $recaptchaSecret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

    }
} else {
    $recaptchaSecret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
}

require 'includes/db.php';


    /**RECAPTCHA VALIDATION */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';


    if (empty($recaptchaResponse)) {
        $_SESSION['error'] = "Captcha response missing. Please check the box.";
        header('Location: login.php');
        exit();
    }


    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($recaptchaSecret) . "&response=" . urlencode($recaptchaResponse);
    
    $verify = @file_get_contents($verifyUrl);


    if ($verify === false) {
        error_log("reCAPTCHA verification failed: Could not contact Google API at $verifyUrl");
        $_SESSION['error'] = "Captcha Verification Failed: Could not reach verification server. Please try again later.";
        header('Location: login.php');
        exit();
    }
    

    $captchaSuccess = json_decode($verify);


    if (!$captchaSuccess || !$captchaSuccess->success) {
        
        $errorCodes = isset($captchaSuccess->{'error-codes'}) ? implode(', ', $captchaSuccess->{'error-codes'}) : 'N/A';
        error_log("reCAPTCHA verification failed: Google response: " . $verify);
        
        $_SESSION['error'] = "Captcha Verification Failed. Error codes: " . $errorCodes . ". Please try again.";
        header('Location: login.php');
        exit();

    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    try {

        $stmt = $conn->prepare("SELECT * FROM user_info WHERE user_name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();


        /**FOR DEBUGGING CURRENT USER OF THE SESSION INFORMATION*/
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['pub_id'];
            $_SESSION['username'] = $user['user_name'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['contact_num'] = $user['contact_num'];
            $_SESSION['success'] = "Login successful!";
            
            header("Location: Frontend Official/index.php");
            exit();

        } else {

            $_SESSION['error'] = "Invalid username or password";
            header('Location: login.php');
            exit();

        }
    } catch (Exception $e) {

        $_SESSION['error'] = "An error occurred during login: " . $e->getMessage();
        header('Location: login.php');
        exit();

    }
}
