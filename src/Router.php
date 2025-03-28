<?php

namespace App;

use App\Attributes\Route;

class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        //$this->routes = $this->parseYaml('../config/routes.yaml');
        $this->registerRoutes();
    }

    private function registerRoutes()
    {
        $controllerFiles = glob(__DIR__ . '/Controller/*.php');

        foreach ($controllerFiles as $file) {
            require_once $file;
            $className = 'App\\Controller\\' . basename($file, '.php');

            if (!class_exists($className)) {
                continue;
            }

            $reflection = new \ReflectionClass($className);
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $attributes = $method->getAttributes(Route::class);

                foreach ($attributes as $attribute) {
                    $instance = $attribute->newInstance();
                    $this->routes[$instance->path] = [$className, $method->getName()];
                }
            }
        }
    }


//    private function dispatch(string $controllerAction, array $params = [])
//    {
//        list($controllerClass, $method) = explode('::', $controllerAction);
//        $controllerClass = new $controllerClass();
//        call_user_func_array([$controllerClass, $method], $params);
//    }

    private function dispatch(string $controllerClass, string $method, array $params = [])
    {
        $controllerInstance = new $controllerClass();
        call_user_func_array([$controllerInstance, $method], $params);
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
        foreach ($this->routes as $path => $controllerData) {
            $pathPattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9-_]+)', $path);
            $regex = '#^' . prefix.$pathPattern . '$#';

            if (preg_match($regex, $requestUri, $matches)) {
                array_shift($matches);
                $this->dispatch($controllerData[0], $controllerData[1], $matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
