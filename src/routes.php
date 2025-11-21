<?php
/**
 * Routes Configuration - Central Route Definitions
 *
 * This file defines ALL the routes for the application. Instead of having
 * separate action files, we map URLs to specific controller methods here.
 *
 * Route Pattern Explanation:
 * - 'users' → matches exactly "users"
 * - 'users/{id}' → matches "users/5", "users/123", etc. ({id} becomes a parameter)
 * - 'tasks/{id}/edit' → matches "tasks/5/edit", "tasks/42/edit", etc.
 *
 * HTTP Methods:
 * - GET: Retrieve/display data (show forms, list items)
 * - POST: Create new data or perform actions
 * - PUT/PATCH: Update existing data (not commonly used in forms)
 * - DELETE: Remove data (not commonly used in forms)
 */

// Create a new router instance
$router = new Router();

/**
 * =============================================================================
 * AUTHENTICATION ROUTES
 * =============================================================================
 * These handle user login, registration, and logout
 */

// Home page / Dashboard
$router->addRoute('GET', '', 'AuthController', 'showAuthForms'); // Empty string = root URL "/"

// Login routes
$router->addRoute('GET', 'login', 'AuthController', 'showLogin');
$router->addRoute('POST', 'login', 'AuthController', 'login');

// Registration routes
$router->addRoute('GET', 'register', 'AuthController', 'showRegister');
$router->addRoute('POST', 'register', 'AuthController', 'register');

// Logout
$router->addRoute('POST', 'logout', 'AuthController', 'logout');

/**
 * =============================================================================
 * TEAM ROUTES
 * =============================================================================
 * Managing teams - create, view, join, delete
 */

// List all teams / Team dashboard
$router->addRoute('GET', 'teams', 'TeamController', 'index');

// Create new team
$router->addRoute('GET', 'teams/create', 'TeamController', 'create');  // Show form
$router->addRoute('POST', 'teams/create', 'TeamController', 'create'); // Process form

// View specific team with projects
$router->addRoute('GET', 'teams/{id}', 'TeamController', 'show');

// Join a team
$router->addRoute('POST', 'teams/{id}/join', 'TeamController', 'join');

// Delete team
$router->addRoute('POST', 'teams/{id}/delete', 'TeamController', 'delete');

/**
 * =============================================================================
 * PROJECT ROUTES
 * =============================================================================
 * Managing projects within teams
 */

// View specific project with tasks
$router->addRoute('GET', 'projects/{id}', 'ProjectController', 'show');

// Create new project
$router->addRoute('POST', 'projects', 'ProjectController', 'create');

// Delete project
$router->addRoute('POST', 'projects/{id}/delete', 'ProjectController', 'delete');

/**
 * =============================================================================
 * TASK ROUTES
 * =============================================================================
 * Managing tasks within projects - create, assign, complete, etc.
 */

// Create new task
$router->addRoute('POST', 'tasks', 'TaskController', 'create');

// Assign task to user
$router->addRoute('POST', 'tasks/{id}/assign', 'TaskController', 'assign');

// Unassign task from user
$router->addRoute('POST', 'tasks/{id}/unassign', 'TaskController', 'unassign');

// Mark task as complete
$router->addRoute('POST', 'tasks/{id}/complete', 'TaskController', 'complete');

/**
 * =============================================================================
 * ROUTE EXPLANATION EXAMPLES
 * =============================================================================
 *
 * Old Action File Approach:
 * -------------------------
 * <form action="/actions/createTask.php" method="POST">
 * <form action="/actions/assignTask.php" method="POST">
 * <form action="/actions/completeTask.php" method="POST">
 *
 * New Routing Approach:
 * ---------------------
 * <form action="/tasks" method="POST">                    → TaskController->create()
 * <form action="/tasks/5/assign" method="POST">          → TaskController->assign(5)
 * <form action="/tasks/5/complete" method="POST">        → TaskController->complete(5)
 *
 * URL Examples:
 * -------------
 * GET  /                     → AuthController->showAuthForms()    (home page)
 * GET  /teams/5              → TeamController->show(5)            (team with ID 5)
 * POST /teams/5/join         → TeamController->join(5)            (join team 5)
 * GET  /projects/10          → ProjectController->show(10)        (project with ID 10)
 * POST /tasks/15/assign      → TaskController->assign(15)         (assign task 15)
 *
 * Benefits:
 * ---------
 * 1. RESTful URLs that make sense
 * 2. All logic organized in controllers
 * 3. Easy to understand and maintain
 * 4. Follows modern web development practices
 * 5. No scattered action files
 */