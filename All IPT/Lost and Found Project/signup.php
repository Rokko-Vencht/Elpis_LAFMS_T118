<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
    } else {
        $check_username = "SELECT * FROM user_info WHERE user_name = ?";
        $stmt_check = $conn->prepare($check_username);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['error'] = "Username already exists. Please choose a different username.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO user_info (user_name ,first_name, last_name, password,  contact_num, email) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $username, $firstname, $lastname, $hashed_password, $contact, $email );

            if ($stmt->execute()) {
                $_SESSION['success'] = "Account created successfully! Please log in.";
                
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['error'] = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found Sign-up</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <div class="signup-container">
        <div class="logo-section">
            <img src="images/lostlink_logo.png" alt="Lost and Found">
        </div>

        <div class="form-section">
            <h2>Sign Up to LostLink</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-container">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <input type="text" name="firstname" placeholder="First Name" id="firstname" required>
                    <input type="text" name="lastname" placeholder="Last Name" id="lastname" required>
                </div>

                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" class="username" id="username" required>
                </div>

                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" id="email" required>
                    <input type="number" name="contact" placeholder="Contact Number" id="contact">
                </div>

                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" id="password" required>
                    <input type="password" name="confirm_password" placeholder="Re-enter Password" id="confirm_password" required>
                </div>
                
                <button type="submit" class="signup-button">Sign Up</button>
            </form>
            
            <p class="login-link">Already have an account? <a href="login.php">Log in here</a></p>
        </div>
    </div>
</body>
</html>