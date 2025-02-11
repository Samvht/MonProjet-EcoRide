<?php

namespace App\Form; 

use App\Entity\Role;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;



class Inscription extends AbstractType
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
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Adresse mail'
                ],
                'label' => 'Adresse mail'
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Mot de passe sécurisé'
                ],
                'label' => 'Mot de passe'
            ])
            ->add('userRoles', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true,
                'label'=> false,
            'attr' => [
                'class' => 'form-check-inline' ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
           'data_class' => Utilisateur::class,
           'validation_group' => ['inscritpion'],

        ]);
    }
}