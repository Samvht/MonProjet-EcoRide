<?php

namespace App\Form; 

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\File;

class Modification extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'attr' => ['placeholder' =>'Pseudo'],
                'label' => 'Pseudo'
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['placeholder' =>'Mot de passe'],
                'label' => 'Nouveau mot de passe sécurisé',
                'required' => false,
                'mapped' => false,  #pour ne pas lier à l'entité utlisateur caar champs peut-être null ici
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Votre Numéro de téléphone',
                'required' =>false
            ])
            ->add('date_naissance', TextType::class, [
                'label' => 'Votre date de naissance',
                'required' =>false
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo de profil (jpeg, png)',
                'required' =>false, 
                'mapped' =>false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                         ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
           'data_class' => Utilisateur::class,
        ]);
    }
}