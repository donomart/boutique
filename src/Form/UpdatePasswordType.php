<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => false, 'disabled' => true, 'attr' => ['placeholder' => 'Adresse email']])
            //->add('roles')
            ->add('currentPassword', PasswordType::class, ['label' => false, 'attr' => ['placeholder' => 'Mot de passe actuel']])
            ->add('newPassword', PasswordType::class, ['label' => false, 'attr' => ['placeholder' => 'Nouveau mot de passe']])
            ->add('newConfirmPassword', PasswordType::class, ['label' => false, 'attr' => ['placeholder' => 'Confirmer le nouveau mot de passe']])
            ->add('submit', SubmitType::class, ['label' => 'Modifier le mot de passe', 'attr' => ['class' => 'col-12 btn btn-primary']]);            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
