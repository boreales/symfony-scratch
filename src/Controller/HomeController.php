<?php

namespace App\Controller;

class HomeController
{
    public function index()
    {
        echo "<h1>Bienvenue sur l'accueil</h1>";
        echo "<a href='events'>Voir les événements</a>";
    }
}
