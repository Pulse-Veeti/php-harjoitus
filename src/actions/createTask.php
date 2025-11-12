<?php
require_once "../helpers/session_manager.php";
startSecureSession();
requireCSRFToken();
$pdo = require "../db/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $task_name = filter_input(INPUT_POST, "name",FILTER_SANITIZE_SPECIAL_CHARS);
    $taskText = filter_input(INPUT_POST,"task_text",FILTER_SANITIZE_SPECIAL_CHARS);
    $taskColor = filter_input(INPUT_POST,"task_color",FILTER_SANITIZE_SPECIAL_CHARS);
    $dueDate = filter_input(INPUT_POST,"due_date",FILTER_SANITIZE_SPECIAL_CHARS);

    $projectId = null;

    if (isset($_POST["project_id"])){
        $projectId = $_POST["project_id"];
    }
    $user_id = $_SESSION["user_id"];

    if ($task_name && $projectId && $user_id && $taskText && $taskColor && $dueDate){
       // Check if the user is in the team that the project belongs to
       $stmt = $pdo->prepare("SELECT * FROM user_teams JOIN projects ON user_teams.team_id = projects.team_id WHERE user_teams.user_id = :user_id AND projects.id = :project_id");
       $stmt->execute([
           ':user_id'=>$user_id,
           ':project_id'=>$projectId
       ]);
       
       // If the user is in the team, proceed to delete
       if ($stmt->rowCount() > 0){
            $stmt = $pdo->prepare('INSERT INTO tasks (task_name, project_id, task_text, task_color, due_date, created_by) VALUES (:task_name, :project_id, :task_text, :task_color, :due_date, :created_by)');
            $stmt->execute([
                ':task_name'=>$task_name,
                ':project_id'=>$projectId,
                ':task_text'=>$taskText,
                ':task_color'=>$taskColor,
                ':due_date'=>$dueDate,
                ':created_by'=>$user_id,
            ]);
       }

        // Redirect to dashboard or home page
        header("Location: /project.php?project_id=" . $projectId);
        exit();
    } else{
        echo "Invalid input";
    }
}
?>