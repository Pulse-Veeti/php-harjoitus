<?php require "helpers/header.php" ?>
<?php require "helpers/csrf.php" ?>
<?php 
$pdo = require "db/db.php";

// Get team id
$teamId = null;
$teamInfo = [];
$userInTeam = false;

if (isset($_GET["team_id"])){
    $teamId = $_GET["team_id"];
}


// Get team info
if ($teamId !== null) {
    $teamId = (int) $teamId;

    if($pdo){
        $stmt = $pdo->prepare("SELECT * FROM teams WHERE id = :id");
        $stmt->bindParam("id", $teamId, PDO::PARAM_INT);
        $stmt->execute();
        $teamInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // if teamInfo not found, redirect to index.php
    if (!$teamInfo) {
        header("Location: /index.php");
        exit;
    }
}

// Get if user is in team
if($pdo && isset($_SESSION["user_id"]) && $teamId !== null){
    $stmt = $pdo->prepare("SELECT * FROM user_teams WHERE user_id = :user_id AND team_id = :team_id");
    $stmt->execute([
        "user_id"=> $_SESSION["user_id"],
        "team_id"=> $teamId
    ]);
    $response = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($response) {
        $userInTeam = true;
    }   
}
?>

<main class="Container">
    <?php if ($userInTeam !== false) : ?>
        <h2>
            Projects for team: <?php echo htmlspecialchars($teamInfo['name'] ?? ''); ?>
        </h2>
        <form method="POST" action="actions/deleteTeam.php" onsubmit="return confirm('Are you sure?')">
            <?php echo csrfField(); ?>
            <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($teamId); ?>">
            <button type="submit">Delete Team</button>
        </form>
        <?php require "db/getprojects.php" ?>
    <?php else : ?>
        <h2>
            You are not a member of the team: <?php echo htmlspecialchars($teamInfo['name'] ?? ''); ?>
        </h2>
        <!-- Join team form -->
        <form action="actions/jointeam.php" method="post">
            <?php echo csrfField(); ?>
            <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($teamInfo['id'] ?? ''); ?>">
            <button type="submit">Join Team</button>
        </form>
    <?php endif; ?>
</main>

<?php require "helpers/footer.php" ?>
