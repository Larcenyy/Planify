<?php

namespace App\Form;

use App\Entity\Events;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'required' => true,
                    'label' => 'Prénom',
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length([
                            'min' => 2,
                            'max' => 30,
                            'minMessage' => 'Le prénom doit contenir au minimum {{ limit }} caractères.',
                            'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
                        ]),
                    ]
            ])
            ->add('lastname', TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlenght' => 2,
                        'maxlenght' => 30
                    ],
                    'required' => true,
                    'label' => 'Nom',
                    'constraints' => [
                        new Assert\Length([
                            'min' => 2,
                            'max' => 30,
                            'minMessage' => 'Le prénom doit contenir au minimum {{ limit }} caractères.',
                            'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
                        ]),
                    ]
                ])
            ->add('email', EmailType::class,
                [
                    'attr' => [
                        'class' => 'form-control',
                        'minlenght' => 3,
                        'maxlenght' => 45
                    ],
                    'required' => true,
                    'label' => 'Adresse email',
                    'constraints' => [
                        new Assert\Email([
                            'message' => 'Veuillez entrer une adresse email valide.',
                        ]),
                        new Assert\Length([
                            'min' => 3,
                            'max' => 45,
                            'minMessage' => 'L\'adresse email doit contenir au minimum {{ limit }} caractères.',
                            'maxMessage' => 'L\'adresse email ne peut pas dépasser {{ limit }} caractères.'
                        ]),
                    ]
                ])
            ->add('password', RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les deux mots de passe doivent être identiques.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'first_options'  => ['label' => 'Mot de passe', 'attr' => ['class' => 'form-control']],
                    'second_options' => ['label' => 'Confirmer votre mot de passe', 'attr' => ['class' => 'form-control', 'minlenght' => 8, 'maxlenght' => 60]],
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Length([
                            'minMessage' => 'Votre mot de passe doit contenir au minimum {{ limit }} caractères',
                            'max' => 50,
                            'min' => 8,
                        ]),
                         new Regex([
                             'pattern' => '/(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).+/',
                             'message' => 'Le mot de passe doit contenir au minimum une majuscule, un chiffre et un caractère spécial'
                         ])
                    ]
                ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => "mt-2 btn btn-primary w-100",
                ],
                'label' => 'Sauvegarder'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
//        $resolver->setDefaults([
//            'data_class' => User::class,
//        ]);
    }
}
