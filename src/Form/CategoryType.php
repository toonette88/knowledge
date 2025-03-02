<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Form type for creating and editing a Category entity.
 */
class CategoryType extends AbstractType
{
    /**
     * Builds the form structure.
     *
     * @param FormBuilderInterface $builder The form builder instance.
     * @param array $options Additional options for the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom :', // Label displayed in the form
                'attr' => [
                    'class' => 'form-control', // Bootstrap styling
                    'placeholder' => 'Entrez le nom de la catégorie' // Input placeholder
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer', // Submit button text
                'attr' => [
                    'class' => 'btn btn-primary btn-custom', // Bootstrap styling for the button
                ],
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
            'data_class' => Category::class, // Binds this form to the Category entity
        ]);
    }
}
