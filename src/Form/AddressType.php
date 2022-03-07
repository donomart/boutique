<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Alias pour cette adresse', 'attr' => ['placeholder' => 'Alias pour cette adresse']])
            ->add('firstName', TextType::class, ['label' => 'Prénom', 'attr' => ['placeholder' => 'Prénom']])
            ->add('lastName', TextType::class, ['label' => 'Nom', 'attr' => ['placeholder' => 'Nom']])
            ->add('company', TextType::class, ['label' => 'Entreprise', 'required' => false, 'attr' => ['placeholder' => 'Entreprise (optionnel)']])
            ->add('address', TextType::class, ['label' => 'Adresse', 'attr' => ['placeholder' => 'Adresse']])
            ->add('zip_code', TextType::class, ['label' => 'Code Postal', 'attr' => ['placeholder' => 'Code Postal']])
            ->add('city', TextType::class, ['label' => 'Ville', 'attr' => ['placeholder' => 'Ville']])
            ->add('country', CountryType::class, ['label' => 'Pays', 'attr' => ['placeholder' => 'Pays']])
            ->add('phone', TelType::class, ['label' => 'Téléphone', 'attr' => ['placeholder' => 'Tél']])
            ->add('submit', SubmitType::class,  ['label' => 'Mettre à jour', 'attr' => ['class' => 'col-12 btn btn-success']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
