<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Le mot de passe et la confirmation doivent être identique.',
            'label' => 'Votre mot de passe',
            'required' => true,
            'first_options' => [
                'label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => 'Merci de saisir votre mot de passe'
                ]],
            'second_options' => [
                'label' => 'Confirmation du mot de passe',
                'attr' => [
                    'placeholder' => 'Merci de confirmez votre mot de passe'
                ]]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Devenez administrateur' => 'ROLE_ADMIN',
                    'Devenez utilisateur' => 'ROLE_USER'
                ],
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new Count([
                        'max' => 1,
                        'maxMessage' => 'Vous ne pouvez sélectionner qu\'un seul rôle.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Mettre à jour mon mot de passe",
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
