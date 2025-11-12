<?php require "helpers/header.php" ?>
<?php 
$pdo = require "db/db.php";

// Get team id
$projectId = null;
$projectInfo = [];
$userInTeam = false;
$user_id = $_SESSION["user_id"];

if (isset($_GET["project_id"])){
    $projectId = $_GET["project_id"];
}


// Get project info
if ($projectId !== null) {
    $projectId = (int) $projectId;

    if($pdo){
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
        $stmt->bindParam("id", $projectId, PDO::PARAM_INT);
        $stmt->execute();
        $projectInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // if projectInfo not found, redirect to index.php
    if (!$projectInfo) {
        header("Location: /index.php");
        exit;
    }
}

// Get if user is in team
if($pdo && isset($_SESSION["user_id"]) && $projectId !== null){
    $stmt = $pdo->prepare("SELECT * FROM user_teams JOIN projects ON user_teams.team_id = projects.team_id WHERE user_teams.user_id = :user_id AND projects.id = :project_id");
    $stmt->execute([
        ':user_id'=>$user_id,
        ':project_id'=>$projectId
    ]);
    
    // If the user is in the team, proceed to delete
    if ($stmt->rowCount() > 0){
        $userInTeam = true;
    }
}
?>

<main class="Container">
    <?php if ($userInTeam !== false) : ?>
        <h1>
            All tasks for team: <?php echo htmlspecialchars($projectInfo['name'] ?? ''); ?>
        </h1>
        <?php require "db/getTasks.php" ?>
    <?php else : ?>
        <h1>
            You do not have access to this project.
        </h1>
    <?php endif; ?>
</main>

<?php require "helpers/footer.php" ?>
