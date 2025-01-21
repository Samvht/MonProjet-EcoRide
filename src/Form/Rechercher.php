<?php

namespace App\Form; 

use Symfony\Component\Form\AbstractType; 
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Covoiturage;

class Rechercher extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('lieu_depart', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Adresse de depart'
                ],
                'label'=> false   #Pour que le label ne s'affiche pas,et ajuster le placeholder
            ])
            ->add('lieu_arrivee', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Adresse d\'arrivee'
                ],
                'label' => false
            ])
            ->add('date_depart', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Date'
                ],
                'label' => false
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