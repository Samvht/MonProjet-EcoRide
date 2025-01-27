<?php

namespace App\Form; 

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class Modification extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Pseudo',
                ],
                'label' => 'Pseudo'
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Mot de passe',
                ],
                'label' => 'Mot de passe sécurisé',
                'required' => false
            ])
            ->add('telephone', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Votre Numéro de téléphone',
                'required' =>false
            ])
            ->add('date_naissance', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Votre date de naissance',
                'required' =>false
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de profil (jpeg, png)',
                'required' =>false, 
                'mapped' =>false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
           'data_class' => Utilisateur::class,
        ]);
    }
}