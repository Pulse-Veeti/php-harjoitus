<?php
$pdo = require "db.php";
$users = [];
if($pdo){
    $stmt = $pdo->query("SELECT * FROM users");

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<ul>
        <?php foreach($users as $user): ?>
            <li>
                <?php echo htmlspecialchars($user['name']); ?> - 
                <?php echo htmlspecialchars($user['email']); ?> - 
                <?php echo htmlspecialchars($user['created_at']); ?>
                <!-- <a href="delete.php?id=<?php echo $user['id'] ?>">
                    Delete
                </a> -->
            </li>
        <?php endforeach; ?>
    </ul>