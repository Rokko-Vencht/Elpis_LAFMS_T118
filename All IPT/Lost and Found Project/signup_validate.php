<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];
    
    if($password !== $confirm){
        $_SESSION["error"] = "Passwords do not match";
        header("Location: signup.html");
        exit();
    }
    
    $stmt = $pdo->prepare("SELECT * FROM user_info WHERE username = ?");
    $stmt->execute([$username]);

    if($stmt->rowCount() > 0){
        $_SESSION["error"] = "Username already exists";
        header("Location: signup.html");
        exit();
    }


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo -> prepare("INSERT INTO user_info (firstname, lastname, username, email, contact, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$firstname, $lastname, $username, $email, $contact, $hashed_password]);

    if($stmt->execute($firstname, $lastname, $username, $email, $contact, $hashed_password)){
        $_SESSION["success"] = "Signup successful";
        header("Location: login.php");
        exit();
    } else {
        echo("There is an error!");
        exit();
    }
}
