<?php

require_once '/var/www/src/models/BaseModel.php';

class TeamModel extends BaseModel {

    /**
     * Get all teams
     */
    public function getAll() {
        return $this->fetchAll("SELECT * FROM teams ORDER BY name ASC");
    }

    /**
     * Find team by ID
     */
    public function findById($id) {
        return $this->fetchOne(
            "SELECT * FROM teams WHERE id = :id",
            [':id' => $id]
        );
    }

    /**
     * Create new team
     */
    public function create($name) {
        return $this->insert(
            "INSERT INTO teams (name) VALUES (:name)",
            [':name' => $name]
        );
    }

    /**
     * Delete team by ID
     */
    public function delete($id) {
        $this->execute(
            "DELETE FROM teams WHERE id = :id",
            [':id' => $id]
        );
    }

    /**
     * Add user to team
     */
    public function addMember($userId, $teamId) {
        try {
            $this->execute(
                "INSERT INTO user_teams (user_id, team_id) VALUES (:user_id, :team_id)",
                [':user_id' => $userId, ':team_id' => $teamId]
            );
            return true;
        } catch (PDOException $e) {
            // User already in team (duplicate key)
            return false;
        }
    }

    /**
     * Check if user is member of team
     */
    public function isUserMember($userId, $teamId) {
        $count = $this->fetchColumn(
            "SELECT COUNT(*) FROM user_teams WHERE user_id = :user_id AND team_id = :team_id",
            [':user_id' => $userId, ':team_id' => $teamId]
        );
        return $count > 0;
    }

    /**
     * Get teams where user is a member
     */
    public function getUserTeams($userId) {
        return $this->fetchAll(
            "SELECT t.* FROM teams t
             JOIN user_teams ut ON t.id = ut.team_id
             WHERE ut.user_id = :user_id
             ORDER BY t.name ASC",
            [':user_id' => $userId]
        );
    }

    /**
     * Get team members
     */
    public function getMembers($teamId) {
        return $this->fetchAll(
            "SELECT u.id, u.name, u.email
             FROM users u
             JOIN user_teams ut ON u.id = ut.user_id
             WHERE ut.team_id = :team_id
             ORDER BY u.name ASC",
            [':team_id' => $teamId]
        );
    }

    /**
     * Remove user from team
     */
    public function removeMember($userId, $teamId) {
        $this->execute(
            "DELETE FROM user_teams WHERE user_id = :user_id AND team_id = :team_id",
            [':user_id' => $userId, ':team_id' => $teamId]
        );
    }

    /**
     * Get users in team
     */
    public function getUsersInTeam($projectId) {
        return $this->fetchAll(
            "SELECT DISTINCT users.id, users.name FROM users JOIN user_teams ON users.id = user_teams.user_id JOIN projects ON user_teams.team_id = projects.team_id WHERE projects.id = :project_id",
            [
                ':project_id'=>$projectId
            ]
        );
    }
}