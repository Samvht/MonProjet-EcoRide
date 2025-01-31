<?php

namespace App\Form; 

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;

class Proposer extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        #Récupérer l'utilisateur connecté depuis les options
        $user = $options['user']; 

        $builder
            ->add('date_depart', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Date de départ'
            ],
            'label'=> false
        ])
             ->add('heure_depart', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Heure de départ'
            ],
            'label'=> false
        ])
            ->add('lieu_depart', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Adresse de départ'
            ],
            'label'=> false
        ])
            ->add('date_arrivee', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Date d\'arrivee'
            ],
            'label'=> false
        ])
            ->add('heure_arrivee', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Heure d\'arrivee'
            ],
            'label'=> false
        ])
            ->add('lieu_arrivee', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Adresse d\'arrivee'
            ],
            'label'=> false
        ])
            ->add('nbre_place', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Nombre de place disponible'
            ],
            'label'=> false
        ])
            ->add('prix_personne', TextType::class, [
                'attr' => [
                'class' => 'form-control',
                'placeholder' =>'Prix par personne'
            ],
            'label'=> false
        ])
        ->add('voiture', EntityType::class, [ #champ  lié à Voiture donc utlisation de EntityType
                    'class' => Voiture::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('v')
                            ->where('v.utilisateur = :utilisateur')
                            ->setParameter('utilisateur', $options['user']->getUtilisateurId()->toBinary());
                },
                    'choice_label' => 'immatriculation',
                    'placeholder' => 'Sélectionnez un véhicule',
                    'mapped' =>true,
                    'label'=> false
            ]);
    ;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
           'data_class' => Covoiturage::class,
        ]);
        $resolver->setRequired(['user']);
    }
}