<?php

require_once '/var/www/src/controllers/BaseController.php';
require_once '/var/www/src/models/UserModel.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel($this->pdo);
    }

    /**
     * Show login and register forms
     */
    public function showAuthForms() {
        $this->view('auth/account');
    }

    /**
     * Handle login - both GET (show form) and POST (process form)
     */
    public function login() {
        return $this->handleRequest(
            // GET: Show login form
            function() {
                $this->view('auth/login');
            },
            // POST: Process login
            function() {
                $this->validateCSRF();

                $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
                $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

                if (!$email || !$password) {
                    $this->redirect('/login', 'Please provide valid email and password', 'error');
                }

                $user = $this->userModel->findByEmail($email);

                if (!$user || !$this->userModel->verifyPassword($user, $password)) {
                    $this->redirect('/login', 'Invalid email or password', 'error');
                }

                // Set session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['logged_in'] = true;
                session_regenerate_id(true);

                $this->redirect('/teams', 'Login successful!', 'success');
            }
        );
    }

    /**
     * Show login form only (separate method if needed)
     */
    public function showLogin() {
        $this->view('auth/login');
    }

    /**
     * Handle registration - both GET (show form) and POST (process form)
     */
    public function register() {
        return $this->handleRequest(
            // GET: Show registration form
            function() {
                $this->view('auth/register');
            },
            // POST: Process registration
            function() {
                $this->validateCSRF();

                $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
                $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
                $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

                if (!$name || !$email || !$password) {
                    $this->redirect('/register', 'All fields are required', 'error');
                }

                if ($this->userModel->emailExists($email)) {
                    $this->redirect('/register', 'Email already exists', 'error');
                }

                $userId = $this->userModel->create($name, $email, $password);

                // Set session data
                $_SESSION['user_id'] = $userId;
                $_SESSION['logged_in'] = true;
                session_regenerate_id(true);

                $this->redirect('/teams', 'Registration successful!', 'success');
            }
        );
    }

    /**
     * Show registration form only (separate method if needed)
     */
    public function showRegister() {
        $this->view('auth/register');
    }

    /**
     * Handle user logout
     */
    public function logout() {
        $_SESSION = array();
        session_destroy();
        $this->redirect('/', 'Logged out successfully', 'success');
    }
}