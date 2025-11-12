<?php require "helpers/header.php" ?>
<?php require "helpers/csrf.php" ?>
<main class="Container">
    <h2>
        Login
    </h2>
    <form action="actions/login.php" method="POST">
        <?php echo csrfField(); ?>
        <div>
            <label for="login_email">Email:</label>
            <input type="email" id="login_email" name="email" required>
        </div>
        <div>
            <label for="login_password">Password:</label>
            <input type="password" id="login_password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>

    <h2>
        Register
    </h2>
    <form action="actions/register.php" method="POST">
        <?php echo csrfField(); ?>
        <div>
            <label for="register_email">Email:</label>
            <input type="email" id="register_email" name="email" required>
        </div>
        <div>
            <label for="register_name">Name:</label>
            <input type="text" id="register_name" name="name" required>
        </div>
        <div>
            <label for="register_password">Password:</label>
            <input type="password" id="register_password" name="password" required>
        </div>
        <button type="submit">Register</button>
    </form>
</main>

<?php require "helpers/footer.php" ?>
