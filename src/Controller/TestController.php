<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index()
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
            'texte' => 'le texte que je veux afficher'
        ]);
    }

    /*  EXERCICES
        Ajouter une route pour le chemin "/test/calcul" qui utilise le fichier test/index.html.twig et qui affiche le résultat de 12 + 7
     */
    #[Route('/test/calcul', name: 'app_test')]
    public function calcul()
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
            'texte' => 'le texte que je veux afficher',
            'calcul' => 12 + 7
        ]);
    }

    #[Route('/test/salut', name: 'app_test')]
    public function salut()
    {
        return $this->render('test/salut.html.twig', ['prenom' => 'Jacques']);
    }

    #[Route('/test/tableau', name: 'app_test')]
    public function tableau()
    {
        $array = [ "bonjour", "je m'appelle", 789, true ];
        return $this->render('test/tableau.html.twig', ["tableau" => $array ]);
    }

    #[Route('/test/assoc', name: 'app_test')]
    public function tab()
    {
        $p = [ 
            "nom" => "Hampartzoumian", 
            "prenom" => "Jacques",
            "age" => 26 
        ];
        return $this->render('test/assoc.html.twig', ["personne" => $p ]);
    }

    #[Route('/test/objet', name: 'app_test')]
    public function objet()
    {
        $objet = new \stdclass;
        $objet->prenom = "Ely";
        $objet->nom = "Sium";
        $objet->age = 26;
        return $this->render('test/assoc.html.twig', ["personne" => $objet ]);
    }
}
