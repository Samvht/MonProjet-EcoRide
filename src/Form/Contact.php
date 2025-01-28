<?php

namespace App\Form; 

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\AbstractType; 
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class Contact extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' =>'Adresse mail',
                    'required' => true,
                ],
                'label' => 'Votre Adresse mail'
            ])
            ->add('titre', TextType::class, [
                'attr' => [
                    'placeholder' =>'Titre de la demande',
                ],
                'label' => 'Titre de la demande'
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'placeholder' =>'Votre message',
                ],
                'label' => 'Votre message'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}