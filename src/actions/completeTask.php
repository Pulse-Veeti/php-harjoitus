<?php
require_once "../helpers/session_manager.php";
startSecureSession();
requireCSRFToken();
$pdo = require "../db/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $task_id = null;
    $project_id = null;
    if (isset($_POST["project_id"])){
        $project_id = $_POST["project_id"];
    }
    if (isset($_POST["task_id"])){
        $task_id = $_POST["task_id"];
    }
    $user_id = $_SESSION["user_id"];

    if ($task_id && $project_id && $user_id){
       // Check if the user is in the team that the project belongs to
       $stmt = $pdo->prepare("SELECT * FROM user_teams JOIN projects ON user_teams.team_id = projects.team_id WHERE user_teams.user_id = :user_id AND projects.id = :project_id");
       $stmt->execute([
           ':user_id'=>$user_id,
           ':project_id'=>$project_id
       ]);
       
       // If the user is in the team, proceed to complete
       if ($stmt->rowCount() > 0){
            $stmt = $pdo->prepare("UPDATE tasks SET status = 'Completed' WHERE id = :task_id AND task_owner = :task_owner");
            $stmt->execute([
                ':task_id'=>$task_id,
                ':task_owner'=>$user_id,
            ]);
       }

        // Redirect to dashboard or home page
        header("Location: /project.php?project_id=" . $project_id);
        exit();
    } else{
        echo "Invalid input";
    }
}
?>