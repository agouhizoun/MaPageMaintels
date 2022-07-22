<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Prestations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PrestationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
             // Photo du formulaire de type File (fichier) et pas de mapping avec l'Entité
            ->add('photoForm', FileType::class, [
                "mapped" => false,
                "required" => false
            ])
            ->add('prixPrestation')
            ->add('categorie' , EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'placeholder' => "choisissez une categorie"
            ])
            ->add('envoyer', SubmitType::class)
            //Je ne veux pas de la dateEnregistrement dans le formulaire
            // Mais je vais utiliser un bouton envoyer et importer la classe SubmitType
            // ->add('dateEnregistrement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestations::class,
        ]);
    }
}
