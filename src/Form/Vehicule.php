<?php

namespace App\Form; 

use App\Entity\Voiture;
use App\Entity\Marque;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;

class Vehicule extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('immatriculation', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Immatriculation du véhicule'
                ],
                'label'=> false   #Pour que le label ne s'affiche pas,et ajuster le placeholder
            ])
            ->add('date_premiere_immatriculation', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Date de la 1ère mise en circulation'
                ],
                'label'=> false, 
                'required' =>false
            ])
            /* choix d'enlever modèle pour une question de simplicité
            ->add('modele', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Modele du véhicule'
                ],
                'label'=> false,
                'required' => false
            ])*/
            ->add('couleur', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Couleur du véhicule'
                ],
                'label'=> false, 
                'required' => false
            ])
            ->add('energie', ChoiceType::class, [
                'choices' => [
                    'Electrique' => 'Electrique',
                    'Diesel' => 'Diesel',
                    'Essence' => 'Essence',
                    'Hybride' => 'Hybride',
                ],
                'expanded'=> true,   #Pour afficher cases à cocher
                'multiple' => false,
            ])
            ->add('marque', EntityType::class, [ #champ  lié à Marque donc utlisation de EntityType
                    'class' => Marque::class,
                    'choice_label' => 'libelle',
                    'placeholder' => 'Sélectionnez la marque du véhicule',
                    'mapped' =>true,
                    'label'=> false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver):void
    { 
            $resolver->setDefaults([
                'data_class' => Voiture::class,
            ]); 
    } 
}
