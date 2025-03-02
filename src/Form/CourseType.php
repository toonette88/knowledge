<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Course;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for creating and editing a Course entity.
 */
class CourseType extends AbstractType
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
            ->add('title') // Course title input field
            ->add('description') // Course description input field
            ->add('price') // Course price input field
            ->add('category', EntityType::class, [
                'class' => Category::class, // Links to the Category entity
                'choice_label' => 'name', // Displays the category name in the dropdown
                'placeholder' => 'Sélectionner une catégorie', // Default empty selection text
                'required' => true, // Makes category selection mandatory
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
            'data_class' => Course::class, // Binds this form to the Course entity
        ]);
    }
}
