<?php
/**
 * Router Class
 * MINALIA Parfüm E-Ticaret Platformu
 */

class Router {
    private $routes = [];
    private $namedRoutes = [];

    /**
     * Add GET route
     */
    public function get($path, $callback, $name = null) {
        $this->addRoute('GET', $path, $callback, $name);
        return $this;
    }

    /**
     * Add POST route
     */
    public function post($path, $callback, $name = null) {
        $this->addRoute('POST', $path, $callback, $name);
        return $this;
    }

    /**
     * Add route for both GET and POST
     */
    public function any($path, $callback, $name = null) {
        $this->addRoute('GET', $path, $callback, $name);
        $this->addRoute('POST', $path, $callback, $name);
        return $this;
    }

    /**
     * Add route
     */
    private function addRoute($method, $path, $callback, $name = null) {
        $path = $this->normalizePath($path);

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'name' => $name
        ];

        if ($name) {
            $this->namedRoutes[$name] = $path;
        }
    }

    /**
     * Normalize path
     */
    private function normalizePath($path) {
        $path = trim($path, '/');
        $path = "/{$path}";
        $path = preg_replace('#[/]{2,}#', '/', $path);
        return $path;
    }

    /**
     * Dispatch request
     */
    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];

        // Remove query string
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        // Get the path from URL (remove base path if exists)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/') {
            $requestUri = substr($requestUri, strlen($scriptName));
        }

        $requestUri = $this->normalizePath($requestUri);

        // Find matching route
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);

            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove full match

                // Extract named parameters
                $params = [];
                if (preg_match_all('/{([^}]+)}/', $route['path'], $paramNames)) {
                    foreach ($paramNames[1] as $index => $name) {
                        $params[$name] = $matches[$index] ?? null;
                    }
                }

                return $this->executeCallback($route['callback'], $params);
            }
        }

        // No route found
        $this->error404();
    }

    /**
     * Convert route path to regex pattern
     */
    private function convertToRegex($path) {
        // Replace {param} with regex pattern
        $pattern = preg_replace('/{([^}]+)}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Execute callback
     */
    private function executeCallback($callback, $params = []) {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        if (is_string($callback)) {
            // Format: "ControllerName@methodName"
            if (strpos($callback, '@') !== false) {
                list($controller, $method) = explode('@', $callback);

                $controllerFile = APP_PATH . '/controllers/' . $controller . '.php';

                if (file_exists($controllerFile)) {
                    require_once $controllerFile;

                    if (class_exists($controller)) {
                        $controllerInstance = new $controller();

                        if (method_exists($controllerInstance, $method)) {
                            return call_user_func_array([$controllerInstance, $method], $params);
                        }
                    }
                }
            }
        }

        $this->error404();
    }

    /**
     * Get URL by route name
     */
    public function url($name, $params = []) {
        if (!isset($this->namedRoutes[$name])) {
            return '#';
        }

        $path = $this->namedRoutes[$name];

        // Replace parameters
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }

        return BASE_URL . $path;
    }

    /**
     * 404 Error
     */
    private function error404() {
        http_response_code(404);
        if (file_exists(APP_PATH . '/views/pages/errors/404.php')) {
            require APP_PATH . '/views/layouts/main.php';
        } else {
            echo '<h1>404 - Sayfa Bulunamadı</h1>';
        }
        exit();
    }

    /**
     * Group routes with prefix
     */
    public function group($prefix, $callback) {
        $previousRoutes = $this->routes;
        $this->routes = [];

        call_user_func($callback, $this);

        $newRoutes = $this->routes;
        $this->routes = $previousRoutes;

        $prefix = $this->normalizePath($prefix);

        foreach ($newRoutes as $route) {
            $route['path'] = $prefix . $route['path'];
            $this->routes[] = $route;
        }

        return $this;
    }
}
