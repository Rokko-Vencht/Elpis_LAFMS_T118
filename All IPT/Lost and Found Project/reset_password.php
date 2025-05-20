<?php
session_start();
require 'includes/db.php';

if(!isset($_SESSION['email']) || !isset($_SESSION['email_sent'])){
    header('location: send_code.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] ==='POST'){
    $newpassword = $_POST['password'];
    $confirmpassword = $_POST['confirm'];

    if($newpassword === $confirmpassword){
        $hashed_password = password_hash($newpassword, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("UPDATE user_info SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $_SESSION['email']);
        if($stmt->execute()){
            unset($_SESSION['email']);
            unset($_SESSION['email_sent']);
            $_SESSION['success'] = "Password reset successfully! You can now login.";
            header('Location: login.php');
            exit();
        } else {
            $_SESSION['error'] = "Database error: Please try again.";
        }
    } else {
        $_SESSION['error'] = "Passwords don't match. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="login_fp.css">
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <img src="images/lostlink_logo.png" alt="Lost and Found">
            <p style="font-weight: bolder; ">LostLink</p>
        </div>


        <div class="alerts-section">
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
        </div>
        
        <div class="form-section">
            <h2 style="color:  #123458;">New Password</h2>
            <form method="POST" action="">
                <input type="password" name="password" placeholder="Create new password" required><br>
                <input type="password" name="confirm" placeholder="Confirm your password" required>
                <div class="login-options" style="justify-content: center;">
                    <button type="submit" class="change-button">Change</button>
                </div>
            </form>
        </div>

    </div>
</body>
</html>