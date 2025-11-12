<?php
    require_once "session_manager.php";
    startSecureSession();
    // If not on index.php and session id is not set, redirect to index.php
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && basename($_SERVER['PHP_SELF']) === 'account.php') {
      header("Location: /index.php");
      exit();
    } else if(!isset($_SESSION['logged_in']) && basename($_SERVER['PHP_SELF']) !== 'account.php'){
        header('Location: /account.php');
        exit();
    }
?>

<?php 
$pdo = require "db/db.php";
// Test the database connection
// if($pdo){
//     echo "Database connected successfully.";
// } else {
//     echo "Database connection failed.";
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>