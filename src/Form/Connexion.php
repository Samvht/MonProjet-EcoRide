<?php

namespace App\Form; 

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType; 
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class Connexion extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Adresse mail',
                    'required' => true,
                ],
                'label' => 'Adresse mail'
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Mot de passe sécurisé',
                    'required' => true,
                ],
                'label' => 'Mot de passe'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
           'data_class' => Utilisateur::class,

        ]);
    }
}