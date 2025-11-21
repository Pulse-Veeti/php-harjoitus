<?php
/**
 * Front Controller - Single Entry Point for All Requests
 *
 * This file receives ALL incoming HTTP requests and routes them to the
 * appropriate controller and method. This is called the "Front Controller Pattern".
 *
 * How it works:
 * 1. User requests any URL (e.g., /tasks, /projects/5, /login)
 * 2. .htaccess redirects ALL requests to this file
 * 3. This file determines which controller method should handle the request
 * 4. The appropriate controller method is called
 * 5. Response is sent back to user
 */

// Start session and include necessary files
require_once '/var/www/src/helpers/session_manager.php';
startSecureSession();

// Include the router and routes
require_once '/var/www/src/Core/Router.php';
require_once '/var/www/src/routes.php';

// Get the current request information
$requestUri = $_SERVER['REQUEST_URI']; // e.g., "/tasks/5/edit?param=value"
$requestMethod = $_SERVER['REQUEST_METHOD']; // e.g., "GET", "POST", "PUT", "DELETE"

// Remove query string from URI (everything after ?)
// Example: "/tasks/5/edit?param=value" becomes "/tasks/5/edit"
$uri = parse_url($requestUri, PHP_URL_PATH);

// Remove leading slash for consistency
// Example: "/tasks/5/edit" becomes "tasks/5/edit"
$uri = ltrim($uri, '/');

try {
    /**
     * Dispatch the request to the appropriate controller
     *
     * The router will:
     * 1. Find a matching route pattern
     * 2. Extract any route parameters (like {id})
     * 3. Instantiate the appropriate controller
     * 4. Call the specified method
     * 5. Pass any extracted parameters to the method
     */
    $router->dispatch($uri, $requestMethod);

} catch (Exception $e) {
    /**
     * Handle errors gracefully
     *
     * If something goes wrong (404, 500, etc.), we catch the exception
     * and show an appropriate error page instead of letting PHP show
     * an ugly error message.
     */

    // Log the error for debugging
    error_log("Router Error: " . $e->getMessage());

    // Show user-friendly error page
    http_response_code(404);
    echo "<h1>Page Not Found</h1>";
    echo "<p>Sorry, the page you're looking for doesn't exist.</p>";
    echo "<a href='/'>← Back to Home</a>";
}