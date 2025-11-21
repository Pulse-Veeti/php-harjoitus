# Modern PHP MVC Routing Transformation Guide

## Overview

This guide documents the complete transformation of your PHP project from a **legacy action-based architecture** to a **modern routing-based MVC system**. This transformation improves code organization, maintainability, and follows modern PHP development practices.

## Table of Contents

1. [What Was Changed](#what-was-changed)
2. [New Architecture Explained](#new-architecture-explained)
3. [Files Created](#files-created)
4. [Files Modified](#files-modified)
5. [Files Removed](#files-removed)
6. [How the New Routing Works](#how-the-new-routing-works)
7. [Code Examples](#code-examples)
8. [Benefits of the New System](#benefits-of-the-new-system)
9. [Testing the Implementation](#testing-the-implementation)

---

## What Was Changed

### Before: Legacy Action Files Architecture
```
/src/
├── actions/
│   ├── createTask.php       # Separate file for creating tasks
│   ├── assignTask.php       # Separate file for assigning tasks
│   ├── login.php            # Separate file for login
│   └── register.php         # Separate file for registration
├── account.php              # Direct entry point for authentication
├── project.php              # Direct entry point for projects
└── teamtasks.php            # Direct entry point for teams
```

### After: Modern Routing Architecture
```
/public/
└── index.php               # Single entry point for ALL requests

/src/
├── Core/
│   └── Router.php          # Routing logic
├── controllers/            # All business logic organized by resource
├── views/                  # All presentation logic
├── models/                 # All data access logic
└── routes.php             # Centralized route definitions
```

---

## New Architecture Explained

### 1. Front Controller Pattern

The **Front Controller Pattern** is a design pattern where a single entry point handles all incoming requests.

**Old Way:**
- User visits `/account.php` → Direct file execution
- User submits form to `/actions/login.php` → Direct file execution

**New Way:**
- User visits any URL → All requests go to `/public/index.php`
- Router determines which controller method to call
- Clean, centralized request handling

### 2. URL Routing System

Instead of accessing files directly, URLs are mapped to controller methods through the router.

**Route Mapping Examples:**
```php
// In src/routes.php
$router->addRoute('GET', 'teams', 'TeamController', 'index');
$router->addRoute('POST', 'teams', 'TeamController', 'create');
$router->addRoute('GET', 'projects/{id}', 'ProjectController', 'show');
$router->addRoute('POST', 'tasks/{id}/assign', 'TaskController', 'assign');
```

### 3. RESTful URL Structure

The new system follows REST (Representational State Transfer) principles:

| HTTP Method | URL Pattern | Controller Method | Description |
|-------------|-------------|-------------------|-------------|
| `GET` | `/teams` | `TeamController@index` | List all teams |
| `POST` | `/teams` | `TeamController@create` | Create new team |
| `GET` | `/teams/{id}` | `TeamController@show` | Show specific team |
| `POST` | `/teams/{id}/join` | `TeamController@join` | Join specific team |
| `POST` | `/tasks/{id}/assign` | `TaskController@assign` | Assign specific task |

---

## Files Created

### 1. `/public/index.php` - Front Controller

**Purpose:** Single entry point for all HTTP requests

**Key Responsibilities:**
- Start secure session
- Include router and route definitions
- Parse incoming URL and HTTP method
- Dispatch request to appropriate controller
- Handle errors gracefully

**How It Works:**
```php
<?php
// 1. Setup
require_once __DIR__ . '/../src/helpers/session_manager.php';
startSecureSession();

// 2. Load routing system
require_once __DIR__ . '/../src/Core/Router.php';
require_once __DIR__ . '/../src/routes.php';

// 3. Parse request
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// 4. Route to controller
try {
    $router->dispatch(ltrim($uri, '/'), $method);
} catch (Exception $e) {
    // Show 404 or error page
}
```

### 2. `/src/Core/Router.php` - Router Class

**Purpose:** Handles URL pattern matching and controller dispatching

**Key Methods:**
- `addRoute($method, $pattern, $controller, $action)` - Register new route
- `dispatch($uri, $method)` - Find matching route and call controller
- `matchRoute($pattern, $uri)` - Check if URI matches pattern
- `callController($controller, $method, $params)` - Instantiate and call controller

**Route Parameter Extraction:**
The router can extract parameters from URLs:
```php
// Route: 'tasks/{id}/assign'
// URL: 'tasks/5/assign'
// Result: $id = '5' passed to controller method
```

### 3. `/src/routes.php` - Route Definitions

**Purpose:** Central location for all application routes

**Organization by Resource:**
```php
// Authentication routes
$router->addRoute('GET', '', 'AuthController', 'showAuthForms');
$router->addRoute('POST', 'login', 'AuthController', 'login');

// Team management routes
$router->addRoute('GET', 'teams', 'TeamController', 'index');
$router->addRoute('POST', 'teams', 'TeamController', 'create');

// Task management routes
$router->addRoute('POST', 'tasks/{id}/assign', 'TaskController', 'assign');
```

### 4. `/public/.htaccess` - Apache Configuration

**Purpose:** Redirect all requests to the front controller

**How it Works:**
```apache
RewriteEngine On

# Don't rewrite actual files/directories
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Send everything to index.php
RewriteRule ^(.*)$ /index.php [QSA,L]
```

**What This Does:**
- User requests `/teams/5`
- Apache checks if `/teams/5` is a real file (it's not)
- Apache redirects to `/index.php`
- PHP router handles `/teams/5` and calls `TeamController@show(5)`

### 5. `/src/views/teams/index.php` - Teams Dashboard

**Purpose:** Main dashboard view for teams listing

---

## Files Modified

### 1. **BaseController.php** - Enhanced with Routing Helpers

**New Methods Added:**
```php
protected function isPost(): bool
protected function isGet(): bool
protected function handleRequest(callable $getCallback, callable $postCallback)
```

**Updated redirect URLs:**
```php
// Old: $this->redirect('/account.php', 'Please log in', 'error');
// New: $this->redirect('/login', 'Please log in', 'error');
```

### 2. **AuthController.php** - Single Methods for GET/POST

**Before:** Separate methods for showing forms and processing
```php
public function showAuthForms() // Show forms
public function login()         // Process login only
public function register()      // Process registration only
```

**After:** Single methods handle both GET and POST
```php
public function login() {
    return $this->handleRequest(
        // GET: Show login form
        function() { $this->view('auth/login'); },

        // POST: Process login
        function() { /* login logic */ }
    );
}
```

### 3. **TaskController.php** - Route Parameters from URL

**Before:** Get task ID from POST data
```php
public function assign() {
    $taskId = filter_input(INPUT_POST, "task_id", ...);
    // ...
}
```

**After:** Get task ID from URL route parameter
```php
public function assign($taskId) {  // $taskId comes from URL /tasks/{id}/assign
    // No need to extract from POST - router provides it
    // ...
}
```

### 4. **TeamController.php** - Added Index Method and Route Parameters

**New Method:**
```php
public function index() {
    // Show teams dashboard - main landing page after login
}
```

**Updated Methods:**
```php
public function join($teamId) {    // $teamId from URL /teams/{id}/join
public function delete($teamId) { // $teamId from URL /teams/{id}/delete
```

### 5. **All View Files** - Updated Form Actions

**Before:** Forms posted to action files
```html
<form action="/actions/createTask.php" method="POST">
<form action="/actions/assignTask.php" method="POST">
```

**After:** Forms post to RESTful routes
```html
<form action="/tasks" method="POST">
<form action="/tasks/5/assign" method="POST">
```

**Navigation Links Updated:**
```php
// Old: <a href="/account.php">Login</a>
// New: <a href="/">Login</a>

// Old: <a href="/project.php?project_id=5">Project</a>
// New: <a href="/projects/5">Project</a>
```

---

## Files Removed

### 1. **Entire `/src/actions/` Directory**
All these files were removed as their functionality moved to controllers:
- `assignTask.php` → `TaskController@assign()`
- `completeTask.php` → `TaskController@complete()`
- `createProject.php` → `ProjectController@create()`
- `createTask.php` → `TaskController@create()`
- `createTeam.php` → `TeamController@create()`
- `deleteTeam.php` → `TeamController@delete()`
- `jointeam.php` → `TeamController@join()`
- `login.php` → `AuthController@login()`
- `logout.php` → `AuthController@logout()`
- `register.php` → `AuthController@register()`
- `unassignTask.php` → `TaskController@unassign()`

### 2. **Direct Entry Point Files**
These files provided direct access that bypassed proper routing:
- `account.php` → Now handled by routes `/` and `/login`
- `index.php` → Now handled by route `/teams`
- `project.php` → Now handled by route `/projects/{id}`
- `teamCreate.php` → Now handled by route `/teams/create`
- `teamtasks.php` → Now handled by route `/teams/{id}`

---

## How the New Routing Works

### Step-by-Step Request Flow

Let's trace what happens when a user submits a form to create a task:

#### 1. **User Action**
User fills out task creation form and clicks "Create Task"

#### 2. **Form Submission**
```html
<form action="/tasks" method="POST">
    <input name="task_name" value="Fix bug">
    <input name="project_id" value="5">
    <!-- ... other fields ... -->
</form>
```

#### 3. **Apache Processing**
- Browser sends: `POST /tasks`
- Apache receives the request
- Apache checks: "Is `/tasks` a real file?" (No)
- Apache redirects to: `POST /index.php`
- Apache preserves original URL in `$_SERVER['REQUEST_URI']`

#### 4. **Front Controller Processing**
```php
// public/index.php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // "/tasks"
$method = $_SERVER['REQUEST_METHOD'];                   // "POST"

$uri = ltrim($uri, '/'); // Remove leading slash: "tasks"
$router->dispatch('tasks', 'POST');
```

#### 5. **Router Processing**
```php
// src/Core/Router.php
public function dispatch($uri, $method) {
    // Look through all POST routes
    foreach($this->routes['POST'] as $route) {
        if ($route['pattern'] === 'tasks') {
            // Found match!
            $this->callController('TaskController', 'create', []);
        }
    }
}
```

#### 6. **Controller Instantiation**
```php
// Router creates new TaskController and calls create() method
require_once "controllers/TaskController.php";
$controller = new TaskController();
$controller->create(); // No parameters for this route
```

#### 7. **Controller Processing**
```php
// TaskController@create()
public function create() {
    $this->validateCSRF(); // Check CSRF token

    // Extract form data
    $taskName = filter_input(INPUT_POST, "task_name", ...);
    $projectId = filter_input(INPUT_POST, "project_id", ...);

    // Validate and process
    if (!$taskName || !$projectId) {
        $this->redirect("/projects/{$projectId}", 'Error!', 'error');
        return;
    }

    // Create task via model
    $this->taskModel->create($taskName, $projectId, ...);

    // Redirect with success message
    $this->redirect("/projects/{$projectId}", 'Task created!', 'success');
}
```

### Route Parameter Extraction Example

For routes with parameters like `/tasks/{id}/assign`:

#### 1. **URL Structure**
- Route pattern: `tasks/{id}/assign`
- Actual URL: `tasks/5/assign`

#### 2. **Router Pattern Matching**
```php
// Convert pattern to regex
$pattern = 'tasks/{id}/assign';
$regexPattern = preg_quote($pattern, '/');              // "tasks/\{id\}/assign"
$regexPattern = preg_replace('/\\{([^}]+)\\}/', '([^/]+)', $regexPattern); // "tasks/([^/]+)/assign"
$regexPattern = "/^{$regexPattern}$/";                  // "/^tasks/([^/]+)/assign$/"

// Match against URL
if (preg_match($regexPattern, 'tasks/5/assign', $matches)) {
    // $matches = ['tasks/5/assign', '5']
    // Parameter 'id' = '5'
}
```

#### 3. **Controller Method Call**
```php
$controller = new TaskController();
$controller->assign('5'); // Pass extracted ID as parameter
```

#### 4. **Controller Method**
```php
public function assign($taskId) {  // $taskId = '5'
    // Use the task ID directly - no need to extract from POST
    $this->taskModel->assignTask($ownerId, $taskId);
}
```

---

## Code Examples

### Example 1: How Forms Work Now

**Old Action File Approach:**
```php
<!-- Form in view -->
<form action="/actions/createTask.php" method="POST">

<!-- Separate action file: actions/createTask.php -->
<?php
require_once "../helpers/session_manager.php";
$pdo = require "../db/db.php";

// Direct database operations here
if ($_POST['task_name']) {
    $stmt = $pdo->prepare("INSERT INTO tasks...");
    // ...
}
?>
```

**New Routing Approach:**
```php
<!-- Form in view -->
<form action="/tasks" method="POST">

<!-- Route definition: routes.php -->
$router->addRoute('POST', 'tasks', 'TaskController', 'create');

<!-- Controller method: TaskController.php -->
public function create() {
    // Proper MVC: Controller uses Model, redirects to View
    $this->validateCSRF();
    $taskName = filter_input(INPUT_POST, 'task_name', FILTER_SANITIZE_SPECIAL_CHARS);

    $this->taskModel->create($taskName, ...);
    $this->redirect('/projects/5', 'Task created!', 'success');
}
```

### Example 2: How URL Parameters Work

**Route with Parameter:**
```php
$router->addRoute('POST', 'tasks/{id}/assign', 'TaskController', 'assign');
```

**Controller Method:**
```php
public function assign($taskId) {  // Router automatically passes the {id} value
    $taskOwner = filter_input(INPUT_POST, 'task_owner', ...);
    $this->taskModel->assignTask($taskOwner, $taskId);
}
```

**How Router Extracts Parameter:**
```php
// URL: /tasks/42/assign
// Pattern: tasks/{id}/assign
// Router extracts: $id = '42'
// Calls: $controller->assign('42')
```

### Example 3: Single Method Handling GET and POST

**Modern Pattern:**
```php
public function create() {
    return $this->handleRequest(
        // GET request: Show the creation form
        function() {
            $this->view('teams/create');
        },

        // POST request: Process the form submission
        function() {
            $this->validateCSRF();
            $name = filter_input(INPUT_POST, 'name', ...);

            if (!$name) {
                $this->redirect('/teams/create', 'Name required', 'error');
            }

            $teamId = $this->teamModel->create($name);
            $this->redirect('/teams', 'Team created!', 'success');
        }
    );
}
```

**Route Definition:**
```php
// Same route handles both GET (show form) and POST (process form)
$router->addRoute('GET', 'teams/create', 'TeamController', 'create');
$router->addRoute('POST', 'teams/create', 'TeamController', 'create');
```

---

## Benefits of the New System

### 1. **Centralized Request Handling**
- **Single entry point** makes security easier to manage
- **Consistent session handling** across all requests
- **Global error handling** and logging

### 2. **Clean, RESTful URLs**
```
Old: /actions/createTask.php
New: POST /tasks

Old: /project.php?project_id=5
New: GET /projects/5

Old: /actions/assignTask.php
New: POST /tasks/5/assign
```

### 3. **Better Code Organization**
- **No scattered action files** - everything in controllers
- **Logical grouping** by resource (teams, projects, tasks)
- **Separation of concerns** - routing, business logic, presentation

### 4. **Improved Security**
- **Centralized CSRF validation**
- **Blocked direct file access** via .htaccess
- **Consistent authentication checks**

### 5. **Easier Maintenance**
- **Single route file** to see all available URLs
- **Consistent URL patterns**
- **Easy to add new routes**

### 6. **Framework-like Structure**
- Follows patterns used by **Laravel**, **Symfony**, **CodeIgniter**
- **Professional PHP development practices**
- **Scalable architecture**

### 7. **Better Error Handling**
- **404 errors** handled gracefully
- **Centralized exception handling**
- **User-friendly error pages**

---

## Testing the Implementation

### 1. **Basic Functionality Test**

Test the main routes manually:

```bash
# Test home page (should show login/register)
curl -X GET http://localhost:8080/

# Test teams dashboard (after login)
curl -X GET http://localhost:8080/teams

# Test project view
curl -X GET http://localhost:8080/projects/5

# Test team view
curl -X GET http://localhost:8080/teams/1
```

### 2. **Form Submission Test**

Test that forms work correctly:

1. **Login Test:**
   - Go to `/`
   - Fill out login form
   - Should redirect to `/teams`

2. **Task Creation Test:**
   - Go to `/projects/5`
   - Fill out "Create New Task" form
   - Should create task and reload project page

3. **Task Assignment Test:**
   - Go to `/projects/5`
   - Find an unassigned task
   - Assign it to a team member
   - Should update task and reload page

### 3. **Route Parameter Test**

Verify that URL parameters work:

1. Visit `/teams/123` - should call `TeamController@show('123')`
2. Submit form to `/tasks/456/assign` - should call `TaskController@assign('456')`

### 4. **Error Handling Test**

Test error scenarios:

1. **404 Test:** Visit `/nonexistent` - should show 404 page
2. **Invalid ID Test:** Visit `/teams/999999` - should redirect with error
3. **CSRF Test:** Submit form without CSRF token - should fail gracefully

### 5. **Security Test**

Verify security measures:

1. **Direct File Access:** Try to access `/src/controllers/TaskController.php` directly - should be blocked
2. **Action Files:** Try to access `/actions/createTask.php` - should return 404 (files removed)
3. **Authentication:** Try to access `/teams` without login - should redirect to `/login`

---

## Conclusion

Your project has been successfully transformed from a **legacy action-based architecture** to a **modern PHP MVC system with routing**. This transformation provides:

- **Better code organization** following industry standards
- **Cleaner, more intuitive URLs**
- **Improved security and maintainability**
- **Professional development practices**

The new architecture follows the same patterns used by major PHP frameworks like Laravel and Symfony, while remaining lightweight and PHP-native. You now have a solid foundation for further development that will scale well as your application grows.

### Next Steps for Learning

1. **Study the Router class** to understand how URL pattern matching works
2. **Examine the route definitions** to see RESTful URL design
3. **Look at the controller updates** to understand the new request handling patterns
4. **Practice adding new routes** for additional features
5. **Consider adding middleware** for authentication and logging

This routing system demonstrates core concepts used in professional PHP development and provides a excellent foundation for learning modern web development practices.