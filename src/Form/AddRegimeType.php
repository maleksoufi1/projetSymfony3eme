<?php

namespace App\Form;

use App\Entity\user;
use App\Entity\Regime;
use App\Entity\CategorieRegime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddRegimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('description')
            ->add('dificulte')
            ->add('CategorieRegime' ,EntityType::class , ['class'=>CategorieRegime::class,
            'choice_label'=>'libelle','placeholder' => 'Sélectionner Catégorie','expanded' => false,'multiple'=>false])
            ->add('prix')
            ->add('image' ,FileType::class , array('data_class' => null),['label' => 'Votre image'])
            ->add('save' ,SubmitType::class , ['label' => 'Valider'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Regime::class,
        ]);
    }
}
