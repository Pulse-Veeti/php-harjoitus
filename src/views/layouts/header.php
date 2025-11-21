<?php require_once '/var/www/src/helpers/session_manager.php'; ?>
<?php require_once '/var/www/src/helpers/csrf.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management System</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <header>
        <a href="/" alt="home"><h1>Project Management System</h1></a>
        <?php if (!isset($_SESSION['logged_in'])) : ?>
        <ul>
            <li><a href="/">Login | Register</a></li>
        </ul>
        <?php else : ?>
        <ul>
            <li><a href="/teams">Dashboard</a></li>
            <li><a href="/teams/create">Create Team</a></li>
            <li>
                <form action="/logout" method="POST" style="display: inline;">
                    <?php echo csrfField(); ?>
                    <button type="submit">Logout</button>
                </form>
            </li>
        </ul>
        <?php endif; ?>
    </header>

    <?php
    // Display flash messages
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>