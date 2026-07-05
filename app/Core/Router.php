<?php
/**
 * Router - Simple regex-based router with named parameters.
 */

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $currentMiddleware = [];

    public function get(string $path, array $action): self
    {
        return $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, array $action): self
    {
        return $this->addRoute('POST', $path, $action);
    }

    public function middleware(array $middleware): self
    {
        $this->currentMiddleware = $middleware;
        return $this;
    }

    public function group(callable $callback): void
    {
        $savedMiddleware = $this->currentMiddleware;
        $callback($this);
        $this->currentMiddleware = $savedMiddleware;
    }

    private function addRoute(string $method, string $path, array $action): self
    {
        $pattern = $this->pathToRegex($path);
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'controller' => $action[0],
            'action' => $action[1],
            'middleware' => $this->currentMiddleware,
        ];
        return $this;
    }

    private function pathToRegex(string $path): string
    {
        // Convert {param} to named capture groups
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        // Handle the .{ext} pattern for CDN routes
        $pattern = str_replace('.(?P<ext>[^/]+)', '\.(?P<ext>[^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run middleware
                foreach ($route['middleware'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    if (is_array($middlewareClass)) {
                        $mwClass = $middlewareClass[0];
                        $mwParams = array_slice($middlewareClass, 1);
                        $middleware = new $mwClass();
                        $middleware->handle($request, $mwParams);
                    } else {
                        $middleware->handle($request);
                    }
                }

                // Instantiate controller and call action
                $controller = new $route['controller']();
                $actionMethod = $route['action'];

                call_user_func_array([$controller, $actionMethod], $params);
                return;
            }
        }

        // No route matched
        \App\Core\Response::error(404, 'The page you\'re looking for doesn\'t exist or has been moved.');
    }
}
