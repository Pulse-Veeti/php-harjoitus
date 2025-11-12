<?php
require_once "../helpers/session_manager.php";
startSecureSession();
requireCSRFToken();
$pdo = require "../db/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $team_id = filter_input(INPUT_POST, "team_id",FILTER_SANITIZE_SPECIAL_CHARS);
    $user_id = $_SESSION["user_id"];

    if ($team_id && $user_id){
        $stmt = $pdo->prepare('INSERT INTO user_teams (user_id, team_id) VALUES (:user_id, :team_id)');
        $stmt->execute([
            ':user_id'=>$user_id,
            ':team_id'=>$team_id
        ]);

        // Redirect to dashboard or home page
        header("Location: /teamtasks.php?team_id=" . $team_id);
        exit();
    } else{
        echo "Invalid input";
    }
}
?>