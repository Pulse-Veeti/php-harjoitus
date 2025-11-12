<?php
echo"<pre>";
var_dump($_FILES);
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = filter_input(INPUT_POST, "name",FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST,"email",FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST,"phone",FILTER_SANITIZE_NUMBER_INT);

    if ($name && $email && $phone){
        echo "Contact added: {$name} ({$email}, {$phone})";
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