<?php

try{
    $pdo = new PDO('sqlite:contacts.db'); // Create (connect to) SQLite database in file
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Add error handling
    $pdo->exec('CREATE TABLE IF NOT EXISTS contacts(
        id INTEGER PRIMARY KEY,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT,
        image TEXT
    );');
    return $pdo;
} catch(PDOException $e){
    return null;
}