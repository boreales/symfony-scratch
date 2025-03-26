<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Charger les classes si on utilise Composer

use App\Router as Router;
const prefix = '/symfony-scratch';

// Charger les routes
$routes = require __DIR__ . '/../config/routes.php';

// Initialiser et exécuter le routeur
$router = new Router($routes);
$router->resolve($_SERVER['REQUEST_URI']);
