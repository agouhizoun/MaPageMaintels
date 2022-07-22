<?php

namespace App\Controller;

use DateTime;
use App\Entity\Categorie;
use App\Entity\Prestations;
use App\Form\CategorieType;
use App\Form\PrestationsType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// AdminController pour la gestion des prestations et la gestion des clients par l'Administrateur
// Je vais déclarer une route préfixe pour toutes les routes se trouvant dans la page AdminController

#[Route('/admin', name: 'admin_')]

// La route pour afficher le formulaire deviendra http://127.0.0.1:8000/admin/ajout_prestations

class AdminController extends AbstractController
{
#[Route('/ajout-prestations', name: 'ajout_prestations')]
    public function ajout(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $prestations = new Prestations();
        $form = $this->createForm(PrestationsType::class, $prestations);
        // je traite le formulaire
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {

             $file = $form->get('photoForm')->getData();

            $fileName = $slugger->slug($prestations->getNom()) . uniqid() . '.' . $file->guessExtension();

            try{
                $file->move($this->getParameter('photos_prestations'), $fileName );
            }catch(FileException $e){
                // gerer les exceptions d'upload image
            }

            $prestations->setPhoto($fileName);
            $prestations->setDateEnregistrement(new DateTime("now"));
            // $manager = $this->getDoctrine()->getManager();

            $manager->persist($prestations);
            $manager->flush();

            return $this->redirectToRoute('admin_gestion_prestations');

        }
        // j'envoie le formulaire sur une page en utilisant le layout
        // le layout est dans prestations mais on peut le mettre directement à la racine
        // la variable formPrestations correspond à la vue de mon formulaire dans le dossier prestations
        // On déplace le formulaire de prestations vers admin et on modifie dans le render prestations en admin
        return $this->render('admin/formulaire.html.twig', [
            'formPrestation' => $form->createView()
        ]);
    }

#[Route('/gestion-prestations', name: 'gestion_prestations')]
    public function gestionPrestations(PrestationsRepository $repo)

        {
        $prestations = $repo->findAll();

        return $this->render("admin/gestion-prestations.html.twig", [
            'prestations' => $prestations
        ]);

    }

     #[Route('/details-prestation-{id<\d+>}', name: 'details_prestation')]

    public function detailsPrestation($id, PrestationsRepository $repo)
    {
        $prestation = $repo->find($id);

        
        return $this->render("admin/details-prestation.html.twig", [
            'prestation' => $prestation
        ]);
    }
   

    #[Route('/update-prestation-{id<\d+>}', name: 'update_prestation')]
    public function update($id, PrestationsRepository $repo, Request $request, SluggerInterface $slugger)
    {
        $prestation = $repo->find($id);

        $form = $this->createForm(PrestationsType::class, $prestation);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid()) {

            if ($form->get('photoForm')->getData()) {

                $file = $form->get('photoForm')->getData();

            $fileName = $slugger->slug($prestations->getNom()) . uniqid() . '.' . $file->guessExtension();

            try{
                $file->move($this->getParameter('photos_prestations'), $fileName );
            }catch(FileException $e){
                // gerer les exceptions d'upload image
            }

            $prestations->setPhoto($fileName);

                $file = $form->get('photoForm')->getData();

                $fileName = $slugger->slug($prestation->getTitre()) . uniqid() . '.' .$file->guessExtension();

                try {
                    $file->move($this->getParameter('photos_prestations'), $fileName);                
                } catch (FileException $e) {
                    // gerer les exceptions d'upload image
                }

                $prestation->setPhoto($fileName);

            }
            
            $repo->add($prestation, 1);

            return $this->redirectToRoute("admin_gestion_prestations");
        }

        return $this->render("admin/formulaire.html.twig", [
            'formPrestation' => $form->createView()
        ]);
    }


     #[Route('/delete-prestation-{id<\d+>}', name: 'delete_prestation')]
    public function delete($id, PrestationsRepository $repo)
    {
        $prestation = $repo->find($id);

        $repo->remove($prestation, 1);

        return $this->redirectToRoute("admin_gestion_prestations");
    }

    /**
     * @Route("/categorie-ajout", name="ajout_categorie")
     */
     #[Route('/categorie-ajout', name: 'ajout_categorie')]

    public function ajoutCategorie(Request $request, CategorieRepository $repo)
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $repo->add($categorie, 1);

            return $this->redirectToRoute("app_home");
        }

        return $this->render("admin/formCategorie.html.twig", [
            "formCategorie" => $form->createView()
        ]);

    }
}
