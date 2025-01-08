<?php

namespace App\Form; 

use Symfony\Component\Form\AbstractType; 
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Utilisateur;


class Connexion extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Pseudo'
                ],
                'label' => 'Pseudo'
            ])
            ->add('password', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Mot de passe sécurisé'
                ],
                'label' => 'Mot de passe'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
           'data_class' => Utilisateur::class,

        ]);
    }
}