<?php
$pdo = require "db.php";
$projects = [];
$teamId = null;

if (isset($_GET["team_id"])){
    $teamId = $_GET["team_id"];
}


if($pdo){
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE team_id = :team_id");
    $stmt->bindParam("team_id", $teamId, PDO::PARAM_INT);
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<h2>
    Projects
</h2>
<ul>
    <?php if($projects) : ?>
        <?php foreach($projects as $project): ?>
            <li>
                <a href="project.php?project_id=<?php echo $project['id'] ?>" alt="<?php echo htmlspecialchars($project['name']); ?>"><?php echo htmlspecialchars($project['name']); ?></a>
            </li>
        <?php endforeach; ?>
    <?php else : ?>
        <li>No projects found for this team.</li>
    <?php endif; ?>
</ul>
<?php require_once "helpers/csrf.php"; ?>
<form action="actions/createProject.php" method="POST">
    <?php echo csrfField(); ?>
    <div>
        <label for="name">Project name:</label>
        <input type="text" id="name" name="name" required>
    </div>
    <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($teamId); ?>">
    <button type="submit">Create a new project</button>
</form>