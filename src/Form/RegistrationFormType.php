<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for user registration.
 */
class RegistrationFormType extends AbstractType
{
    /**
     * Builds the registration form.
     *
     * @param FormBuilderInterface $builder The form builder instance.
     * @param array $options Additional options for the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [ 
                'attr' => [
                    'class' => 'form-control' // Adds Bootstrap styling
                ],
                'label' => 'Adresse mail' // Field label
            ])
            ->add("name", TextType::class, [ 
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => "Nom"
            ])
            ->add("firstname", TextType::class, [ 
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => "Prénom"
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class, // Uses password input
                'mapped' => false, // Not mapped to the entity directly
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password'
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir un mot de passe', // Ensures password is not empty
                        ]),
                        new Length([
                            'min' => 6, // Minimum length requirement
                            'minMessage' => 'Votre mot de passe doit contenir au moins 6 caractères',
                            'max' => 4096, // Security limit for passwords
                        ]),
                    ],
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe'
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre', // Error message when passwords don't match
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
