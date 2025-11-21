<?php include '/var/www/src/views/layouts/header.php'; ?>

<main class="Container">
    <?php if ($isUserMember): ?>
        <h2>Projects for team: <?php echo htmlspecialchars($team['name']); ?></h2>

        <!-- Team Management -->
        <form method="POST" action="/teams/<?php echo htmlspecialchars($team['id']); ?>/delete" onsubmit="return confirm('Are you sure you want to delete this team?')">
            <?php echo csrfField(); ?>
            <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team['id']); ?>">
            <button type="submit">Delete Team</button>
        </form>

        <!-- Projects List -->
        <h3>Projects</h3>
        <ul>
            <?php if($projects): ?>
                <?php foreach($projects as $project): ?>
                    <li>
                        <a href="/projects/<?php echo $project['id']; ?>">
                            <?php echo htmlspecialchars($project['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No projects found for this team.</li>
            <?php endif; ?>
        </ul>

        <!-- Create Project Form -->
        <h3>Create New Project</h3>
        <form action="/projects" method="POST">
            <?php echo csrfField(); ?>
            <div>
                <label for="project_name">Project Name:</label>
                <input type="text" id="project_name" name="name" required>
            </div>
            <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team['id']); ?>">
            <button type="submit">Create Project</button>
        </form>

        <!-- Team Members -->
        <h3>Team Members</h3>
        <ul>
            <?php foreach($members as $member): ?>
                <li><?php echo htmlspecialchars($member['name']); ?> - <?php echo htmlspecialchars($member['email']); ?></li>
            <?php endforeach; ?>
        </ul>

    <?php else: ?>
        <h2>You are not a member of the team: <?php echo htmlspecialchars($team['name']); ?></h2>

        <!-- Join Team Form -->
        <form action="/teams/<?php echo htmlspecialchars($team['id']); ?>/join" method="post">
            <?php echo csrfField(); ?>
            <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team['id']); ?>">
            <button type="submit">Join Team</button>
        </form>
    <?php endif; ?>
</main>

<?php include '/var/www/src/views/layouts/footer.php'; ?>