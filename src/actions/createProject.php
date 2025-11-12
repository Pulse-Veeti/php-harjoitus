<?php
require_once "../helpers/session_manager.php";
startSecureSession();
requireCSRFToken();
$pdo = require "../db/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = filter_input(INPUT_POST, "name",FILTER_SANITIZE_SPECIAL_CHARS);
    $teamId = null;
    if (isset($_POST["team_id"])){
        $teamId = $_POST["team_id"];
    }
    $user_id = $_SESSION["user_id"];

    if ($name && $teamId && $user_id){
       // Check if the user is in the team
       $stmt = $pdo->prepare("SELECT * FROM user_teams WHERE user_id = :user_id AND team_id = :team_id");
       $stmt->execute([
           ':user_id'=>$user_id,
           ':team_id'=>$teamId
       ]);
       
       // If the user is in the team, proceed to delete
       if ($stmt->rowCount() > 0){
            $stmt = $pdo->prepare('INSERT INTO projects (name, team_id) VALUES (:name, :team_id)');
            $stmt->execute([
                ':name'=>$name,
                ':team_id'=>$teamId
            ]);
       }

        // Redirect to dashboard or home page
        header("Location: /teamtasks.php?team_id=" . $teamId);
        exit();
    } else{
        echo "Invalid input";
    }
}
?>