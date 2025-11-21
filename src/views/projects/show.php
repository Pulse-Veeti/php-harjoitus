<?php include '/var/www/src/views/layouts/header.php'; ?>

<main class="Container">
    <h2>Project: <?php echo htmlspecialchars($project['name']); ?></h2>
    <h3>Team: <?php echo htmlspecialchars($project['team_name']); ?></h3>

    <!-- Available Tasks -->
    <h3>Available Tasks</h3>
    <?php if($availableTasks): ?>
        <ul style="display: flex;flex-wrap:wrap;gap:1rem;">
            <?php foreach($availableTasks as $task): ?>
                <li class="task-card" style="border-radius:5px;display:flex;flex-direction:column;gap:1rem;padding: 1rem;background: <?php echo htmlspecialchars($task['task_color']); ?>;">
                    <h4 style="margin-bottom: 0;"><?php echo htmlspecialchars($task['task_name']); ?></h4>
                    <p><?php echo htmlspecialchars($task['task_text']); ?></p>
                    <span>Owner: <?php echo $task['owner_name'] ? htmlspecialchars($task['owner_name']) : "Unassigned"; ?></span>
                    <span>Created by: <?php echo htmlspecialchars($task['creator_name']); ?></span>
                    <span>Due: <?php echo htmlspecialchars($task['due_date']); ?></span>

                    <!-- Task Actions -->
                    <?php if(!$task["task_owner"] && $teammates): ?>
                        <!-- Assign Task Form -->
                        <form action="/tasks/<?php echo htmlspecialchars($task['id']); ?>/assign" method="POST">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>">
                            <label for="task_owner">Assign to:</label>
                            <select name="task_owner" required>
                                <?php foreach($teammates as $mate): ?>
                                    <option value="<?php echo htmlspecialchars($mate['id']); ?>"><?php echo htmlspecialchars($mate['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Assign Task</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No available tasks in this project.</p>
    <?php endif; ?>

    <!-- My Tasks -->
    <h3>My Tasks</h3>
    <?php if($userTasks): ?>
        <ul style="display: flex;flex-wrap:wrap;gap:1rem;">
            <?php foreach($userTasks as $task): ?>
                <li class="task-card" style="border-radius:5px;display:flex;flex-direction:column;gap:1rem;padding: 1rem;background: <?php echo htmlspecialchars($task['task_color']); ?>;">
                    <h4 style="margin-bottom: 0;"><?php echo htmlspecialchars($task['task_name']); ?></h4>
                    <p><?php echo htmlspecialchars($task['task_text']); ?></p>
                    <span>Created by: <?php echo htmlspecialchars($task['creator_name']); ?></span>
                    <span>Due: <?php echo htmlspecialchars($task['due_date']); ?></span>

                    <!-- My Task Actions -->
                    <div style="display: flex; gap: 0.5rem;">
                        <form action="/tasks/<?php echo htmlspecialchars($task['id']); ?>/unassign" method="POST" style="display: inline;">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>">
                            <button type="submit">Unassign</button>
                        </form>

                        <form action="/tasks/<?php echo htmlspecialchars($task['id']); ?>/complete" method="POST" style="display: inline;">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>">
                            <button type="submit">Mark Complete</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You have no assigned tasks in this project.</p>
    <?php endif; ?>

    <!-- Completed Tasks -->
    <h3>Completed Tasks</h3>
    <?php if($completedTasks): ?>
        <ul style="display: flex;flex-wrap:wrap;gap:1rem;">
            <?php foreach($completedTasks as $task): ?>
                <li class="task-card" style="border-radius:5px;display:flex;flex-direction:column;gap:1rem;padding: 1rem;background: <?php echo htmlspecialchars($task['task_color']); ?>; opacity: 0.7;">
                    <h4 style="margin-bottom: 0;"><?php echo htmlspecialchars($task['task_name']); ?></h4>
                    <p><?php echo htmlspecialchars($task['task_text']); ?></p>
                    <span>Completed by: <?php echo $task['owner_name'] ? htmlspecialchars($task['owner_name']) : "Unassigned"; ?></span>
                    <span>Created by: <?php echo htmlspecialchars($task['creator_name']); ?></span>
                    <span>Due: <?php echo htmlspecialchars($task['due_date']); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No completed tasks found.</p>
    <?php endif; ?>

    <!-- Create New Task -->
    <h3>Create New Task</h3>
    <form action="/tasks" method="POST">
        <?php echo csrfField(); ?>
        <div>
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" required>
        </div>
        <div>
            <label for="task_text">Task Description:</label>
            <input type="text" id="task_text" name="task_text" required>
        </div>
        <div>
            <label for="task_color">Task Color:</label>
            <input type="color" id="task_color" name="task_color" value="#ffcccc" required>
        </div>
        <div>
            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date" required>
        </div>
        <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>">
        <button type="submit">Create Task</button>
    </form>

    <!-- Back to team -->
    <a href="/teams/<?php echo htmlspecialchars($project['team_id']); ?>">
        ← Back to Team
    </a>
</main>

<?php include '/var/www/src/views/layouts/footer.php'; ?>