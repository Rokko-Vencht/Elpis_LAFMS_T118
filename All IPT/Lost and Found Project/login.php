<?php
session_start();

// Debugging information
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if vendor directory exists
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';

    // Try to load environment variables if .env file exists
    if (class_exists('Dotenv\Dotenv') && file_exists('.env')) {
        try {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
            
            // Check if the RECAPTCHA_SITE_KEY is set and not empty
            if (isset($_ENV['RECAPTCHA_SITE_KEY']) && !empty($_ENV['RECAPTCHA_SITE_KEY'])) {
                $siteKey = $_ENV['RECAPTCHA_SITE_KEY'];
                // Remove any quotes that might have been accidentally added
                $siteKey = trim($siteKey, '"\'');
            } else {
                throw new Exception('RECAPTCHA_SITE_KEY is not set in .env file');
            }
        } catch (Exception $e) {
            // Log the error with more details
            error_log('Error loading reCAPTCHA configuration: ' . $e->getMessage());
            $_SESSION['error'] = 'Error loading reCAPTCHA configuration. Please contact administrator.';
            // Fallback to test key only in development
            $siteKey = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
        }
    } else {
        $_SESSION['error'] = '.env file is missing or Dotenv is not available';
        $siteKey = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    }
} else {
    $_SESSION['error'] = 'vendor/autoload.php is missing. Please run composer install';
    $siteKey = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found Log-in</title>
    <link rel="stylesheet" href="login_fp.css">
    <!-- Add reCAPTCHA API Script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        .google-login {
            margin: 20px 0;
            text-align: center;
            width: 300px; /* Match your form width */
        }

        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
            color: #3c4043;
            border: 1px solid #dadce0;
            border-radius: 4px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            width: 100%;
            box-sizing: border-box;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            font-family: 'Roboto', Arial, sans-serif;
            margin-bottom: 15px;
        }

        .google-btn img {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            vertical-align: middle;
        }

        .google-btn:hover {
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.12);
        }

        .google-btn:active {
            background-color: #f1f3f4;
            box-shadow: 0 1px 2px rgba(0,0,0,0.15);
            transform: translateY(1px);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            justify-content: center;
            margin: 15px 0;
            color: #5f6368;
        }

        .alert {
            margin: 10px 0;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }

        .recaptcha-container {
            display: flex;
            flex-direction: column;
            align-items: left;
            margin-bottom: 15px;
        }

        .g-recaptcha {
            margin: 0 auto;
            margin-bottom: 5px;
            transform: scale(0.95);
            transform-origin: left center;
            height: 78px; /* Match login button height + padding */
            width: 100%;
        }
        
        .recaptcha-instruction {
            font-size: 12px;
            color: #5f6368;
            margin-bottom: 10px;
            text-align: left;
            width: 100%;
        }

        .login-button {
            margin-top: 10px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="login-container">    
        <div class="logo-section">
            <img src="images/lostlink_logo.png" alt="Lost and Found">
        </div>

        <form action="login_validate.php" method="POST">
            <div class="form-section">
                <h2>Login to LostLink</h2>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success']; ?>
                        <?php unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                
                <div class="recaptcha-container">
                    <p class="recaptcha-instruction">Please verify that you are not a robot</p>
                    <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($siteKey) ?>"></div>
                </div>

                <button class="login-button" type="submit" style="width: 100%;">Log In</button>

                <div class="google">
                    <div class="divider">
                        <span>or</span>
                    </div>
                    <a href="googleAuth/google-login.php" class="google-btn">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
                        Sign in with Google
                    </a>
                </div>

                <div class="login-options">
                    <label><input type="checkbox" name="remember"> Remember me</label>
                    <a href="forgotpassword.php">Forgot Password?</a>
                </div>
                <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
            </div>
        </form>
    </div>
</body>
</html>
