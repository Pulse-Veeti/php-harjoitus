<?php

require_once '/var/www/src/controllers/BaseController.php';
require_once '/var/www/src/models/TaskModel.php';
require_once '/var/www/src/models/ProjectModel.php';

class TaskController extends BaseController {
    private $taskModel;
    private $projectModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->projectModel = new ProjectModel($this->pdo);
        $this->taskModel = new TaskModel($this->pdo);
    }

    /**
     * Create new task
     */
    public function create() {
        $this->validateCSRF();

        $taskName = filter_input(INPUT_POST, "task_name", FILTER_SANITIZE_SPECIAL_CHARS);
        $taskText = filter_input(INPUT_POST, "task_text", FILTER_SANITIZE_SPECIAL_CHARS);
        $taskColor = filter_input(INPUT_POST, "task_color", FILTER_SANITIZE_SPECIAL_CHARS);
        $dueDate = filter_input(INPUT_POST, "due_date", FILTER_SANITIZE_SPECIAL_CHARS);
        $projectId = filter_input(INPUT_POST, "project_id", FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$taskName || !$taskText || !$taskColor || !$dueDate || !$projectId) {
            $this->redirect("/projects/{$projectId}", 'All task fields are required', 'error');
        }

        $userId = $this->getCurrentUserId();

        // Check if user can access this project
        if (!$this->projectModel->canUserAccess($userId, $projectId)) {
            $this->redirect('/teams', 'You do not have access to this project', 'error');
        }

        // Convert date to datetime format for TIMESTAMP field
        $dueDatetime = $dueDate . ' 23:59:59';

        $this->taskModel->create($taskName, $projectId, $taskText, $taskColor, $dueDatetime, $userId);
        $this->redirect("/projects/{$projectId}", 'Task created successfully!', 'success');
    }

    /**
     * Assign task to user
     * $taskId comes from the URL route parameter {id}
     */
    public function assign($taskId) {
        $this->validateCSRF();

        $taskOwner = filter_input(INPUT_POST, "task_owner", FILTER_SANITIZE_SPECIAL_CHARS);
        $projectId = filter_input(INPUT_POST, "project_id", FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$taskId || !$taskOwner || !$projectId) {
            $this->redirect("/projects/{$projectId}", 'Invalid assignment data', 'error');
        }

        $userId = $this->getCurrentUserId();

        // Check if user can access this project
        if (!$this->projectModel->canUserAccess($userId, $projectId)) {
            $this->redirect('/teams', 'You do not have access to this project', 'error');
        }

        $this->taskModel->assignTask($taskOwner, $taskId);
        $this->redirect("/projects/{$projectId}", 'Task assigned successfully!', 'success');
    }

    /**
     * Unassign task from user
     * $taskId comes from the URL route parameter {id}
     */
    public function unassign($taskId) {
        $this->validateCSRF();

        $projectId = filter_input(INPUT_POST, "project_id", FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$taskId || !$projectId) {
            $this->redirect("/projects/{$projectId}", 'Invalid task data', 'error');
        }

        $userId = $this->getCurrentUserId();

        // Check if user can access this project
        if (!$this->projectModel->canUserAccess($userId, $projectId)) {
            $this->redirect('/teams', 'You do not have access to this project', 'error');
        }

        $this->taskModel->unassignTask($taskId);
        $this->redirect("/projects/{$projectId}", 'Task unassigned successfully!', 'success');
    }

    /**
     * Mark task as complete
     * $taskId comes from the URL route parameter {id}
     */
    public function complete($taskId) {
        $this->validateCSRF();

        $projectId = filter_input(INPUT_POST, "project_id", FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$taskId || !$projectId) {
            $this->redirect("/projects/{$projectId}", 'Invalid task data', 'error');
        }

        $userId = $this->getCurrentUserId();

        // Check if user can access this project
        if (!$this->projectModel->canUserAccess($userId, $projectId)) {
            $this->redirect('/teams', 'You do not have access to this project', 'error');
        }

        $this->taskModel->completeTask($userId, $taskId);
        $this->redirect("/projects/{$projectId}", 'Task completed successfully!', 'success');
    }
}