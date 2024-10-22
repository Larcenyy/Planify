<?php

namespace App\Form;

use App\Entity\Events;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class EventsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'required' => true,
                    'label' => "Titre de l'événément",
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length([
                            'min' => 3,
                            'max' => 30,
                            'minMessage' => 'Le titre doit contenir au minimum {{ limit }} caractères.',
                            'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères.'
                        ]),
                    ]
                ])
            ->add('content', TextareaType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlenght' => 10,
                        'maxlenght' => 255
                    ],
                    'required' => true,
                    'label' => "Description de l'événement",
                    'constraints' => [
                        new Assert\Length([
                            'min' => 10,
                            'max' => 255,
                            'minMessage' => 'La description doit contenir au minimum {{ limit }} caractères.',
                            'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.'
                        ]),
                    ]
                ])
            ->add('startAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                ],
                'required' => true,
                'label' => "L'événement démarrera le",
            ])
            ->add('endAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                ],
                'required' => true,
                'label' => "L'évenement se terminera le",
            ])
            ->add('location', TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'required' => true,
                    'label' => "Lieux de l'événément",
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length([
                            'min' => 3,
                            'max' => 30,
                            'minMessage' => 'Le lieux doit contenir au minimum {{ limit }} caractères.',
                            'maxMessage' => 'Le lieux ne peut pas dépasser {{ limit }} caractères.'
                        ]),
                    ]
                ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => "mt-2 btn btn-primary w-100",
                ],
                'label' => 'Soumettre'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
        ]);
    }
}
