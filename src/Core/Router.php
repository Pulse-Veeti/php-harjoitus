<?php
/**
 * Router Class - Handles URL Routing to Controllers
 *
 * This class is responsible for:
 * 1. Storing route definitions (URL patterns → Controller methods)
 * 2. Matching incoming URLs against stored routes
 * 3. Extracting parameters from URLs (like {id} in /tasks/{id})
 * 4. Calling the appropriate controller method
 *
 * Example Usage:
 * $router = new Router();
 * $router->addRoute('GET', 'tasks/{id}', 'TaskController', 'show');
 * $router->dispatch('tasks/5', 'GET'); // Calls TaskController->show(5)
 */
class Router
{
    /**
     * Array to store all registered routes
     *
     * Structure:
     * [
     *     'GET' => [
     *         ['pattern' => 'tasks/{id}', 'controller' => 'TaskController', 'method' => 'show'],
     *         ['pattern' => 'projects', 'controller' => 'ProjectController', 'method' => 'index']
     *     ],
     *     'POST' => [
     *         ['pattern' => 'tasks', 'controller' => 'TaskController', 'method' => 'create']
     *     ]
     * ]
     */
    private array $routes = [];

    /**
     * Add a route to the router
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $pattern URL pattern (can include {parameter} placeholders)
     * @param string $controller Name of the controller class
     * @param string $action Name of the method to call on the controller
     *
     * Example:
     * addRoute('GET', 'tasks/{id}/edit', 'TaskController', 'edit')
     * This means: GET /tasks/5/edit → TaskController->edit(5)
     */
    public function addRoute(string $method, string $pattern, string $controller, string $action): void
    {
        // Store the route in the routes array, organized by HTTP method
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'controller' => $controller,
            'method' => $action
        ];
    }

    /**
     * Dispatch a request to the appropriate controller
     *
     * @param string $uri The requested URI (e.g., "tasks/5/edit")
     * @param string $method The HTTP method (e.g., "GET", "POST")
     * @throws Exception if no matching route is found
     */
    public function dispatch(string $uri, string $method): void
    {
        // Check if we have any routes for this HTTP method
        if (!isset($this->routes[$method])) {
            throw new Exception("No routes found for method: $method");
        }

        // Try to find a matching route
        foreach ($this->routes[$method] as $route) {
            $match = $this->matchRoute($route['pattern'], $uri);

            if ($match !== false) {
                // We found a matching route!
                $this->callController($route['controller'], $route['method'], $match['params']);
                return;
            }
        }

        // No matching route found
        throw new Exception("Route not found: $method $uri");
    }

    /**
     * Check if a URI matches a route pattern and extract parameters
     *
     * @param string $pattern Route pattern (e.g., "tasks/{id}/edit")
     * @param string $uri Actual URI (e.g., "tasks/5/edit")
     * @return array|false Returns array with params if match, false if no match
     *
     * Example:
     * Pattern: "tasks/{id}/edit"
     * URI: "tasks/5/edit"
     * Returns: ['params' => ['id' => '5']]
     */
    private function matchRoute(string $pattern, string $uri): array|false
    {
        // Simple approach: if no parameters, do exact match
        if (strpos($pattern, '{') === false) {
            return ($pattern === $uri) ? ['params' => []] : false;
        }

        // Split both pattern and URI by '/'
        $patternParts = explode('/', $pattern);
        $uriParts = explode('/', $uri);

        // Must have same number of parts
        if (count($patternParts) !== count($uriParts)) {
            return false;
        }

        $params = [];

        // Check each part
        for ($i = 0; $i < count($patternParts); $i++) {
            $patternPart = $patternParts[$i];
            $uriPart = $uriParts[$i];

            // If pattern part is a parameter (contains {})
            if (preg_match('/^{([^}]+)}$/', $patternPart, $matches)) {
                // Extract parameter name and value
                $paramName = $matches[1];
                $params[$paramName] = $uriPart;
            } else {
                // Must be exact match
                if ($patternPart !== $uriPart) {
                    return false;
                }
            }
        }

        return ['params' => $params];
    }

    /**
     * Instantiate controller and call the specified method
     *
     * @param string $controllerName Name of controller class
     * @param string $methodName Name of method to call
     * @param array $params Parameters to pass to the method
     * @throws Exception if controller or method doesn't exist
     */
    private function callController(string $controllerName, string $methodName, array $params): void
    {
        // Include the controller file
        $controllerFile = "/var/www/src/controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: {$controllerFile}");
        }

        require_once $controllerFile;

        // Check if controller class exists
        if (!class_exists($controllerName)) {
            throw new Exception("Controller class not found: {$controllerName}");
        }

        // Instantiate the controller
        $controller = new $controllerName();

        // Check if method exists
        if (!method_exists($controller, $methodName)) {
            throw new Exception("Method not found: {$controllerName}->{$methodName}");
        }

        // Call the controller method with parameters
        // Example: $controller->show('5') for TaskController->show($id)
        call_user_func_array([$controller, $methodName], array_values($params));
    }
}