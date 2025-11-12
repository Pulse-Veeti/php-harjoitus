<?php
$pdo = require "db.php";
$teams = [];
if($pdo){
    $stmt = $pdo->query("SELECT * FROM teams");

    $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<ul>
    <?php foreach($teams as $team): ?>
        <li>
            <a href="teamtasks.php?team_id=<?php echo $team['id'] ?>" alt="<?php echo htmlspecialchars($team['name']); ?>"><?php echo htmlspecialchars($team['name']); ?></a>
        </li>
    <?php endforeach; ?>
    <li><a href="teamCreate.php">Create team</a></li>
</ul>