<?php
function checkSessionTimeout(){
    $timeout = 1440; // 24 minutes in seconds

    if(isset($_SESSION['last_activity'])){
        if(time() - $_SESSION['last_activity'] > $timeout){
            // Session expired
            session_unset();
            session_destroy();
            header('Location: /account.php?timeout=1');
            exit();
        }
    }

    // Update last activity timestamp
    $_SESSION['last_activity'] = time();
}
function startSecureSession(){
    // Start session with secure checks
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true){
        checkSessionTimeout();
    }
}
function setFlashMessage($type, $message){
    $_SESSION['flash_message'] = [
        'type'=> $type, // 'success', 'error', 'warning', 
        'message'=> $message
    ];
}
function getFlashMessage(){
    if(isset($_SESSION['flash_message'])){
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
}

function generateCSRFToken(){
    if(!isset($_SESSION['csrf_token'])){
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function validateCSRFToken($token){
    if(!isset($_SESSION['csrf_token'])){
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function requireCSRFToken(){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $token = $_POST['csrf_token'] ?? '';
        if(!validateCSRFToken($token)){
            error_log("CSRF token validation failed.");

            setFlashMessage("error","Security token validation failed. Please try again.");
            
            $redirect = $_SERVER["HTTP_REFERER"] ?? '/account.php';
            header("Location: " . $redirect);
            exit();
        }
    }
}
?>