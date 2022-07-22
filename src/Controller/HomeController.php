<?php

namespace App\Controller;

use App\Repository\PrestationsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]

    public function index(PrestationsRepository $repo): Response
    {
        // on recupere les 6 dernières prestations ajoutées en bdd
        $dernièresPrestations = $repo->findBy([], ["dateEnregistrement" => "DESC"], 6 );
        
        // dd($dernièresPrestations);

        return $this->render('home/index.html.twig', [
            "prestations" => $dernièresPrestations
        ]);
    }
}
