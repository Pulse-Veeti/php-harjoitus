<?php
require_once "../helpers/session_manager.php";
startSecureSession();
requireCSRFToken();
$pdo = require "../db/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = filter_input(INPUT_POST, "name",FILTER_SANITIZE_SPECIAL_CHARS);
    $user_id = $_SESSION["user_id"];

    if ($name && $user_id){
        $stmt = $pdo->prepare('INSERT INTO teams (name) VALUES (:name)');
        $stmt->execute([
            ':name'=>$name
        ]);

        // Get the newly created team ID
        $team_id = $pdo->lastInsertId();

        // Add the creator to the team
        if ($team_id){
            $stmt = $pdo->prepare('INSERT INTO user_teams (user_id, team_id) VALUES (:user_id, :team_id)');
            $stmt->execute([
                ':user_id'=>$user_id,
                ':team_id'=>$team_id
            ]);
        }

        // flash message
        setFlashMessage('success','Team created successfully!');

        // Redirect to dashboard or home page
        header("Location: /teamtasks.php?team_id=" . $team_id);
        exit();
    } else{
        setFlashMessage("error","Invalid input");
        header("Location: /teamtasks.php?team_id=" . $team_id);
        exit();
    }
}
?>