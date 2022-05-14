<?php

namespace App\Form;

use App\Entity\CreateProgramme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateProgrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lundi')
            ->add('mardi')
            ->add('mercredi')
            ->add('jeudi')
            ->add('vendredi')
            ->add('samedi')
            ->add('dimanche')
            ->add('conseils')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateProgramme::class,
        ]);
    }
}
