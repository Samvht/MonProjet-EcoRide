<?php

namespace App\Form;

use App\Document\Preference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Preferences extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fumeur', CheckboxType::class, [
                'required' => false,
                'label' => 'Fumeur'
            ])
            ->add('animal', CheckboxType::class, [
                'required' => false,
                'label' => 'Animal'
            ])
            ->add('preferenceSupplementaire', TextareaType::class, [
                'required' => false,
                'label' => 'Préférences supplémentaires'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Preference::class,
        ]);
    }
}