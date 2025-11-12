<?php
$pdo = require "db.php";

if (isset($_GET["id"])){
    $contactId = $_GET["id"];

    $stmt = $pdo->prepare("SELECT image FROM contacts WHERE id = :id");
    $stmt->execute([':id'=> $contactId]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

    if($contact && $contact['image']){
        $imagePath = "uploads/" . $contact['image'];
        if(file_exists($imagePath)){
            unlink($imagePath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = :id");
    $stmt->execute([':id' => $contactId]);
    

    echo "Contact deleted.";
}