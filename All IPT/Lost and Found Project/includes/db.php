<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "lafcms_sampledb";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("MySQLi Connection failed: " . $conn->connect_error);
    }

    try{
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname",  $username, $password);
    } 
    catch (PDOException $e){
        die("PDO Connection failed: " . $e -> getMessage());
    }

?>