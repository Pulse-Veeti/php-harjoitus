<?php

require_once "/var/www/src/models/BaseModel.php";

class TaskModel extends BaseModel {
    /**
     * Create new task
     */
    public function create($task_name, $projectId, $taskText, $taskColor, $dueDate, $user_id) {
        return $this->insert(
            "INSERT INTO tasks (task_name, project_id, task_text, task_color, due_date, created_by) VALUES (:task_name, :project_id, :task_text, :task_color, :due_date, :created_by)",
            [
                ':task_name'=>$task_name,
                ':project_id'=>$projectId,
                ':task_text'=>$taskText,
                ':task_color'=>$taskColor,
                ':due_date'=>$dueDate,
                ':created_by'=>$user_id,
            ]
        );
    }

    /**
     * Get available tasks
     */
    public function getAvailableTasks($user_id, $projectId) {
        return $this->fetchAll(
            "SELECT tasks.*, users.name as owner_name, creator.name as creator_name FROM tasks LEFT JOIN users ON tasks.task_owner = users.id LEFT JOIN users as creator ON tasks.created_by = creator.id WHERE project_id = :project_id AND status != 'Completed' AND (task_owner != :task_owner OR task_owner IS NULL)",
            [
                ':project_id'=>$projectId,
                ':task_owner'=>$user_id,
            ]
        );
    }

    /**
     * Get users tasks
     */
    public function getUserTasks($user_id, $projectId) {
        return $this->fetchAll(
            "SELECT tasks.*, users.name as owner_name, creator.name as creator_name FROM tasks LEFT JOIN users ON tasks.task_owner = users.id LEFT JOIN users as creator ON tasks.created_by = creator.id WHERE project_id = :project_id AND task_owner = :task_owner AND status != 'Completed'",
            [
                ':project_id'=>$projectId,
                ':task_owner'=>$user_id,
            ]
        );
    }

    /**
     * Get completed tasks
     */
    public function getCompletedTasks($projectId) {
        return $this->fetchAll(
            "SELECT tasks.*, users.name as owner_name, creator.name as creator_name FROM tasks LEFT JOIN users ON tasks.task_owner = users.id LEFT JOIN users as creator ON tasks.created_by = creator.id WHERE project_id = :project_id AND status = 'Completed'",
            [
                ':project_id'=>$projectId
            ]
        );
    }

    /**
     * Get complete task
     */
    public function completeTask($user_id, $task_id) {
        $this->execute(
            "UPDATE tasks SET status = 'Completed' WHERE id = :task_id AND task_owner = :task_owner",
            [':task_id' => $task_id, ':task_owner' => $user_id]
        );
    }

    /**
     * Assign task for user
     */
    public function assignTask($user_id, $task_id) {
        $this->execute(
            "UPDATE tasks SET task_owner = :task_owner WHERE id = :task_id",
            [':task_id' => $task_id, ':task_owner' => $user_id]
        );
    }

    /**
     * Assign task for user
     */
    public function unassignTask($task_id) {
        $this->execute(
            "UPDATE tasks SET task_owner = null WHERE id = :task_id",
            [':task_id' => $task_id]
        );
    }
}