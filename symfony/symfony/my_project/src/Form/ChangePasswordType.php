<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [

                'disabled' => true, 
                'label'  => 'Mon adresse email',
            ])

            ->add('firstname', TypeTextType::class, [

                'disabled' => true, 
                'label'  => 'Mon prénom',
            ])
            ->add('lastname', TypeTextType::class, [

                'disabled' => true, 
                'label'  => 'Mon nom',
            ])

            ->add('old_password', PasswordType::class, [

                'label'  => 'Mon mot de passe actuel',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre mot de passe actuel'
                ]

            ])

            ->add('new_password', RepeatedType::class, [
                'type' =>PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'le mot de passe et la confirmation doivent être identique',
                'label' => 'Mon nouveau mot de passe',
                'constraints' => new Length([
                    'min' =>2,
                    'max'=> 30
                ]),

                'required' => true,
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'attr'  => ['placeholder' => 'Merci de saisir votre nouveau mot de passe']
                ],

                'second_options' => [
                    'label' => 'Confirmez votre nouveau mot de passe',
                    'attr'  => ['placeholder' => 'Merci de confirmez votre nouveau mot de passe']
                ]

            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour',

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
