<?php
    session_start();
    require 'includes/db.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';

    if(isset($_POST['email'])){
        $email = $_POST['email'];

        $stmt = $conn->prepare("SELECT * FROM user_info WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if($user){
            $reset_code = rand(100000, 999999);
            
            $update = $conn->prepare('UPDATE user_info SET reset_code = ? WHERE email = ?');
            $update->bind_param("is", $reset_code, $email);
            $update->execute();

            $_SESSION['email'] = $email;

            $mail = new PHPMailer(true);
            try{
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'robertjamesnahial@gmail.com';
                $mail->Password = 'fkqt kyyd rhxy qfdc';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('robertjamesnahial@gmail.com','Robert James Nahial');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Password Reset Code";

                $mail->Body = "
                    <p>Hello! This is your password Reset Code: {$reset_code}</p>
                ";
                $mail->AltBody = "Hello, Use the code below to reset your password: \n\n {$reset_code} \n\n";
                
                if($mail->send()){
                    $_SESSION['email_sent'] = true;
                    $_SESSION['message'] = "A verification code has been sent to your email.";
                    header("Location: send_code.php");
                    exit();
                }

            } catch(Exception $e) {
                $_SESSION['error'] = "Message couldn't be sent. Mailer Error: {$mail->ErrorInfo}";
                header("Location: forgotpassword.php");
                exit();
            }
            
        } else {
            $_SESSION['error'] = "Email not found. Please try again or create a new account.";
            header("Location: forgotpassword.php");
            exit();
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <link rel="stylesheet" href="login_fp.css">
    <style>
        .form-section {
            max-width: 350px;
            margin: 0 auto;
            text-align: center;
        }
        .alerts-section {
            margin-bottom: 20px;
        }
        .alert {
            padding: 16px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }
        .alert-danger {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: none;
            border-bottom: 2px solid #ccc;
            background: #f9f9f9;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="text"]:focus {
            border-bottom: 2px solid #123458;
            outline: none;
        }
        .continue-button {
            background-color: #123458;
            color: #f1efec;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .continue-button:hover {
            background-color: #224264;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <img src="images/lostlink_logo.png" alt="Lost and Found">
            <p style="font-weight: bolder; ">LostLink</p>
        </div>

        <div class="form-section">
            <form action="" method="POST">
                <h2 style="color: #123458";>Forgot Password</h2>

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
                <input type="text" name="email" placeholder="Enter email address" required><br>
                <div class="login-options" style="justify-content: center;">
                    <button type="submit" class="continue-button">Send Code</button>
                </div>
            </form>
        </div>

    </div>
</body>
</html>
