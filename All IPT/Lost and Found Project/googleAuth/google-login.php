<?php
session_start();

if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
    
    /**LOAD .ENV FILE */
    if (class_exists('Dotenv\Dotenv') && file_exists('../.env')) {
        try {

            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
            

            $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
            $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
            

            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $path = '/All%20IPT/Lost%20and%20Found%20Project/googleAuth/google-callback.php';
            $redirectUri = $protocol . $host . $path;
            

            $clientId = trim($clientId, '"\'');
            $clientSecret = trim($clientSecret, '"\'');
            

            if (empty($clientId) || empty($clientSecret)) {
                throw new Exception('Google OAuth credentials are not properly configured in .env file');
            }

        } catch (Exception $e) {

            $_SESSION['error'] = "Error loading environment variables: " . $e->getMessage();
            header('Location: ../login.php');
            exit();

        }
    } else {

        $_SESSION['error'] = "Google login is not configured. Please set up your .env file with Google OAuth credentials.";
        header('Location: ../login.php');
        exit();

    }

    try {

        $client = new \Google\Client();
        $client->setApplicationName('Lost and Found Project');
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUri);
        

        $client->addScope('email');
        $client->addScope('profile');
        $client->addScope('openid');
        

        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();
        if (empty($authUrl)) {
            throw new Exception('Failed to create authentication URL');
        }

        header('Location: ' . $authUrl);
        exit();

    } catch (Exception $e) {

        $_SESSION['error'] = "Google authentication error: " . $e->getMessage();
        header('Location: ../login.php');
        exit();

    }
} else {

    $_SESSION['error'] = "Required dependencies are missing. Please run 'composer install'.";
    header('Location: ../login.php');
    exit();
    
}
?>