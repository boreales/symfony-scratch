<?php

namespace App\Controller;

use App\Attributes\Route;

class EventController{

    #[Route('/events')]
    public function list()
    {
        echo "<h1>Bienvenue sur la page des événements</h1>";
        echo "<a href='/symfony-scratch'>Retour à l'accueil</a>";
    }
}
