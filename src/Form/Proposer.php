<?php

namespace App\Form; 

use App\Entity\Covoiturage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;

class Proposer extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
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
    ;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
           'data_class' => Covoiturage::class,
        ]);
    }
}