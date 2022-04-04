<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GameRepository;
use App\Entity\Game;
use App\Form\GameType;
use Doctrine\ORM\EntityManagerInterface;

class GameController extends AbstractController
{
    #[Route('/admin/game', name: 'app_admin_game')]
    public function index(GameRepository $gameRepository): Response
    {
        /*On ne PEUT PAS instancier d'objets d'une classe Repository
            On doit les passer dans les arguments d'une méthode d'un contrôleur
            NB : pour chaque classe Entity créée, il ya une classe Repository qui correspond et qui permet de faire une requête SELECT sur la table correspondante */
        // $gameRepository = new GameRepository;
        return $this->render('admin/game/index.html.twig', [
            'games' => $gameRepository->findAll()
        ]);
    }

    #[Route('/admin/game/new', name: 'app_admin_game_new')]
    public function new(Request $request, EntityManagerInterface $em)
    {
        /**
        * Ces valeurs sont dans des propriétés.
        * $request->query     contient    $_GET
        * $request->request   contient    $_POST
        * $request->server    contient    $SERVER
        * ...
        * Pour accéder aux valeurs, on utilisera sur ces propriétés la * méthode * ->get('indice')
        * La classe EntityManager va permettre d'exécuter les requêtes * qui modifient les données (INSERT, UPDATE, DELETE).
        *L'EntityManager va toujours utiliser des objets Entity pour modifier les données.
        */

        $jeu = new Game;
        /* On crée un objet $form pour gérer le formulaire. Il est créé à partir de la classe GameType. On relie ce formulaire à l'objet $jeu*/
        $form = $this->createForm(GameType::class, $jeu);
        /* L'objet $form va gérer ce qui vient de la requête HTTP
            (avec l'objet$request) */
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ) {
            // la méthode persist() prépare la requête INSERT avec les données de l'objet passé en argument
            $em->persist($jeu);

            // la méthode flush() exécute les requêtes en attente et donc modifie la base de données
            $em->flush();

            // Redirection vers une route du projet
            return $this->redirectToRoute("app_admin_game");
        }

        return $this->render('admin/game/form.html.twig', [
            "formGame" => $form->createView()
        ]);
    }

    #[Route('/admin/game/edit/{id}', name: 'app_admin_game_edit')]
    public function edit(request $rq, EntityManagerInterface $em, GameRepository $gameRepository, $id)
    {
        $jeu = $gameRepository->find($id);
        $form = $this->createForm(GameType::class, $jeu);
        $form->handleRequest($rq);
        if($form->isSubmitted() && $form->isValid()){
            if($fichier = $form->get('image')->getData()){
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nomFichier = str_replace(" ", "_", $nomFichier);
                $nomFichier .= "_" . uniqid() . "." . $fichier->guessExtension();
                $fichier->move("images", $nomFichier);
                $jeu->setImage($nomFichier);
            }
            $em->flush();
            return $this->redirectToRoute('app_admin_game');
        }


        return $this->render("admin/game/form.html.twig", [
            "formGame" => $form->createView()
        ]);
    }

    #[Route('/admin/game/modifier/{title}', name: 'app_admin_game_modifier')]
    // Si le chemin de la route contient une partie variable (donc entre {}), on peut récupérer un objet entité directement avec la valeur de cette partie de l'URL. Il faut que le nom de ce paramètre soit le nom d'une propriété de la classe Entity.
    // Par exemple, le paramètre est {title}, car dans l'entité Game il y a une propriété title.
    // Dans les arguements de la méthode, on peut alors utiliser un objet de la classe Game ($jeu dans l'exemple)
    public function modifier(request $rq, EntityManagerInterface $em, Game $jeu)
    {
        // $jeu = $gameRepository->find($id);
        $form = $this->createForm(GameType::class, $jeu);
        $form->handleRequest($rq);
        if( $form->isSubmitted() && $form->isValid() ) {
            $em->flush();
            return $this->redirectToRoute("app_admin_game");
        }

        return $this->render("admin/game/form.html.twig", [
            "formGame" => $form->createView()
        ]);
    }


    #[Route('/admin/game/delete/{id}', name: 'app_admin_game_delete')]
    public function delete($id, GameRepository $gr, Request $rq, EntityManagerInterface $em)
    {
        $jeu = $gr->find($id);
        if( $rq->isMethod("POST") ) {
            $em->remove($jeu);
            $em->flush();
            return $this->redirectToRoute("app_admin_game");
        }
        return $this->render("admin/game/delete.html.twig", [
        "game" => $jeu
        ]);
    }
}