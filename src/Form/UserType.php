<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for user profile management.
 */
class UserType extends AbstractType
{
    /**
     * Builds the user profile form.
     *
     * @param FormBuilderInterface $builder The form builder instance.
     * @param array $options Additional options for the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'], // Bootstrap styling
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('email', TextType::class, [
                'label' => 'Adresse email',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false, // Not mapped to the entity to avoid updating unless explicitly set
                'required' => false, // Allows leaving the password field empty if no change is needed
                'attr' => [
                    'placeholder' => 'Nouveau mot de passe',
                    'class' => 'form-control'
                ],
                'label' => 'Mot de passe',
            ]);
    }

    /**
     * Configures the default options for this form type.
     *
     * @param OptionsResolver $resolver The resolver to configure.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Links this form to the User entity
        ]);
    }
}
