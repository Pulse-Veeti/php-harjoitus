<?php

require_once '/var/www/src/helpers/session_manager.php';

class BaseController {
    protected $pdo;

    public function __construct() {
        startSecureSession();
        $this->pdo = require '/var/www/src/db/db.php';
    }

    /**
     * Render a view with data
     */
    protected function view($viewPath, $data = []) {
        // Extract data array to variables
        extract($data);

        // Include the view file
        include "/var/www/src/views/{$viewPath}.php";
    }

    /**
     * Redirect with flash message
     */
    protected function redirect($location, $message = null, $type = 'info') {
        if ($message) {
            setFlashMessage($type, $message);
        }
        header("Location: {$location}");
        exit();
    }

    /**
     * Get current user ID from session
     */
    protected function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Check if user is logged in
     */
    protected function requireAuth() {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            $this->redirect('/login', 'Please log in to continue', 'error');
        }
    }

    /**
     * Validate CSRF for POST requests
     */
    protected function validateCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireCSRFToken();
        }
    }

    /**
     * Check if request method is POST
     */
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request method is GET
     */
    protected function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Handle both GET and POST in single method
     *
     * @param callable $getCallback Function to call for GET requests
     * @param callable $postCallback Function to call for POST requests
     */
    protected function handleRequest(callable $getCallback, callable $postCallback) {
        if ($this->isPost()) {
            return $postCallback();
        }
        return $getCallback();
    }
}