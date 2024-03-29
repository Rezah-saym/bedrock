<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel nom souhaitez-vous donner à votre adrress',
                'attr'  => [
                        "placeholder" => 'Nommez votre addresse'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'attr'  => [
                        "placeholder" => 'Entrez votre prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'attr'  => [
                        "placeholder" => 'Entrer votre nom'
                ]
            ])
            ->add('company', TextType::class, [
                'label' => 'Votre Société',
                'required' =>false,
                'attr'  => [
                        "placeholder" => 'Entrer votre company (facultatif)'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Votre addresse',
                'attr'  => [
                        "placeholder" => 'Entrer votre addresse ...'
                ]
            ])
            ->add('postal', TextType::class, [
                'label' => 'Votre code postal',
                'attr'  => [
                        "placeholder" => 'Entrer votre code pastal'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr'  => [
                        "placeholder" => 'Entrer votre ville'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr'  => [
                        "placeholder" => 'Votre pays'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Votre téléphone',
                'attr'  => [
                        "placeholder" => 'Entrer votre numero'
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Valider',
                'attr' =>[
                    'class' => 'mt-4 btn-outline-dark'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
