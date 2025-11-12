<?php
require_once "../helpers/session_manager.php";
startSecureSession();
$pdo = require "../db/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    requireCSRFToken();

    $name = filter_input(INPUT_POST, "name",FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST,"email",FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST,"password",FILTER_SANITIZE_SPECIAL_CHARS);

    if ($name && $email && $password){
        // hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
        $stmt->execute([
            ':name'=>$name,
            ':email'=>$email,
            ':password'=>$hashedPassword
        ]);

        // Store the user ID in session
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['logged_in'] = true;
        session_regenerate_id(true);

        setFlashMessage('success', 'Registration successful!');

        // Redirect to dashboard or home page
        header("Location: /index.php");
        exit();
    } else{
        setFlashMessage("error","Invalid input");
        header("Location: /account.php");
        exit();
    }
}
?>