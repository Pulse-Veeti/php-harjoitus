<?php require "helpers/header.php" ?>
<?php
$pdo = require "db/db.php";
function getCurrentUserName($pdo, $user_id){
    $stmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
    $stmt->execute(["id"=> $user_id]);

    return $stmt->fetchColumn();
}
$username = getCurrentUserName($pdo, $_SESSION['user_id']);
?>
<main class="Container">
    <a href="actions/logout.php">Logout</a>

    <h2>Welcome <?php echo $username ?></h2>
    <h2>All users</h2>
    <?php require "db/getusers.php" ?>
</main>

<?php require "helpers/footer.php" ?>
