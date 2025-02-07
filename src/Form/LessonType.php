<?php

namespace App\Form;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Entity\LessonContent;  // Ajouter l'entité LessonContent
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType; 
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la leçon'
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix de la leçon'
            ])
            ->add('course', EntityType::class, [
                'class' => Course::class,
                'label' => 'Cursus',
                'choices' => $options['courses'], 
                'choice_label' => 'title', 
                'placeholder' => 'Sélectionner un cursus',
                'required' => true,
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
            'courses' => []  // Définit une valeur par défaut pour éviter les erreurs
        ]);
    }
}

