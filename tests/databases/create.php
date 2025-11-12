<!-- ?php
echo"<pre>";
var_dump($_FILES);
? -->
<?php
$pdo = require "db.php";

$uploadsDir = 'uploads/';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = filter_input(INPUT_POST, "name",FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST,"email",FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST,"phone",FILTER_SANITIZE_NUMBER_INT);

    if ($name && $email && $phone && isset($_FILES['profile_picture'])){
        if(!is_dir($uploadsDir)){
            mkdir($uploadsDir, 0777, recursive: true);
        }
        $imageName = time() . "_" . basename($_FILES['profile_picture']['name']);
        $imagePath = $uploadsDir . $imageName;

        if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $imagePath)){
            $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone, image) VALUES (:name, :email, :phone, :image)');
            $stmt->execute([
                ':name'=>$name,
                ':email'=>$email,
                ':phone'=>$phone,
                ':image'=>$imagePath
            ]);

            echo "Contact added: {$name} ({$email}, {$phone})";
        }else{
            echo "Image upload failed";
        }
        
    } else{
        echo "Invalid input";
    }
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
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="name" required/><br>

        <label>email:</label>
        <input type="email" name="email" required/><br>

        <label>Phone:</label>
        <input type="text" name="phone" required/><br>

        <label>Profile picture</label>
        <input type="file" name="profile_picture" accept="image/*"/><br>

        <button type="submit">Submit</button><br>
    </form>
</body>
</html>