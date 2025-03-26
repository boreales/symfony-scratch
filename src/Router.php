<?php

namespace App;

class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $this->parseYaml('../config/routes.yaml');
    }


    private function dispatch(string $controllerAction, array $params = [])
    {
        list($controllerClass, $method) = explode('::', $controllerAction);
        $controllerClass = new $controllerClass();
        call_user_func_array([$controllerClass, $method], $params);
    }

    private function parseYaml($file)
    {
        $yaml = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $data = [];
        $currentKey = null;

        foreach ($yaml as $line) {
            $line = trim($line);
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);

                if ($value === '') {
                    $currentKey = $key;
                    $data[$currentKey] = [];
                } else {
                    if ($currentKey) {
                        $data[$currentKey][$key] = $value;
                    } else {
                        $data[$key] = $value;
                    }
                }
            }
        }
        return $data;
    }

    public function resolve(string $requestUri)
    {
        foreach ($this->routes as $route) {
            $pathPattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9-_]+)', $route['path']);
            $regex = '#^' . prefix.$pathPattern . '$#';

            if (preg_match($regex, $requestUri, $matches)) {
                array_shift($matches);
                $this->dispatch($route['controller'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
