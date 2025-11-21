<?php include '/var/www/src/views/layouts/header.php'; ?>

<main class="Container">
    <h2>Create Team</h2>
    <form action="/teams/create" method="POST">
        <?php echo csrfField(); ?>
        <div>
            <label for="name">Team Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <button type="submit">Create and Join Team!</button>
    </form>
</main>

<?php include '/var/www/src/views/layouts/footer.php'; ?>