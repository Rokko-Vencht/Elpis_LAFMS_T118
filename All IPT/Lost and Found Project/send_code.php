<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_code = $_POST['code'];

    $stmt = $conn->prepare("SELECT email FROM user_info WHERE reset_code = ?");
    $stmt->bind_param("i", $entered_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['email_sent'] = true;
        $_SESSION['message'] = "Code verified. Please enter your new password.";
        header('Location: reset_password.php');
        exit();
    } else {
        $_SESSION['message'] = "Invalid code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Confirmation</title>
    <link rel="stylesheet" href="login_fp.css">
</head>
<body>

    <style>
        input[type="number"] {
            padding: 8px;
            width: 150px;
            border: none;
            border-bottom: 2px solid #ccc;
            border-radius: 0;
            outline: none;
            transition: border-color 0.3s;
            margin-bottom: 15px;
            background-color: transparent;
            font-size: 16px;
            font-family: sans-serif;
            box-sizing: border-box;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        input {
            margin: 0;
            background-color: transparent;
            border: none;
            border-bottom: 2px solid #ccc;
            font-family: inherit;
        }

        input:focus {
            border-bottom: 2px solid #123458;
            outline: none;
        }

    </style>
    <div class="login-container">
        <div class="logo-section">
            <img src="images/lostlink_logo.png" alt="Lost and Found">
            <p style="font-weight: bolder;">Lostlink</p>
        </div>

        <div class="form-section">
            <h2 style="color: #123458;">Submit One-Time Code</h2>
            
            <div class="h2">
                <?php if(isset($_SESSION['message'])): ?>
                    <div class="alert alert-info text-center">
                        <?= $_SESSION['message']; ?>
                        <?php unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <form method="POST" action="">    
                <input type="number" name="code" placeholder="Code" required><br>
                <div class="login-options" style="justify-content: center;">
                    <button type="submit" class="continue-button">Submit</button>
                </div>
            </form>
        </div>

    </div>
</body>
</html>
