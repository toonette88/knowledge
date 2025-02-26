<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\LessonContent;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\Progression;
use App\Enum\OrderStatus;
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
            //echo "Catégorie créée : " . $category->getName() . "\n";

            foreach ($courseList as $courseData) {
                $course = new Course();
                $course->setTitle($courseData['title']);
                $course->setPrice($courseData['price']);
                $course->setCategory($category);
                $course->setDescription('Parfait pour apprendre les bases');
                $course->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($course);
                //echo "Cursus créé : " . $course->getTitle() . "\n";


                foreach ($courseData['lessons'] as $lessonData) {
                    $lesson = new Lesson();
                    $lesson->setTitle($lessonData['title']);
                    $lesson->setPrice($lessonData['price']);
                    $lesson->setCourse($course);
                    $manager->persist($lesson);
                    //echo "Leçon créée : " . $lesson->getTitle() . "\n";
                
                    // Ajouter plusieurs parties de contenu à chaque leçon
                    for ($i = 1; $i <= 3; $i++) { 
                        $lessonContent = new LessonContent();
                        $lessonContent->setLesson($lesson);
                        $lessonContent->setContent($faker->paragraphs(5, true)); // Génère 3 paragraphes
                        $lessonContent->setPart($i); // Si tu as un champ "part" pour numéroter les contenus
                        $manager->persist($lessonContent);
                        //echo "Contenu créé" . "\n";                
                    }
                }
            }
        }   
        $manager->flush();

         // --- Création des utilisateurs ---
         $users = [];
         for ($i = 0; $i < 10; $i++) {
             $user = new User();
             $user->setName('user' . $i)
                 ->setFirstname('firstname' . $i)
                 ->setEmail('user' . $i . '@example.fr')
                 ->setRoles(['ROLE_USER']);
 
             $password = $this->hasher->hashPassword($user, 'pass_1234');
             $user->setPassword($password);
             $manager->persist($user);
             $users[] = $user;
 
             //echo "Utilisateur créé : " . $user->getEmail() . "\n";
         }
 
         // --- Création des commandes et des détails de commande ---
         $courses = $manager->getRepository(Course::class)->findAll();
 
         if (empty($users)) {
            // echo "Aucun utilisateur n'a été créé.\n";
         }
 
         if (empty($courses)) {
             //echo "Aucun cours n'a été créé.\n";
         }
 
         foreach ($users as $user) {
             $order = new Order();
             $order->setUser($user)
                 ->setStatus(OrderStatus::PAID)
                 ->setTotal(0)
                 ->setCreatedAt(new \DateTimeImmutable());
 
             $total = 0;
 
             /*for ($i = 0; $i < 2; $i++) {
                 $detail = new OrderDetail();
                 $randomCourse = $faker->randomElement($courses);
 
                 $detail->setCourse($randomCourse)
                     ->setUnitPrice($randomCourse->getPrice())
                     ->setOrder($order);
 
                 $total += $randomCourse->getPrice();
                 $manager->persist($detail);
 
                // echo "Ajout du cours : " . $randomCourse->getTitle() . " à la commande de l'utilisateur " . $user->getEmail() . "\n";
             }
 
             $order->setTotal($total);
             $manager->persist($order);*/
             //echo "Commande créée pour l'utilisateur : " . $user->getEmail() . "\n";
         }
 
         // --- Création d'un utilisateur admin ---
         $admin = new User();
         $admin->setName('admin')
             ->setFirstname('admin')
             ->setEmail('admin@example.fr')
             ->setRoles(['ROLE_ADMIN']);
 
         $password = $this->hasher->hashPassword($admin, 'pass_1234');
         $admin->setPassword($password);
         $manager->persist($admin);
        // echo "Utilisateur admin créé : " . $admin->getEmail() . "\n";
 
         $manager->flush();

        // --- Création des progressions ---
        $users = $manager->getRepository(User::class)->findAll();
        $lessons = $manager->getRepository(Lesson::class)->findAll();
        $courses = $manager->getRepository(Course::class)->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) { // 3 progressions par utilisateur
                $progression = new Progression();

                $lesson = $faker->randomElement($lessons);
                $course = $lesson->getCourse(); // Récupération du cursus lié à la leçon

                $progression->setUser($user)
                    ->setLesson($lesson)
                    ->setCourse($course)
                    ->setChapter(rand(1, 5))
                    ->setPercentage(rand(0, 100));

                $manager->persist($progression);
            }
        }

        $manager->flush();

        $user = $manager->getRepository(User::class)->findOneBy(['name' => 'user3']);

        $lesson1 = $manager->getRepository(Lesson::class)->find(9);
        $lesson2 = $manager->getRepository(Lesson::class)->find(10);
        
        if ($user && $lesson1 && $lesson2) {
            // Vérifier si les progressions existent déjà
            $progression1 = $manager->getRepository(Progression::class)->findOneBy([
                'user' => $user,
                'lesson' => $lesson1
            ]);
        
            $progression2 = $manager->getRepository(Progression::class)->findOneBy([
                'user' => $user,
                'lesson' => $lesson2
            ]);
        
            // Si elles existent, mettre à jour le pourcentage
            if ($progression1) {
                $progression1->setPercentage(0);
            } else {
                $progression1 = new Progression();
                $progression1->setUser($user)
                    ->setLesson($lesson1)
                    ->setCourse($lesson1->getCourse())
                    ->setChapter(1)
                    ->setPercentage(0);
                $manager->persist($progression1);
            }
        
            if ($progression2) {
                $progression2->setPercentage(100);
            } else {
                $progression2 = new Progression();
                $progression2->setUser($user)
                    ->setLesson($lesson2)
                    ->setCourse($lesson2->getCourse())
                    ->setChapter(3)
                    ->setPercentage(100);
                $manager->persist($progression2);
            }
        }
        
        $manager->flush();
    }        
}
 