<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\LessonContent;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Création des catégories
        $categoriesData = [
            'Musique' => [
                [
                    'title' => 'Cursus d’initiation à la guitare',
                    'price' => 50,
                    'lessons' => [
                        ['title' => 'Découverte de l’instrument', 'price' => 26],
                        ['title' => 'Les accords et les gammes', 'price' => 26],
                    ],
                ],
                [
                    'title' => 'Cursus d’initiation au piano',
                    'price' => 50,
                    'lessons' => [
                        ['title' => 'Découverte de l’instrument', 'price' => 26],
                        ['title' => 'Les accords et les gammes', 'price' => 26],
                    ],
                ],
            ],
            'Informatique' => [
                [
                    'title' => 'Cursus d’initiation au développement web',
                    'price' => 60,
                    'lessons' => [
                        ['title' => 'Les langages Html et CSS', 'price' => 32],
                        ['title' => 'Dynamiser votre site avec Javascript', 'price' => 32],
                    ],
                ],
            ],
            'Jardinage' => [
                [
                    'title' => 'Cursus d’initiation au jardinage',
                    'price' => 30,
                    'lessons' => [
                        ['title' => 'Les outils du jardinier', 'price' => 16],
                        ['title' => 'Jardiner avec la lune', 'price' => 16],
                    ],
                ],
            ],
            'Cuisine' => [
                [
                    'title' => 'Cursus d’initiation à la cuisine',
                    'price' => 44,
                    'lessons' => [
                        ['title' => 'Les modes de cuisson', 'price' => 23],
                        ['title' => 'Les saveurs', 'price' => 23],
                    ],
                ],
                [
                    'title' => 'Cursus d’initiation à l’art du dressage culinaire',
                    'price' => 48,
                    'lessons' => [
                        ['title' => 'Mettre en oeuvre le style dans l’assiette', 'price' => 26],
                        ['title' => 'Harmoniser un repas à quatre plats', 'price' => 26],
                    ],
                ],
            ],
        ];


        foreach ($categoriesData as $categoryName => $courseList) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            foreach ($courseList as $courseData) {
                $course = new Course();
                $course->setTitle($courseData['title']);
                $course->setPrice($courseData['price']);
                $course->setCategory($category);
                $course->setDescription('Parfait pour apprendre les bases');
                $course->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($course);

                foreach ($courseData['lessons'] as $lessonData) {
                    $lesson = new Lesson();
                    $lesson->setTitle($lessonData['title']);
                    $lesson->setPrice($lessonData['price']);
                    $lesson->setCourse($course);
                    $manager->persist($lesson);
                
                    // Ajouter plusieurs parties de contenu à chaque leçon
                    for ($i = 1; $i <= 3; $i++) { // Ici, on génère 3 parties de contenu
                        $lessonContent = new LessonContent();
                        $lessonContent->setLesson($lesson);
                        $lessonContent->setContent($faker->paragraphs(3, true)); // Génère 3 paragraphes
                        $lessonContent->setPart($i); // Si tu as un champ "part" pour numéroter les contenus
                        $manager->persist($lessonContent);                   
                    }
                }
            }
        }   

        // Création de 10 utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setName('user' . $i);
            $user->setFirstname('firstname' . $i);
            $user->setEmail('user' . $i . '@example.fr');
            $user->setRoles(['ROLE_USER']);

            $password = $this->hasher->hashPassword($user, 'pass_1234');
            $user->setPassword($password);
            $manager->persist($user);

            $this->addReference('user_' . $i, $user);
        }

        // Création d'un utilisateur admin
        $user = new User();
        $user->setName('admin');
        $user->setFirstname('admin');
        $user->setEmail('admin@example.fr');
        $user->setRoles(['ROLE_ADMIN']);

        $password = $this->hasher->hashPassword($user, 'pass_1234');
        $user->setPassword($password);
        $manager->persist($user);

        $manager->flush();
    }
}
