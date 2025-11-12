<?php
require_once "helpers/csrf.php";
$pdo = require "db.php";
$tasks = [];
$myTasks = [];
$completedTasks = [];
$teammates = [];
$projectId = null;

if (isset($_GET["project_id"])){
    $projectId = $_GET["project_id"];
}
$user_id = $_SESSION["user_id"];


if($pdo){
    // Get available tasks
    $stmt = $pdo->prepare("SELECT tasks.*, users.name as owner_name, creator.name as creator_name FROM tasks LEFT JOIN users ON tasks.task_owner = users.id LEFT JOIN users as creator ON tasks.created_by = creator.id WHERE project_id = :project_id AND status != 'Completed' AND (task_owner != :task_owner OR task_owner IS NULL)");
    $stmt->bindParam("project_id", $projectId, PDO::PARAM_INT);
    $stmt->bindParam("task_owner", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get users tasks
    $stmt = $pdo->prepare("SELECT tasks.*, users.name as owner_name, creator.name as creator_name FROM tasks LEFT JOIN users ON tasks.task_owner = users.id LEFT JOIN users as creator ON tasks.created_by = creator.id WHERE project_id = :project_id AND task_owner = :task_owner AND status != 'Completed'");
    $stmt->bindParam("project_id", $projectId, PDO::PARAM_INT);
    $stmt->bindParam("task_owner", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $myTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get users in this team
    $stmt = $pdo->prepare("SELECT DISTINCT users.id, users.name FROM users JOIN user_teams ON users.id = user_teams.user_id JOIN projects ON user_teams.team_id = projects.team_id WHERE projects.id = :project_id");
    $stmt->bindParam("project_id", $projectId, PDO::PARAM_INT);
    // $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $teammates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get completed tasks
    $stmt = $pdo->prepare("SELECT tasks.*, users.name as owner_name, creator.name as creator_name FROM tasks LEFT JOIN users ON tasks.task_owner = users.id LEFT JOIN users as creator ON tasks.created_by = creator.id WHERE project_id = :project_id AND status = 'Completed'");
    $stmt->bindParam("project_id", $projectId, PDO::PARAM_INT);
    $stmt->execute();
    $completedTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<h2>
    My tasks
</h2>
<ul style="display: flex;flex-wrap:wrap;gap:1rem;">
    <?php if($myTasks) : ?>
        <?php foreach($myTasks as $task): ?>
            <li style="border-radius:5px;display:flex;flex-direction:column;gap:1rem;padding: 1rem;background: <?php echo htmlspecialchars($task['task_color']); ?>;">
                <h3 style="margin-bottom: 0;"><?php echo htmlspecialchars($task['task_name']); ?></h3>
                <p><?php echo htmlspecialchars($task['task_text']); ?></p>
                <span>Owner: <?php echo $task['owner_name'] ? htmlspecialchars($task['owner_name']) : "Unassigned"; ?></span>
                <?php 
                    if($task["task_owner"] !== null){
                        ?>
                        <form action="actions/unassignTask.php" method="POST">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>">
                            <button type="submit">Unassign Task</button>
                        </form>
                        <?php
                    }
                ?>
                <span>Created by: <?php echo htmlspecialchars($task['creator_name']); ?></span>
                <span>Status: <?php echo htmlspecialchars($task['status']); ?></span>
                <?php 
                    if($task["task_owner"] !== null){
                        ?>
                        <form action="actions/completeTask.php" method="POST">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>">
                            <button type="submit">Mark Complete</button>
                        </form>
                        <?php
                    }
                ?>
                <span>Due: <?php echo htmlspecialchars($task['due_date']); ?></span>
            </li>
        <?php endforeach; ?>
    <?php else : ?>
        <li>No tasks assigned to you.</li>
    <?php endif; ?>
</ul>
<h2>
    AllTasks
</h2>
<ul style="display: flex;flex-wrap:wrap;gap:1rem;">
    <?php if($tasks) : ?>
        <?php foreach($tasks as $task): ?>
            <li style="border-radius:5px;display:flex;flex-direction:column;gap:1rem;padding: 1rem;background: <?php echo htmlspecialchars($task['task_color']); ?>;">
                <h3 style="margin-bottom: 0;"><?php echo htmlspecialchars($task['task_name']); ?></h3>
                <p><?php echo htmlspecialchars($task['task_text']); ?></p>
                <span>Owner: <?php echo $task['owner_name'] ? htmlspecialchars($task['owner_name']) : "Unassigned"; ?></span>
                <?php 
                    if($task["task_owner"] === null && count($teammates) > 0){
                        ?>
                        <form action="actions/assignTask.php" method="POST">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>">
                            <label for="task_owner">Assign to:</label>
                            <select name="task_owner" id="task_owner" required>
                                <?php 
                                    foreach($teammates as $mate){
                                        ?>
                                        <option value="<?php echo htmlspecialchars($mate['id']); ?>"><?php echo htmlspecialchars($mate['name']); ?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                            <button type="submit">Assign Task</button>
                        </form>
                        <?php
                    }
                ?>
                <span>Created by: <?php echo htmlspecialchars($task['creator_name']); ?></span>
                <span>Status: <?php echo htmlspecialchars($task['status']); ?></span>
                <span>Due: <?php echo htmlspecialchars($task['due_date']); ?></span>
            </li>
        <?php endforeach; ?>
    <?php else : ?>
        <li>No available tasks in this project.</li>
    <?php endif; ?>
</ul>
<h2>
    Completed tasks
</h2>
<ul style="display: flex;flex-wrap:wrap;gap:1rem;">
    <?php if($completedTasks) : ?>
        <?php foreach($completedTasks as $task): ?>
            <li style="border-radius:5px;display:flex;flex-direction:column;gap:1rem;padding: 1rem;background: <?php echo htmlspecialchars($task['task_color']); ?>;">
                <h3 style="margin-bottom: 0;"><?php echo htmlspecialchars($task['task_name']); ?></h3>
                <p><?php echo htmlspecialchars($task['task_text']); ?></p>
                <span>Completed by: <?php echo $task['owner_name'] ? htmlspecialchars($task['owner_name']) : "Unassigned"; ?></span>
                <span>Created by: <?php echo htmlspecialchars($task['creator_name']); ?></span>
                <span>Status: <?php echo htmlspecialchars($task['status']); ?></span>
                <span>Due: <?php echo htmlspecialchars($task['due_date']); ?></span>
            </li>
        <?php endforeach; ?>
    <?php else : ?>
        <li>No completed tasks found</li>
    <?php endif; ?>
</ul>
<h2>
    Create a new task
</h2>
<form action="actions/createTask.php" method="POST">
    <?php echo csrfField(); ?>
    <div>
        <label for="name">Task name:</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div>
        <label for="task_text">Task text:</label>
        <input type="text" id="task_text" name="task_text" required>
    </div>
    <div>
        <label for="task_color">Task color:</label>
        <input type="text" id="task_color" name="task_color" required>
    </div>
    <div>
        <label for="due_date">Due date:</label>
        <input type="date" id="due_date" name="due_date" required>
    </div>
    <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>">
    <button type="submit">Create a new task</button>
</form>