<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Commande;
use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_depart')
            ->add('date_fin')
            ->add('vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choice_label' => "titre"
                ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => "pseudo"
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
