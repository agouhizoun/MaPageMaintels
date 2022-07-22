<?php

namespace App\Controller;

use DateTime;
// Attention à importer DateTime au lieu de use Symfony\Component\Validator\Constraints\DateTime;
use App\Entity\Prestations;
use App\Form\PrestationsType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Lancer un controller Admin car la gestion des prestations doit se faire côté Administrateur.
// Donc passer la commande "symfony console make:controller Admin"
// et prendre tout le contenu de la route et de la fonction ajout pour le mettre dans AdminController

class PrestationsController extends AbstractController
{
    #[Route('/prestations/{id<\d+>}', name: 'prestation_show')]

    public function show($id, PrestationsRepository $repo)
    {
        $prestations = $repo->find($id);       

        return $this->render("prestations/show.html.twig", [
            'prestation' => $prestations
        ]);
    }     
       
 
     #[Route('/prestations', name: 'prestations_all')]

    public function all(PrestationsRepository $repo, CategorieRepository $repoCat)
    {
        $prestations = $repo->findAll();
        $categories = $repoCat->findAll();
       
        return $this->render("prestations/all.html.twig", [
            'prestations' => $prestations,
            'categories' => $categories          
        ]);
}


    #[Route('/categorie-{id<\d+>}', name: 'prestations_categorie')]

    public function categoriePrestations($id, CategorieRepository $repo)
    {
        // on récupère la categorie sur laquelle on a cliqué pour accéder aux prestations qui y sont liées
        $categorie = $repo->find($id);

        // $prestations = $categorie->getPrestations();

        $categories = $repo->findAll();
       

        return $this->render("prestations/all.html.twig", [
            // 'prestations' => $prestations,
            // Les lignes de codes : $prestations = $categorie->getPrestations(); et 'prestations' => $prestations,
            // sont remplacées par 'prestations' => $categorie->getprestations(),

            // on récupère les prestations de la categorie cliquée grâce à la relation
            'prestations' => $categorie->getPrestations(),
            'categories' => $categories
        ]);
}

}
