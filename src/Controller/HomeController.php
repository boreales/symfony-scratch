<?php

namespace App\Controller;

use App\Attributes\Route;

class HomeController
{
    #[Route('/')]
    public function index()
    {
        echo "<h1>Bienvenue sur l'accueil</h1>";
        echo "<a href='events'>Voir les événements</a>";
    }
}
