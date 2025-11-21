<?php

require_once '/var/www/src/models/BaseModel.php';

class UserModel extends BaseModel {

    /**
     * Find user by email
     */
    public function findByEmail($email) {
        return $this->fetchOne(
            "SELECT id, name, password, email FROM users WHERE email = :email",
            [':email' => $email]
        );
    }

    /**
     * Find user by ID
     */
    public function findById($id) {
        return $this->fetchOne(
            "SELECT id, name, email, created_at FROM users WHERE id = :id",
            [':id' => $id]
        );
    }

    /**
     * Create new user
     */
    public function create($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return $this->insert(
            "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)",
            [
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashedPassword
            ]
        );
    }

    /**
     * Check if email already exists
     */
    public function emailExists($email) {
        return $this->fetchColumn(
            "SELECT COUNT(*) FROM users WHERE email = :email",
            [':email' => $email]
        ) > 0;
    }

    /**
     * Get all users
     */
    public function getAll() {
        return $this->fetchAll(
            "SELECT id, name, email, created_at FROM users ORDER BY name ASC"
        );
    }

    /**
     * Verify user password
     */
    public function verifyPassword($user, $password) {
        return password_verify($password, $user['password']);
    }
}