<?php

namespace App\Form;

use App\Entity\Role;
use Symfony\Component\Form\AbstractType; 
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class RoleMetier extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('libelle', ChoiceType::class, [
                'choices' => [
                    'Chauffeur' => 'chauffeur',
                    'Passager' => 'passager',
                    'Les 2' => 'Les 2 (chauffeur et passager'
                ],
                'expanded'=> true,   #Pour afficher cases à cocher
                'multiple' => false, #Ne selectionne qu'une seule case
                'label' => false,
            ]);
    } 
        
    public function configureOptions(OptionsResolver $resolver):void
    { 
            $resolver->setDefaults([
                'data_class' => Role::class,
            ]); 
    } 
}