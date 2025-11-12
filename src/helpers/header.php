<?php require "head.php" ?>
<header>
    <a href="/" alt="home"><h1>Project Management System</h1></a>
    <?php if (!isset($_SESSION['logged_in'])) : ?>
    <ul>
        <li><a href="account.php">Login | Register</a></li>
    </ul>
    <?php else : ?>
    <?php require "db/getteams.php" ?>
    <?php endif; ?>
</header>
<?php require "flash.php" ?>