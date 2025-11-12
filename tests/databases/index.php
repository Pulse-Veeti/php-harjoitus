<?php
$pdo = require "db.php";
$contacts = [];
if($pdo){
    $stmt = $pdo->query("SELECT * FROM contacts");

    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!--<?php require "header.php"?>--><!-- If not exists, then code returns and nothing after is printed -->
    <?php include "header.php"?> <!-- If not exists, then this is no printed but everything after is printed -->
    <a href="create.php">Create new contact</a>

    <ul>
        <?php foreach($contacts as $contact): ?>
            <li>
                <img src="<?php echo htmlspecialchars($contact['image']); ?>" alt="Profile Picture" width="50" height="50"/>
                <?php echo htmlspecialchars($contact['name']); ?> - 
                <?php echo htmlspecialchars($contact['email']); ?> - 
                <?php echo htmlspecialchars($contact['phone']); ?>
                <a href="delete.php?id=<?php echo $contact['id'] ?>">
                    Delete
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>