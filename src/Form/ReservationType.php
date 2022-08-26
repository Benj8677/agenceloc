<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'format' => 'dd-MM-yyyy',
                'label' => "Départ",
                'widget' => 'single_text',
                'mapped' => false,
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false,
            ])
            ->add('dateRetour', DateType::class, [
                'format' => 'dd-MM-yyyy',
                'label' => "Retour",
                'widget' => 'single_text',
                'mapped' => false,
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false,
            ])
            ->add('heureDebut', ChoiceType::class,[
                'label' => "heure",
                'mapped' => false,
                'choices' => [
                    0 => 0,
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                    6 => 6,
                    7 => 7,
                    8 => 8,
                    9 => 9,
                    10 => 10,
                    11 => 11,
                    12 => 12,
                    13 => 13,
                    14 => 14,
                    15 => 15,
                    16 => 16,
                    17 => 17,
                    18 => 18,
                    19 => 19,
                    20 => 20,
                    21 => 21,
                    22 => 22,
                    23 => 23
                ],
            ])
            ->add('heureRetour', ChoiceType::class,[
                'label' => "heure",
                'mapped' => false,
                'choices' => [
                    0 => 0,
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                    6 => 6,
                    7 => 7,
                    8 => 8,
                    9 => 9,
                    10 => 10,
                    11 => 11,
                    12 => 12,
                    13 => 13,
                    14 => 14,
                    15 => 15,
                    16 => 16,
                    17 => 17,
                    18 => 18,
                    19 => 19,
                    20 => 20,
                    21 => 21,
                    22 => 22,
                    23 => 23
                ],
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
