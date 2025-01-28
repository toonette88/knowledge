<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Cursus;
use App\Entity\Lesson;
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

        $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi sapien ligula, eleifend eget lobortis quis, viverra pretium dolor. Nunc dictum neque elit. Phasellus ut nisl lorem. Sed lobortis, magna id pulvinar tristique, mi dui aliquet sapien, a varius nisi tortor eu urna. Phasellus mollis elit non ornare molestie. In in ligula ac mauris rutrum accumsan. Integer faucibus ante tellus, sit amet ullamcorper quam accumsan id.

Donec sodales aliquam interdum. Sed semper dolor eu aliquam porttitor. Morbi vel vulputate velit. Cras bibendum tellus feugiat, dapibus felis fringilla, sagittis nisl. Vestibulum congue nunc quis maximus vulputate. Mauris feugiat dolor at arcu mattis sollicitudin. Donec nec auctor nunc.

Morbi tincidunt, metus sit amet pretium euismod, arcu tortor condimentum mauris, quis mattis nisi tellus non libero. Curabitur quis odio diam. Nulla et justo ex. Quisque tempor dui risus, ornare dictum odio viverra at. Proin sollicitudin varius ipsum at tincidunt. Aliquam a efficitur nisi. Donec sed ex vitae risus accumsan sodales. Donec lobortis dictum porttitor. Aliquam elit eros, tempus quis lorem id, tempor dapibus nibh.

Duis at fringilla purus, eget ornare orci. Proin aliquet placerat leo, eget maximus urna rhoncus scelerisque. In hac habitasse platea dictumst. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vivamus at metus lectus. Proin magna elit, venenatis eu nisi sed, semper eleifend ante. Maecenas viverra velit neque, non imperdiet tellus rutrum at. Etiam augue felis, molestie non semper vel, imperdiet eu eros. Pellentesque volutpat eros lacus. Pellentesque eu ultrices quam, vitae tincidunt nunc. Suspendisse blandit sapien luctus justo aliquet ultrices. Proin enim erat, pretium sit amet efficitur lacinia, ultrices et ex. Cras vel nunc massa. Ut maximus metus id tincidunt finibus. Aenean gravida convallis metus sit amet tincidunt. Interdum et malesuada fames ac ante ipsum primis in faucibus.

Pellentesque ullamcorper pulvinar dui vitae rutrum. Nulla cursus nisl a elit venenatis mattis a ut sem. In fermentum eros diam, id tincidunt eros faucibus ac. Nulla cursus, turpis sit amet eleifend dictum, eros dolor sagittis erat, in hendrerit nulla velit a mi. Donec fermentum purus eu finibus dapibus. Suspendisse elit nunc, consequat eget ipsum a, cursus condimentum ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec malesuada tincidunt augue, sit amet rhoncus justo euismod sit amet. Suspendisse vel purus dolor. Ut a elit luctus orci vehicula fermentum non non nisi.'
        ;

        foreach ($categoriesData as $categoryName => $cursusList) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            foreach ($cursusList as $cursusData) {
                $cursus = new Cursus();
                $cursus->setTitle($cursusData['title']);
                $cursus->setPrice($cursusData['price']);
                $cursus->setCategory($category);
                $cursus->setDescription('Parfait pour apprendre les bases');
                $cursus->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($cursus);

                foreach ($cursusData['lessons'] as $lessonData) {
                    $lesson = new Lesson();
                    $lesson->setTitle($lessonData['title']);
                    $lesson->setPrice($lessonData['price']);
                    $lesson->setContent($content);
                    $lesson->setCursus($cursus);
                    $manager->persist($lesson);
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
