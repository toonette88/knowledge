<?php

namespace App\Form;

use App\Entity\Lesson;
use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType; 
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la leçon'
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix de la leçon'
            ])
            ->add('course', ChoiceType::class, [
                'choices' => $options['courses'],
                'choice_label' => function($course) {
                    return $course->getTitle();
                },
                'label' => 'Cursus associé'
            ])
            ->add('contents', CollectionType::class, [
                'entry_type' => TextareaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => [
                    'label' => false, // Ne pas ajouter de label à chaque champ de contenu
                ],
                'prototype' => true,  // Permet de rendre le prototype accessible en JavaScript
                'prototype_name' => '__name__', // Nom du prototype pour la substitution dans JS
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter la leçon'
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
            'courses' => []  // Permet de passer une liste de cursus depuis le contrôleur
        ]);
    }
}
