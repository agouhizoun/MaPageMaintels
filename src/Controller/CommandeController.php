<?php

namespace App\Controller;

use DateTime;
use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestationsRepository;
use App\Repository\CommandeDetailRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    #[Route('/passer-ma-commande', name: 'passer_commande')]
    public function passerCommande(SessionInterface $session, PrestationsRepository $repo, CommandeRepository $repoCom, CommandeDetailRepository $repoDet, EntityManagerInterface $manager)
    {

        $commande = new Commande();
        $panier = $session->get('panier', []);

       // dd($panier);
       // on récupère l'utilisateur en cours
       $user = $this->getUser();

       // s'il n'y a pas d'utilisateur en cours(connecté) il ne peut pas passer commande
       if(!$user)
       {
        $this->addFlash("error", "Veuillez vous connecter, ou vous inscrire dans le cas échéant, pour pouvoir passer commande !");

        return $this->redirectToRoute("app_login");


       }
       if(empty($panier))
       {
        $this->addFlash("error", "Votre panier est vide, vous ne pouvez pas passer commande !!!");

        return $this->redirectToRoute("prestations_all");

       }


       $dataPanier = [];
        $total = 0;
       
        foreach ($panier as $id => $quantite ) {

            $Prestation = $repo->find($id);
            $dataPanier[]=
            [
                "Prestation" => $Prestation, // prestation que j'ai récupérée avec l'id
                "quantite" => $quantite, 
                "sousTotal" => $Prestation->getPrixPrestation() * $quantite,
            ];

            $total += $Prestation->getPrixPrestation() * $quantite;
            // $total += $dataPanier["sousTotal"];

        }

        // dd($dataPanier);

        $commande->setUser($user)
            ->setDateDeCommande(new DateTime("now"))
            ->setMontant($total)
        ;

    $repoCom->add($commande);

    foreach ($dataPanier as $key => $value) {

        $commandeDetail = new CommandeDetail();

        $Prestation = $value["Prestation"];
        $quantite = $value["quantite"];
        $sousTotal = $value["sousTotal"];

        $commandeDetail->setCommande($commande)   
                        ->setPrestation($Prestation)
                        ->setQuantite($quantite) 
                        ->setPrix($sousTotal);    
                        
        $repoDet->add($commandeDetail);

    }

    $manager->flush();

    // une fois la commande passée on supprime le panier
    $session->remove("panier");

    $this->addFlash("success", "Bravo, votre commande a bien été enregistrée !!!");
    
    return $this->redirectToRoute("app_home");

    }
}
