<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType; 
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RoleMetier extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('userRoles', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true,
                'label'=> false,
            ]);
    } 
        
    public function configureOptions(OptionsResolver $resolver):void
    { 
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]); 
    } 
}