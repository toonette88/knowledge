<?php

namespace App\Form;

use App\Entity\Lesson;
use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Form type for creating and editing Lesson entities.
 */
class LessonType extends AbstractType
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
            ->add('title', TextType::class, [
                'label' => 'Titre de la leçon' // Field label
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix de la leçon' // Field label
            ])
            ->add('course', EntityType::class, [
                'class' => Course::class, // Links this field to the Course entity
                'label' => 'Cursus', // Field label
                'choices' => $options['courses'], // List of available courses
                'choice_label' => 'title', // Display the course title
                'placeholder' => 'Sélectionner un cursus', // Placeholder text
                'required' => true, // This field is mandatory
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
            'data_class' => Lesson::class, // Binds this form to the Lesson entity
            'courses' => [] // Default value to avoid errors
        ]);
    }
}
