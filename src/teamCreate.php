<?php require "helpers/header.php" ?>
<?php require "helpers/csrf.php" ?>
<main class="Container">
    <h2>
        Create team
    </h2>
    <form action="actions/createTeam.php" method="POST">
        <?php echo csrfField(); ?>
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <button type="submit">Create and join!</button>
    </form>
</main>

<?php require "helpers/footer.php" ?>
