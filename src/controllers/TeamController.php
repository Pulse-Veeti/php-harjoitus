<?php

require_once '/var/www/src/controllers/BaseController.php';
require_once '/var/www/src/models/TeamModel.php';
require_once '/var/www/src/models/ProjectModel.php';

class TeamController extends BaseController {
    private $teamModel;
    private $projectModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->teamModel = new TeamModel($this->pdo);
        $this->projectModel = new ProjectModel($this->pdo);
    }

    /**
     * Show teams dashboard - list all teams user can see
     */
    public function index() {
        $userId = $this->getCurrentUserId();
        $userTeams = $this->teamModel->getUserTeams($userId);
        $allTeams = $this->teamModel->getAll();

        $this->view('teams/index', [
            'userTeams' => $userTeams,
            'allTeams' => $allTeams
        ]);
    }

    /**
     * Show/Handle create team form - GET shows form, POST processes
     */
    public function create() {
        return $this->handleRequest(
            // GET: Show create form
            function() {
                $this->view('teams/create');
            },
            // POST: Process form
            function() {
                $this->validateCSRF();

                $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);

                if (!$name) {
                    $this->redirect('/teams/create', 'Team name is required', 'error');
                }

                $teamId = $this->teamModel->create($name);
                $this->teamModel->addMember($this->getCurrentUserId(), $teamId);

                $this->redirect('/teams', 'Team created successfully!', 'success');
            }
        );
    }

    /**
     * Show create team form only (separate method if needed)
     */
    public function showCreateForm() {
        $this->view('teams/create');
    }

    /**
     * Show team details with projects
     */
    public function show($teamId) {
        $team = $this->teamModel->findById($teamId);
        if (!$team) {
            $this->redirect('/teams', 'Team not found', 'error');
        }

        $userId = $this->getCurrentUserId();
        $isUserMember = $this->teamModel->isUserMember($userId, $teamId);
        $projects = $this->projectModel->getByTeamId($teamId);
        $members = $this->teamModel->getMembers($teamId);

        $this->view('teams/show', [
            'team' => $team,
            'isUserMember' => $isUserMember,
            'projects' => $projects,
            'members' => $members
        ]);
    }


    /**
     * Join existing team
     * $teamId comes from the URL route parameter {id}
     */
    public function join($teamId) {
        $this->validateCSRF();

        if (!$teamId) {
            $this->redirect('/teams', 'Invalid team ID', 'error');
        }

        $userId = $this->getCurrentUserId();

        if ($this->teamModel->isUserMember($userId, $teamId)) {
            $this->redirect("/teams/{$teamId}", 'You are already a member of this team', 'info');
        }

        if ($this->teamModel->addMember($userId, $teamId)) {
            $this->redirect("/teams/{$teamId}", 'Successfully joined team!', 'success');
        } else {
            $this->redirect('/teams', 'Failed to join team', 'error');
        }
    }

    /**
     * Delete team (only for members)
     * $teamId comes from the URL route parameter {id}
     */
    public function delete($teamId) {
        $this->validateCSRF();

        if (!$teamId) {
            $this->redirect('/teams', 'Invalid team ID', 'error');
        }

        $userId = $this->getCurrentUserId();

        // Check if user is member of the team
        if (!$this->teamModel->isUserMember($userId, $teamId)) {
            $this->redirect('/teams', 'You can only delete teams you are a member of', 'error');
        }

        $this->teamModel->delete($teamId);
        $this->redirect('/teams', 'Team deleted successfully!', 'success');
    }
}