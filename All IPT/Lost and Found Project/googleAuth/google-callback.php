<?php
session_start();
require_once '../includes/db.php';

// Check if vendor directory exists
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
    
    // Try to load environment variables if .env file exists
    if (class_exists('Dotenv\Dotenv') && file_exists('../.env')) {
        try {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
            
            $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
            $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
            

            /**REDIRECT TO THE LOGIN LINK AFTER VERIFICATION*/
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

        if(isset($_GET['code'])){
            try {

                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                
                if(!isset($token['error'])){
                    $client->setAccessToken($token);
                    
                    $payload = $client->verifyIdToken();
                    
                    if ($payload) {
                        
                        /**EXTRACT INFO FROM THE GOOGLE ACC */
                        $email = $payload['email'];
                        $name = $payload['name'];
                        

                        $name_parts = explode(' ', $name);
                        $first_name = $name_parts[0];
                        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
                        
/**             GENERATE THE USERNAME*/              
                $username = preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $email)[0]);
                        


                        /**CHECK IF THE USER EXISTS IN THE DATABASE */
                        $stmt = $conn->prepare("SELECT * FROM user_info WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        
                        /**IF IT EXISTS, JUST FETCH THEIR INFO FOR THE SESSION DATA */
                        if ($result->num_rows > 0) {

                            $user = $result->fetch_assoc();
                            
                            $_SESSION['username'] = $user['user_name'];
                            $_SESSION['first_name'] = $user['first_name'];
                            $_SESSION['last_name'] = $user['last_name'];
                            $_SESSION['email'] = $user['email'];
                        } else {
                            
                            /**SET THEIR EMAIL AS THEIR PASSWORD (ENCRYPTED) */
                            $password_hash = password_hash($email, PASSWORD_DEFAULT);
                            

                            $contact_num = "";
                            
                            /**INSERT NEW ACC IN THE USER INFO TABLE */
                            $stmt = $conn->prepare("INSERT INTO user_info (user_name, first_name, last_name, password, contact_num, email) 
                                                 VALUES (?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssssss", $username, $first_name, $last_name, $password_hash, $contact_num, $email);
                            $stmt->execute();
                            
                            
                            /**INITIALIZE THE SESSION DATA WITH THE ONE WHO JUST SIGNED UP */
                            $_SESSION['username'] = $username;
                            $_SESSION['first_name'] = $first_name;
                            $_SESSION['last_name'] = $last_name;
                            $_SESSION['email'] = $email;
                        }
                        
                        $_SESSION['success'] = 'Login with Google successful! If this is your first time, your username is "'. $username .'" and your password is set to your email address.';
                        header('Location: ../Frontend Official/index.php');
                        exit();
                    } else {
                        throw new Exception("Unable to verify user information");
                    }
                } else {
                    throw new Exception('Login Failed: ' . ($token['error'] ?? 'Unknown error'));
                }
            } catch (Exception $e) {
                throw new Exception('Error processing Google response: ' . $e->getMessage());
            }
        } else {
            throw new Exception('No authorization code received from Google');
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Google Authentication Error: ' . $e->getMessage();
        header('Location: ../login.php');
        exit();
    }
} else {
    $_SESSION['error'] = "Required dependencies are missing. Please run 'composer install'.";
    header('Location: ../login.php');
    exit();
}
?>