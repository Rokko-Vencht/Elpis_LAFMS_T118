
<?php
    session_start();
    require 'includes/db.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require'vendor/autoload.php';

    var_dump($_POST);

    if(isset($_POST['email'])){
        $email = $_POST['email'];

        $stmt = $pdo->prepare("SELECT * FROM user_information WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user){
            $reset_code = rand(100000, 999999);
            $update = $pdo->prepare('UPDATE user_information SET reset_code = ? WHERE email = ?');
            $update->execute([$reset_code, $email]);

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
                $mail->addAddress($email, 'THIS IS YOUR CLIENT');

                $mail->isHTML(true);
                $mail->Subject = "Password Reset Code";

                $mail->Body = "
                    <p> Hello! This is your password Reset Code: {$reset_code} </p>
                ";
                $mail->AltBody = "Hello, Use the code below to reset your Evel Account Password: \n\n {$reset_code} \n\n";
                $mail->send();

                $_SESSION['email_sent'] = true;

                $_SESSION['message'] = "A verification code has been sent to your email.";
                header('Location: send_code.php');
                exit();

            } catch(Exception $e) {

                $_SESSION['error'] = "Message couldn't be sent. Mailer Error: {$mail->ErrorInfo}";
                header('Location: forgot_passwords.php');
                exit();
            }
            
        } else{
            $_SESSION['error'] = "User not found. Please try to create a new account";
            header('Location: forgot_passwords.php');
            exit();
        }
    }

?>