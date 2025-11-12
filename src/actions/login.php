<?php
require_once "../helpers/session_manager.php";
startSecureSession();
$pdo = require "../db/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    requireCSRFToken();

    $email = filter_input(INPUT_POST,"email",FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST,"password",FILTER_SANITIZE_SPECIAL_CHARS);

    if ($email && $password){
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = :email");
        $stmt->execute([
            ":email"=>$email
        ]);

        $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($userData){
            $user = $userData[0];
            if (password_verify($password, $user['password'])){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['logged_in'] = true;
                session_regenerate_id(true);

                setFlashMessage('success', 'Login successful!');

                // Redirect to dashboard or home page
                header("Location: /index.php");
                exit();
            } else{
                setFlashMessage("error","Invalid email or password");
                header("Location: /account.php");
                exit();
            }
        }else{
            setFlashMessage("error","Invalid email or password");
            header("Location: /account.php");
            exit();
        }
    } else{
        setFlashMessage("error","Invalid input");
        header("Location: /account.php");
        exit();
    }
}
?>