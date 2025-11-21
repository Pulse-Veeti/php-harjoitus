<?php

require_once '/var/www/src/models/BaseModel.php';

class ProjectModel extends BaseModel {

    /**
     * Find project by ID
     */
    public function findById($id) {
        return $this->fetchOne(
            "SELECT * FROM projects WHERE id = :id",
            [':id' => $id]
        );
    }

    /**
     * Get projects by team ID
     */
    public function getByTeamId($teamId) {
        return $this->fetchAll(
            "SELECT * FROM projects WHERE team_id = :team_id ORDER BY name ASC",
            [':team_id' => $teamId]
        );
    }

    /**
     * Create new project
     */
    public function create($name, $teamId) {
        return $this->insert(
            "INSERT INTO projects (name, team_id) VALUES (:name, :team_id)",
            [':name' => $name, ':team_id' => $teamId]
        );
    }

    /**
     * Update project
     */
    public function update($id, $name) {
        $this->execute(
            "UPDATE projects SET name = :name WHERE id = :id",
            [':name' => $name, ':id' => $id]
        );
    }

    /**
     * Delete project
     */
    public function delete($id) {
        $this->execute(
            "DELETE FROM projects WHERE id = :id",
            [':id' => $id]
        );
    }

    /**
     * Check if user can access project (through team membership)
     */
    public function canUserAccess($userId, $projectId) {
        $count = $this->fetchColumn(
            "SELECT COUNT(*) FROM user_teams ut
             JOIN projects p ON ut.team_id = p.team_id
             WHERE ut.user_id = :user_id AND p.id = :project_id",
            [':user_id' => $userId, ':project_id' => $projectId]
        );
        return $count > 0;
    }

    /**
     * Get project with team information
     */
    public function getWithTeam($id) {
        return $this->fetchOne(
            "SELECT p.*, t.name as team_name
             FROM projects p
             JOIN teams t ON p.team_id = t.id
             WHERE p.id = :id",
            [':id' => $id]
        );
    }
}