<?php

require_once '/var/www/src/controllers/BaseController.php';
require_once '/var/www/src/models/ProjectModel.php';
require_once '/var/www/src/models/TeamModel.php';
require_once '/var/www/src/models/TaskModel.php';

class ProjectController extends BaseController {
    private $projectModel;
    private $teamModel;
    private $taskModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->projectModel = new ProjectModel($this->pdo);
        $this->teamModel = new TeamModel($this->pdo);
        $this->taskModel = new TaskModel($this->pdo);
    }

    /**
     * Show project details with tasks
     */
    public function show($projectId) {
        $project = $this->projectModel->getWithTeam($projectId);
        if (!$project) {
            $this->redirect('/index.php', 'Project not found', 'error');
        }

        $userId = $this->getCurrentUserId();

        // Check if user can access this project
        if (!$this->projectModel->canUserAccess($userId, $projectId)) {
            $this->redirect('/index.php', 'You do not have access to this project', 'error');
        }

        // Load task data
        $availableTasks = $this->taskModel->getAvailableTasks($userId, $projectId);
        $userTasks = $this->taskModel->getUserTasks($userId, $projectId);
        $completedTasks = $this->taskModel->getCompletedTasks($projectId);
        $teammates = $this->teamModel->getUsersInTeam($projectId);

        $this->view('projects/show', [
            'project' => $project,
            'projectId' => $projectId,
            'availableTasks' => $availableTasks,
            'userTasks' => $userTasks,
            'completedTasks' => $completedTasks,
            'teammates' => $teammates
        ]);
    }

    /**
     * Create new project
     */
    public function create() {
        $this->validateCSRF();

        $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
        $teamId = filter_input(INPUT_POST, "team_id", FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$name || !$teamId) {
            $this->redirect('/index.php', 'Project name and team are required', 'error');
        }

        $userId = $this->getCurrentUserId();

        // Check if user is member of the team
        if (!$this->teamModel->isUserMember($userId, $teamId)) {
            $this->redirect('/index.php', 'You can only create projects in teams you are a member of', 'error');
        }

        $projectId = $this->projectModel->create($name, $teamId);
        $this->redirect("/teamtasks.php?team_id={$teamId}", 'Project created successfully!', 'success');
    }

    /**
     * Delete project
     */
    public function delete() {
        $this->validateCSRF();

        $projectId = filter_input(INPUT_POST, "project_id", FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$projectId) {
            $this->redirect('/index.php', 'Invalid project ID', 'error');
        }

        $userId = $this->getCurrentUserId();

        // Check if user can access this project
        if (!$this->projectModel->canUserAccess($userId, $projectId)) {
            $this->redirect('/index.php', 'You do not have access to this project', 'error');
        }

        $project = $this->projectModel->findById($projectId);
        $teamId = $project['team_id'];

        $this->projectModel->delete($projectId);
        $this->redirect("/teamtasks.php?team_id={$teamId}", 'Project deleted successfully!', 'success');
    }
}