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
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Adresse mail'
                ],
                'label' => 'Adresse mail'
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