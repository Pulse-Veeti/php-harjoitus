<?php include '/var/www/src/views/layouts/header.php'; ?>

<main class="Container">
    <h2>Teams Dashboard</h2>

    <!-- My Teams -->
    <h3>My Teams</h3>
    <?php if($userTeams): ?>
        <ul>
            <?php foreach($userTeams as $team): ?>
                <li>
                    <a href="/teams/<?php echo htmlspecialchars($team['id']); ?>">
                        <?php echo htmlspecialchars($team['name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You haven't joined any teams yet.</p>
    <?php endif; ?>

    <!-- All Available Teams -->
    <h3>All Teams</h3>
    <?php if($allTeams): ?>
        <ul>
            <?php foreach($allTeams as $team): ?>
                <li>
                    <a href="/teams/<?php echo htmlspecialchars($team['id']); ?>">
                        <?php echo htmlspecialchars($team['name']); ?>
                    </a>
                    - Created: <?php echo htmlspecialchars($team['created_at']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No teams found.</p>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div style="margin-top: 2rem;">
        <a href="/teams/create" class="button">Create New Team</a>
    </div>
</main>

<?php include '/var/www/src/views/layouts/footer.php'; ?>